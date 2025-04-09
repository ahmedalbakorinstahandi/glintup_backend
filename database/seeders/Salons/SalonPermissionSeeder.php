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
                    'en' => 'Working Hours',
                    'ar' => 'ساعات العمل',
                ],
                'key' => 'working_hours',
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
                    'en' => 'Clients',
                    'ar' => 'العملاء',
                ],
                'key' => 'clients',
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
            SalonPermission::create([
                'name' => $permission['name'],
                'key' => $permission['key'],
            ]);
        }
    }
}
