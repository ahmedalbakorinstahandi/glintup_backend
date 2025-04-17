<?php

namespace App\Http\Requests\Salons\SalonPayment;

use App\Http\Requests\BaseFormRequest;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'status'       => 'nullable|in:pending,confirm,canceled,rejected',
            'method'       => 'nullable|in:wallet,stripe,cash',
            'is_refund'    => 'nullable|boolean',
            'system_percentage' => 'nullable|numeric|min:0',
        ];
    }
}