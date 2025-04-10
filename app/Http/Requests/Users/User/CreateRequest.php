<?php

// CreateRequest.php
namespace App\Http\Requests\Users\User;

use App\Http\Requests\BaseFormRequest;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'first_name'  => 'required|string|max:255',
            'last_name'   => 'required|string|max:255',
            'gender'      => 'required|in:male,female',
            'birth_date'  => 'required|date',
            'avatar'      => 'nullable|string|max:110',
            'phone_code'  => 'required|string|max:7',
            'phone'       => 'required|string|max:12|unique:users,phone',
            'password'    => 'required|string|min:6',
            'role'        => 'required|in:customer,salon_owner,admin,staff',
            'is_active'   => 'nullable|boolean',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'language'    => 'nullable|string|max:10',
            'added_by'    => 'nullable|in:admin,salon,register',
        ];
    }
}
