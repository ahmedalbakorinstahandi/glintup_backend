<?php

namespace App\Http\Services\Users;

use App\Models\Rewards\GiftCard;
use App\Models\Users\User;
use App\Services\FirebaseService;
use App\Services\MessageService;
use App\Services\PhoneService;
use App\Services\WhatsappMessageService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class UserAuthService
{
    public function login($loginUserData)
    {
        // $inputPhone = str_replace(' ', '', $loginUserData['phone']);

        $phoneParts = PhoneService::parsePhoneParts($loginUserData['phone']);
        $countryCode = $phoneParts['country_code'];
        $phoneNumber = $phoneParts['national_number'];

        $user = User::where('phone', $phoneNumber)
            ->where('phone_code', $countryCode)
            ->where('role', 'customer')
            ->first();

        if ($user && $user->is_active == 0) {
            MessageService::abort(422, 'messages.user.is_banned');
        }

        if ($user && $user->is_verified == 0) {
            MessageService::abort(422, 'messages.user.registered_but_not_verified');
        }



        if (!$user || !Hash::check($loginUserData['password'], $user->password)) {
            return null;
        }

        return $user;
    }


    // check phone number valid or not
    public function checkPhoneNumber($phoneNumber)
    {
        $phoneParts = PhoneService::parsePhoneParts($phoneNumber, false);

        if (!$phoneParts) {
            return [
                'valid' => false,
                'error_message' => trans('messages.phone.invalid'),
            ];
        }

        $countryCode = $phoneParts['country_code'];
        $phoneNumber = $phoneParts['national_number'];


        return [
            'valid' => true,
            'phone_code' => $countryCode,
            'phone' => $phoneNumber,
        ];
    }

    public function register($requestData)
    {

        $phoneParts = PhoneService::parsePhoneParts($requestData['phone']);

        $countryCode = $phoneParts['country_code'];
        $phoneNumber = $phoneParts['national_number'];


        $user = User::where('phone', $phoneNumber)
            ->where('phone_code', $countryCode)
            ->where('role', 'customer')
            ->first();


        $verifyCode = rand(100000, 999999);
        $codeExpiry = Carbon::now()->addMinutes(10);

        if ($user && $user->is_active == 0) {
            MessageService::abort(422, 'messages.user.is_banned');
        } elseif ($user && $user->is_verified == 1) {
            MessageService::abort(422, 'messages.user.already_registered');
        } elseif ($user && $user->added_by == 'salon') {

            $user->update(
                [
                    'first_name' => $requestData['first_name'],
                    'last_name' => $requestData['last_name'],
                    'password' => Hash::make($requestData['password']),
                    'phone' =>  $phoneNumber,
                    'phone_code' => $countryCode,
                    'otp' => $verifyCode,
                    'otp_expire_at' => $codeExpiry,
                    'role' => 'customer',
                    'gender' => $requestData['gender'],
                    'birth_date' => $requestData['birth_date'],
                    'avatar' => $requestData['avatar'] ?? null,
                    'is_active' => 1,
                    'is_verified' => 0,
                    'language' => $requestData['language'] ?? 'ar',
                    'register_at' => Carbon::now(),
                ]
            );
        } elseif ($user && $user->is_verified == 0) {
            MessageService::abort(422, 'messages.user.registered_but_not_verified');
        }


        if (!$user) {


            $user = User::create([
                'first_name' => $requestData['first_name'],
                'last_name' => $requestData['last_name'],
                'password' => Hash::make($requestData['password']),
                'phone' =>  $phoneNumber,
                'phone_code' => $countryCode,
                'otp' => $verifyCode,
                'otp_expire_at' => $codeExpiry,
                'role' => 'customer',
                'gender' => $requestData['gender'],
                'birth_date' => $requestData['birth_date'],
                'avatar' => $requestData['avatar'] ?? null,
                'is_active' => 1,
                'is_verified' => 0,
                'language' => $requestData['language'] ?? 'ar',
                'added_by' => Auth::user() ? Auth::user()->id : null,
                'register_at' => Carbon::now(),
            ]);
        }

        // $this->sendSms($user->phone, $user->phone_code, $verifyCode);
        WhatsappMessageService::send(
            $user->phone_code . $user->phone,
            trans("messages.activation_code_message", ['verifyCode' => $verifyCode])
        );


        // check have gift card for this user phone 

        $giftCards = GiftCard::where('phone', $user->phone)
            ->where('phone_code', $user->phone_code)
            ->whereNull('recipient_id')
            ->get();



        if ($giftCards) {
            foreach ($giftCards as $giftCard) {
                $giftCard->update([
                    'recipient_id' => $user->id,
                ]);
            }

            // send notification to the sender and recipient
        }


        return $user;
    }

    public function verifyCode($requestData)
    {


        $phoneParts = PhoneService::parsePhoneParts($requestData['phone']);
        $countryCode = $phoneParts['country_code'];
        $phoneNumber = $phoneParts['national_number'];

        $user = User::where('phone', $phoneNumber)
            ->where('phone_code', $countryCode)
            ->where('role', 'customer')
            ->first();

        if (!$user) {
            MessageService::abort(404, 'messages.phone_not_found');
        }



        if ($user->otp !== $requestData['verify_code'] || Carbon::now()->greaterThan($user->otp_expire_at)) {
            MessageService::abort(401, 'messages.invalid_or_expired_verification_code');
        }



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
        // $inputPhone = str_replace(' ', '', $requestData['phone']);
        $phoneParts = PhoneService::parsePhoneParts($requestData['phone']);
        $countryCode = $phoneParts['country_code'];
        $phoneNumber = $phoneParts['national_number'];


        $user = User::where('phone', $phoneNumber)
            ->where('phone_code', $countryCode)
            ->where('role', 'customer')
            ->first();


        if (!$user) {
            return false;
        }

        $verifyCode = rand(100000, 999999);
        $codeExpiry = Carbon::now()->addMinutes(10);

        $user->update([
            'otp' => $verifyCode,
            'otp_expire_at' => $codeExpiry,
        ]);


        WhatsappMessageService::send(
            $user->phone_code . $user->phone,
            trans("messages.password_reset_code_message", ['verifyCode' => $verifyCode])
        );

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

    public function requestDeleteAccount(User $user)
    {
        $otp = rand(100000, 999999);
        $otpExpideAt = Carbon::now()->addMinutes(30);

        $user->update([
            'otp' => $otp,
            'otp_expide_at' => $otpExpideAt,
        ]);

        WhatsappMessageService::send(
            $user->phone_code . $user->phone,
            trans("messages.delete_account_code_message", ['verifyCode' => $otp])
        );
    }

    public function confirmDeleteAccount(User $user, $code)
    {
        if ($user->otp !== $code || Carbon::now()->greaterThan($user->otp_expide_at)) {
            return false;
        }

        $user->tokens()->delete();
        $user->delete();

        return true;
    }
}
