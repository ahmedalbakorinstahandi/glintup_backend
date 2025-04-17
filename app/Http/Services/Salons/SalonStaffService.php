<?php

namespace App\Http\Services\Salons;

use App\Http\Permissions\Salons\SalonStaffPermission;
use App\Models\Salons\SalonStaff;
use App\Models\Users\User;
use App\Models\Salons\UserSalonPermission;
use App\Services\FilterService;
use App\Services\MessageService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SalonStaffService
{
    public function index($data)
    {
        $query = SalonStaff::with(['user.salonPermissions.permission', 'salon']);


        $query = SalonStaffPermission::filterIndex($query);

        return FilterService::applyFilters(
            $query,
            $data,
            [],
            [],
            ['created_at'],
            ['salon_id', 'user_id'],
            ['id']
        );
    }

    public function show($id)
    {
        $item = SalonStaff::with(['user.salonPermissions.permission', 'salon'])->find($id);
        if (!$item) {
            MessageService::abort(404, 'messages.salon_staff.item_not_found');
        }
        return $item;
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone_code' => $data['phone_code'],
                'phone' => $data['phone'],
                'gender' => $data['gender'],
                'birth_date' => $data['birth_date'],
                'password' => Hash::make($data['password']),
                'role' => 'staff',
                'is_active' => $data['is_active'] ?? true,
            ]);

            $staff = SalonStaff::create([
                'user_id' => $user->id,
                'salon_id' => $data['salon_id'],
                'position' => $data['position'],
                'is_active' => $data['is_active'] ?? true,
            ]);

            if (isset($data['permissions'])) {
                foreach ($data['permissions'] as $permission_id) {
                    UserSalonPermission::create([
                        'user_id' => $user->id,
                        'permission_id' => $permission_id,
                        'salon_id' => $data['salon_id'],
                    ]);
                }
            }

            return $staff->load(['user.salonPermissions.permission', 'salon']);
        });
    }

    public function update(SalonStaff $staff, array $data)
    {
        $staff->update([
            'position' => $data['position'] ?? $staff->position,
            'is_active' => $data['is_active'] ?? $staff->is_active,
        ]);

        if (isset($data['user'])) {

            if (isset($data['user']['password'])) {
                $data['user']['password'] = Hash::make($data['user']['password']);
            }

            $staff->user->update(
                [
                    'first_name' => $data['user']['first_name'] ?? $staff->user->first_name,
                    'last_name' => $data['user']['last_name'] ?? $staff->user->last_name,
                    'gender' => $data['user']['gender'] ?? $staff->user->gender,
                    'password' => $data['user']['password'] ?? $staff->user->password,
                ]
            );
        }

        return $staff->refresh()->load(['user.salonPermissions.permission', 'salon']);
    }

    public function destroy(SalonStaff $staff)
    {
        $staff->user->salonPermissions()->delete();
        $staff->delete();
        $staff->user->delete();
        return true;
    }

    public function updatePermissions(SalonStaff $staff, array $data)
    {
        UserSalonPermission::where('user_id', $staff->user_id)->where('salon_id', $staff->salon_id)->delete();

        foreach ($data['permissions'] as $permission_id) {
            UserSalonPermission::create([
                'user_id' => $staff->user_id,
                'permission_id' => $permission_id,
                'salon_id' => $staff->salon_id,
            ]);
        }
    }
}
