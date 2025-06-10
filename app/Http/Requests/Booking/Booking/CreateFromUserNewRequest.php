<?php

namespace App\Http\Requests\Booking\Booking;

use App\Http\Requests\BaseFormRequest;
use App\Models\Salons\Salon;
use App\Models\Users\User;
use App\Services\MessageService;

class CreateFromUserNewRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $salon = null;
        $salon_id = request()->salon_id;

        if ($salon_id) {
            $salon = Salon::where('id', $salon_id)->first();
        }

        if (!$salon) {
            MessageService::abort(404, 'messages.salon.not_found');
        }

        $rules = [
            'date' => 'required|date_format:Y-m-d',
            'services' => 'required|array|min:1',
            'services.*.id' => 'required|exists:services,id,deleted_at,NULL',
            'services.*.start_time' => 'required|date_format:H:i',
            'services.*.end_time' => 'required|date_format:H:i',
            'coupon_id' => 'nullable|exists:coupons,id,deleted_at,NULL',
            'payment_method' => 'required|in:partially_paid,full_paid',
            'notes'          => 'nullable|string',
            'salon_id' => 'required|exists:salons,id,deleted_at,NULL',
            'use_free_services' => 'nullable|boolean',
        ];



        if ($salon->type == 'beautician') {

            if ($salon->service_location == 'in_house') {
                $rules['address_id'] = 'required|exists:addresses,id,deleted_at,NULL';
            } elseif ($salon->service_location == 'in_house_and_center') {
                $rules['service_location'] = 'required|in:in_house,in_center';
                $rules['address_id'] = 'required_if:service_location,in_house|exists:addresses,id,deleted_at,NULL';
            }
        }

        if ($salon->type == 'home_service') {
            $rules['address_id'] = 'required|exists:addresses,id,deleted_at,NULL';
        }


        return $rules;
    }
}
