<?php

namespace App\Http\Requests\Salons\Auth;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class VerifyCodeRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'phone' => ['required', 'phone:AUTO'],
            'verify_code' => 'required|string|max:6',
        ];
    }
}
