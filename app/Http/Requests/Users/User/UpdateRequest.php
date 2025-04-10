<?php

namespace App\Http\Requests\Users\User;

use Illuminate\Foundation\Http\FormRequest;

// UpdateRequest.php
namespace App\Http\Requests\Users\User;

use App\Http\Requests\BaseFormRequest;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'first_name'  => 'nullable|string|max:255',
            'last_name'   => 'nullable|string|max:255',
            'gender'      => 'nullable|in:male,female',
            'birth_date'  => 'nullable|date',
            'avatar'      => 'nullable|string|max:110',
            'phone_code'  => 'nullable|string|max:7',
            'phone'       => 'nullable|string|max:12|unique:users,phone,' . $this->route('id'),
            'password'    => 'nullable|string|min:6',
            'role'        => 'nullable|in:customer,salon_owner,admin,staff',
            'is_active'   => 'nullable|boolean',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'language'    => 'nullable|string|max:10',
            'added_by'    => 'nullable|in:admin,salon,register',
        ];
    }
}
