<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsappMessageService
{


    public static function send(string $number, string $message): array
    {
        // $endpoint = 'https://otp.metaphilia.com/api/send-message';
        // $apiKey = 'nxCYTXJmn6yXG8vL8PlWw4pQDOJ1MO';
        // $sender = '352681532331';

        // $response = Http::post($endpoint, [
        //     'api_key' => $apiKey,
        //     'sender'  => $sender,
        //     'number'  => $number,
        //     'message' => $message,
        // ]);

        // if ($response->successful()) {
        //     return $response->json();
        // }

        return [
            'status' => false,
            'msg'    => 'Failed to send message',
            // 'error'  => $response->body(),
        ];
    }
}
