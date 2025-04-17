<?php

namespace Database\Seeders\Salons;

use App\Models\Salons\SalonPermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SalonPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $SalonPermission = [
            [
                'name' => [
                    'en' => 'Dashboard',
                    'ar' => 'لوحة البيانات',
                ],
                'key' => 'dashboard',
            ],
            [
                'name' => [
                    'en' => 'Appointments',
                    'ar' => 'المواعيد',
                ],
                'key' => 'appointments',
            ],
            [
                'name' => [
                    'en' => 'Services',
                    'ar' => 'الخدمات',
                ],
                'key' => 'services',
            ],
            [
                'name' => [
                    'en' => 'Coupons',
                    'ar' => 'الكوبونات',
                ],
                'key' => 'coupons',
            ],
            [
                'name' => [
                    'en' => 'Working Hours',
                    'ar' => 'ساعات العمل',
                ],
                'key' => 'working_hours',
            ],
            [
                'name' => [
                    'en' => 'Gift Cards',
                    'ar' => 'بطاقات الهدايا',
                ],
                'key' => 'gift_cards',
            ],
            [
                'name' => [
                    'en' => 'Loyalty',
                    'ar' => 'الولاء',
                ],
                'key' => 'loyalty',
            ],
            [
                'name' => [
                    'en' => 'Ads',
                    'ar' => 'الإعلانات',
                ],
                'key' => 'ads',
            ],
            [
                'name' => [
                    'en' => 'Customers',
                    'ar' => 'العملاء',
                ],
                'key' => 'customers',
            ],
            [
                'name' => [
                    'en' => 'Payments',
                    'ar' => 'المدفوعات',
                ],
                'key' => 'payments',
            ],
            [
                'name' => [
                    'en' => 'Reviews',
                    'ar' => 'المراجعات',
                ],
                'key' => 'reviews',
            ],
            [
                'name' => [
                    'en' => 'Audit Log',
                    'ar' => 'سجل التدقيق',
                ],
                'key' => 'audit_log',
            ],
            [
                'name' => [
                    'en' => 'Settings',
                    'ar' => 'الإعدادات',
                ],
                'key' => 'settings',
            ],
            [
                'name' => [
                    'en' => "Staff",
                    "ar" => "الموظفين",
                ],
                "key" => "staff",
            ]
        ];

        foreach ($SalonPermission as $permission) {
            SalonPermission::updateOrCreate(
                ['key' => $permission['key']],
                ['name' => $permission['name']]
            );
        }
    }
}
