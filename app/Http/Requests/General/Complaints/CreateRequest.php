<?php

namespace App\Http\Requests\General\Complaints;

use App\Http\Requests\BaseFormRequest;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'hide_identity' => 'required|boolean',
            'content'       => 'required|string|max:1000',
            'phone_number'  => 'required_if:hide_identity,true|nullable|string|max:15',
        ];
    }
}
