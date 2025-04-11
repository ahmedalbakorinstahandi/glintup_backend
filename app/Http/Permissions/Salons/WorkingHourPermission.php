<?php


namespace App\Http\Permissions\Salons;

use App\Models\Salons\WorkingHour;
use App\Models\Users\User;
use App\Services\MessageService;

class WorkingHourPermission
{
    public static function filterIndex($query)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            $query->where('salon_id', $user->salon?->id);
        }

        return $query;
    }

    public static function canShow(WorkingHour $item)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            if ($item->salon_id !== $user->salon?->id) {
                MessageService::abort(403, 'messages.working_hour.item_not_found');
            }
        }
    }

    public static function create($data)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            $data['salon_id'] = $user->salon?->id;
        }

        return $data;
    }

    public static function canUpdate(WorkingHour $item, $data)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            if ($item->salon_id !== $user->salon?->id) {
                MessageService::abort(403, 'messages.working_hour.item_not_found');
            }
        }
    }

    public static function canDelete(WorkingHour $item)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            if ($item->salon_id !== $user->salon?->id) {
                MessageService::abort(403, 'messages.working_hour.item_not_found');
            }
        }
    }
}
