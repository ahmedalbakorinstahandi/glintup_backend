<?php

namespace App\Http\Services\Users;

use App\Models\Users\User;
use App\Services\FirebaseService;
use App\Services\PhoneService;
use Illuminate\Support\Facades\Hash;

class AdminAuthService
{
    public function login($loginUserData)
    {
        $phoneParts = PhoneService::parsePhoneParts($loginUserData['phone']);
        $countryCode = $phoneParts['country_code'];
        $phoneNumber = $phoneParts['national_number'];

        $user = User::where('phone', $phoneNumber)
            ->where('phone_code', $countryCode)
            ->where('role', 'admin')
            ->first();

        if (!$user || !Hash::check($loginUserData['password'], $user->password)) {
            return null;
        }


        // $user->load(['salonPermissions.permission']);

        return $user;
    }
}
