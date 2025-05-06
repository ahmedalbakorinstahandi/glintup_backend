<?php

namespace App\Http\Requests\Services\Review;

use App\Http\Requests\BaseFormRequest;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'salon_id'         => 'required|exists:salons,id,deleted_at,NULL',
            'rating'           => 'required|integer|min:1|max:5',
            'comment'          => 'nullable|string|max:1000',
            // 'salon_reply'      => 'nullable|string|max:1000',
            // 'salon_report'     => 'nullable|string|max:1000',
            // 'salon_reported_at' => 'nullable|date',
        ];
    }
}
