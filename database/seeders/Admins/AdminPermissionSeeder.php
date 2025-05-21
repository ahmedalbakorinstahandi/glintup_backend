<?php

namespace Database\Seeders\Admins;

use App\Models\Admins\AdminPermission;
use Illuminate\Database\Seeder;

class AdminPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['ar' => 'لوحة المعلومات', 'en' => 'Dashboard', 'key' => 'dashboard'],
            ['ar' => 'الصالونات', 'en' => 'Salons', 'key' => 'salons'],
            ['ar' => 'المستخدمين', 'en' => 'Users', 'key' => 'users'],
            ['ar' => 'المجموعات', 'en' => 'Groups', 'key' => 'groups'],
            ['ar' => 'بطاقات الهدايا', 'en' => 'Gift Cards', 'key' => 'gift_cards'],
            ['ar' => 'الحجوزات', 'en' => 'Appointments', 'key' => 'appointments'],
            ['ar' => 'المعاملات المالية', 'en' => 'Payments', 'key' => 'payments'],
            ['ar' => 'الإعلانات', 'en' => 'Ads', 'key' => 'ads'],
            ['ar' => 'التنبيهات', 'en' => 'Notifications', 'key' => 'notifications'],
            ['ar' => 'الشكاوى والدعم', 'en' => 'Support & Complaints', 'key' => 'support'],
            ['ar' => 'الإشعارات', 'en' => 'Alerts', 'key' => 'alerts'],
            ['ar' => 'الإعدادات', 'en' => 'Settings', 'key' => 'settings'],
        ];

        foreach ($permissions as $index => $item) {
            AdminPermission::updateOrCreate(
                ['key' => $item['key']],
                [
                    'name' => ['en' => $item['en'], 'ar' => $item['ar']],
                    'orders' => $index + 1,
                ]
            );
        }
    }
}
