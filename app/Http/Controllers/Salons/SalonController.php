<?php

namespace App\Http\Controllers\Salons;

use App\Http\Controllers\Controller;
use App\Http\Resources\Salons\SalonPermissionResource;
use App\Http\Services\Salons\SalonService;
use App\Models\Salons\SalonPermission;
use Illuminate\Http\Request;

class SalonController extends Controller
{
    protected $salonService;

    public function __construct(SalonService $salonService)

    {
        $this->salonService = $salonService;
    }

    public function getPermissions()
    {
        $permissions = $this->salonService->getPermissions();

        return response()->json([
            'success' => true,
            'data' => SalonPermissionResource::collection($permissions),
        ]);
    }
}
