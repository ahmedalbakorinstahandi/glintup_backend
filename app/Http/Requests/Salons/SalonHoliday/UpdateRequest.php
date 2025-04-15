<?php

namespace App\Http\Requests\Salons\SalonHoliday;

use App\Http\Requests\BaseFormRequest;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'holiday_date' => 'required|date',
            'reason'       => 'required|string|max:255',
            'is_full_day'  => 'required|boolean',
            'start_time'   => 'required_if:is_full_day,false|nullable|date_format:H:i',
            'end_time'     => 'required_if:is_full_day,false|nullable|date_format:H:i|after:start_time',
        ];
    }
}
