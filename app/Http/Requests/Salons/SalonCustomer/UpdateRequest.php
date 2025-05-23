<?php

namespace App\Http\Requests\Salons\SalonCustomer;

use App\Http\Requests\BaseFormRequest;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'is_banned' => 'nullable|boolean',
            'notes'     => 'nullable|string|max:1000',
        ];
    }
}
