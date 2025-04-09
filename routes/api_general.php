<?php

namespace App\Http\Controllers\General;

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('general')->group(function () {
        Route::post('upload-image', [ImageController::class, 'uploadImage']);
    });
});
