<?php

use App\Http\Middleware\SetLocaleMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware(SetLocaleMiddleware::class)->group(function () {


    require_once __DIR__ . '/api_customer.php';
    require_once __DIR__ . '/api_salon.php';
});
