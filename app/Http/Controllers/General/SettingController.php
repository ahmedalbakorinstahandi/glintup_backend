<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Http\Requests\General\Settings\UpdateRequest;
use App\Http\Resources\General\SettingResource;
use App\Http\Services\General\SettingService;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected $settingService;
    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }


    public function index()
    {
        $settings = $this->settingService->index(request()->all());

        return response()->json([
            'success' => true,
            'data' => SettingResource::collection($settings->items()),
            'meta' => ResponseService::meta($settings),
        ]);
    }


    public function show($id)
    {
        $setting = $this->settingService->show($id);

        return response()->json([
            'success' => true,
            'data' => new SettingResource($setting),
        ]);
    }

    // updateSettings
    public function updateSettings(UpdateRequest $request)
    {
        $this->settingService->updateSettings($request->validated()['settings']);



        return response()->json([
            'success' => true,
            'data' => [],
            'message' => trans('messages.setting.item_updated_successfully'),
        ]);
    }

    // public function show($id)
    // {
    //     $item = $this->settingService->show($id);
    //      return response()->json([
    //         'success' => true,
    //         'data' => new SettingResource($item),
    //     ]);
    // }

    // public function create(CreateRequest $request)
    // {
    //     $data = SettingPermission::create($request->validated());
    //     $item = $this->settingService->create($data);
    //     return response()->json([
    //         'success' => true,
    //         'message' => trans('messages.setting.item_created_successfully'),
    //         'data' => new SettingResource($item),
    //     ]);
    // }

    // public function update($id, UpdateRequest $request)
    // {
    //     $item = $this->settingService->show($id);
    //     SettingPermission::canUpdate($item, $request->validated());
    //     $item = $this->settingService->update($item, $request->validated());
    //     return response()->json([
    //         'success' => true,
    //         'message' => trans('messages.setting.item_updated_successfully'),
    //         'data' => new SettingResource($item),
    //     ]);
    // }

    // public function destroy($id)
    // {
    //     $item = $this->settingService->show($id);
    //     SettingPermission::canDelete($item);
    //     $deleted = $this->settingService->destroy($item);
    //     return response()->json([
    //         'success' => $deleted,
    //         'message' => $deleted
    //             ? trans('messages.setting.item_deleted_successfully')
    //             : trans('messages.setting.failed_delete_item'),
    //     ]);
    // }
}
