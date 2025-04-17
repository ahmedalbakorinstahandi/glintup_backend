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


        if (isset($data['search']) && $data['search'] != '') {
            $data['search'] =  str_replace(' ', '', $data['search']);
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

        if (isset($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        }

        if (isset($validatedData['phone'])) {
            $validatedData['phone'] = str_replace(' ', '', $validatedData['phone']);
        }
        if (isset($validatedData['phone_code'])) {
            $validatedData['phone_code'] = str_replace(' ', '', $validatedData['phone_code']);
        }

        // role 
        $validatedData['role'] = 'customer';

        // added by admin
        $validatedData['added_by'] = 'admin';

        $user = User::create($validatedData);

        return $user;
    }

    public function update($user, $validatedData)
    {

        if (isset($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        }

        $user->update($validatedData);
        return $user;
    }

    public function destroy($user)
    {

        return $user->delete();
    }


    public function getProfile()
    {
        $user = User::auth();

        return $user;
    }

    public function updateProfile($validatedData)
    {

        $user = User::auth();

        if (isset($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        }

        // update location

        $user->update($validatedData);


        return $user;
    }
}
