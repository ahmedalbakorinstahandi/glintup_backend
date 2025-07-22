<?php

namespace App\Services;

use App\Models\General\ActivityLog;

class ActivityLogHelper
{
    public static function createActivityLog($user_id, $action, $description, $activityable_type = null, $activityable_id = null)
    {
        ActivityLog::create([
            'user_id' => $user_id,
            'action' => $action,
            'description' => $description,
            'activityable_type' => $activityable_type,
            'activityable_id' => $activityable_id,
        ]);
    }
}
