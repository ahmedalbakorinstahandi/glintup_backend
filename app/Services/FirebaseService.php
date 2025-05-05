<?php

namespace App\Services;

use App\Http\Services\General\NotificationService;
use App\Models\Users\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class FirebaseService
{
    protected static $firebaseMessaging;

    /**
     * Subscribe to a topic using a token.
     */
    public static function subscribeToTopic($registrationToken, $topic)
    {
        if (!self::isValidTopic($topic)) {
            return [
                'success' => false,
                'message' => 'Topic name format is invalid',
            ];
        }

        $messaging = self::getFirebaseMessaging()->createMessaging();

        try {
            $response = $messaging->subscribeToTopic($topic, $registrationToken);
            return [
                'success' => true,
                'message' => 'Successfully subscribed to topic',
                'response' => $response,
            ];
        } catch (\Throwable $e) {
            return self::handleException($e);
        }
    }


    public static function subscribeToAllTopic($request, $user)
    {


        $deviceToken = $request->device_token;

        Log::info('device_token' . $deviceToken);

        $latestToken = $user->tokens()->latest()->first();

        Log::info('latestToken' . $latestToken);
        if ($latestToken) {
            DB::table('personal_access_tokens')
                ->where('id', $latestToken->id)
                ->update(['device_token' => $deviceToken]);
        }

        $APP_ENV_TYPE = env('APP_ENV_TYPE', 'staging');

        $topics = [
            'user-' . $user->id,
            'role-' . $user->role,
            'all-users',
        ];

        if ($APP_ENV_TYPE != 'production') {
            for ($i = 0; $i < count($topics); $i++) {
                $topics[$i] = $topics[$i] . '-' . $APP_ENV_TYPE;
            }
        }


        foreach ($topics as $topic) {

            $subscriptionResult = FirebaseService::subscribeToTopic($deviceToken, $topic);

            // i need store device token in personal access token table

            if (!$subscriptionResult['success']) {
                Log::error('Failed to subscribe to topic', [
                    'topic' => $topic,
                    'device_token' => $deviceToken,
                    'error' => $subscriptionResult['error'] ?? 'Unknown error',
                ]);
            }
        }
    }


    /**
     * Send notification to a specific topic.
     */
    public static function sendToTopic($topic, $title, $body, $data = [], $channelId = null)
    {
        $messaging = self::getFirebaseMessaging()->createMessaging();

        $messageConfig = self::createMessageConfig($topic, $title, $body, $data, $channelId);
        $message = CloudMessage::fromArray($messageConfig);

        try {
            $response = $messaging->send($message);
            return [
                'success' => true,
                'message' => 'Notification sent successfully',
                'response' => $response,
            ];
        } catch (\Throwable $e) {
            return self::handleException($e);
        }
    }
    public static function sendToTopicAndStorage($topic, $users_ids, $notificationable, $title, $body, $replace, $data = [], $isCustom = false, $channelId = null)
    {
        $messaging = self::getFirebaseMessaging()->createMessaging();


        NotificationService::storeNotification(
            $users_ids,
            $notificationable,
            $title,
            $body,
            $replace,
            $data,
            $isCustom
        );

        $data['notificationable_id'] = $notificationable['id'] ?? null;
        $type = $notificationable['type'] ?? 'Custom';
        $data['notificationable_type'] = $type;

        if ($isCustom) {
            $messageConfig = self::createMessageConfig($topic, $title,  $body,  $data, $channelId);
        } else {
            $messageConfig = self::createMessageConfig($topic, __($title, $replace), __($body, $replace), $data, $channelId);
        }
        $message = CloudMessage::fromArray($messageConfig);


        try {
            $response = $messaging->send($message);
            return [
                'success' => true,
                'message' => 'Notification sent successfully',
                'response' => $response,
            ];
        } catch (\Throwable $e) {
            return self::handleException($e);
        }
    }

    /**
     * Unsubscribe from a specific topic.
     */
    public static function unsubscribeFromTopic($registrationToken, $topic)
    {
        $messaging = self::getFirebaseMessaging()->createMessaging();

        try {
            $response = $messaging->unsubscribeFromTopic($topic, $registrationToken);
            return [
                'success' => true,
                'message' => 'Successfully unsubscribed from topic',
                'response' => $response,
            ];
        } catch (\Throwable $e) {
            return self::handleException($e);
        }
    }
    public static function unsubscribeFromAllTopic($personalAccessToken)
    {

        if ($personalAccessToken) {

            $deviceToken = $personalAccessToken->device_token;

            if ($deviceToken) {
                $user = Auth::user();

                $APP_ENV_TYPE = env('APP_ENV_TYPE', 'staging');

                $topics = [
                    'user-' . $user->id,
                    'role-' . $user->role,
                    'all-users',
                ];

                if ($APP_ENV_TYPE != 'production') {
                    for ($i = 0; $i < count($topics); $i++) {
                        $topics[$i] = $topics[$i] . '-' . $APP_ENV_TYPE;
                    }
                }

                foreach ($topics as $topic) {
                    FirebaseService::removeTopicFromToken($deviceToken, $topic);
                }
            }

            // $personalAccessToken->delete();
        }
    }

    /**
     * Remove a topic from a specific token.
     */
    public static function removeTopicFromToken($registrationToken, $topic)
    {
        if (!self::isValidTopic($topic)) {
            return [
                'success' => false,
                'message' => 'Topic name format is invalid',
            ];
        }

        $messaging = self::getFirebaseMessaging()->createMessaging();

        try {
            $response = $messaging->unsubscribeFromTopic($topic, $registrationToken);
            return [
                'success' => true,
                'message' => 'Successfully removed topic from token',
                'response' => $response,
            ];
        } catch (\Throwable $e) {
            return self::handleException($e);
        }
    }

    /**
     * Validate topic name.
     */
    protected static function isValidTopic($topic)
    {
        return preg_match('/^[a-zA-Z0-9_-]+$/', $topic);
    }

    /**
     * Setup Firebase Messaging.
     */
    protected static function getFirebaseMessaging()
    {
        if (!self::$firebaseMessaging) {
            $serviceAccount = self::loadServiceAccount();
            self::$firebaseMessaging = (new Factory)->withServiceAccount($serviceAccount);
        }

        return self::$firebaseMessaging;
    }

    /**
     * Load service account data.
     */
    protected static function loadServiceAccount()
    {
        $serviceAccountPath = storage_path('firebase/glint-up-firebase-adminsdk-fbsvc-f7a7261316.json');

        if (!file_exists($serviceAccountPath)) {
            throw new \Exception("Firebase service account file not found.");
        }

        $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);

        return $serviceAccount;
    }

    /**
     * Handle exceptions and log them.
     */
    protected static function handleException(\Throwable $e)
    {
        Log::error('Firebase Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return [
            'success' => false,
            'message' => $e->getMessage(),
            'error' => $e->getMessage(),
        ];
    }

    /**
     * Create message configuration.
     */
    protected static function createMessageConfig($topic, $title, $body, $data, $channelId)
    {

        $config = [
            'topic' => $topic,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            'data' => $data,

        ];

        if ($channelId) {
            $config['android'] = [
                'notification' => [
                    'channel_id' => $channelId,
                ],
            ];
        }

        return $config;
    }


    public static function sendIncomingCall($topic, $serviceRequestId, $requestFilter, $customerName, $avatar, $phone)
    {
        $messaging = self::getFirebaseMessaging()->createMessaging();

        $data = [
            'call_id' => uniqid(),
            'type' => 'incoming_call',
            'service_request_id' => $serviceRequestId,
            'caller_name' => $customerName,
            'avatar' => $avatar,
            'handle' => $phone,
            'user_id' => Auth::check() ? User::auth()->id : 0,
        ];

        $messageConfig = [
            'topic' => $topic,
            'data' => $data,
            'android' => [
                'priority' => 'high',
                'ttl' => '30s',
                // 'notification' => [
                //     'channel_id' => 'incoming_calls',
                //     'default_sound' => true,
                //     'default_vibrate_timings' => true,
                //     'sound' => 'default',
                // ],
            ],
            'apns' => [
                'headers' => [
                    'apns-priority' => '10',
                ],
                'payload' => [
                    'aps' => [
                        'category' => 'INCOMING_CALL',
                        'content-available' => 1,
                    ],
                ],
            ],
        ];

        $message = CloudMessage::fromArray($messageConfig);

        try {
            $response = $messaging->send($message);
            return [
                'success' => true,
                'message' => 'Incoming call notification sent successfully to topic',
                'response' => $response,
            ];
        } catch (\Throwable $e) {
            return self::handleException($e);
        }
    }
}
