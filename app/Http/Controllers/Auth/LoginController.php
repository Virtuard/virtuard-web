<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\UserMeta;
use Carbon\Carbon;
use Dotenv\Util\Str;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Matrix\Exception;
use Modules\User\Events\SendMailUserRegistered;
use \Laravel\Socialite\Facades\Socialite;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Modules\User\Resources\UserResource;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/user/profile';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectTo()
    {
        if(Auth::user()->hasPermission('dashboard_access')){
            return '/admin';
        }else{
            return $this->redirectTo;
        }
    }

    public function showLoginForm()
    {
        return view('auth.login',['page_title'=> __("Login")]);
    }

    public function socialLogin($provider)
    {
        $this->initConfigs($provider);
        $redirectTo = request()->server('HTTP_REFERER',url('/'));
        session()->put('url.intended',$redirectTo);

        return Socialite::driver($provider)->redirect();
    }

    protected function initConfigs($provider)
    {
        switch($provider){
            case "facebook":
            case "google":
            case "twitter":
                config()->set([
                    'services.'.$provider.'.client_id'=>setting_item($provider.'_client_id'),
                    'services.'.$provider.'.client_secret'=>setting_item($provider.'_client_secret'),
                    'services.'.$provider.'.redirect'=>'/social-callback/'.$provider,
                ]);
            break;
        }
    }

    public function socialCallBack($provider)
    {
        try {
            $this->initConfigs($provider);

            $user = Socialite::driver($provider)->user();

            $redirectTo = $this->getRedirectTo();
            session()->forget('url.intended');


            if (empty($user)) {
                return redirect()->to('login')->with('error', __('Can not authorize'));
            }

            $existUser = User::getUserBySocialId($provider, $user->getId());

            if (empty($existUser)) {

                $meta = UserMeta::query()->where('name', 'social_' . $provider . '_id')->where('val', $user->getId())->first();
                if (!empty($meta)) {
                    $meta->delete();
                }

                // if we can not get email, then fake email will be generated
                $email = $user->getEmail();
                $email = $email?:$user->getId().'@'.$provider;

                $userByEmail = User::query()->where('email', $email)->first();
                if (!empty($userByEmail)) {

                    $userByEmail->addMeta('social_' . $provider . '_id', $user->getId());
                    $userByEmail->addMeta('social_' . $provider . '_email', $email);
                    $userByEmail->addMeta('social_' . $provider . '_name', $user->getName());
                    $userByEmail->addMeta('social_' . $provider . '_avatar', $user->getAvatar());
                    $userByEmail->addMeta('social_meta_avatar', $user->getAvatar());

                    $userByEmail->need_update_pw = 0;
                    $userByEmail->save();

                    // Login with user
                    Auth::login($userByEmail);

                    return redirect($redirectTo);
                }

                // Create New User
                $realUser = new User();
                $realUser->email = $email;
                $realUser->password = Hash::make(uniqid() . time());
                $realUser->name = $user->getName();
                $realUser->first_name = $user->getName();
                $realUser->user_name = generate_user_name($user->getName());
                $realUser->status = 'publish';
                $realUser->last_login_at = Carbon::now();
                $realUser->email_verified_at = Carbon::now();

                $realUser->save();

                $realUser->addMeta('social_' . $provider . '_id', $user->getId());
                $realUser->addMeta('social_' . $provider . '_email', $email);
                $realUser->addMeta('social_' . $provider . '_name', $user->getName());
                $realUser->addMeta('social_' . $provider . '_avatar', $user->getAvatar());
                $realUser->addMeta('social_meta_avatar', $user->getAvatar());

                $realUser->assignRole(setting_item('user_role'));

                try {
                    event(new SendMailUserRegistered($realUser));
                } catch (Exception $exception) {
                    Log::warning("SendMailUserRegistered: " . $exception->getMessage());
                }

                // Login with user
                Auth::login($realUser);

                return redirect($redirectTo);

            } else {

                if ($existUser->deleted == 1) {
                    return redirect()->route('login')->with('error', __('User blocked'));
                }
                if (in_array($existUser->status, ['blocked'])) {
                    return redirect()->route('login')->with('error', __('Your account has been blocked'));
                }

                $existUser->last_login_at = now();
                $existUser->need_update_pw = 0;
                $existUser->save();

                Auth::login($existUser);

                return redirect($redirectTo);
            }
        }catch (\Exception $exception)
        {
            $message = $exception->getMessage();
            if(empty($message) and request()->get('error_message')) $message = request()->get('error_message');
            if(empty($message)) $message = $exception->getCode();

            return redirect()->route('login')->with('error',$message);
        }
    }

    public function getRedirectTo(){
        $url = session()->get('url.intended', url('/'));
        session()->forget('url.intended');
        if($url == url('/') or $url ==route('login') or $url == route('auth.register')){
            $url = url('/');
        }
        return $url;
    }


    public function handleGoogleAccount(Request $request)
    {
        try {
            $id = $request->input('id');;
            $email = $request->input('email');
            $name = $request->input('display_name');
            $spittedName = explode(" ", $name);
            $firstName = $spittedName[0];
            $lastName = null;
            if(count($spittedName) > 1){
                $lastName = $spittedName[1];
            }
            $user_name = generate_user_name($name);
            
            $photoUrl = $request->input('photo_url');

            // Optional: validate the email
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json(['error' => 'Invalid or missing email'], 400);
            }

            $userByEmail = User::query()->where('email', $email)->first();
            if (!empty($userByEmail)) {
                $provider = 'google';
                $userByEmail->addMeta('social_' . $provider . '_id', $id);
                $userByEmail->addMeta('social_' . $provider . '_email', $email);
                $userByEmail->addMeta('social_' . $provider . '_name', $name);
                $userByEmail->addMeta('social_' . $provider . '_avatar', $photoUrl);;
                $userByEmail->addMeta('social_meta_avatar',  $photoUrl);

                $userByEmail->need_update_pw = 0;
                $userByEmail->save();
                
                $token = $userByEmail->createToken('access_token')->plainTextToken;
                
                $responseJson = [
                    'token' => $token,
                    'user' => new UserResource($userByEmail),
                    'status'    => 1,
                ];

                // Login with user
                Auth::login($userByEmail);
                
                return response()->json($responseJson, 200);

               
            }
            
            
            $userDto = [
                'google_user_id' => $id,
                'name' => $name,
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'user_name' => $user_name,
                'photo_profile' => $photoUrl,
            ];
            
            $userResponse = $this->createGoogleUser($userDto);
            return response()->json([
                'token' => $userResponse->createToken('access_token')->plainTextToken,
                'user' => new UserResource($userResponse),
                'status'    => 1,
            ], 200);
            
            

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }   
    
    
    public function createGoogleUser($user) {
        // Create New User
        $provider = 'google';
        try{
            $realUser = new User();
            $realUser->email = $user['email'];
            $realUser->password = Hash::make(uniqid() . time());
            $realUser->name = $user['name'];
            $realUser->first_name = $user['first_name'];
            $realUser->last_name = $user['last_name'];
            $realUser->user_name = $user['user_name'];
            $realUser->status = 'publish';
            $realUser->last_login_at = Carbon::now();
            $realUser->email_verified_at = Carbon::now();

            $realUser->addMeta('social_' . $provider . '_id', $user["google_user_id"]);
            $realUser->addMeta('social_' . $provider . '_email', $user["email"]);
            $realUser->addMeta('social_' . $provider . '_name', $user["name"]);
            $realUser->addMeta('social_' . $provider . '_avatar', $user["photo_profile"]);
            $realUser->addMeta('social_meta_avatar',  $user["photo_profile"]);


            $realUser->save();
            
            return $realUser;
      
        }catch(exception $e){
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
      
    }
        



}

