<?php

namespace App\Http\Requests\Admins\User;

use App\Http\Requests\BaseFormRequest;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => 'nullable|string|max:255',
            'last_name'  => 'nullable|string|max:255',
            'email'      => 'nullable|email|unique:users,email,' . $this->route('id'),
            'password'   => 'nullable|string|min:6',
            'is_active'  => 'nullable|boolean',
        ];
    }
}