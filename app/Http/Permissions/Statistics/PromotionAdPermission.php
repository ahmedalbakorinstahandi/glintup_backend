<?php

namespace App\Http\Permissions\Statistics;

use App\Models\Statistics\PromotionAd;
use App\Models\Users\User;
use App\Services\MessageService;

class PromotionAdPermission
{
    public static function filterIndex($query)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            $query->where('salon_id', $user->salon?->id);
        }

        return $query;
    }

    public static function canShow(PromotionAd $ad)
    {
        $user = User::auth();

        if ($user->isUserSalon() && $ad->salon_id != $user->salon?->id) {
            MessageService::abort(403, 'messages.permission_error');
        }

        return true;
    }

    public static function create($data)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            $data['salon_id'] = $user->salon?->id;
        }

        return $data;
    }

    public static function canUpdate(PromotionAd $ad, $data)
    {
        $user = User::auth();

        if ($user->isUserSalon() && $ad->salon_id != $user->salon?->id) {
            return false;
        }

        return true;
    }

    public static function canDelete(PromotionAd $ad)
    {
        $user = User::auth();

        if ($user->isUserSalon() && $ad->salon_id != $user->salon?->id) {
            return false;
        }

        return true;
    }
}
