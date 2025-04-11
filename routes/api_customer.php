<?php

use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\Salons\SalonController;
use App\Http\Controllers\Services\GroupController;
use App\Http\Controllers\Services\ReviewController;
use App\Http\Controllers\Users\UserAuthController;
use App\Http\Controllers\Users\UserController;
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

    Route::middleware('auth:sanctum')->group(function () {
        // Booking
        Route::prefix('bookings')->controller(BookingController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
        });

        Route::prefix('home')->group(function () {
            Route::get('/search', [SalonController::class, 'index']);
            Route::get('/data', [UserController::class, 'homeData']);
        });

        // salons
        Route::prefix('salons')->controller(SalonController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
        });

        Route::prefix('reviews')->controller(ReviewController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('/', 'create');
            // Route::put('{id}', 'update');
            // Route::delete('{id}', 'destroy');
        });

        Route::prefix('groups')->controller(GroupController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
        });
    });
});
