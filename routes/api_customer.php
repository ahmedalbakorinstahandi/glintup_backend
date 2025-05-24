<?php

use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\Booking\BookingNewController;
use App\Http\Controllers\Booking\CouponController;
use App\Http\Controllers\General\ComplaintController;
use App\Http\Controllers\Rewards\GiftCardController;
use App\Http\Controllers\Rewards\LoyaltyPointController;
use App\Http\Controllers\Salons\SalonController;
use App\Http\Controllers\Salons\SalonPaymentController;
use App\Http\Controllers\Services\GroupController;
use App\Http\Controllers\Services\ReviewController;
use App\Http\Controllers\Services\ServiceController;
use App\Http\Controllers\Statistics\AdStatisticController;
use App\Http\Controllers\Users\UserAuthController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Users\WalletTransactionController;
use App\Http\Middleware\CustomerMiddleware;
use Illuminate\Support\Facades\Route;


Route::prefix('guests')->group(function () {

    Route::prefix('home')->group(function () {
        Route::get('/search', [SalonController::class, 'index']);
        Route::get('/data', [UserController::class, 'homeData']);
        Route::get('/second-data', [UserController::class, 'secondData']);
    });


    // salons
    Route::prefix('salons')->controller(SalonController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('{id}', 'show');
    });


    Route::prefix('groups')->controller(GroupController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('{id}', 'show');
    });

    // services
    Route::prefix('services')->controller(ServiceController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('{id}', 'show');
    });

    Route::prefix('reviews')->controller(ReviewController::class)->group(function () {
        Route::get('/', 'index');

        Route::get('{id}', 'show');
    });
});

Route::prefix('customer')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/login', [UserAuthController::class, 'login']);
        Route::post('/register', [UserAuthController::class, 'register']);
        Route::post('/verify-code', [UserAuthController::class, 'verifyCode']);
        Route::post('/forgot-password', [UserAuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [UserAuthController::class, 'resetPassword'])->middleware('auth:sanctum');
        Route::post('/logout', [UserAuthController::class, 'logout'])->middleware('auth:sanctum');
    });


    Route::middleware(['auth:sanctum', CustomerMiddleware::class])->group(function () {
        // Booking
        Route::prefix('bookings')->controller(BookingController::class)->group(function () {
            Route::post('/details', 'returnBookingDetails');
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('/', 'createFromUser');
             Route::post('/new',  [BookingNewController::class, 'createFromUser']);
            Route::post('{id}/reschedule', 'rescheduleBooking');
            Route::post('{id}/cancel', 'cancelBooking');
        });

        Route::prefix('home')->group(function () {
            Route::get('/search', [SalonController::class, 'index']);
            Route::get('/data', [UserController::class, 'homeData']);
            Route::get('/second-data', [UserController::class, 'secondData']);
        });


        // salons
        Route::prefix('salons')->controller(SalonController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::get('{id}/available-dates', 'getAvailableDates');
        });


        Route::prefix('groups')->controller(GroupController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
        });

        // services
        Route::prefix('services')->controller(ServiceController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::get('{id}/available-times', 'getAvailableTimes');
        });


        Route::get('/salons/{id}/coupons/{code}', [CouponController::class, 'check']);

        Route::prefix('reviews')->controller(ReviewController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('/', 'create');
            Route::delete('{id}', 'destroy');
        });

        // wallet transactions
        Route::prefix('wallet')->group(function () {
            Route::post('/transactions/deposit', [WalletTransactionController::class, 'createPaymentIntent']);
            Route::get('/transactions', [WalletTransactionController::class, 'index']);
            Route::get('/transactions/{id}', [WalletTransactionController::class, 'show']);
            // deposit createPaymentIntent
        });


        // gift cards
        Route::prefix('gift-cards')->group(function () {
            Route::get('/sent', [GiftCardController::class, 'getSentGiftCards']);
            Route::get('/', [GiftCardController::class, 'index']);
            Route::post('/send', [GiftCardController::class, 'createByUser']);
            Route::get('{id}', [GiftCardController::class, 'show']);
            Route::post('{id}/receive', [GiftCardController::class, 'receive']);
        });



        Route::prefix('salon-payments')->controller(SalonPaymentController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
        });

        Route::prefix('loyalty-points')->controller(LoyaltyPointController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('{id}/receive', 'receive');
        });

        Route::prefix('complaints')->controller(ComplaintController::class)->group(function () {
            Route::post('/', 'create');
        });



        Route::prefix('ads')->controller(AdStatisticController::class)->group(function () {
            Route::post('{id}/clicked', 'clicked');
            Route::post('{id}/viewed', 'viewed');
        });
    });
});
