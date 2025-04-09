<?php

namespace App\Http\Permissions\Bookings;

use App\Models\Booking\Booking;
use App\Models\Users\User;
use App\Services\MessageService;

class BookingPermission
{
    public static function filterIndex($query)
    {
        $user = User::auth();

        if ($user->isCustomer()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isUserSalon()) {
            $query->where('salon_id', $user->salon->id);
        }

        return $query;
    }
    public static function canShow(Booking $booking)
    {
        $user = User::auth();

        if ($user->isCustomer()) {
            if ($booking->user_id != $user->id) {
                MessageService::abort(403, 'messages.booking.permission_error');
            }
        } elseif ($user->isUserSalon()) {
            if ($booking->salon_id != $user->salon->id) {
                MessageService::abort(403, 'messages.booking.permission_error');
            }
        }
    }
    public static function create($data)
    {
        $user = User::auth();

        if ($user->isCustomer()) {
            $data['user_id'] = $user->id;
        } elseif ($user->isUserSalon()) {
            $data['salon_id'] = $user->salon->id;
        }

        return $data;
    }
    public static function canUpdate(Booking $booking)
    {
        $user = User::auth();

        if ($user->isCustomer()) {
            if ($booking->user_id != $user->id) {
                MessageService::abort(403, 'messages.booking.permission_error');
            }
        } elseif ($user->isUserSalon()) {
            if ($booking->salon_id != $user->salon->id) {
                MessageService::abort(403, 'messages.booking.permission_error');
            }
        }
    }
    public static function canDelete(Booking $booking)
    {
        $user = User::auth();

        if ($user->isCustomer()) {
            if ($booking->user_id != $user->id) {
                MessageService::abort(403, 'messages.booking.permission_error');
            }
        } elseif ($user->isUserSalon()) {
            if ($booking->salon_id != $user->salon->id) {
                MessageService::abort(403, 'messages.booking.permission_error');
            }
        }
    }
}
