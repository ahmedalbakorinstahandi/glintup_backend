<?php

namespace App\Http\Services\Salons;

use App\Models\Salons\SalonCustomer;
use App\Services\FilterService;
use App\Services\MessageService;
use App\Http\Permissions\Salons\SalonCustomerPermission;

class SalonCustomerService
{
    public function index($data)
    {
        $query = SalonCustomer::query()->with(['salon', 'user']);

        $query = SalonCustomerPermission::filterIndex($query);

        return FilterService::applyFilters(
            $query,
            $data,
            ['notes'],
            [],
            ['created_at'],
            ['salon_id', 'user_id', 'is_banned'],
            ['id']
        );
    }

    public function show($id)
    {
        $item = SalonCustomer::with(['salon', 'user'])->find($id);

        if (!$item) {
            MessageService::abort(404, 'messages.salon_customer.item_not_found');
        }

        return $item;
    }

    public function create($validated)
    {
        return SalonCustomer::create($validated);
    }

    public function update($item, $validated)
    {
        $item->update($validated);
        return $item;
    }

    public function destroy($item)
    {
        return $item->delete();
    }
}
