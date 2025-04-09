<?php

use App\Http\Controllers\Services\ServiceController;
use App\Http\Controllers\Users\AdminAuthController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/login', [AdminAuthController::class, 'login']);
    });


    Route::middleware(['auth:sanctum'])->group(function () {
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
    });
});
