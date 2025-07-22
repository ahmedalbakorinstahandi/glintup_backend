<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Http\Resources\General\ActivityLogResource;
use App\Http\Services\General\ActivityLogService;
use App\Services\PermissionHelper;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        PermissionHelper::checkAdminPermission('audit_log');
        $this->activityLogService = $activityLogService;
    }

    public function index(Request $request)
    {
        $data = $request->all();

        $logs = $this->activityLogService->index($data);

        return response()->json([
            'success' => true,
            'data' => ActivityLogResource::collection($logs->items()),
            'meta' => ResponseService::meta($logs),
        ]);
    }
}
