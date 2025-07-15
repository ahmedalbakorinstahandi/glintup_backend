<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Admins\AdminPermissionSeeder;
use Database\Seeders\General\SettingSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // run SalonPermissionSeeder
        // $this->call(Salons\SalonSeeder::class);
        // $this->call(Salons\SalonStaffSeeder::class);
        // $this->call(Users\AdminSeeder::class);
        // $this->call(Salons\SalonPermissionSeeder::class);
        // $this->call(SettingSeeder::class);
        $this->call(AdminPermissionSeeder::class);
    }
}
