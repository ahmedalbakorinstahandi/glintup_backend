<?php

namespace App\Http\Requests\Users\User;

use App\Http\Requests\BaseFormRequest;

class UpdateProfileRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'first_name'  => 'nullable|string|max:255',
            'last_name'   => 'nullable|string|max:255',
            'gender'      => 'nullable|in:male,female',
            'birth_date'  => 'nullable|date',
            'avatar'      => 'nullable|string|max:110',
            'password'    => 'nullable|string|min:6',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'language'    => 'nullable|string|max:10',
            'email'      => 'nullable|email|max:255',
            'email_offers' => 'nullable|boolean',
        ];
    }
}
