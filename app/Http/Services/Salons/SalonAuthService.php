<?php

namespace App\Http\Services\Salons;

use App\Models\Users\User;
use App\Services\FirebaseService;
use App\Services\MessageService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class SalonAuthService
{
    public function login($loginUserData)
    {
        $inputPhone = str_replace(' ', '', $loginUserData['phone']);

        $user = User::whereRaw("REPLACE(CONCAT(phone_code, phone), ' ', '') = ?", [$inputPhone])
            ->whereIn('role', ['staff', 'salon_owner'])
            ->first();

        if (!$user || !Hash::check($loginUserData['password'], $user->password)) {
            return null;
        }

        // $user->load(['salonPermissions.permission']);

        return $user;
    }


    public function register($requestData)
    {
        $verifyCode = rand(100000, 999999);
        $codeExpiry = Carbon::now()->addMinutes(10);

        $user = User::create([
            'first_name' => $requestData['first_name'],
            'last_name' => $requestData['last_name'],
            'password' => Hash::make($requestData['password']),
            'phone' => $requestData['phone'],
            'phone_code' => $requestData['phone_code'],
            'verified' => 0,
            'otp' => $verifyCode,
            'otp_expire_at' => $codeExpiry,
            'role' => 'customer',
            'gender' => $requestData['gender'],
            'birth_date' => $requestData['birth_date'],
            'avatar' => $requestData['avatar'] ?? null,
            'is_active' => 1,
            'is_verified' => 0,
            'language' => $requestData['language'] ?? 'ar',
        ]);

        // TODO send sms to user
        // $this->sendSms($user->phone, $user->phone_code, $verifyCode);

        return $user;
    }

    public function verifyCode($requestData)
    {


        $inputPhone = str_replace(' ', '', $requestData['phone']);

        $user = User::whereRaw("REPLACE(CONCAT(phone_code, phone), ' ', '') = ?", [$inputPhone])
            ->where('role', 'customer')
            ->first();

        if (!$user) {
            MessageService::abort(404, 'messages.phone_not_found');
        }



        // TODO: Uncomment the following lines when the verification code logic is implemented
        // if ($user->verify_code !== $requestData['verify_code'] || Carbon::now()->greaterThan($user->code_expiry_date)) {
        //     MessageService::abort(401, 'messages.invalid_or_expired_verification_code');
        // }



        if ($user->is_verified == 0)
            $user->update([
                'is_verified' => 1,
                'otp' => null,
                'otp_expire_at' => null,
            ]);


        return $user;
    }


    public function forgotPassword($requestData)
    {
        $inputPhone = str_replace(' ', '', $requestData['phone']);

        $user = User::whereRaw("REPLACE(CONCAT(phone_code, phone), ' ', '') = ?", [$inputPhone])
            ->where('role', 'customer')
            ->first();


        if (!$user) {
            return false;
        }

        $verifyCode = rand(1000, 9999);
        $codeExpiry = Carbon::now()->addMinutes(10);

        $user->update([
            'otp' => $verifyCode,
            'otp_expire_at' => $codeExpiry,
        ]);


        // $subject = "Reset Your Password - InstaHandi";
        // $formattedCode = "<span style='font-size: 24px; font-weight: bold;'>$verifyCode</span>";
        // $content = "Hello,<br><br>We received a request to reset your password for your InstaHandi account. Use the code below to proceed:<br><br>🔢 Your password reset code: $formattedCode<br><br>This code is valid for 10 minutes. If you didn’t request this, please ignore this email, and your password will remain unchanged.<br><br>If you need help, feel free to contact our support team.<br><br>Best,<br>InstaHandi Team";

        // EmailService::sendEmail($user->email, $subject, $content);

        // TODO send sms to user
        // $this->sendSms($user->phone, $user->phone_code, $verifyCode);

        return true;
    }


    public function resetPassword($requestData)
    {

        $user = User::auth();

        $password = $requestData['password'];
        $user->update([
            'password' => Hash::make($password),
        ]);

        $user->tokens()->delete();

        $newToken = $user->createToken($user->first_name)->plainTextToken;


        return [
            'success' => true,
            'token' => $newToken,
        ];
    }

    public function logout($token)
    {
        $personalAccessToken = PersonalAccessToken::findToken($token);

        FirebaseService::unsubscribeFromAllTopic($personalAccessToken->tokenable);

        return $personalAccessToken->delete();
    }
}
