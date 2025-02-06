<?php


	namespace Modules\User\Controllers\Auth;


	use App\Helpers\ReCaptchaEngine;
    use App\Models\User;
    use Illuminate\Auth\Events\Registered;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Support\MessageBag;
    use Illuminate\Validation\Rules\Password;
    use Matrix\Exception;
    use Modules\User\Events\SendMailUserNeedConfirm;
    use Modules\User\Events\SendMailUserRegistered;
    use Illuminate\Support\Facades\Cookie;


    class RegisterController extends \App\Http\Controllers\Auth\RegisterController
	{

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
                    'string',
                    Password::min(8)
                        ->mixedCase()
                        ->numbers()
                        ->symbols()
                        ->uncompromised(),
                ],
                'phone'       => ['nullable','unique:users'],
                'term'       => ['required'],
            ];
            $messages = [
                'phone.required'      => __('Phone is required field'),
                'email.required'      => __('Email is required field'),
                'email.email'         => __('Email invalidate'),
                'password.required'   => __('Password is required field'),
                'first_name.required' =>    __('The first name is required field'),
                'last_name.required'  => __('The last name is required field'),
                'term.required'       => __('The terms and conditions field is required'),
            ];
            if (ReCaptchaEngine::isEnable() and setting_item("user_enable_register_recaptcha")) {
                $codeCapcha = $request->input('g-recaptcha-response');
                if (!$codeCapcha or !ReCaptchaEngine::verify($codeCapcha)) {
                    $errors = new MessageBag(['message_error' => __('Please verify the captcha')]);
                    return response()->json([
                        'error'    => true,
                        'messages' => $errors
                    ], 200);
                }
            }
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([
                    'error'    => true,
                    'messages' => $validator->errors()
                ], 200);
            } else {
                $register_confirm_email = setting_item('register_confirm_email');

                $email = $request->input('email');
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors = new MessageBag(['message_error' => __('Email invalid')]);
                    return response()->json([
                        'error'    => true,
                        'messages' => $errors
                    ], 200);
                }

                $dataUser = [
                    'first_name' => $request->input('first_name'),
                    'last_name'  => $request->input('last_name'),
                    'email'      => $email,
                    'password'   => Hash::make($request->input('password')),
                    'status'    => 'publish',
                    'phone'    => $request->input('phone'),
                    'user_name' => generate_user_name($request->input('first_name'), $request->input('last_name')),
                    'confirmation_code' => md5(uniqid(mt_rand(), true)),
                ];

                if (Cookie::has('affiliate_id')) {
                    $dataUser['affiliate_plan_user_id'] = Cookie::get('affiliate_id');
                }

                if ($register_confirm_email) {
                    $dataUser['status'] = 'pending';
                }

                $user = \App\User::create($dataUser);

                $user->assignRole(setting_item('user_role'));

                // event(new Registered($user));

                try {
                    if ($register_confirm_email) {
                        event(new SendMailUserNeedConfirm($user));
                    } else {
                        event(new SendMailUserRegistered($user));
                    }
                } catch (Exception $exception) {
                    Log::warning("SendMailUserRegistered: " . $exception->getMessage());
                }

                Cookie::queue(Cookie::forget('affiliate_id'));

                $response = [
                    'error'    => false,
                    'messages' => false,
                    'redirect' => '/plan' ?? $request->headers->get('referer') ?? url(app_get_locale(false, '/'))
                ];

                if ($register_confirm_email) {
                    $response['redirect'] = '/need-confirm-email';
                }

                if(isset($request->is_auto_login)) {
                    Auth::login($user);
                }
                
                return response()->json($response, 200);
            }
        }

        public function emailConfirmed($str)
        {
            try {
                $user = User::where('confirmation_code', $str)->first();
                
                if ($user && $user->status == 'pending') {
                    $user->update([
                        'status' => 'publish',
                    ]);

                    return redirect()
                    ->route('login')
                    ->with('success', 'Thank you for confirmation. Your account actived.');
                }

                return redirect()->route('login');
            } catch (Exception $exception) {
                return redirect()->route('login');
            }
        }
    }
