<?php

namespace App\Http\Services\Salons;

use App\Models\General\Setting;
use App\Models\Salons\Salon;
use App\Models\Salons\SalonPermission;
use App\Models\Salons\SalonStaff;
use App\Models\Salons\UserSalonPermission;
use App\Models\Users\User;
use App\Services\FirebaseService;
use App\Services\MessageService;
use App\Services\PhoneService;
use App\Services\WhatsappMessageService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

use function PHPSTORM_META\type;

class SalonAuthService
{
    public function login($loginUserData)
    {
        // $inputPhone = str_replace(' ', '', $loginUserData['phone']);

        $phoneParts = PhoneService::parsePhoneParts($loginUserData['phone']);
        $countryCode = $phoneParts['country_code'];
        $phoneNumber = $phoneParts['national_number'];

        $user = User::where('phone', $phoneNumber)
            ->where('phone_code', $countryCode)
            ->whereIn('role', ['staff', 'salon_owner'])
            ->first();

        if (!$user || !Hash::check($loginUserData['password'], $user->password)) {
            return null;
        }

        // $user->load(['salonPermissions.permission']);

        return $user;
    }

    // $verifyCode = rand(100000, 999999);
    // $codeExpiry = Carbon::now()->addMinutes(10);
    // TODO send sms to user
    // $this->sendSms($user->phone, $user->phone_code, $verifyCode);

    public function register($requestData)
    {


        $userData = $requestData['user'];


        $phoneParts = PhoneService::parsePhoneParts($userData['phone']);
        $countryCode = $phoneParts['country_code'];
        $phoneNumber = $phoneParts['national_number'];

        $userData['phone_code'] = $countryCode;
        $userData['phone'] = $phoneNumber;

        $fulPhoneNumber = $countryCode . $phoneNumber;


        $user = User::where('phone', $phoneNumber)
            ->where('phone_code', $countryCode)
            // where role is salon_owner or staff    
            ->whereIn('role', ['salon_owner', 'staff'])
            ->first();


        if ($user) {
            MessageService::abort(409, 'messages.phone_already_taken');
        }

        $userData['password'] = Hash::make($userData['password']);
        $userData['role'] = 'salon_owner';

        $userData['is_verified'] = 1;
        $userData['is_active'] = 1;
        $userData['language'] = app()->getLocale();
        $userData['added_by'] = 'register';

        unset($requestData['user']);

        $userSalonOnwer = User::create($userData);

        $salon = Salon::create([
            'owner_id' => $userSalonOnwer->id,
            'merchant_legal_name' => $requestData['merchant_legal_name'],
            'merchant_commercial_name' => $requestData['merchant_commercial_name'],
            'address' => $requestData['address'],
            'city_street_name' => $requestData['city_street_name'],
            'contact_name' => $requestData['contact_name'],
            'contact_number' => $requestData['contact_number'],
            'contact_email' => $requestData['contact_email'],
            'business_contact_name' => $requestData['business_contact_name'],
            'business_contact_email' => $requestData['business_contact_email'],
            'business_contact_number' => $requestData['business_contact_number'],

            'icon' => $requestData['icon'],
            'description' => $requestData['description'],
            'latitude' => $requestData['latitude'],
            'longitude' => $requestData['longitude'],
            'type' => $requestData['type'],
            'bio' => $requestData['bio'],

            'service_location' => $requestData['service_location'] ?? null,
            'bank_name' => $requestData['bank_name'],
            'bank_account_number' => $requestData['bank_account_number'],
            'bank_account_holder_name' => $requestData['bank_account_holder_name'],
            'bank_account_iban' => $requestData['bank_account_iban'],
            'services_list' => $requestData['services_list'],
            'trade_license' => $requestData['trade_license'],
            'vat_certificate' => $requestData['vat_certificate'],
            'bank_account_certificate' => $requestData['bank_account_certificate'],


            // old data
            'name' => '',
            'phone_code' => '',
            'phone' => '',
            'email' => null,
            'location' => '',
            'country' => '',
            'city' => '',
            'types' => '',
        ]);

        SalonStaff::create([
            'user_id' => $userSalonOnwer->id,
            'salon_id' => $salon->id,
            'position' => 'owner',
            'is_active' => 1,
        ]);

        $salonPermissions = SalonPermission::get();

        foreach ($salonPermissions as $permission) {
            UserSalonPermission::create([
                'user_id' => $userSalonOnwer->id,
                'salon_id' => $salon->id,
                'permission_id' => $permission->id,
            ]);
        }

        $report = $this->registerMessage(
            $userSalonOnwer,
            $salon,
            app()->getLocale()
        );


        WhatsappMessageService::send($fulPhoneNumber, $report);

        return $userSalonOnwer->load([
            'salonPermissions.permission',
            'salon',
        ]);
    }


