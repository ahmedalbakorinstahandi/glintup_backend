<?php

namespace App\Http\Requests\Services\Review;

use App\Http\Requests\BaseFormRequest;

class ReplayRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'salon_reply' => 'required|string|max:1000',
        ];
    }
}
