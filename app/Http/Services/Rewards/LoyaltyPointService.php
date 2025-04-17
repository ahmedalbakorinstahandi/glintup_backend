<?php

namespace App\Http\Services\Rewards;

use App\Models\Rewards\LoyaltyPoint;
use App\Services\FilterService;
use App\Services\MessageService;
use App\Http\Permissions\Rewards\LoyaltyPointPermission;

class LoyaltyPointService
{
    public function index($data)
    {
        $query = LoyaltyPoint::with(['user', 'salon']);
        $query = LoyaltyPointPermission::filterIndex($query);
        return FilterService::applyFilters(
            $query,
            $data,
            ['description'],
            ['points'],
            ['taken_at', 'used_at'],
            ['user_id', 'salon_id'],
            ['id']
        );
    }

    public function show($id)
    {
        $item = LoyaltyPoint::with(['user', 'salon'])->find($id);
        if (!$item) {
            MessageService::abort(404, 'messages.loyalty_point.item_not_found');
        }
        return $item;
    }

    public function create($data)
    {
        return LoyaltyPoint::create($data);
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
