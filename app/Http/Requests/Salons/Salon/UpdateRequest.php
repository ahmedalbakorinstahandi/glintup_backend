<?php

namespace App\Http\Requests\Salons\Salon;

use App\Http\Requests\BaseFormRequest;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'merchant_legal_name'       => 'nullable|string|max:255',
            'merchant_commercial_name'  => 'nullable|string|max:255',
            'address'                   => 'nullable|string|max:255',
            'city_street_name'          => 'nullable|string|max:255',
            'contact_name'              => 'nullable|string|max:255',
            'contact_number'            => 'nullable|string|max:25',
            'contact_email'             => 'nullable|email|max:255',
            'business_contact_name'     => 'nullable|string|max:255',
            'business_contact_email'    => 'nullable|email|max:255',
            'business_contact_number'   => 'nullable|string|max:25',
            'types'                     => 'nullable|array|distinct', // TODO: add distinct rule
            'types.*'                   => 'nullable|string|in:salon,home_service,beautician,clinic',
            'bio'                       => 'nullable|string',
            'latitude'                  => 'nullable|numeric',
            'longitude'                 => 'nullable|numeric',
            'name'                      => 'nullable|string|max:255',
            'icon'                      => 'nullable|string',
            'phone_code'                => 'nullable|string|max:7',
            'phone'                     => 'nullable|string|max:12',
            'email'                     => 'nullable|email',
            'description'               => 'nullable|string',
            'location'                  => 'nullable|string|max:255',
            'is_approved'               => 'nullable|boolean',
            'is_active'                 => 'nullable|boolean',
            'type'                      => 'nullable|in:salon,home_service,beautician,clinic',
            'country'                   => 'nullable|string|max:255',
            'city'                      => 'nullable|string|max:255',
            'block_message'             => 'nullable|string|max:255',
            'tags'                      => 'nullable|string',

            'images' => 'nullable|array',
            'images.*' => 'nullable|string|max:255',
            'images_remove' => 'nullable|array',
            'images_remove.*' => 'nullable|integer|exists:images,id,deleted_at,NULL',
        ];
    }
}
