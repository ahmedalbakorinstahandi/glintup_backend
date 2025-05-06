<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Salons\SalonPermissionController;
use App\Http\Controllers\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::post('general/upload-image', [ImageController::class, 'uploadImage']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('general')->group(function () {


        Route::prefix('notifications')->controller(NotificationController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::get('/unread-count', [NotificationController::class, 'getNotificationsUnreadCount']);
        });

        // me
        Route::prefix('profile')->group(function () {
            Route::get('/', [UserController::class, 'getProfile']);
            Route::put('/', [UserController::class, 'updateProfile']);
        });

        Route::get('/salon-permissions', [SalonPermissionController::class, 'index']);
    });
});
