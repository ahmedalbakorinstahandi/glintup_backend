<?php

namespace App\Http\Permissions\Booking;

use App\Models\Booking\Coupon;
use App\Models\Users\User;
use App\Services\MessageService;

class CouponPermission
{
    public static function filterIndex($query)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            $query->where('salon_id', $user->salon?->id);
        }

        return $query;
    }

    public static function canShow(Coupon $item)
    {
        return true;
    }

    public static function create($data)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            $data['salon_id'] = $user->salon?->id;
        }

        $code = $data['code'];

        // unique code check
        $coupon = Coupon::where('code', $code)->where('salon_id', $data['salon_id'])->first();

        if ($coupon) {
            MessageService::abort(422, 'messages.coupon.code_already_exists', [
                'code' => $code,
            ]);
        }

        return $data;
    }

    public static function canUpdate(Coupon $item, $data)
    {
        return true;
    }

    public static function canDelete(Coupon $item)
    {
        return true;
    }
}
