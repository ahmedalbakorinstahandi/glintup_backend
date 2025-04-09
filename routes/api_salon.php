<?php

use App\Http\Controllers\Salons\SalonAuthController;
use App\Http\Controllers\Salons\SalonController;
use App\Http\Services\Salons\SalonService;
use Illuminate\Support\Facades\Route;


Route::prefix('salon')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/login', [SalonAuthController::class, 'login']);
        // Route::post('/register', [UserAuthController::class, 'register']);
        // Route::post('/verify-code', [UserAuthController::class, 'verifyCode']);
        // Route::post('/forgot-password', [UserAuthController::class, 'forgotPassword']);
        // Route::post('/reset-password', [UserAuthController::class, 'resetPassword'])->middleware('auth:sanctum');
        // Route::post('/logout', [UserAuthController::class, 'logout'])->middleware('auth:sanctum');
    });

    Route::middleware(['auth:sanctum'])->group(function () {
        // group me
        Route::prefix('me')->group(function () {
            Route::get('/permissions', [SalonController::class, 'getPermissions']);
        });
    });
});
