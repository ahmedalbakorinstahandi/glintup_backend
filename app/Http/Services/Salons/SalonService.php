<?php

namespace App\Http\Services\Salons;

use App\Models\Salons\Salon;
use App\Models\Salons\SalonPermission;
use App\Models\Salons\UserSalonPermission;
use App\Models\Users\User;
use App\Services\FilterService;
use App\Services\MessageService;

class SalonService
{
    public function getPermissions()
    {
        $user = User::auth();

        $userPermissions = UserSalonPermission::where('user_id', $user->id)
            ->with('permission')
            ->get();

        $permissions = SalonPermission::whereIn('id', $userPermissions->pluck('permission_id'))->get();

        return $permissions;
    }

    //getSalonData
    public function getSalonData()
    {
        $user = User::auth();

        $salon = $user->salon;

        return $salon;
    }

    public function index($data)
    {
        $query = Salon::query();

        $searchFields = ['name', 'email', 'description', 'location'];
        $numericFields = [];
        $dateFields = ['created_at'];
        $exactMatchFields = ['is_active', 'is_approved', 'type', 'city', 'country'];
        $inFields = ['id'];

        $query = SalonPermission::filterIndex($query);

        return FilterService::applyFilters(
            $query,
            $data,
            $searchFields,
            $numericFields,
            $dateFields,
            $exactMatchFields,
            $inFields
        );
    }

    public function show($id)
    {
        $salon = Salon::find($id);

        if (!$salon) {
            MessageService::abort(404, 'messages.salon.item_not_found');
        }

        return $salon;
    }

    public function create($data)
    {
        $salon = Salon::create($data);

        return $salon;
    }

    public function update($salon, $data)
    {
        $salon->update($data);
        
        return $salon;
    }

    public function destroy($salon)
    {
        return $salon->delete();
    }
}
