<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Salons\SalonPermissionController;
use App\Http\Controllers\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::post('general/upload-image', [ImageController::class, 'uploadImage']);


Route::prefix('guests')->group(function () {
    Route::prefix('notifications')->controller(NotificationController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/unread-count', 'unreadCount');
    });
});
Route::prefix('general')->group(function () {





    Route::middleware(['auth:sanctum'])->group(function () {


        Route::prefix('notifications')->controller(NotificationController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/unread-count', 'unreadCount');
            Route::post('/{id}/read',  'readNotification');
            Route::get('{id}', 'show');
        });

        // me
        Route::prefix('profile')->group(function () {
            Route::get('/', [UserController::class, 'getProfile']);
            Route::put('/', [UserController::class, 'updateProfile']);
        });

        Route::get('/salon-permissions', [SalonPermissionController::class, 'index']);
    });
});
