<?php

namespace App\Http\Requests\Salons\Auth;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'phone' => ['required', 'phone:AUTO', 'trim'],
            'password' => 'required|min:8',
            'device_token' => 'nullable|string',
        ];
    }
}
