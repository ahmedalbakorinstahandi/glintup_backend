<?php

use App\Http\Controllers\Users\UserAuthController;
use Illuminate\Support\Facades\Route;


Route::prefix('customer')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/login', [UserAuthController::class, 'login']);
        Route::post('/register', [UserAuthController::class, 'register']);
        Route::post('/verify-code', [UserAuthController::class, 'verifyCode']);
        Route::post('/forgot-password', [UserAuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [UserAuthController::class, 'resetPassword'])->middleware('auth:sanctum');
        Route::post('/logout', [UserAuthController::class, 'logout'])->middleware('auth:sanctum');
    });
});
