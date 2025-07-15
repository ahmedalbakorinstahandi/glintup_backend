<?php

namespace App\Http\Services\Admins;

use App\Http\Notifications\AdminPermissionNotification;
use App\Models\Users\User;
use App\Models\Admins\{AdminPermission, UserAdminPermission};
use App\Services\{FilterService, MessageService, PhoneService};
use Illuminate\Support\Facades\{DB, Hash};

class AdminUserService
{
    public function index($data)
    {
        $query = User::where('role', 'admin')->with(['adminPermissions']);

        return FilterService::applyFilters(
            $query,
            $data,
            [],
            [],
            ['created_at'],
            ['id'],
            ['id']
        );
    }

    public function show($id)
    {
        $user = User::where('role', 'admin')->with(['adminPermissions'])->find($id);
        if (!$user) {
            MessageService::abort(404, 'messages.admin_users.item_not_found');
        }
        return $user;
    }

    public function create(array $data)
    {

        $phoneParts = PhoneService::parsePhoneParts($data['phone']);

        $data['phone_code'] = $phoneParts['country_code'];
        $data['phone'] = $phoneParts['national_number'];


        return DB::transaction(function () use ($data) {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'admin',
                'is_active' => $data['is_active'] ?? true,
                'birth_date' => '2000-01-01',
                'phone_code' => $data['phone_code'],
                'phone' => $data['phone'],
                'avatar' => $data['avatar'] ?? null,
            ]);

            if (isset($data['permissions'])) {
                foreach ($data['permissions'] as $permission_id) {
                    UserAdminPermission::create([
                        'user_id' => $user->id,
                        'permission_id' => $permission_id,
                    ]);
                }
            }



            return $user->load(['adminPermissions']);
        });
    }

    public function update(User $user, array $data)
    {
        $user->update([
            'first_name' => $data['first_name'] ?? $user->first_name,
            'last_name' => $data['last_name'] ?? $user->last_name,
            // 'email' => $data['email'] ?? $user->email,
            'is_active' => $data['is_active'] ?? $user->is_active,
            'password' => isset($data['password']) ? Hash::make($data['password']) : $user->password,
            'avatar' => $data['avatar'] ?? $user->avatar,
        ]);

        return $user->refresh()->load(['adminPermissions']);
    }

    public function destroy(User $user)
    {
        $user->adminPermissions()->delete();
        return $user->delete();
    }

    public function updatePermissions(User $user, array $data)
    {
        $newPermissions = collect($data['permissions'])->unique()->values()->all();

        $existing = UserAdminPermission::where('user_id', $user->id)->get();

        foreach ($existing as $permission) {
            if (!in_array($permission->permission_id, $newPermissions)) {
                $permission->delete();
            }
        }

        foreach ($newPermissions as $permissionId) {
            $exists = $existing->firstWhere('permission_id', $permissionId);
            if (!$exists) {
                UserAdminPermission::create([
                    'user_id' => $user->id,
                    'permission_id' => $permissionId,
                ]);
            }
        }

        AdminPermissionNotification::updateEmployeePermission($user);
    }
}
