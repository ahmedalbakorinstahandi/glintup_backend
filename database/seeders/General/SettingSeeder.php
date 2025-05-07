<?php

namespace Database\Seeders\General;

use App\Models\General\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $settings = [
            [
                'key' => 'adver_cost_per_day',
                'value' => 100,
                'type' => 'float',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'system_percentage_gift',
                'value' => 12,
                'type' => 'float',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'system_percentage_booking',
                'value' => 4.5,
                'type' => 'float',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'salons_provider_percentage',
                'value' => 10,
                'type' => 'float',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'clinics_provider_percentage',
                'value' => 10,
                'type' => 'float',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'home_service_provider_percentage',
                'value' => 10,
                'type' => 'float',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'makeup_artists_provider_percentage',
                'value' => 10,
                'type' => 'float',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'tax',
                'value' => null,
                'type' => 'float',
                'allow_null' => true,
                'is_settings' => true,
            ],
            [
                'key' => 'app_name',
                'value' => 'Glintup',
                'type' => 'text',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'app_url',
                'value' => 'https://glintup.com',
                'type' => 'text',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'admin_email',
                'value' => 'admin@glintup.com',
                'type' => 'text',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'support_email',
                'value' => 'support@glintup.com',
                'type' => 'text',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'app_version',
                'value' => '1.2.0',
                'type' => 'text',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'min_supported_version',
                'value' => '1.0.0',
                'type' => 'text',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'android_app_url',
                'value' => 'https://play.google.com/store/apps/details?id=com.glintup',
                'type' => 'text',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'ios_app_url',
                'value' => 'https://apps.apple.com/app/glintup/id1234567890',
                'type' => 'text',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'help_ar',
                'value' => '<!DOCTYPE html>
            <html lang="ar" dir="rtl">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>مركز المساعدة</title>
                <link href="https://fonts.googleapis.com/css2?family=Sans+Arabic:wght@300;400;600&display=swap" rel="stylesheet">
                <style>
                    body {
                        font-family: \'Sans Arabic\', sans-serif;
                        background-color: transparent;
                        text-align: right;
                        margin: 0;
                        padding: 0;
                        color: #333;
                    }
                    .container {
                        max-width: 600px;
                        margin: 0 auto;
                        background: transparent;
                        padding: 0;
                        border-radius: 12px;
                        box-shadow: none;
                        line-height: 1.8;
                    }
                    h3 {
                        font-weight: 600;
                        margin-bottom: 15px;
                        color: #222;
                    }
                    p, li {
                        font-weight: 400;
                        font-size: 16px;
                        color: #555;
                    }
                    ul {
                        padding-right: 20px;
                    }
                    ul li {
                        margin-bottom: 10px;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <h3>كيف أقوم بإنشاء حساب جديد؟</h3>
                    <p>لإنشاء حساب جديد، يرجى اتباع الخطوات البسيطة التالية:</p>
                    <ul>
                        <li>افتح التطبيق وانتقل إلى شاشة تسجيل الدخول.</li>
                        <li>أسفل نموذج تسجيل الدخول، سترى خيار "تسجيل"، اضغط عليه.</li>
                        <li>سيُطلب منك إدخال رقم هاتفك، البريد الإلكتروني، واسمك الكامل.</li>
                        <li>بعد إدخال البيانات، اضغط على "تسجيل".</li>
                        <li>سيتم إرسال رمز تحقق إلى رقم هاتفك.</li>
                        <li>تحقق من الرمز الوارد في الرسائل وأدخله في التطبيق.</li>
                        <li>بمجرد التحقق، سيتم تسجيل دخولك تلقائيًا.</li>
                        <li>يمكنك إدخال بعض المعلومات الإضافية لاحقًا.</li>
                        <li>اضبط مستوى الأمان الخاص بحسابك.</li>
                        <li>لقد انتهيت الآن من إنشاء حسابك!</li>
                    </ul>
                    <p>إذا واجهت أي مشاكل أثناء عملية التسجيل، فلا تتردد في التواصل مع فريق الدعم لدينا للحصول على المساعدة.</p>
                </div>
            </body>
            </html>',
                'type' => 'html',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'help_en',
                'value' => '<!DOCTYPE html>
            <html lang="en" dir="ltr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Help Center</title>
                <link href="https://fonts.googleapis.com/css2?family=Sans+Arabic:wght@300;400;600&display=swap" rel="stylesheet">
                <style>
                    body {
                        font-family: \'Sans Arabic\', sans-serif;
                        background-color: transparent;
                        text-align: left;
                        margin: 0;
                        padding: 0;
                        color: #333;
                    }
                    .container {
                        max-width: 600px;
                        margin: 0 auto;
                        background: transparent;
                        padding: 0;
                        border-radius: 12px;
                        box-shadow: none;
                        line-height: 1.8;
                    }
                    h3 {
                        font-weight: 600;
                        margin-bottom: 15px;
                        color: #222;
                    }
                    p, li {
                        font-weight: 400;
                        font-size: 16px;
                        color: #555;
                    }
                    ul {
                        padding-left: 20px;
                    }
                    ul li {
                        margin-bottom: 10px;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <h3>How do I create a new account?</h3>
                    <p>To create a new account, please follow these simple steps:</p>
                    <ul>
                        <li>Open the app and go to the login screen.</li>
                        <li>Below the login form, you will see a "Sign Up" option, click on it.</li>
                        <li>You will be asked to enter your phone number, email, and full name.</li>
                        <li>After entering the details, click "Sign Up".</li>
                        <li>A verification code will be sent to your phone number.</li>
                        <li>Check your messages for the code and enter it in the app.</li>
                        <li>Once verified, you will be automatically logged in.</li>
                        <li>You can add additional personal information later.</li>
                        <li>Adjust the security level of your account.</li>
                        <li>You have now successfully created your account!</li>
                    </ul>
                    <p>If you face any issues during the registration process, do not hesitate to contact our support team for assistance.</p>
                </div>
            </body>
            </html>',
                'type' => 'html',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'terms_and_condition_en',
                'value' => '<!-- Terms and Conditions English Page -->
            <!DOCTYPE html>
            <html lang="en" dir="ltr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Terms and Conditions</title>
            </head>
            <body>
                <div class="container">
                    <p>
                        By using our application, you agree to the following terms and conditions:
                    </p>
                    <ul>
                        <li>You must be at least 18 years old to use this service.</li>
                        <li>You are responsible for maintaining the confidentiality of your account.</li>
                        <li>Unauthorized use of the application is prohibited.</li>
                        <li>We reserve the right to suspend accounts that violate our policies.</li>
                    </ul>
                </div>
            </body>
            </html>',
                'type' => 'html',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'terms_and_condition_ar',
                'value' => '<!-- Terms and Conditions Arabic Page -->
            <!DOCTYPE html>
            <html lang="ar" dir="rtl">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>الشروط والأحكام</title>
            </head>
            <body>
                <div class="container">
                    <p>
                        باستخدامك لتطبيقنا، فإنك توافق على الشروط والأحكام التالية:
                    </p>
                    <ul>
                        <li>يجب أن يكون عمرك 18 عامًا على الأقل لاستخدام هذه الخدمة.</li>
                        <li>أنت مسؤول عن الحفاظ على سرية حسابك.</li>
                        <li>يحظر الاستخدام غير المصرح به للتطبيق.</li>
                        <li>نحتفظ بالحق في تعليق الحسابات التي تنتهك سياساتنا.</li>
                    </ul>
                </div>
            </body>
            </html>',
                'type' => 'html',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'privacy_policy_en',
                'value' => '<!-- Privacy Policy English Page -->
            <!DOCTYPE html>
            <html lang="en" dir="ltr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Privacy Policy</title>
            </head>
            <body>
                <div class="container">
                    <p>
                        By using our application, you agree to the following privacy policies:
                    </p>
                    <ul>
                        <li>We collect personal data such as name, email, and phone number for account management.</li>
                        <li>Your data is securely stored and not shared with third parties without your consent.</li>
                        <li>You have the right to request access to, modify, or delete your personal data.</li>
                    </ul>
                </div>
            </body>
            </html>',
                'type' => 'html',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'privacy_policy_ar',
                'value' => '<!DOCTYPE html>
            <html lang="ar" dir="rtl">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>سياسة الخصوصية</title>
                <link href="https://fonts.googleapis.com/css2?family=Sans+Arabic:wght@300;400;600&display=swap" rel="stylesheet">
                <style>
                    body {
                        font-family: \'Sans Arabic\', sans-serif;
                        background-color: transparent;
                        text-align: right;
                        margin: 0;
                        padding: 0;
                        color: #333;
                    }
                    .container {
                        max-width: 600px;
                        margin: 0 auto;
                        background: transparent;
                        padding: 0;
                        border-radius: 12px;
                        box-shadow: none;
                        line-height: 1.8;
                    }
                    h3 {
                        font-weight: 600;
                        margin-bottom: 15px;
                        color: #222;
                    }
                    p, li {
                        font-weight: 400;
                        font-size: 16px;
                        color: #555;
                    }
                    ul {
                        padding-right: 20px;
                    }
                    ul li {
                        margin-bottom: 10px;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <p>نحن ملتزمون بحماية خصوصيتك. توضح سياسة الخصوصية هذه كيفية جمع بياناتك الشخصية واستخدامها وحمايتها.</p>
                    <ul>
                        <li>نقوم بجمع بيانات شخصية مثل الاسم، البريد الإلكتروني، ورقم الهاتف لإدارة الحساب.</li>
                        <li>يتم تخزين بياناتك بشكل آمن ولا تتم مشاركتها مع جهات خارجية دون موافقتك.</li>
                        <li>لديك الحق في طلب الوصول إلى بياناتك الشخصية أو تعديلها أو حذفها.</li>
                    </ul>
                </div>
            </body>
            </html>',
                'type' => 'html',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'about_app_en',
                'value' => '<!-- About App English Page -->
            <!DOCTYPE html>
            <html lang="en" dir="ltr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>About the App</title>
            </head>
            <body>
                <div class="container">
                    <p>
                        Our application provides a seamless experience for users to manage their accounts, access services, and stay connected efficiently.
                    </p>
                    <ul>
                        <li>Easy account registration and management.</li>
                        <li>Secure data storage and privacy protection.</li>
                        <li>User-friendly interface for smooth navigation.</li>
                        <li>Regular updates to enhance performance and security.</li>
                    </ul>
                </div>
            </body>
            </html>',
                'type' => 'html',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'about_app_ar',
                'value' => '<!-- About App Arabic Page -->
            <!DOCTYPE html>
            <html lang="ar" dir="rtl">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>عن التطبيق</title>
            </head>
            <body>
                <div class="container">
                    <p>
                        يوفر تطبيقنا تجربة سلسة للمستخدمين لإدارة حساباتهم والوصول إلى الخدمات والبقاء على اتصال بكفاءة.
                    </p>
                    <ul>
                        <li>تسجيل الحساب وإدارته بسهولة.</li>
                        <li>تخزين البيانات بشكل آمن وحماية الخصوصية.</li>
                        <li>واجهة مستخدم سهلة الاستخدام لتجربة سلسة.</li>
                        <li>تحديثات منتظمة لتعزيز الأداء والأمان.</li>
                    </ul>
                </div>
            </body>
            </html>',
                'type' => 'html',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'email',
                'value' => 'masbar@gmail.com',
                'type' => 'text',
                'allow_null' => false,
                'is_settings' => true,
            ],
            [
                'key' => 'phone',
                'value' => '+9713852711',
                'type' => 'text',
                'allow_null' => false,
                'is_settings' => true,
            ],

        ];



        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'allow_null' => $setting['allow_null'],
                    'is_settings' => $setting['is_settings'],
                ]
            );
        }
    }
}
