<?php

namespace App\Http\Requests\Users\Auth;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class ForgetPasswordRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'phone' => 'required|string|max:20',
        ];
    }
}
