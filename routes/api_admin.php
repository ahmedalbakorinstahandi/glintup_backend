<?php

use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\Salons\SalonController;
use App\Http\Controllers\Salons\WorkingHourController;
use App\Http\Controllers\Services\GroupController;
use App\Http\Controllers\Services\GroupServiceController;
use App\Http\Controllers\Services\ServiceController;
use App\Http\Controllers\Statistics\DashboardController;
use App\Http\Controllers\Statistics\PromotionAdController;
use App\Http\Controllers\Users\AdminAuthController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/login', [AdminAuthController::class, 'login']);
    });


    Route::middleware(['auth:sanctum'])->group(function () {


        //DashboardController
        Route::prefix('dashboard')->controller(DashboardController::class)->group(function () {
            Route::get('/', 'index');
        });

        Route::prefix('services')->controller(ServiceController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('/', 'create');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
        });
        // Route::prefix('me')->group(function () {
        //     Route::get('/permissions', [AdminAuthController::class, 'getPermissions']);
        // });

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

        Route::prefix('salons')->controller(SalonController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('/', 'create');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
        });

        Route::prefix('bookings')->controller(BookingController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('/', 'create');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
        });

        Route::prefix('promotion-ads')->controller(PromotionAdController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('/', 'create');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
        });

        Route::prefix('working-hours')->controller(WorkingHourController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('/', 'create');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
        });
    });
});
