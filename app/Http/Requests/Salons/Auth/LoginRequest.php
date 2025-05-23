<?php

namespace App\Http\Requests\Salons\Auth;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends BaseFormRequest
{

    protected function prepareForValidation()
    {
        $this->merge([
            'phone' => $this->phone ? trim($this->phone) . "1" : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'phone:AUTO'],
            'password' => 'required|min:8',
            'device_token' => 'nullable|string',
        ];
    }
}
