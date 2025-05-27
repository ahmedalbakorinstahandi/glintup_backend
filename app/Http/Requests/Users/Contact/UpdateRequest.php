<?php

namespace App\Http\Requests\Users\Contact;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'name'       => 'sometimes|string|max:50',
            'phone' => ['sometimes', 'phone:AUTO'],
            'avatar'     => 'nullable|string|max:100',
        ];
    }
}
