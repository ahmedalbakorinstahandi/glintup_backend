<?php

namespace App\Http\Permissions\Salons;

use App\Models\Salons\Salon;
use App\Models\Users\User;
use App\Services\MessageService;

class SalonPermission
{
    public static function filterIndex($query)
    {
        $user = User::auth();
        
        // تسجيل معلومات المستخدم
        \Illuminate\Support\Facades\Log::info('Filtering salons for user: ' . $user->id . ' - Role: ' . $user->role);

        if ($user->isCustomer()) {
            $query->where('is_approved', true)
                  ->where('is_active', true);
            
            // تسجيل الاستعلام بعد التصفية
            \Illuminate\Support\Facades\Log::info('Filtered query: ' . $query->toSql());
            \Illuminate\Support\Facades\Log::info('Filtered bindings: ' . json_encode($query->getBindings()));
        }

        return $query;
    }
    public static function canShow(Salon $salon)
    {

        return true;
    }
    public static function create($data)
    {
        return $data;
    }
    public static function canUpdate(Salon $salon)
    {

        // $user = User::auth();

        // if ($user->isSalonOwner()) {
        //     // if ($user->id != $salon->owner_id) {
        //     //     MessageService::abort(403, 'messages.permission_error');
        //     // }
        // } elseif (!$user->isAdmin()) {
        //     MessageService::abort(403, 'messages.permission_error');
        // }


        return true;
    }
    public static function canDelete(Salon $salon)
    {
        return true;
    }
}
