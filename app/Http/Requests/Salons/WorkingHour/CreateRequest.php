<?php

namespace App\Http\Requests\Salons\WorkingHour;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseFormRequest;
use App\Models\Users\User;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {

        $user =  User::auth();

        $rules = [
            'day_of_week'   => 'required|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'is_closed'     => 'required|boolean',
            'opening_time'  => 'required_if:is_closed,false|date_format:H:i',
            'closing_time'  =>  'required_if:is_closed,false|date_format:H:i|after:opening_time',
            'break_start'   => 'required_with:break_end|nullable|date_format:H:i',
            'break_end'     =>  'required_with:break_start|date_format:H:i|after:break_start',
        ];

        if ($user->isAdmin()) {
            $rules['salon_id'] = 'required|exists:salons,id,deleted_at,NULL';
        }

        return $rules;
    }
}
