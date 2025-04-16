<?php

namespace App\Http\Requests\Users\Auth;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string',
            'gender' => 'required|string|in:male,female',
            'birth_date' => 'required|date|before:today',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'language' => 'nullable|string|in:en,ar',
        ];
    }
}
