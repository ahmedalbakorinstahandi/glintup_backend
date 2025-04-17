<?php

namespace App\Http\Requests\Salons\SalonPayment;

use App\Http\Requests\BaseFormRequest;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'user_id'      => 'required|exists:users,id',
            'salon_id'     => 'required|exists:salons,id',
            'amount'       => 'required|numeric|min:0',
            'currency'     => 'required|string|max:5',
            'method'       => 'required|in:wallet,stripe,cash',
            'status'       => 'required|in:pending,confirm,canceled,rejected',
            'is_refund'    => 'nullable|boolean',
            'system_percentage' => 'nullable|numeric|min:0',
            'paymentable_id' => 'required|integer',
            'paymentable_type' => 'required|string',
        ];
    }
}
