<?php

namespace App\Http\Requests\Services\Review;

use App\Http\Requests\BaseFormRequest;

class ReportRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'salon_report' => 'required|string|max:1000',
            'reason_for_report' => 'required|in:inappropriate_content,spam,fake_review,other',
        ];
    }
}
