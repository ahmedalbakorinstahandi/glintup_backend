<?php

namespace App\Http\Controllers\Rewards;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rewards\GiftCard\CreateRequest;
use App\Http\Requests\Rewards\GiftCard\UpdateRequest;
use App\Http\Permissions\Rewards\GiftCardPermission;
use App\Http\Requests\Rewards\GiftCard\CreateByUserRequest;
use App\Http\Services\Rewards\GiftCardService;
use App\Http\Resources\Rewards\GiftCardResource;
use App\Models\Users\User;
use App\Services\ResponseService;

class GiftCardController extends Controller
{
    protected $giftCardService;

    public function __construct(GiftCardService $giftCardService)
    {
        $this->giftCardService = $giftCardService;
    }

    public function index()
    {
        $items = $this->giftCardService->index(request()->all());

        // $user = User::auth();


        return response()->json([
            'success' => true,
            'info' =>  $items['info'],
            'data' => GiftCardResource::collection($items['data']->items()),
            'meta' => ResponseService::meta($items['data']),
        ]);
    }

    public function show($id)
    {
        $item = $this->giftCardService->show($id);
        GiftCardPermission::canShow($item);
        return response()->json([
            'success' => true,
            'data' => new GiftCardResource($item),
        ]);
    }

    public function create(CreateRequest $request)
    {
        $data = GiftCardPermission::create($request->validated());
        $item = $this->giftCardService->create($data);
        return response()->json([
            'success' => true,
            'message' => trans('messages.gift_card.item_created_successfully'),
            'data' => new GiftCardResource($item),
        ]);
    }

    public function update($id, UpdateRequest $request)
    {
        $item = $this->giftCardService->show($id);
        GiftCardPermission::canUpdate($item, $request->validated());
        $item = $this->giftCardService->update($item, $request->validated());
        return response()->json([
            'success' => true,
            'message' => trans('messages.gift_card.item_updated_successfully'),
            'data' => new GiftCardResource($item),
        ]);
    }

    public function destroy($id)
    {
        $item = $this->giftCardService->show($id);
        GiftCardPermission::canDelete($item);
        $deleted = $this->giftCardService->destroy($item);
        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? trans('messages.gift_card.item_deleted_successfully')
                : trans('messages.gift_card.failed_delete_item'),
        ]);
    }



    // create gift card by user 
    public function createByUser(CreateByUserRequest $request)
    {
        $gift = $this->giftCardService->createByUser($request->validated());

        return response()->json(
            [
                'success' => true,
                'data' => new GiftCardResource($gift),
            ]
        );
    }

    //$receive
    public function receive($id)
    {
        $giftCard = $this->giftCardService->show($id);


        $giftCard = $this->giftCardService->receive($giftCard);

        return response()->json(
            [
                'success' => true,
                'data' => new GiftCardResource($giftCard),
                'message' => trans('messages.gift_card.item_received_successfully'),
            ]
        );
    }
}
