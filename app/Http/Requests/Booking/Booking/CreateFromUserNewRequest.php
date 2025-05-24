<?php

namespace App\Http\Requests\Booking\Booking;

use App\Http\Requests\BaseFormRequest;
use App\Models\Users\User;
use App\Services\MessageService;

class CreateFromUserNewRequest extends BaseFormRequest
{
    public function rules(): array
    {



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


        return $rules;
    }
}
