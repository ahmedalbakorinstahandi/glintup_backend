<?php


namespace App\Http\Permissions\Salons;

use App\Models\Salons\SalonSocialMediaSite;
use App\Models\Users\User;
use App\Services\MessageService;
use Google\Protobuf\Internal\Message;

class SalonSocialMediaSitePermission
{
    public static function filterIndex($query)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            $query->where('salon_id', $user->salon?->id);
        }

        return $query;
    }

    public static function canShow(SalonSocialMediaSite $item)
    {
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

    public static function canUpdate(SalonSocialMediaSite $item, $data)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            if ($item->salon_id != $user->salon?->id) {
                MessageService::abort(403, 'messages.permission_error');
            }
        }

        return true;
    }

    public static function canDelete(SalonSocialMediaSite $item)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            if ($item->salon_id != $user->salon?->id) {
                MessageService::abort(403, 'messages.permission_error');
            }
        }
        return true;
    }
}
