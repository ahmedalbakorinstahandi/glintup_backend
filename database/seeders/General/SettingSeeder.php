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
        // تكلفة الاعلان في اليوم
        // نسبة النظام من الهدايا
        // نسبة النظام من الحجوزات

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
                'key' => 'tax',
                'value' => null,
                'type' => 'float',
                'allow_null' => true,
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
