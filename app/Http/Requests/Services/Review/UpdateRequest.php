<?php

namespace App\Http\Requests\Services\Review;

use Illuminate\Foundation\Http\FormRequest;


use App\Http\Requests\BaseFormRequest;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'rating'           => 'nullable|integer|min:1|max:5',
            'comment'          => 'nullable|string|max:1000',
            'salon_reply'      => 'nullable|string|max:1000',
            'salon_report'     => 'nullable|string|max:1000',
            'salon_reported_at' => 'nullable|date',
            'is_reviewed'     => 'nullable|boolean',
            'is_visible'      => 'nullable|boolean',
        ];
    }
}
