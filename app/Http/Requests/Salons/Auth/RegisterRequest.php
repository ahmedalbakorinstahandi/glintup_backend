<?php

namespace App\Http\Requests\Salons\Auth;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'user.first_name' => 'required|string|max:255',
            'user.last_name' => 'required|string|max:255',
            'user.password' => 'required|string|min:8|confirmed',
            'user.phone_code' => 'required|string|max:5',
            'user.phone' => ['required', 'phone:AUTO'],
            'user.gender' => 'required|string|in:male,female',
            'user.birth_date' => 'required|date|before:today',
            'merchant_legal_name' => 'required|string|max:255',
            'merchant_commercial_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city_street_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:25',
            'contact_email' => 'required|email|max:255',
            'business_contact_name' => 'required|string|max:255',
            'business_contact_email' => 'required|email|max:255',
            'business_contact_number' => 'required|string|max:25',
            'icon' => 'required|string',
            'type' => 'required|string|in:salon,home_service,beautician,clinic',
            'types' => [
                'required',
                'array',
                'distinct',
                function ($attribute, $value, $fail) {
                    $type = request()->input('type');
                    $allowedTypes = match ($type) {
                        'salon' => ['home_service', 'beautician'],
                        'clinic' => ['home_service'],
                        default => [],
                    };

                    if (!empty($value) && array_diff($value, $allowedTypes)) {
                        $fail("The selected {$attribute} is invalid for the type {$type}.");
                    }
                },
            ],
            'types.*' => 'required|string|in:salon,home_service,beautician,clinic',
            'description' => 'nullable|string',
            'bio' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ];
    }
}
