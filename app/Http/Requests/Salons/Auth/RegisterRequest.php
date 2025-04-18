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
            'user.phone' => 'required|string|max:25',
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
            'types' => 'required|array|distinct', // TODO: add distinct rule
            'types.*' => 'required|string|in:salon,home_service,beautician,clinic',
            'description' => 'nullable|string',
            'bio' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ];
    }
}
