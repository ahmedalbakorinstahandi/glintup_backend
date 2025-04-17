<?php

use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\Booking\CouponController;
use App\Http\Controllers\Rewards\GiftCardController;
use App\Http\Controllers\Rewards\LoyaltyPointController;
use App\Http\Controllers\Salons\SalonAuthController;
use App\Http\Controllers\Salons\SalonController;
use App\Http\Controllers\Salons\SalonCustomerController;
use App\Http\Controllers\Salons\SalonHolidayController;
use App\Http\Controllers\Salons\SalonPaymentController;
use App\Http\Controllers\Salons\SalonPermissionController;
use App\Http\Controllers\Salons\SalonSocialMediaSiteController;
use App\Http\Controllers\Salons\SalonStaffController;
use App\Http\Controllers\Salons\SocialMediaSiteController;
use App\Http\Controllers\Salons\WorkingHourController;
use App\Http\Controllers\Services\GroupController;
use App\Http\Controllers\Services\GroupServiceController;
use App\Http\Controllers\Services\ReviewController;
use App\Http\Controllers\Services\ServiceController;
use App\Http\Controllers\Statistics\PromotionAdController;
use App\Http\Controllers\Users\UserAuthController;
use App\Http\Controllers\Users\UserController;
use App\Http\Services\Salons\SalonService;
use Illuminate\Support\Facades\Route;


Route::prefix('salon')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/login', [SalonAuthController::class, 'login']);
        Route::post('/logout', [UserAuthController::class, 'logout'])->middleware('auth:sanctum');
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

        Route::prefix('working-hours')->controller(WorkingHourController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('/', 'create');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
        });


        Route::prefix('reviews')->controller(ReviewController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('{id}/reply', 'reply');
            Route::post('{id}/report', 'report');
            // Route::post('/', 'create');
            // Route::put('{id}', 'update');
            // Route::delete('{id}', 'destroy');
        });


        Route::prefix('salon-holidays')->controller(SalonHolidayController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('/', 'create');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
        });


        Route::prefix('social-media-sites')->controller(SocialMediaSiteController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
        });


        Route::prefix('salon-social-media-sites')->controller(SalonSocialMediaSiteController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('/', 'create');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
        });


        Route::prefix('customers')->controller(SalonCustomerController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('/', 'create');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
        });



        Route::prefix('ads')->controller(PromotionAdController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('/get-ad-details', 'getAdDetails');
            Route::post('/request-post-ad', 'requestPostAd');
        });

        Route::prefix('coupons')->controller(CouponController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('/', 'create');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
        });

        Route::prefix('gift-cards')->group(function () {
            Route::get('/', [GiftCardController::class, 'index']);
        });

        Route::prefix('salon-staff')->controller(SalonStaffController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('/', 'create');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
            Route::post('{id}/permissions', 'updatePermissions');
        });


        Route::prefix('salon-payments')->controller(SalonPaymentController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            // Route::post('/', 'create');
            // Route::put('{id}', 'update');
            // Route::delete('{id}', 'destroy');
        });

        Route::prefix('loyalty-points')->controller(LoyaltyPointController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
        });
    });
});
