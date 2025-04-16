<?php

namespace App\Http\Requests\Booking\Booking;

use App\Http\Requests\BaseFormRequest;
use App\Models\Users\User;
use App\Services\MessageService;

class GetBookingDetailsRequest extends BaseFormRequest
{
    public function rules(): array
    {



        $rules = [
            'services' => 'required|array|min:1',
            'services.*.id' => 'required|exists:services,id,deleted_at,NULL',
            'coupon_id' => 'nullable|exists:coupons,id,deleted_at,NULL',
            'payment_method' => 'required|in:partially_paid,full_paid',
            'salon_id' => 'required|exists:salons,id,deleted_at,NULL',
        ];


        return $rules;
    }
}
