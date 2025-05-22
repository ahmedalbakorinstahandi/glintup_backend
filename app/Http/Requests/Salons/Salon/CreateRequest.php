<?php

namespace App\Http\Requests\Salons\Salon;

use App\Http\Requests\BaseFormRequest;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'owner_id'     => 'nullable|exists:users,id',
            'name'         => 'required|string|max:255',
            'icon'         => 'required|string|max:110',
            'phone' => ['required', 'phone:AUTO'],
            'email'        => 'nullable|email',
            'description'  => 'nullable|string',
            'location'     => 'required|string|max:255',
            'is_approved'  => 'nullable|boolean',
            'is_active'    => 'required|boolean',
            'type'         => 'required|in:salon,home_service,beautician,clinic',
            'latitude'     => 'nullable|numeric',
            'longitude'    => 'nullable|numeric',
            'country'      => 'required|string|max:255',
            'city'         => 'required|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'nullable|string|max:255',
        ];
    }
}
