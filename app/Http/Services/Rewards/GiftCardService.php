<?php

namespace App\Http\Services\Rewards;

use App\Models\Rewards\GiftCard;
use App\Services\FilterService;
use App\Services\MessageService;
use App\Http\Permissions\Rewards\GiftCardPermission;

class GiftCardService
{
    public function index($data)
    {
        $query = GiftCard::query()->with(['sender', 'recipient']);


        $query = GiftCardPermission::filterIndex($query);
        
        
        return FilterService::applyFilters(
            $query,
            $data,
            ['code', 'message'],
            [],
            ['created_at'],
            ['type', 'is_used'],
            ['id']
        );
    }

    public function show($id)
    {
        $item = GiftCard::with(['sender', 'recipient'])->find($id);
        if (!$item) {
            MessageService::abort(404, 'messages.gift_card.item_not_found');
        }
        return $item;
    }

    public function create($data)
    {
        return GiftCard::create($data);
    }

    public function update($item, $data)
    {
        $item->update($data);
        return $item;
    }

    public function destroy($item)
    {
        return $item->delete();
    }
}
