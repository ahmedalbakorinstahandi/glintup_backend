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
        $query = SalonPayment::with(['paymentable', 'user']);

        $query = SalonPaymentPermission::filterIndex($query);

        if (isset($data['search'])) {
            $query->where(function ($query) use ($data) {
                $query->whereHas('user', function ($query) use ($data) {
                    $query->where('first_name', 'like', '%' . $data['search'] . '%')
                        ->orWhere('last_name', 'like', '%' . $data['search'] . '%')
                        ->orWhere('phone', 'like', '%' . $data['search'] . '%')
                        ->orWhere('email', 'like', '%' . $data['search'] . '%')
                        ->orWhere('phone_code', 'like', '%' . $data['search'] . '%');
                });

                $query->orWhere('code', 'like', '%' . $data['search'] . '%');
            });
        }

        return FilterService::applyFilters(
            $query,
            $data,
            [],
            ['amount'],
            ['created_at'],
            ['salon_id', 'user_id'],
            ['id']
        );
    }

    public function show($id)
    {
        $item = SalonPayment::with(['paymentable', 'user'])->find($id);
        if (!$item) {
            MessageService::abort(404, 'messages.salon_payment.item_not_found');
        }
        return $item;
    }

    public function create($data)
    {
        $salonPayment = SalonPayment::create($data);

        $salonPayment->code = 'SP' . str_pad($salonPayment->id, 6, '0', STR_PAD_LEFT);
        $salonPayment->save();

        return $salonPayment;
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
