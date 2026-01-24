<?php

namespace Modules\Api\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Ichtrojan\Otp\Otp;
use Validator;

class ResetPasswordController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/otp/forgot-password",
     *     tags={"Auth"},
     *     summary="Request OTP for password reset",
     *     description="Generate and send OTP to user's email for password reset. OTP will expire in 2 minutes.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP generated and sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="message", type="string", example="OTP has been sent to your email")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error or user not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="message", type="string", example="Email not found or user is not active")
     *         )
     *     )
     * )
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->sendError('', ['errors' => $validator->errors()])->setStatusCode(400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->sendError(__('Email not found'))->setStatusCode(400);
        }

        // Cek jika user status publish
        if ($user->status !== 'publish') {
            return $this->sendError(__('User account is not active'))->setStatusCode(400);
        }

        try {
            // Generate OTP: 6 digits, valid for 2 minutes
            $otp = (new Otp)->generate($user->email, 'numeric', 6, 2);

            if ($otp->status) {
                // Send OTP via email
                $this->sendOtpEmail($user, $otp->token);

                return $this->sendSuccess(__('OTP has been sent to your email'));
            } else {
                return $this->sendError(__('Failed to generate OTP'))->setStatusCode(500);
            }
        } catch (\Exception $e) {
            Log::error('ForgotPassword Error: ' . $e->getMessage());
            return $this->sendError(__('An error occurred. Please try again.'))->setStatusCode(500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/otp/forgot-password/verify",
     *     tags={"Auth"},
     *     summary="Verify OTP and generate reset token",
     *     description="Validate OTP and generate Laravel password reset token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "otp"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="otp", type="string", example="123456", description="6 digit OTP code")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified and token generated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="message", type="string", example="OTP verified successfully"),
     *             @OA\Property(property="token", type="string", example="abc123...", description="Password reset token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error or invalid OTP",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="message", type="string", example="OTP is not valid")
     *         )
     *     )
     * )
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return $this->sendError('', ['errors' => $validator->errors()])->setStatusCode(400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->sendError(__('Email not found'))->setStatusCode(400);
        }

        try {
            // Validate OTP
            $otpValidation = (new Otp)->validate($user->email, $request->otp);

            if (!$otpValidation->status) {
                return $this->sendError(__($otpValidation->message))->setStatusCode(400);
            }

            // Generate Laravel password reset token
            $token = $this->createPasswordResetToken($user);

            return $this->sendSuccess([
                'token' => $token,
                'message' => __('OTP verified successfully')
            ]);
        } catch (\Exception $e) {
            Log::error('VerifyOtp Error: ' . $e->getMessage());
            return $this->sendError(__('An error occurred. Please try again.'))->setStatusCode(500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/otp/reset-password",
     *     tags={"Auth"},
     *     summary="Reset password using token",
     *     description="Reset user password using Laravel password reset token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password", "password_confirmation", "token"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", minLength=6, example="newPassword123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", minLength=6, example="newPassword123"),
     *             @OA\Property(property="token", type="string", example="abc123...", description="Password reset token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="message", type="string", example="Password has been reset successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error or invalid token",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="message", type="string", example="Invalid token or token expired")
     *         )
     *     )
     * )
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('', ['errors' => $validator->errors()])->setStatusCode(400);
        }

        try {
            // Use Laravel's Password reset mechanism
            $response = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->password = Hash::make($password);
                    $user->save();
                }
            );

            if ($response == Password::PASSWORD_RESET) {
                return $this->sendSuccess(__('Password has been reset successfully'));
            } else {
                $message = match($response) {
                    Password::INVALID_TOKEN => __('Invalid token or token expired'),
                    Password::INVALID_USER => __('Email not found'),
                    default => __('Unable to reset password'),
                };
                return $this->sendError($message)->setStatusCode(400);
            }
        } catch (\Exception $e) {
            Log::error('ResetPassword Error: ' . $e->getMessage());
            return $this->sendError(__('An error occurred. Please try again.'))->setStatusCode(500);
        }
    }

    /**
     * Create password reset token for user
     *
     * @param User $user
     * @return string
     */
    private function createPasswordResetToken($user)
    {
        // Delete existing tokens for this email
        DB::table('password_resets')->where('email', $user->email)->delete();

        // Generate new token
        $token = Str::random(60);

        // Insert token into password_resets table
        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now()
        ]);

        return $token;
    }

    /**
     * Send OTP email to user
     *
     * @param User $user
     * @param string $otpToken
     * @return void
     */
    private function sendOtpEmail($user, $otpToken)
    {
        try {
            $subject = __('Reset Password OTP');
            $message = __('Your OTP code for password reset is: :otp. This code will expire in 10 minutes.', ['otp' => $otpToken]);

            Mail::raw($message, function ($mail) use ($user, $subject) {
                $mail->to($user->email)
                    ->subject($subject);
            });
        } catch (\Exception $e) {
            Log::error('SendOtpEmail Error: ' . $e->getMessage());
        }
    }
}
