<?php

namespace App\Http\Services\Salons;

use App\Http\Permissions\Salons\SalonPermission;
use App\Http\Resources\Services\GroupResource;
use App\Models\Salons\Salon;
use App\Models\Salons\SalonPermission as SalonPermissionModel;
use App\Models\Salons\UserSalonPermission;
use App\Models\Services\Group;
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

        $permissions = SalonPermissionModel::whereIn('id', $userPermissions->pluck('permission_id'))->get();

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

        $searchFields = ['name', 'email', 'description', 'location', 'city', 'country'];
        $numericFields = [];
        $dateFields = ['created_at'];
        $exactMatchFields = ['is_active', 'is_approved', 'type', 'city', 'country'];
        $inFields = ['id', 'type'];

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


        $salon->load(['socialMediaSites', 'images', 'workingHours', 'owner', 'latestReviews']);

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
