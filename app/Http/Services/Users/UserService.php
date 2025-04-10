<?php


namespace App\Http\Services\Users;

use App\Models\Users\User;
use App\Services\FilterService;
use App\Services\MessageService;
use App\Http\Permissions\Users\UserPermission;

class UserService
{
    public function index($data)
    {
        $query = User::query();

        $query = UserPermission::filterIndex($query);

        $data['search'] =  str_replace(' ', '', $data['search']);

        if (isset($data['search']) && $data['search'] != '') {
            $query->whereRaw("CONCAT(phone_code, phone) LIKE ?", ['%' . $data['search'] . '%']);
        }

        return FilterService::applyFilters(
            $query,
            $data,
            [],
            [],
            ['created_at'],
            ['role', 'is_active'],
            ['id']
        );
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            MessageService::abort(404, 'messages.user.item_not_found');
        }

        return $user;
    }

    public function create($validatedData)
    {
        return User::create($validatedData);
    }

    public function update($user, $validatedData)
    {
        $user->update($validatedData);
        return $user;
    }

    public function destroy($user)
    {
        return $user->delete();
    }
}
