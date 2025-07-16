<?php

namespace App\Http\Notifications;

use App\Models\GiftCard;
use App\Services\FirebaseService;

//send notification to user when he receive a gift card

class GiftCardNotification
{
    public static function sendGiftCardToUser($giftCard)
    {
        $user = $giftCard->recipient;

        $title = 'notifications.gift_card.new.title';
        $body = 'notifications.gift_card.new.body';

        $data = [
            'gift_card_id' => $giftCard->id,
            'sender' => $giftCard->sender->first_name . ' ' . $giftCard->sender->last_name,
        ];

        FirebaseService::sendToTokensAndStorage(
            [$user->id],
            [
                'id' => $giftCard->id,
                'type' => 'GiftCard',
            ],
            $title,
            $body,
            $data,
            $data
        );
    }

    // receive gift card
    public static function receiveGiftCard($giftCard)
    {

        $title = 'notifications.gift_card.receive.title';
        $body = 'notifications.gift_card.receive.body';

        $data = [
            'gift_card_id' => $giftCard->id,
            'sender' => $giftCard->sender->first_name . ' ' . $giftCard->sender->last_name,
        ];

        FirebaseService::sendToTokensAndStorage(
            [$giftCard->sender->id],
            [
                'id' => $giftCard->id,
                'type' => 'GiftCard',
            ],
            $title,
            $body,
            $data,
            $data
        );
    }
}
