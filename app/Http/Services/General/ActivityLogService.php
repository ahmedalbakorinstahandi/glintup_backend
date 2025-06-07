<?php

namespace App\Http\Services\General;

use App\Models\General\ActivityLog;
use App\Models\Users\User;
use App\Services\FilterService;

class ActivityLogService
{

    public function index($data)
    {
        $query = ActivityLog::query();

        $user = User::auth();

        if ($user->isUserSalon()) {

            $salon = $user->salon;

            $staff = $salon->staff;

            $query->whereIn('user_id', $staff->pluck('user_id'));
        }


        $query = FilterService::applyFilters(
            $query,
            $data,
        );

        return $query;
    }
}
