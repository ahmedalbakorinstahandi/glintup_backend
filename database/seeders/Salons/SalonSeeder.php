<?php

namespace Database\Seeders\Salons;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // add 3 salons
        DB::table('salons')->insert([
            [
                'owner_id' => null,
                'name' => 'Glamour Salon',
                'icon' => 'glamour_icon.png',
                'phone_code' => '+971',
                'phone' => '34567890',
                'email' => 'contact@glamoursalon.com',
                'description' => 'A premium salon offering top-notch services.',
                'location' => '123 Main Street, City Center',
                'is_approved' => true,
                'is_active' => true,
                'type' => 'salon',
                'latitude' => 40.712776,
                'longitude' => -74.005974,
                'country' => 'UAE',
                'city' => 'Abu Dhabi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'owner_id' => null,
                'name' => 'Home Beauty Services',
                'icon' => 'home_beauty_icon.png',
                'phone_code' => '+44',
                'phone' => '9876543210',
                'email' => 'info@homebeauty.com',
                'description' => 'Bringing beauty services to your doorstep.',
                'location' => '456 Elm Street, Suburbia',
                'is_approved' => true,
                'is_active' => true,
                'type' => 'home_service',
                'latitude' => 51.507351,
                'longitude' => -0.127758,
                'country' => 'UAE',
                'city' => 'Abu Dhabi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'owner_id' => null,
                'name' => 'Elite Clinic',
                'icon' => 'elite_clinic_icon.png',
                'phone_code' => '+971',
                'phone' => '555123456',
                'email' => 'support@eliteclinic.com',
                'description' => 'Specialized beauty and wellness clinic.',
                'location' => '789 Palm Avenue, Downtown',
                'is_approved' => true,
                'is_active' => true,
                'type' => 'clinic',
                'latitude' => 25.276987,
                'longitude' => 55.296249,
                'country' => 'UAE',
                'city' => 'Abu Dhabi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
