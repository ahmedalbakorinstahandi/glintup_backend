<?php


namespace App\Http\Permissions\Salons;

use App\Models\Salons\SalonStaff;
use App\Models\Users\User;
use App\Services\MessageService;

class SalonStaffPermission
{
    public static function filterIndex($query)
    {

        $user = User::auth();

        if ($user->isUserSalon()) {
            $query->where('salon_id', $user->salon->id);
        }

        return $query;
    }

    public static function canShow(SalonStaff $item)
    {

        $user = User::auth();

        if ($user->isUserSalon()) {
            if ($item->salon_id != $user->salon->id) {
                MessageService::abort(403, 'messages.permission_error');
            }
        }

        return true;
    }

    public static function create($data)
    {

        $user = User::auth();

        if (!$user->isAdmin()) {
            $data['salon_id'] =  $user->salon->id;
        } else {
            $data['salon_id'] = $data['salon_id'] ?? null;
        }

        return $data;
    }

    public static function canUpdate(SalonStaff $item, $data)
    {

        $editor = User::auth();

        if ($editor->isUserSalon()) {
            $salon = $editor->salon;

            if ($item->salon_id != $salon->id) {
                MessageService::abort(403, 'messages.permission_error');
            }
        }

        return true;
    }

    public static function canDelete(SalonStaff $item)
    {

        $user = User::auth();

        if ($user->isUserSalon()) {
            if ($item->salon_id != $user->salon->id) {
                MessageService::abort(403, 'messages.permission_error');
            }
        }

        return true;
    }
}
