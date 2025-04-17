<?php

namespace App\Http\Services\Salons;

use App\Models\Salons\SalonPayment;
use App\Services\FilterService;
use App\Services\MessageService;
use App\Http\Permissions\Salons\SalonPaymentPermission;

class SalonPaymentService
{
    public function index($data)
    {
        $query = SalonPayment::with(['paymentable']);
        $query = SalonPaymentPermission::filterIndex($query);
        return FilterService::applyFilters(
            $query,
            $data,
            ['status', 'method'],
            ['amount'],
            ['created_at'],
            ['salon_id', 'user_id'],
            ['id']
        );
    }

    public function show($id)
    {
        $item = SalonPayment::with(['paymentable'])->find($id);
        if (!$item) {
            MessageService::abort(404, 'messages.salon_payment.item_not_found');
        }
        return $item;
    }

    public function create($data)
    {
        return SalonPayment::create($data);
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
