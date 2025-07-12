<?php

use App\Services\FirebaseService;
use Illuminate\Support\Facades\Route;

Route::prefix('test')->group(function () {


    // sendToTokens
    Route::post('/send-to-tokens', function () {
        $registrationTokens = [
            'dqUjPWgKSg-GfnZ9VADuPH:APA91bH7Sg4e1y1E0DU4WR_nOYkjgwx7bOYxBV-Z-G9t6CvhaxYHh3yPFX1fOz5Y2caGR1bVgVFPPbR6QxX4fNJmR8uA7hEsoeUX-iISBt-hap9SJKmQKMU',
            'f-6sHKuQSE2QIxp3IcbZkl:APA91bFDAjr16_6hU1a-rBMPPDJ2g5YRj-7DinXfqt7eM0TEjl0bc6ezak2PxBLX_0vi7IGqOsOXf29Gj5GkdMhfJoGx1PwvQj8vUaV3MSAScqMfm5thRes',
        ];
        $title = 'Test Title';
        $body = 'Test Body';
        $data = ['key1' => 'value1', 'key2' => 'value2'];
        $channelId = null;

        $report = FirebaseService::sendToTokens($registrationTokens, $title, $body, $data, $channelId);
    });
});
