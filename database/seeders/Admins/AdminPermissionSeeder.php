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
            ['ar' => 'المجموعات', 'en' => 'Groups', 'key' => 'services'],
            ['ar' => 'بطاقات الهدايا', 'en' => 'Gift Cards', 'key' => 'gift-cards'],
            ['ar' => 'الحجوزات', 'en' => 'Appointments', 'key' => 'appointments'],
            ['ar' => 'المعاملات المالية', 'en' => 'Payments', 'key' => 'payments'],
            ['ar' => 'الإعلانات', 'en' => 'Ads', 'key' => 'advertisements'],
            ['ar' => 'التنبيهات', 'en' => 'Notifications', 'key' => 'notifications'],
            ['ar' => 'الشكاوى والدعم', 'en' => 'Support & Complaints', 'key' => 'complaints'],
            ['ar' => 'الإشعارات', 'en' => 'Alerts', 'key' => 'alerts'],
            ['ar' => 'الإعدادات', 'en' => 'Settings', 'key' => 'settings'],
            ['ar' => 'إدارة الطاقم', 'en' => 'Admin Users', 'key' => 'admin-users'],
            ['ar' => 'المراجعات', 'en' => 'Reviews', 'key' => 'reviews'],
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
