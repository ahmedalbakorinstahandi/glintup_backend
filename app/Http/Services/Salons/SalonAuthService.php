<?php

namespace App\Http\Services\Salons;

use App\Models\Salons\Salon;
use App\Models\Salons\SalonPermission;
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

            'service_location' => $requestData['service_location'],
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

        $reportMessage = '';


        if ($lang === 'ar') {
            $reportMessage .= "🎉 تم تسجيل صالون جديد على منصة GlintUp! 🎉\n\n";
            $reportMessage .= "🕒 تاريخ ووقت التسجيل: " . Carbon::now()->format('Y-m-d H:i:s') . "\n\n";
            $reportMessage .= "مرحباً " . ($userSalonOnwer->first_name ?? '') . " " . ($userSalonOnwer->last_name ?? '') . "،\n\n";
            $reportMessage .= "لقد تم تسجيل صالون جديد باستخدام بياناتك على منصة GlintUp.\n";
            $reportMessage .= "إذا كنت أنت من قام بالتسجيل، فهذا تأكيد على نجاح العملية.\n";
            $reportMessage .= "أما إذا لم تقم بذلك، يرجى التواصل مع فريق الدعم فوراً لاتخاذ الإجراءات اللازمة.\n\n";

            $reportMessage .= "🔹 بيانات المالك:\n";
            $reportMessage .= "الاسم: " . ($userSalonOnwer->first_name ?? '') . " " . ($userSalonOnwer->last_name ?? '') . "\n";
            $reportMessage .= "رقم الجوال: +" . $userSalonOnwer->phone_code . $userSalonOnwer->phone . "\n";
            $reportMessage .= "البريد الإلكتروني: " . ($userSalonOnwer->email ?? '---') . "\n";

            $reportMessage .= "🔹 بيانات الصالون:\n";
            $reportMessage .= "الاسم القانوني: " . ($salon->merchant_legal_name ?? '') . "\n";
            $reportMessage .= "الاسم التجاري: " . ($salon->merchant_commercial_name ?? '') . "\n";
            $reportMessage .= "العنوان: " . ($salon->address ?? '') . "\n";
            $reportMessage .= "الشارع/المدينة: " . ($salon->city_street_name ?? '') . "\n";
            $reportMessage .= "الوصف: " . ($salon->description ?? '') . "\n";
            $reportMessage .= "نوع المزود: " . ($salon->type ?? '') . "\n";
            $reportMessage .= "نبذة: " . ($salon->bio ?? '') . "\n";
            if ($salon->latitude && $salon->longitude) {
                $googleMapsUrl = "https://maps.google.com/?q={$salon->latitude},{$salon->longitude}";
                $reportMessage .= "📍 الموقع على الخريطة: $googleMapsUrl\n\n";
            }

            $reportMessage .= "🔹 بيانات التواصل:\n";
            $reportMessage .= "اسم جهة الاتصال: " . ($salon->contact_name ?? '') . "\n";
            $reportMessage .= "رقم جهة الاتصال: " . ($salon->contact_number ?? '') . "\n";
            $reportMessage .= "بريد جهة الاتصال: " . ($salon->contact_email ?? '') . "\n";
            $reportMessage .= "اسم مسؤول الأعمال: " . ($salon->business_contact_name ?? '') . "\n";
            $reportMessage .= "بريد مسؤول الأعمال: " . ($salon->business_contact_email ?? '') . "\n";
            $reportMessage .= "رقم مسؤول الأعمال: " . ($salon->business_contact_number ?? '') . "\n\n";

            $reportMessage .= "🔗 رابط المنصة: https://glintup.ae\n";
            $reportMessage .= "ℹ️ منصة GlintUp: منصة متخصصة لإدارة وحجز مواعيد الصالونات بسهولة واحترافية.\n";
            $reportMessage .= "-----------------------------\n";
        } else {
            $reportMessage .= "🎉 A new salon has been registered on GlintUp! 🎉\n\n";
            $reportMessage .= "🕒 Registration Date & Time: " . Carbon::now()->format('Y-m-d H:i:s') . "\n\n";
            $reportMessage .= "Hello " . ($userSalonOnwer->first_name ?? '') . " " . ($userSalonOnwer->last_name ?? '') . ",\n\n";
            $reportMessage .= "A new salon has been registered using your information on GlintUp.\n";
            $reportMessage .= "If you initiated this registration, this is a confirmation of success.\n";
            $reportMessage .= "If you did not, please contact support immediately to take necessary action.\n\n";

            $reportMessage .= "🔹 Owner Details:\n";
            $reportMessage .= "Name: " . ($userSalonOnwer->first_name ?? '') . " " . ($userSalonOnwer->last_name ?? '') . "\n";
            $reportMessage .= "Mobile: +" . $userSalonOnwer->phone_code . $userSalonOnwer->phone . "\n";
            $reportMessage .= "Email: " . ($userSalonOnwer->email ?? '---') . "\n";

            $reportMessage .= "🔹 Salon Details:\n";
            $reportMessage .= "Legal Name: " . ($salon->merchant_legal_name ?? '') . "\n";
            $reportMessage .= "Commercial Name: " . ($salon->merchant_commercial_name ?? '') . "\n";
            $reportMessage .= "Address: " . ($salon->address ?? '') . "\n";
            $reportMessage .= "City/Street: " . ($salon->city_street_name ?? '') . "\n";
            $reportMessage .= "Description: " . ($salon->description ?? '') . "\n";
            $reportMessage .= "Provider Type: " . ($salon->type ?? '') . "\n";
            $reportMessage .= "Bio: " . ($salon->bio ?? '') . "\n";
            if ($salon->latitude && $salon->longitude) {
                $googleMapsUrl = "https://maps.google.com/?q={$salon->latitude},{$salon->longitude}";
                $reportMessage .= "📍 Location on map: $googleMapsUrl\n\n";
            }

            $reportMessage .= "🔹 Contact Details:\n";
            $reportMessage .= "Contact Name: " . ($salon->contact_name ?? '') . "\n";
            $reportMessage .= "Contact Number: " . ($salon->contact_number ?? '') . "\n";
            $reportMessage .= "Contact Email: " . ($salon->contact_email ?? '') . "\n";
            $reportMessage .= "Business Manager Name: " . ($salon->business_contact_name ?? '') . "\n";
            $reportMessage .= "Business Manager Email: " . ($salon->business_contact_email ?? '') . "\n";
            $reportMessage .= "Business Manager Number: " . ($salon->business_contact_number ?? '') . "\n\n";

            $reportMessage .= "🔗 Platform Link: https://glintup.ae\n";
            $reportMessage .= "ℹ️ GlintUp: A specialized platform for managing and booking salon appointments easily and professionally.\n";
            $reportMessage .= "-----------------------------\n";
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
