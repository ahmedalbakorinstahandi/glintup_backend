<?php

namespace App\Http\Controllers\Rewards;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rewards\GiftCard\CreateRequest;
use App\Http\Requests\Rewards\GiftCard\UpdateRequest;
use App\Http\Permissions\Rewards\GiftCardPermission;
use App\Http\Services\Rewards\GiftCardService;
use App\Http\Resources\Rewards\GiftCardResource;
use App\Services\ResponseService;

class GiftCardController extends Controller
{
    protected $service;

    public function __construct(GiftCardService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $items = $this->service->index(request()->all());

        
        return response()->json([
            'success' => true,
            'data' => GiftCardResource::collection($items->items()),
            'meta' => ResponseService::meta($items),
        ]);
    }

    public function show($id)
    {
        $item = $this->service->show($id);
        GiftCardPermission::canShow($item);
        return response()->json([
            'success' => true,
            'data' => new GiftCardResource($item),
        ]);
    }

    public function create(CreateRequest $request)
    {
        $data = GiftCardPermission::create($request->validated());
        $item = $this->service->create($data);
        return response()->json([
            'success' => true,
            'message' => trans('messages.gift_card.item_created_successfully'),
            'data' => new GiftCardResource($item),
        ]);
    }

    public function update($id, UpdateRequest $request)
    {
        $item = $this->service->show($id);
        GiftCardPermission::canUpdate($item, $request->validated());
        $item = $this->service->update($item, $request->validated());
        return response()->json([
            'success' => true,
            'message' => trans('messages.gift_card.item_updated_successfully'),
            'data' => new GiftCardResource($item),
        ]);
    }

    public function destroy($id)
    {
        $item = $this->service->show($id);
        GiftCardPermission::canDelete($item);
        $deleted = $this->service->destroy($item);
        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? trans('messages.gift_card.item_deleted_successfully')
                : trans('messages.gift_card.failed_delete_item'),
        ]);
    }
}