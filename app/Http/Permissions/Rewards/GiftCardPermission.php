<?php

namespace App\Http\Permissions\Rewards;

use App\Models\Rewards\GiftCard;
use App\Models\Users\User;
use App\Services\MessageService;

class GiftCardPermission
{
    public static function filterIndex($query)
    {

        $user = User::auth();

        // filter by sender_id, recipient_id
        if ($user->isUserSalon()) {
            $query->where('salon_id', $user->salon->id);
        } elseif ($user->isCustomer()) {
            $query->where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)->orWhere('recipient_id', $user->id);
            });
        }


        return $query;
    }

    public static function canShow(GiftCard $item)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            if ($item->salon_id != $user->salon->id) {
                MessageService::abort(503, 'messages.permission_error');
            }
        } elseif ($user->isCustomer()) {
            if ($item->sender_id != $user->id && $item->recipient_id != $user->id) {
                MessageService::abort(503, 'messages.permission_error');
            }
        }

        return false;
    }

    public static function create($data)
    {
        return $data;
    }

    public static function canUpdate(GiftCard $item, $data)
    {
        return true;
    }

    public static function canDelete(GiftCard $item)
    {
        return true;
    }
}
