<?php

namespace App\Http\Requests\Salons\WorkingHour;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseFormRequest;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'is_closed'     => 'required|boolean',
            'opening_time'  => 'required_if:is_closed,false|date_format:H:i',
            'closing_time'  => 'required_if:is_closed,false|date_format:H:i|after:opening_time',
            'break_start'   => 'required_with:break_end|nullable|date_format:H:i',
            'break_end'     => 'required_with:break_start|date_format:H:i|after:break_start',
            'delete_break_time' => 'boolean',
        ];
    }
}