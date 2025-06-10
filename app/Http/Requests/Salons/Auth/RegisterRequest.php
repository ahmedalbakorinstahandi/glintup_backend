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
            'description' => 'nullable|string',
            'bio' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'service_location' => 'required_if:type,beautician|nullable|string|in:in_house,in_center,in_house_and_center',
            'bank_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:255',
            'bank_account_holder_name' => 'required|string|max:255',
            'bank_account_iban' => 'required|string|max:255',
            'services_list' => 'required|string',
            'trade_license' => 'required|string',
            'vat_certificate' => 'required|string',
            'bank_account_certificate' => 'required|string',
            'vat_number' => 'required|string|max:255',

        ];
    }
}
