<?php

namespace App\Http\Permissions\Services;

use App\Models\Services\Group;
use App\Models\Users\User;
use App\Services\MessageService;

class GroupPermission
{
    public static function filterIndex($query)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            $query->where('salon_id', $user->salon->id)->orWhereNull('salon_id');
        }

        return $query;
    }

    public static function canShow(Group $group)
    {

        $user = User::auth();

        if ($user->isUserSalon()) {
            if ($group->salon_id != $user->salon->id && $group->salon_id != null) {
                MessageService::abort(403, 'messages.permission_error');
            }
        }

        return true;
    }

    public static function create($data)
    {

        $user = User::auth();

        if ($user->isUserSalon()) {
            $data['salon_id'] = $user->salon->id;
        }

        return $data;
    }

    public static function canUpdate(Group $group, $data)
    {

        $user = User::auth();

        if ($user->isUserSalon()) {
            if ($group->salon_id != $user->salon->id && $group->salon_id != null) {
                MessageService::abort(403, 'messages.permission_error');
            }
        }

        return true;
    }

    // canDelete
    public static function canDelete(Group $group)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            if ($group->salon_id != $user->salon->id && $group->salon_id != null) {
                MessageService::abort(403, 'messages.permission_error');
            }
        }

        return true;
    }
}
