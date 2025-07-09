<?php

namespace Database\Seeders\Users;

use App\Models\Admins\AdminPermission;
use App\Models\Admins\UserAdminPermission;
use App\Models\Users\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'gender' => 'male',
            'birth_date' => '1990-01-01',
            'avatar' => null,
            'phone_code' => '+971',
            'phone' => '1234567890',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
            'latitude' => null,
            'longitude' => null,
            'otp' => null,
            'otp_expire_at' => null,
            'is_verified' => true,
            'language' => 'en',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // assign admin permission to admin
        $adminPermissions = AdminPermission::all();
        

        foreach ($adminPermissions as $permission) {
            UserAdminPermission::create([
                'user_id' => $admin->id,
                'permission_id' => $permission->id,
            ]);
        }

    }
}
