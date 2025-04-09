<?php


namespace App\Http\Permissions\Services;

use App\Models\Services\Service;
use App\Models\Users\User;
use App\Services\MessageService;

class ServicePermission
{
    public static function filterIndex($query)
    {

        $user = User::auth();


        if ($user->isUserSalon()) {
            $query->where('salon_id', $user->salon->id);
        }


        return $query;
    }

    public static function canShow(Service $service)
    {

        $user = User::auth();

        if ($user->isUserSalon()) {
            if ($service->salon_id != $user->salon->id) {
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

    public static function canUpdate(Service $service, $data)
    {

        $user = User::auth();

        if ($user->isUserSalon()) {
            if ($service->salon_id != $user->salon->id) {
                return false;
            }
        }

        return true;
    }

    public static function canDelete(Service $service)
    {

        $user = User::auth();

        if ($user->isUserSalon()) {
            if ($service->salon_id != $user->salon->id) {
                return false;
            }
        }

        return true;
    }
}
