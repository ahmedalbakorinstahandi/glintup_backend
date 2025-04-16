<?php

namespace App\Http\Requests\Booking\Booking;

use App\Http\Requests\BaseFormRequest;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'date'           => 'nullable|date',
            'time'           => 'nullable|date_format:H:i:s',
            'status'         => 'nullable|in:pending,confirmed,completed,cancelled',
            'notes'          => 'nullable|string',
            'salon_notes'    => 'nullable|string',
        ];
    }
}
