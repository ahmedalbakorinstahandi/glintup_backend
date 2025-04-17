<?php

namespace App\Http\Permissions\Rewards;

use App\Models\Rewards\LoyaltyPoint;
use App\Models\Users\User;
use App\Services\MessageService;

class LoyaltyPointPermission
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

    public static function canShow(LoyaltyPoint $item)
    {
        $user = User::auth();

        if ($user->isCustomer()) {
            if ($item->user_id != $user->id) {
                MessageService::abort(403, 'messages.permission_error');
            }
        }

        if ($user->isUserSalon()) {
            if ($item->salon_id != $user->salon->id) {
                MessageService::abort(403, 'messages.permission_error');
            }
        }

        return true;
    }

    public static function create($data)
    {
        return $data;
    }

    public static function canUpdate(LoyaltyPoint $item, $data)
    {
        return true;
    }

    public static function canDelete(LoyaltyPoint $item)
    {
        return true;
    }
}
