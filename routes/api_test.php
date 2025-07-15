<?php

use App\Services\FirebaseService;
use Illuminate\Support\Facades\Route;

Route::prefix('test')->group(function () {

    // Test Firebase configuration
    Route::get('/firebase-config', function () {
        try {
            $serviceAccount = FirebaseService::loadServiceAccount();
            return response()->json([
                'success' => true,
                'message' => 'Firebase configuration loaded successfully',
                'project_id' => $serviceAccount['project_id'] ?? 'N/A',
                'client_email' => $serviceAccount['client_email'] ?? 'N/A',
                'has_private_key' => !empty($serviceAccount['private_key']),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Firebase configuration error',
                'error' => $e->getMessage(),
            ], 500);
        }
    });

    // Test Firebase initialization
    Route::get('/firebase-init', function () {
        try {
            // Reset Firebase instance
            FirebaseService::$firebaseMessaging = null;
            
            $messaging = FirebaseService::getFirebaseMessaging()->createMessaging();
            
            return response()->json([
                'success' => true,
                'message' => 'Firebase initialized successfully',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Firebase initialization failed',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    });

    // Test topic subscription
    Route::post('/firebase-subscribe', function () {
        $token = request('token');
        $topic = request('topic', 'test-topic');
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token is required',
            ], 400);
        }
        
        $result = FirebaseService::subscribeToTopic($token, $topic);
        
        return response()->json($result);
    });

    // Test notification sending
    Route::post('/firebase-notification', function () {
        $topic = request('topic', 'test-topic');
        $title = request('title', 'Test Notification');
        $body = request('body', 'This is a test notification');
        
        $result = FirebaseService::sendToTopic($topic, $title, $body, ['test' => 'data']);
        
        return response()->json($result);
    });

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
        
        return response()->json($report);
    });
});
