<?php

namespace App\Http\Requests\Booking\Booking;

use App\Http\Requests\BaseFormRequest;
use App\Models\Users\User;
use App\Services\MessageService;

class CreateFromUserRequest extends BaseFormRequest
{
    public function rules(): array
    {



        $rules = [
            'services' => 'required|array|min:1',
            'services.*.id' => 'required|exists:services,id,deleted_at,NULL',
            'coupon_id' => 'nullable|exists:coupons,id,deleted_at,NULL',
            'date'           => 'required|date',
            'time'           => 'required|date_format:H:i',
            'payment_method' => 'required|in:partially_paid,full_paid',
            'notes'          => 'nullable|string',
            'salon_id' => 'required|exists:salons,id,deleted_at,NULL',
            'use_free_services' => 'nullable|boolean',
        ];


        return $rules;
    }
}
