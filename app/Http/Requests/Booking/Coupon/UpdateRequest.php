<?php

namespace App\Http\Requests\Booking\Coupon;

use App\Http\Requests\BaseFormRequest;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'discount_type'      => 'nullable|in:percentage,fixed',
            'discount_value'     => 'nullable|numeric|min:0',
            'max_uses'           => 'nullable|integer|min:1',
            'max_uses_per_user'  => 'nullable|integer|min:1',
            'start_date'         => 'nullable|date',
            'end_date'           => 'nullable|date|after_or_equal:start_date',
            'min_age'            => 'nullable|integer|min:1',
            'max_age'            => 'nullable|integer|min:1',
            'gender'             => 'nullable|in:male,female',
            'is_active'          => 'nullable|boolean',
        ];
    }
}
