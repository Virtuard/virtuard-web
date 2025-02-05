<?php

namespace Modules\Api\Controllers;

use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Matrix\Exception;
use Modules\User\Events\SendMailUserRegistered;
use Modules\User\Resources\UserResource;
use Validator;

class AuthController extends Controller
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum', [
            'except' => [
                'login',
                'register',
                'checkEmailAvailability',
                'sendResetLinkEmail'
                ]
        ]);
    }

    /**
     * Get a JWT via given credentials.
     *
     */

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Auth"},
     *     summary="User login",
     *     description="Authenticate a user and return a token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password","device_name"},
     *             @OA\Property(property="email", type="string", format="email", example="your_email"),
     *             @OA\Property(property="password", type="string", example="your_password"),
     *             @OA\Property(property="device_name", type="string", example="your_device")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR..."),
     *             @OA\Property(property="user", type="object", 
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="jhondoe@example.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="errors", type="object", 
     *                 @OA\AdditionalProperties(type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized, invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="message", type="string", example="Password is not correct"),
     *             @OA\Property(property="code", type="string", example="invalid_credentials")
     *         )
     *     )
     * )
     */

    public function login(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('', ['errors' => $validator->errors()])->setStatusCode(400);;
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->sendError(__("Password is not correct"), ['code' => 'invalid_credentials'])->setStatusCode(401);;
        }

        return [
            'token' => $user->createToken($request->device_name)->plainTextToken,
            'user' => new UserResource($user),
            'status' => 1
        ];
    }

    public function register(Request $request)
    {
        if(!is_enable_registration()){
            return $this->sendError(__("You are not allowed to register"));
        }
        $rules = [
            'first_name' => [
                'required',
                'string',
                'max:255'
            ],
            'last_name'  => [
                'required',
                'string',
                'max:255'
            ],
            'email'      => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users'
            ],
            'password'   => [
                'required',
                'string'
            ],
            'term'       => ['required'],
        ];
        $messages = [
            'email.required'      => __('Email is required field'),
            'email.email'         => __('Email invalidate'),
            'password.required'   => __('Password is required field'),
            'first_name.required' => __('The first name is required field'),
            'last_name.required'  => __('The last name is required field'),
            'term.required'       => __('The terms and conditions field is required'),
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        } else {
            $user = \App\User::create([
                'first_name' => $request->input('first_name'),
                'last_name'  => $request->input('last_name'),
                'email'      => $request->input('email'),
                'password'   => Hash::make($request->input('password')),
                'phone'    => $request->input('phone'),
                'status'    => 'publish',
                'confirmation_code' => md5(uniqid(mt_rand(), true)),
            ]);
            event(new Registered($user));
            //Auth::loginUsingId($user->id);
            try {
                event(new SendMailUserRegistered($user));
            } catch (Exception $exception) {
                Log::warning("SendMailUserRegistered: " . $exception->getMessage());
            }
            $user->assignRole(setting_item('user_role'));
            return $this->sendSuccess(__('Register successfully'));
        }
    }

    public function checkEmailAvailability(Request $request){
        $user = User::where('email', $request->email)->first();
        if($user){
            return $this->sendError(__('Email is already in use'));
        }
        return $this->sendSuccess(__('Email is available'));
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = auth()->user();
    
        $user['avatar_url'] = get_file_url($user['avatar_id'] ?? 'default_avatar_id', 'full');
        $user['avatar_thumb_url'] = get_file_url($user['avatar_id'] ?? 'default_avatar_id');
    
        return $this->sendSuccess([
            'data' => $user
        ]);
    }

    public function updateUser(Request $request){
        $user = Auth::user();
        $rules = [
            'first_name' => 'required|max:255',
            'last_name'  => 'required|max:255',
            'email'      => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
        ];
        $messages = [
            'first_name.required' => __('The first name is required field'),
            'last_name.required'  => __('The last name is required field'),
            'email.required'       => __('The email field is required'),
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $user->fill($request->input());
        $user->birthday = date("Y-m-d", strtotime($user->birthday));
        $user->save();
        return $this->sendSuccess(__('Update successfully'));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function changePassword(Request $request){

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return $this->sendError('',['errors'=>$validator->errors()]);
        }
        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->sendError(__("Current password is not correct"),['code'=>'invalid_current_password']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        // Invalidate all Tokens
        $user->tokens()->delete();

        return $this->sendSuccess(['message'=>__("Password updated. Please re-login"),'code'=>"need_relogin"]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/forgot-password",
     *     tags={"Auth"},
     *     summary="Send reset password link",
     *     description="Send a reset password link to the specified email address.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reset link sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="message", type="string", example="Reset link sent!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Unable to send reset link",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="message", type="string", example="Unable to send reset link.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The email must be a valid email address."),
     *             @OA\Property(property="errors", type="object", 
     *                 @OA\AdditionalProperties(type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $response = Password::sendResetLink($request->only('email'));

        return $response == Password::RESET_LINK_SENT
            ? response()->json([
                'status' => 1,
                'message' => 'Reset link sent!'
            ], 200)
            : response()->json([
                'status' => 0,
                'message' => 'Unable to send reset link.'
            ], 500);
    }
}
