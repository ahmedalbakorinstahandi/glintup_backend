<?php

namespace App\Http\Requests\Users\Auth;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'phone' => ['required', 'phone:AUTO'],
            'password' => 'required|min:8',
            'device_token' => 'nullable|string',
        ];
    }
}
