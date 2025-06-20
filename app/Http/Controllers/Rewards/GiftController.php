<?php

namespace App\Http\Controllers\Rewards;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gift\CreateRequest;
use App\Http\Requests\Gift\UpdateRequest;
use App\Http\Resources\Rewards\GiftResource;
use App\Http\Services\Rewards\GiftService;
use App\Models\Rewards\Gift;
use Illuminate\Http\Request;

class GiftController extends Controller
{
    protected $giftService;

    public function __construct(GiftService $giftService)
    {
        $this->giftService = $giftService;
    }

    public function index()
    {
        $gifts = $this->giftService->index(request()->all());

        return response()->json(
            [
                'success' => true,
                'data' => GiftResource::collection($gifts),
            ]
        );
    }


    public function show($id)
    {
        $gift = $this->giftService->show($id);

        return response()->json(
            [
                'success' => true,
                'data' => new GiftResource($gift),
            ]
        );
    }

    public function create(CreateRequest $request)
    {
        $gift = $this->giftService->create($request->validated());

        return response()->json(
            [
                'success' => true,
                'message' => trans('messages.gift.item_created_successfully'),
                'data' => new GiftResource($gift),
            ]
        );
    }

    public function update(UpdateRequest $request, $id)
    {
        $gift = $this->giftService->show($id);

        $gift = $this->giftService->update($gift, $request->validated());

        return response()->json(
            [
                'success' => true,
                'message' => trans('messages.gift.item_updated_successfully'),
                'data' => new GiftResource($gift),
            ]
        );
    }

    public function destroy($id)
    {
        $gift = $this->giftService->show($id);

        $this->giftService->destroy($gift);

        return response()->json(
            [
                'success' => true,
                'message' => trans('messages.gift.item_deleted_successfully'),
            ]
        );
    }
}
