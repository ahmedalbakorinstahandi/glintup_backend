<?php

namespace App\Http\Notifications;

use App\Http\Notifications\NotificationHelper;
use App\Services\FirebaseService;

class BookingNotification
{
    public static function newBooking($booking)
    {
        $user = $booking->user;

        $title = 'notifications.salon.booking.new_booking';
        $body = 'notifications.salon.booking.new_booking_body';

        $data = [
            'booking_id' => $booking->id,
            'salon_name' => $booking->salon->merchant_commercial_name,
        ];

        $pemissionKey = 'bookings';

        $users = NotificationHelper::getUsersSalonPermissions($booking->salon_id, $pemissionKey);

        FirebaseService::sendToTokensAndStorage(
            $users->pluck('id'),
            [
                'id' => $booking->id,
                'type' => 'Booking',
            ],
            $title,
            $body,
            $data,
            $data
        );
    }


    // "completed", "cancelled", "Rejected"
    // completed
    public static function bookingCompleted($booking)
    {
        $user = $booking->user;

        $title = 'notifications.user.booking.completed.title';
        $body = 'notifications.user.booking.completed.body';

        $data = [
            'booking_id' => $booking->id,
            'salon_name' => $booking->salon->merchant_commercial_name,
        ];

        FirebaseService::sendToTokensAndStorage(
            [$user->id],
            [
                'id' => $booking->id,
                'type' => 'Booking',
            ],
            $title,
            $body,
            $data,
            $data
        );
    }
    // cancelled   
    public static function bookingCancelled($booking)
    {
        $user = $booking->user;

        $title = 'notifications.user.booking.cancelled.title';
        $body = 'notifications.user.booking.cancelled.body';

        $data = [
            'booking_id' => $booking->id,
            'salon_name' => $booking->salon->merchant_commercial_name,
        ];

        FirebaseService::sendToTokensAndStorage(
            [$user->id],
            [
                'id' => $booking->id,
                'type' => 'Booking',
            ],
            $title,
            $body,
            $data,
            $data
        );
    }
    // rejected
    public static function bookingRejected($booking)
    {
        $user = $booking->user;

        $title = 'notifications.user.booking.rejected.title';
        $body = 'notifications.user.booking.rejected.body';

        $data = [
            'booking_id' => $booking->id,
            'salon_name' => $booking->salon->merchant_commercial_name,
        ];

        FirebaseService::sendToTokensAndStorage(
            [$user->id],
            [
                'id' => $booking->id,
                'type' => 'Booking',
            ],
            $title,
            $body,
            $data,
            $data
        );
    }

    // new booking for user when salon create new booking
    public static function newBookingForUser($booking)
    {
        $user = $booking->user;

        $title = 'notifications.user.booking.new_booking.title';
        $body = 'notifications.user.booking.new_booking_body';

        $data = [
            'booking_id' => $booking->id,
            'salon_name' => $booking->salon->merchant_commercial_name,
        ];

        FirebaseService::sendToTokensAndStorage(
            [$user->id],
            [
                'id' => $booking->id,
                'type' => 'Booking',
            ],
            $title,
            $body,
            $data,
            $data
        );
    }
}
