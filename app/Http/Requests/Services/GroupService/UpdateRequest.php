<?php

namespace App\Http\Requests\Services\GroupService;

use App\Http\Requests\BaseFormRequest;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'order' => 'nullable|integer|min:1',
        ];
    }
}
