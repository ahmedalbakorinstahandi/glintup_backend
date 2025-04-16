<?php


namespace App\Http\Services\Booking;

use App\Models\Booking\Coupon;
use App\Services\FilterService;
use App\Services\MessageService;
use App\Http\Permissions\Booking\CouponPermission;

class CouponService
{
    public function index($data)
    {
        $query = Coupon::query()->with('salon');

        $query = CouponPermission::filterIndex($query);

        return FilterService::applyFilters(
            $query,
            $data,
            ['code'],
            ['discount_value'],
            ['created_at', 'start_date', 'end_date'],
            ['salon_id', 'discount_type', 'gender', 'is_active'],
            ['id']
        );
    }

    public function show($id)
    {
        $item = Coupon::with('salon')->find($id);

        if (!$item) {
            MessageService::abort(404, 'messages.coupon.item_not_found');
        }

        return $item;
    }

    public function create($data)
    {
        return Coupon::create($data);
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

    // showByCode
    public function showByCode($salon, $code)
    {
        $item = Coupon::with('salon')->where('code', $code)->where('salon_id', $salon->id)->first();

        if (!$item) {
            MessageService::abort(404, 'messages.coupon.is_invalid');
        }

        if(!$item->is_valid) {
            MessageService::abort(404, 'messages.coupon.is_invalid');
        }

        return $item;
    }
}
