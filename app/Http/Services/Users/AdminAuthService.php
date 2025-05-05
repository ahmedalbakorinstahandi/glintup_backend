<?php

namespace App\Http\Services\Users;

use App\Models\Users\User;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Hash;

class AdminAuthService
{
    public function login($loginUserData)
    {
        $inputPhone = str_replace(' ', '', $loginUserData['phone']);

        $user = User::whereRaw("REPLACE(CONCAT(phone_code, phone), ' ', '') = ?", [$inputPhone])
            ->where('role', 'admin')
            ->first();

        if (!$user || !Hash::check($loginUserData['password'], $user->password)) {
            return null;
        }


        // $user->load(['salonPermissions.permission']);

        return $user;
    }
}