    public function registerMessage(
        $userSalonOnwer,
        $salon,
        $lang // 'ar','en'
    ) {

        $ios_link_app = Setting::where('key', 'ios_app_url')->first()->value;
        $android_link_app = Setting::where('key', 'android_app_url')->first()->value;

        $reportMessage = '';
        
        // Get provider type text based on type
        $providerTypeAr = match($salon->type) {
            'salon' => 'صالون',
            'home_service' => 'خدمة منزلية',
            'beautician' => 'خبيرة تجميل',
            'clinic' => 'عيادة',
            default => 'صالون'
        };
        
        $providerTypeEn = match($salon->type) {
            'salon' => 'salon',
            'home_service' => 'home service',
            'beautician' => 'makeup artist',
            'clinic' => 'clinic',
            default => 'salon'
        };

        if ($lang === 'ar') {
            $reportMessage .= "عزيزي المتعامل " . ($userSalonOnwer->first_name ?? '') . " " . ($userSalonOnwer->last_name ?? '') . "،\n";
            $reportMessage .= "تم تسجيل (" . $providerTypeAr . ") جديد باسم (" . ($salon->merchant_commercial_name ?? '') . ") على منصة GlintUp بتاريخ (" . Carbon::now()->format('Y-m-d') . ") بنجاح.\n";
            $reportMessage .= "يُعتبر هذا إشعاراً رسمياً بتأكيد العملية، يمكنك الان الدخول إلى حسابك باستخدام بيانات التسجيل.\n";
            $reportMessage .= "للاطلاع على تفاصيل التسجيل يرجى الدخول على https://glintup.ae\n";
            $reportMessage .= "للاستفسار والتواصل: 0557380080\n";
            $reportMessage .= "للدعم البريد الالكتروني: Contact@glintup.ae\n";
            $reportMessage .= "حمل تطبيق GlintUp للاستمتاع بخدماتنا\n\n";
            $reportMessage .= "للتحميل على الايفون: " . $ios_link_app . "\n";
            $reportMessage .= "للتحميل على الاندرويد: " . $android_link_app . "\n";
        } else {
            $reportMessage .= "Dear Customer, " . ($userSalonOnwer->first_name ?? '') . " " . ($userSalonOnwer->last_name ?? '') . ",\n\n";
            $reportMessage .= "A new " . $providerTypeEn . " named " . ($salon->merchant_commercial_name ?? '') . " has been successfully registered on the GlintUp platform on " . Carbon::now()->format('Y-m-d') . ".\n";
            $reportMessage .= "This is an official confirmation of the registration. You can now log in to your account using your registration details.\n\n";
            $reportMessage .= "To view the registration details, please visit: https://glintup.ae\n";
            $reportMessage .= "For inquiries and support: 0557380080\n";
            $reportMessage .= "Support Email: Contact@glintup.ae\n\n";
            $reportMessage .= "Download the GlintUp app now to enjoy our services!\n\n";
            $reportMessage .= "iOS: " . $ios_link_app . "\n";
            $reportMessage .= "Android: " . $android_link_app . "\n";
        }

        return $reportMessage;
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

        $verifyCode = rand(1000, 9999);
        $codeExpiry = Carbon::now()->addMinutes(10);

        $user->update([
            'otp' => $verifyCode,
            'otp_expire_at' => $codeExpiry,
        ]);


        // $subject = "Reset Your Password - InstaHandi";
        // $formattedCode = "<span style='font-size: 24px; font-weight: bold;'>$verifyCode</span>";
        // $content = "Hello,<br><br>We received a request to reset your password for your InstaHandi account. Use the code below to proceed:<br><br>🔢 Your password reset code: $formattedCode<br><br>This code is valid for 10 minutes. If you didn't request this, please ignore this email, and your password will remain unchanged.<br><br>If you need help, feel free to contact our support team.<br><br>Best,<br>InstaHandi Team";

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
