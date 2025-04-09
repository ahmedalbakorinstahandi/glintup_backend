<?php

namespace App\Http\Requests\Users\Auth;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class RestPasswordRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'password' => 'required|string|min:8|confirmed'
        ];
    }
}
