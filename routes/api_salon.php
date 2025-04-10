<?php

use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\Salons\SalonAuthController;
use App\Http\Controllers\Salons\SalonController;
use App\Http\Controllers\Services\GroupController;
use App\Http\Controllers\Services\GroupServiceController;
use App\Http\Controllers\Services\ServiceController;
use App\Http\Controllers\Users\UserController;
use App\Http\Services\Salons\SalonService;
use Illuminate\Support\Facades\Route;


Route::prefix('salon')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/login', [SalonAuthController::class, 'login']);
    });

    Route::middleware(['auth:sanctum'])->group(function () {
        // group me
        Route::prefix('me')->group(function () {
            Route::get('/permissions', [SalonController::class, 'getPermissions']);
            Route::get('/data', [SalonController::class, 'getSalonData']);
        });

        Route::prefix('services')->controller(ServiceController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('/', 'create');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
        });


        Route::prefix('groups')->controller(GroupController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('/', 'create');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
        });


        Route::prefix('group-services')->controller(GroupServiceController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('/', 'create');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
        });

        Route::prefix('bookings')->controller(BookingController::class)->group(function () {
            Route::get('/users', [UserController::class, 'index']);
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('/', 'create');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
        });
    });
});
