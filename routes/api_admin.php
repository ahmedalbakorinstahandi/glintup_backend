<?php

use App\Http\Controllers\Admins\AdminUserController;
use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\Booking\CouponController;
use App\Http\Controllers\General\ActivityLogController;
use App\Http\Controllers\General\ComplaintController;
use App\Http\Controllers\General\NotificationController;
use App\Http\Controllers\General\SettingController;
use App\Http\Controllers\Rewards\GiftCardController;
use App\Http\Controllers\Rewards\LoyaltyPointController;
use App\Http\Controllers\Salons\SalonAuthController;
use App\Http\Controllers\Salons\SalonController;
use App\Http\Controllers\Salons\SalonCustomerController;
use App\Http\Controllers\Salons\SalonHolidayController;
use App\Http\Controllers\Salons\SalonMenuRequestController;
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
use App\Http\Controllers\Statistics\DashboardController;
use App\Http\Controllers\Statistics\PromotionAdController;
use App\Http\Controllers\Users\AdminAuthController;
use App\Http\Controllers\Users\UserAuthController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Users\WalletTransactionController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;


Route::prefix('admin')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/login', [AdminAuthController::class, 'login']);
        Route::post('/logout', [UserAuthController::class, 'logout'])->middleware('auth:sanctum');
    });


    Route::middleware(['auth:sanctum', AdminMiddleware::class])->group(function () {


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


        Route::prefix('admin-users')->group(function () {
            Route::get('/', [AdminUserController::class, 'index']);
            Route::get('/{id}', [AdminUserController::class, 'show']);
            Route::post('/', [AdminUserController::class, 'create']);
            Route::put('/{id}', [AdminUserController::class, 'update']);
            Route::delete('/{id}', [AdminUserController::class, 'destroy']);
            Route::post('/{id}/update-permissions', [AdminUserController::class, 'updatePermissions']);
        });

        Route::get('/permissions', [AdminUserController::class, 'getPermissions']);




        Route::prefix('groups')->controller(GroupController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('/', 'create');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
            Route::post('{id}/reorder', 'reorder');
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
            // Route::post('/', 'create');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');

            Route::post('{id}/send-notification', [NotificationController::class, 'sendNotificationToSalonOwner']);
        });

        Route::post('/salons/register', [SalonAuthController::class, 'register']);

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


        Route::prefix('reviews')->controller(ReviewController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            // Route::post('/', 'create');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
            Route::post('{id}/reply', 'reply');
            Route::post('{id}/report', 'report');
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
            Route::post('/', 'create');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
        });

        Route::prefix('salon-social-media-sites')->controller(SalonSocialMediaSiteController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('/', 'create');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
        });



        Route::prefix('salon-customers')->controller(SalonCustomerController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            // Route::post('/', 'create');
            Route::put('{id}', 'update');
            // Route::delete('{id}', 'destroy');
        });


        Route::prefix('coupons')->controller(CouponController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('/', 'create');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
        });


        Route::prefix('transactions')->group(function () {
            Route::post('/deposit', [WalletTransactionController::class, 'createPaymentIntent']);
            Route::get('/', [WalletTransactionController::class, 'index']);
            Route::get('{id}', [WalletTransactionController::class, 'show']);
            // deposit createPaymentIntent
        });


        // gift cards
        Route::prefix('gift-cards')->group(function () {
            Route::get('/', [GiftCardController::class, 'index']);
        });


        // settings
        Route::prefix('settings')->group(function () {
            Route::get('/', [SettingController::class, 'index']);
            Route::put('/', [SettingController::class, 'updateSettings']);
        });


        Route::prefix('salon-staff')->controller(SalonStaffController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('/', 'create');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
            Route::post('{id}/permissions', 'updatePermissions');
        });

        //SalonPermissionController
        // get 
        Route::get('/salon-permissions', [SalonPermissionController::class, 'index']);


        Route::prefix('salon-payments')->controller(SalonPaymentController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            // Route::post('/', 'create');
            // Route::put('{id}', 'update');
            // Route::delete('{id}', 'destroy');
        });



        // users
        Route::post('users/send-notification', [NotificationController::class, 'sendNotificationToAllUsers']);
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::get('{id}', [UserController::class, 'show']);
            Route::post('/', [UserController::class, 'create']);
            Route::put('{id}', [UserController::class, 'update']);
            Route::delete('{id}', [UserController::class, 'destroy']);
        });

        Route::prefix('loyalty-points')->controller(LoyaltyPointController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
        });

        Route::prefix('complaints')->controller(ComplaintController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
        });


        Route::prefix('salon-menu-requests')->controller(SalonMenuRequestController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
        });

        Route::prefix('activity-logs')->controller(ActivityLogController::class)->group(function () {
            Route::get('/', 'index');
        });
    });
});
