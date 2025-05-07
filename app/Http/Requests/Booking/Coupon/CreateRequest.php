<?php

namespace App\Http\Requests\Booking\Coupon;

use App\Http\Requests\BaseFormRequest;
use App\Models\Users\User;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $rules = [
            'code'               => 'required|string|max:255|unique:coupons,code,NULL,id,deleted_at,NULL',
            'discount_type'      => 'required|in:percentage,fixed',
            'discount_value'     => 'required|numeric|min:0',
            'max_uses'           => 'nullable|integer|min:1',
            'max_uses_per_user'  => 'nullable|integer|min:1',
            'start_date'         => 'nullable|date',
            'end_date'           => 'nullable|date|after_or_equal:start_date',
            'min_age'            => 'nullable|integer|min:1',
            'max_age'            => 'nullable|integer|min:1',
            'gender'             => 'nullable|in:male,female',
            'is_active'          => 'nullable|boolean',
        ];


        $user = User::auth();

        if ($user->isAdmin()) {
            $rules['salon_id'] = 'required|exists:salons,id,deleted_at,NULL';
        }

        return $rules;
    }
}
