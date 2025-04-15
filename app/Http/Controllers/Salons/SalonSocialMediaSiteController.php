<?php

namespace App\Http\Controllers\Salons;

use App\Http\Controllers\Controller;
use App\Http\Requests\Salons\SalonSocialMediaSite\CreateRequest;
use App\Http\Requests\Salons\SalonSocialMediaSite\UpdateRequest;
use App\Http\Permissions\Salons\SalonSocialMediaSitePermission;
use App\Http\Services\Salons\SalonSocialMediaSiteService;
use App\Http\Resources\Salons\SalonSocialMediaSiteResource;
use App\Services\ResponseService;

class SalonSocialMediaSiteController extends Controller
{
    protected $service;

    public function __construct(SalonSocialMediaSiteService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $items = $this->service->index(request()->all());

        return response()->json([
            'success' => true,
            'data' => SalonSocialMediaSiteResource::collection($items->items()),
            'meta' => ResponseService::meta($items),
        ]);
    }

    public function show($id)
    {
        $item = $this->service->show($id);

        SalonSocialMediaSitePermission::canShow($item);

        return response()->json([
            'success' => true,
            'data' => new SalonSocialMediaSiteResource($item),
        ]);
    }

    public function create(CreateRequest $request)
    {
        $data = SalonSocialMediaSitePermission::create($request->validated());

        $item = $this->service->create($data);

        return response()->json([
            'success' => true,
            'message' => trans('messages.salon_social_media_site.item_created_successfully'),
            'data' => new SalonSocialMediaSiteResource($item),
        ]);
    }

    public function update($id, UpdateRequest $request)
    {
        $item = $this->service->show($id);

        SalonSocialMediaSitePermission::canUpdate($item, $request->validated());

        $item = $this->service->update($item, $request->validated());

        return response()->json([
            'success' => true,
            'message' => trans('messages.salon_social_media_site.item_updated_successfully'),
            'data' => new SalonSocialMediaSiteResource($item),
        ]);
    }

    public function destroy($id)
    {
        $item = $this->service->show($id);

        SalonSocialMediaSitePermission::canDelete($item);

        $deleted = $this->service->destroy($item);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? trans('messages.salon_social_media_site.item_deleted_successfully')
                : trans('messages.salon_social_media_site.failed_delete_item'),
        ]);
    }
}
