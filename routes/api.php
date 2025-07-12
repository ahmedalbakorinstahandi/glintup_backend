<?php

use App\Http\Controllers\Booking\InvoiceController;
use App\Http\Controllers\Providers\StripeWebhookController;
use App\Http\Middleware\SetLocaleMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);


Route::middleware(SetLocaleMiddleware::class)->group(function () {
    require_once __DIR__ . '/api_customer.php';
    require_once __DIR__ . '/api_salon.php';
    require_once __DIR__ . '/api_admin.php';
    require_once __DIR__ . '/api_general.php';
    require_once __DIR__ . '/api_test.php';
});

Route::get('/invoice/{id}', [InvoiceController::class, 'show']);



