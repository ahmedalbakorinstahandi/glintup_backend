<?php

namespace App\Http\Requests\Salons\SalonStaff;

use App\Http\Requests\BaseFormRequest;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'position'  => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'user'      => 'nullable|array',
            'user.first_name' => 'nullable|string|max:255',
            'user.last_name'  => 'nullable|string|max:255',
            'user.password'   => 'nullable|string|min:6',
            'user.gender'     => 'nullable|in:male,female',
        ];
    }
}