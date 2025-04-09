<?php

namespace App\Http\Requests\Salons\Salon;

use App\Http\Requests\BaseFormRequest;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'owner_id'     => 'nullable|exists:users,id',
            'name'         => 'nullable|string|max:255',
            'icon'         => 'nullable|string|max:110',
            'phone_code'   => 'nullable|string|max:7',
            'phone'        => 'nullable|string|max:12',
            'email'        => 'nullable|email',
            'description'  => 'nullable|string',
            'location'     => 'nullable|string|max:255',
            'is_approved'  => 'nullable|boolean',
            'is_active'    => 'nullable|boolean',
            'type'         => 'nullable|in:salon,home_service,beautician,clinic',
            'latitude'     => 'nullable|numeric',
            'longitude'    => 'nullable|numeric',
            'country'      => 'nullable|string|max:255',
            'city'         => 'nullable|string|max:255',
        ];
    }
}
