<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Booking\InvoiceController;
use App\Http\Controllers\Rewards\GiftController;
use App\Http\Controllers\Salons\SalonPermissionController;
use App\Http\Controllers\Users\UserAuthController;
use App\Http\Controllers\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::post('general/upload-image', [ImageController::class, 'uploadImage']);
Route::post('general/upload-file', [ImageController::class, 'uploadFile']);



Route::prefix('guests')->group(function () {
    Route::prefix('notifications')->controller(NotificationController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/unread-count', 'unreadCount');
    });
});


// settings
Route::prefix('settings')->controller(SettingController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('{id}', 'show');
});


Route::prefix('general')->group(function () {

    Route::prefix('gifts')->controller(GiftController::class)->group(function () {
        Route::get('/', 'index');
    });

    Route::post('check-phone-number', [UserAuthController::class, 'checkPhoneNumber']);



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

Route::get('/invoices/{id}', [InvoiceController::class, 'show']);
Route::get('/invoices/{id}/pdf', [InvoiceController::class, 'showPdf']);
