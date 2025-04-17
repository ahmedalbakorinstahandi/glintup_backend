<?php

namespace App\Http\Services\Salons;

use App\Models\Salons\SalonPermission;
use App\Services\FilterService;

class SalonPermissionService
{
    public static function index($data)
    {
        $query = SalonPermission::query();

        $data['limit'] = $data['limit'] ?? 100;
        $data['sort_order'] = 'asc';

        return  FilterService::applyFilters(
            $query,
            $data,
            [],
            [],
            [],
            [],
            ['id']
        );
    }
}
