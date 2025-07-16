<?php

namespace App\Http\Notifications;


use App\Models\Users\User;
use App\Services\LanguageService;

class NotificationHelper
{
    public static function getUsersSalonPermissions($salonId, $permissionKey)
    {
        return User::whereIn('role', ['salon_owner', 'staff'])->whereHas('salon', function ($query) use ($salonId) {
            $query->where('salons.id', $salonId);
        })->whereHas('salonPermissions', function ($query) use ($permissionKey) {
            $query->whereHas('permission', function ($query) use ($permissionKey) {
                $query->where('key', $permissionKey);
            });
        })->get();
    }

    public static function getUsersAdminPermissions($permissionKey)
    {
        return User::where('role', 'admin')->whereHas('adminPermissions', function ($query) use ($permissionKey) {
            $query->where('key', $permissionKey);
        })->get();
    }

    // handle locales
    public static function handleLocales($values, $key)
    {

        $data = [];

        $locales = config('translatable.locales');

        foreach ($locales as $locale) {
            $data[$locale] = $values[$locale];
        }


        return $data;
    }
}
