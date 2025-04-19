<?php

namespace App\Http\Requests\Booking\Booking;

use App\Http\Requests\BaseFormRequest;

class RescheduleRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'date'           => 'required|date',
            'time'           => 'required|date_format:H:i:s',
        ];
    }
}
