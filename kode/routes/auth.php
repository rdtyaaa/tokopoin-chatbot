<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\GoogleAuthenticatedController;
use App\Http\Controllers\Auth\FacebookAuthenticatedController;
use Illuminate\Support\Facades\Route;


Route::middleware(['guest','sanitizer'])->group(function () {

    Route::get('login', [AuthenticatedSessionController::class, 'login'])->name('login');

    Route::post('register', [RegisteredUserController::class, 'store'])->name('register.store');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.store');


    Route::get('otp/verify/view', [AuthenticatedSessionController::class, 'otpVerificationView'])->name('otp.verification.view');
    Route::post('otp/verify', [AuthenticatedSessionController::class, 'verifyOTP'])->name('otp.verify');

    
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('password/code/verify', [PasswordResetLinkController::class, 'passwordResetCodeVerify'])->name('password.verify.code');
    Route::post('password/code/verify', [PasswordResetLinkController::class, 'emailVerificationCode'])->name('email.password.verify.code');



    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::middleware(['auth','sanitizer'])->group(function () {
    Route::get('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});


Route::get('auth/google', [GoogleAuthenticatedController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [GoogleAuthenticatedController::class, 'handleGoogleCallback']);

Route::get('auth/facebook', [FacebookAuthenticatedController::class, 'redirectToFacebook']);
Route::get('auth/facebook/callback', [FacebookAuthenticatedController::class, 'handleFacebookCallback']);

