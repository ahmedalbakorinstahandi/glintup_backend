<?php

namespace App\Http\Controllers\Salons;

use App\Http\Controllers\Controller;
use App\Http\Requests\Salons\SocialMediaSite\CreateRequest;
use App\Http\Requests\Salons\SocialMediaSite\UpdateRequest;
use App\Http\Permissions\Salons\SocialMediaSitePermission;
use App\Http\Services\Salons\SocialMediaSiteService;
use App\Http\Resources\Salons\SocialMediaSiteResource;
use App\Services\ResponseService;

class SocialMediaSiteController extends Controller
{
    protected $socialMediaSiteService;

    public function __construct(SocialMediaSiteService $socialMediaSiteService)
    {
        $this->socialMediaSiteService = $socialMediaSiteService;
    }

    public function index()
    {
        $data = $this->socialMediaSiteService->index(request()->all());

        return response()->json([
            'success' => true,
            'data' => SocialMediaSiteResource::collection($data->items()),
            'meta' => ResponseService::meta($data),
        ]);
    }

    public function show($id)
    {
        $data = $this->socialMediaSiteService->show($id);

        SocialMediaSitePermission::canShow($data);

        return response()->json([
            'success' => true,
            'data' => new SocialMediaSiteResource($data),
        ]);
    }

    public function create(CreateRequest $request)
    {
        $validated = SocialMediaSitePermission::create($request->validated());

        $data = $this->socialMediaSiteService->create($validated);

        return response()->json([
            'success' => true,
            'message' => trans('messages.social_media_site.item_created_successfully'),
            'data' => new SocialMediaSiteResource($data),
        ]);
    }

    public function update($id, UpdateRequest $request)
    {
        $data = $this->socialMediaSiteService->show($id);

        SocialMediaSitePermission::canUpdate($data, $request->validated());

        $data = $this->socialMediaSiteService->update($data, $request->validated());

        return response()->json([
            'success' => true,
            'message' => trans('messages.social_media_site.item_updated_successfully'),
            'data' => new SocialMediaSiteResource($data),
        ]);
    }

    public function destroy($id)
    {
        $data = $this->socialMediaSiteService->show($id);

        SocialMediaSitePermission::canDelete($data);

        $deleted = $this->socialMediaSiteService->destroy($data);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? trans('messages.social_media_site.item_deleted_successfully')
                : trans('messages.social_media_site.failed_delete_item'),
        ]);
    }
}
