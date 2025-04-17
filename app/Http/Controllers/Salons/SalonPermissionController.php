<?php

namespace App\Http\Controllers\Salons;

use App\Http\Controllers\Controller;
use App\Http\Resources\Salons\SalonPermissionResource;
use App\Http\Services\Salons\SalonPermissionService;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class SalonPermissionController extends Controller
{
    protected $SalonPermissionService;

    public function __construct(SalonPermissionService $SalonPermissionService)
    {
        $this->SalonPermissionService = $SalonPermissionService;
    }

    public function index(Request $request)
    {
        $permissions = $this->SalonPermissionService->index($request->all());

        return response()->json([
            'success' => true,
            'data' => SalonPermissionResource::collection($permissions->items()),
            'meta' => ResponseService::meta($permissions),
        ]);
    }
}
