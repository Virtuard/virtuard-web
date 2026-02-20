<?php

namespace App\Http\Controllers\v2\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Modules\User\Events\SendMailUserRegistered;

class AuthController extends Controller
{
    use AuthenticatesUsers {
        login as traitLogin;
        logout as traitLogout;
    }

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // =========================================================================
    // LOGIN
    // =========================================================================

    public function showLoginForm()
    {
        return view('v2.auth.login', [
            'page_title' => __('Sign In'),
        ]);
    }

    public function login(Request $request)
    {
        return $this->traitLogin($request);
    }

    public function logout(Request $request)
    {
        return $this->traitLogout($request);
    }

    protected function redirectTo()
    {
        if (Auth::user() && Auth::user()->hasPermission('dashboard_access')) {
            return '/admin';
        }
        return $this->redirectTo;
    }

    // =========================================================================
    // REGISTER
    // =========================================================================

    public function showRegisterForm()
    {
        return view('v2.auth.register', [
            'page_title' => __('Sign Up'),
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'term' => 'required',
        ], [
            'term.required' => __('You must agree to the Terms and Privacy Policy'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'publish',
            'user_name' => generate_user_name($request->name),
        ]);

        $user->assignRole(setting_item('user_role'));

        try {
            event(new SendMailUserRegistered($user));
        } catch (\Exception $e) {
            Log::warning("SendMailUserRegistered: " . $e->getMessage());
        }

        Auth::login($user);

        return redirect($this->redirectTo)
            ->with('success', __('Your account has been created successfully'));
    }

    // =========================================================================
    // FORGOT PASSWORD
    // =========================================================================

    public function showForgotPasswordForm()
    {
        return view('v2.auth.forgot-password', [
            'page_title' => __('Forgot Password'),
        ]);
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    // =========================================================================
    // VERIFY OTP (for password reset flow)
    // =========================================================================

    public function showVerifyOtpForm()
    {
        return view('v2.auth.verify-otp', [
            'page_title' => __('Verify Your Code'),
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        // OTP verification logic — to be implemented based on
        // how OTP codes are stored (DB, cache, etc.)
        // For now, redirect to reset password form
        return redirect()->route('password.reset')
            ->with('otp_verified', true);
    }

    public function resendOtp(Request $request)
    {
        // Resend OTP logic — to be implemented
        return back()->with('success', __("We've sent you another code. Please check your inbox."));
    }

    // =========================================================================
    // RESET PASSWORD
    // =========================================================================

    public function showResetPasswordForm(Request $request, $token = null)
    {
        return view('v2.auth.reset-password', [
            'page_title' => __('Create a New Password'),
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    // =========================================================================
    // SOCIAL LOGIN
    // =========================================================================

    public function socialLogin($provider)
    {
        $this->initSocialConfig($provider);
        session()->put('url.intended', request()->server('HTTP_REFERER', url('/')));
        return Socialite::driver($provider)->redirect();
    }

    public function socialCallback($provider)
    {
        try {
            $this->initSocialConfig($provider);
            $socialUser = Socialite::driver($provider)->user();

            if (empty($socialUser)) {
                return redirect()->route('login')
                    ->with('error', __('Can not authorize'));
            }

            $existUser = User::getUserBySocialId($provider, $socialUser->getId());

            if (empty($existUser)) {
                $email = $socialUser->getEmail() ?: $socialUser->getId() . '@' . $provider;
                $userResponse = User::where('email', $email)->first();

                if ($userResponse) {
                    $this->saveSocialMeta($userResponse, $provider, $socialUser);
                    Auth::login($userResponse);
                    return redirect($this->getRedirectUrl());
                }

                // Create new user
                $newUser = new User();
                $newUser->email = $email;
                $newUser->password = Hash::make(uniqid() . time());
                $newUser->name = $socialUser->getName();
                $newUser->first_name = $socialUser->getName();
                $newUser->user_name = generate_user_name($socialUser->getName());
                $newUser->status = 'publish';
                $newUser->last_login_at = Carbon::now();
                $newUser->email_verified_at = Carbon::now();
                $newUser->save();

                $this->saveSocialMeta($newUser, $provider, $socialUser);
                $newUser->assignRole(setting_item('user_role'));

                try {
                    event(new SendMailUserRegistered($newUser));
                } catch (\Exception $e) {
                    Log::warning("SendMailUserRegistered: " . $e->getMessage());
                }

                Auth::login($newUser);
                return redirect($this->getRedirectUrl());
            }

            if ($existUser->deleted == 1 || $existUser->status === 'blocked') {
                return redirect()->route('login')
                    ->with('error', __('Your account has been blocked'));
            }

            $existUser->last_login_at = now();
            $existUser->save();
            Auth::login($existUser);

            return redirect($this->getRedirectUrl());
        } catch (\Exception $e) {
            return redirect()->route('v2.auth.login')
                ->with('error', $e->getMessage() ?: 'Social login failed');
        }
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    private function initSocialConfig($provider)
    {
        if (in_array($provider, ['facebook', 'google', 'twitter'])) {
            config([
                "services.{$provider}.client_id" => setting_item("{$provider}_client_id"),
                "services.{$provider}.client_secret" => setting_item("{$provider}_client_secret"),
                "services.{$provider}.redirect" => "/v2/social-callback/{$provider}",
            ]);
        }
    }

    private function saveSocialMeta($user, $provider, $socialUser)
    {
        $user->addMeta("social_{$provider}_id", $socialUser->getId());
        $user->addMeta("social_{$provider}_email", $socialUser->getEmail());
        $user->addMeta("social_{$provider}_name", $socialUser->getName());
        $user->addMeta("social_{$provider}_avatar", $socialUser->getAvatar());
        $user->addMeta('social_meta_avatar', $socialUser->getAvatar());
        $user->need_update_pw = 0;
        $user->last_login_at = now();
        $user->save();
    }

    private function getRedirectUrl()
    {
        $url = session()->get('url.intended', url('/'));
        session()->forget('url.intended');
        if (in_array($url, [url('/'), route('login'), route('auth.register')])) {
            $url = url('/');
        }
        return $url;
    }
}
