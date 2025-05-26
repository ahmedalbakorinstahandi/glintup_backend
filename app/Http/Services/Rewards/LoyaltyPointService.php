<?php

namespace App\Http\Services\Rewards;

use App\Models\Rewards\LoyaltyPoint;
use App\Services\FilterService;
use App\Services\MessageService;
use App\Http\Permissions\Rewards\LoyaltyPointPermission;
use App\Models\Rewards\FreeService;
use App\Models\Services\Service;
use App\Models\Users\User;

class LoyaltyPointService
{
    public function index($data)
    {
        $query = LoyaltyPoint::with(['user', 'salon', 'freeService']);
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
        $item = LoyaltyPoint::with(['user', 'salon', 'freeService'])->find($id);
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

    // receive
    public function receive($item)
    {

        $user = User::auth();

        // يجب أن يحتوي الكوبون على 5 أختام 
        if ($item->points < 5) {
            MessageService::abort(422, 'messages.loyalty_point.not_enough_points');
        }


        if ($item->token_at) {
            MessageService::abort(422, 'messages.loyalty_point.already_received');
        }

        $salon = $item->salon;

        $loyalty_service_id = $salon->loyalty_service_id;

        if (!$loyalty_service_id) {
            MessageService::abort(422, 'messages.loyalty_point.salon_deos_not_have_loyalty_service');
        }


        $service = Service::find($loyalty_service_id);


        if (!$service) {
            MessageService::abort(422, 'messages.loyalty_point.salon_deos_not_have_loyalty_service');
        }

        if ($service) {
            FreeService::create([
                'user_id' => $user->id,
                'service_id' => $service->id,
                'salon_id' => $salon->id,
                'freeable_id' => $item->id,
                'freeable_type' => LoyaltyPoint::class,
                'source' => 'loyalty',
                'is_used' => false,
                'booking_id' => null,
            ]);
        }



        $item->update([
            'taken_at' => now(),
        ]);

        // TODO: send notification to salon

        $item->load(['user', 'salon', 'freeService.service']);


        return $item;
    }
}
