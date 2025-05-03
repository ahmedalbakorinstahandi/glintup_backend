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
