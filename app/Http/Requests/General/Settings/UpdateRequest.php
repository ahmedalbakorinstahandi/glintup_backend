<?php

namespace App\Http\Requests\General\Settings;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        // i need array have key and value
        return [
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'required|string',
        ];
    }
}
