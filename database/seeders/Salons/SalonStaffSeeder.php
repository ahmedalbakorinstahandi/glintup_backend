<?php

namespace Database\Seeders\Salons;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Users\User;
use App\Models\Salons\SalonStaff;
use App\Models\Salons\UserSalonPermission;

class SalonStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // add 6 salon staff 
        // first  add 6 users as staff after that add 6 salon staff
        // add 6 salon staff to 3 salons 1,2,3

        $salonIds = [1, 2, 3];

        // Create 6 users as staff
        $staffUsers = [];
        $names = [
            ['first_name' => 'Alice', 'last_name' => 'Smith'],
            ['first_name' => 'Bob', 'last_name' => 'Johnson'],
            ['first_name' => 'Charlie', 'last_name' => 'Brown'],
            ['first_name' => 'Diana', 'last_name' => 'White'],
            ['first_name' => 'Ethan', 'last_name' => 'Green'],
            ['first_name' => 'Fiona', 'last_name' => 'Black'],
        ];

        foreach ($names as $index => $name) {
            $staffUsers[] = User::create([
                'first_name' => $name['first_name'],
                'last_name' => $name['last_name'],
                'gender' => $index % 2 == 0 ? 'male' : 'female',
                'birth_date' => now()->subYears(22 + $index)->toDateString(),
                'phone_code' => '+971',
                'phone' => '5551245' . $index,
                'password' => bcrypt('password'),
                'role' => 'staff',
                'is_active' => true,
                'is_verified' => true,
            ]);
        }


        // Position names for staff: Admin, Manager, Receptionist
        // Assign 6 staff users to 3 salons
        $positions = ['Admin', 'Manager', 'Receptionist'];

        foreach ($staffUsers as $index => $user) {
            SalonStaff::create([
                'salon_id' => $salonIds[$index % count($salonIds)],
                'user_id' => $user->id,
                'position' => $positions[$index % count($positions)],
                'is_active' => true,
            ]);
        }

        // Assign permissions to staff for their respective salons
        $permissions = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13];

        foreach ($staffUsers as $index => $user) {
            $position = $positions[$index % count($positions)];
            $userPermissions = [];

            if ($position === 'Admin') {
                $userPermissions = $permissions; // Admin gets all permissions
            } elseif ($position === 'Manager') {
                $userPermissions = array_diff($permissions, [9]); // Manager gets all except 9
            } elseif ($position === 'Receptionist') {
                $userPermissions = [2, 6, 7]; // Receptionist gets only 2, 6, 7
            }

            foreach ($userPermissions as $permissionId) {
                UserSalonPermission::create([
                    'user_id' => $user->id,
                    'permission_id' => $permissionId,
                    'salon_id' => $salonIds[$index % count($salonIds)],
                ]);
            }
        }
    }
}
