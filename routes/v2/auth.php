<?php

use App\Http\Controllers\v2\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// =========================================================================
// V2 Auth Routes
// =========================================================================

// Login
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.submit');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Register
Route::get('register', [AuthController::class, 'showRegisterForm'])->name('auth.register');
Route::post('register', [AuthController::class, 'register'])->name('auth.register.store');

// Forgot Password
Route::get('forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

// Verify OTP
Route::get('verify-otp', [AuthController::class, 'showVerifyOtpForm'])->name('auth.verify-otp');
Route::post('verify-otp', [AuthController::class, 'verifyOtp'])->name('auth.verify-otp.submit');
Route::post('resend-otp', [AuthController::class, 'resendOtp'])->name('auth.resend-otp');

// Reset Password
Route::get('reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Social Login
Route::get('social-login/{provider}/{affiliate_id?}', [AuthController::class, 'socialLogin'])->name('auth.social');
Route::get('social-callback/{provider}', [AuthController::class, 'socialCallback'])->name('auth.social.callback');
