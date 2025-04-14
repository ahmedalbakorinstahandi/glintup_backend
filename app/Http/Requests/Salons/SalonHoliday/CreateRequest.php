<?php

namespace App\Http\Requests\Salons\SalonHoliday;

use App\Http\Requests\BaseFormRequest;
use App\Models\Users\User;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $user = User::auth();

        $rules = [
            'holiday_date' => 'required|date',
            'reason'       => 'required|string|max:255',
            'is_full_day'  => 'required|boolean',
            'start_time'   => 'required_if:is_full_day,false|nullable|date_format:H:i:s',
            'end_time'     => 'required_if:is_full_day,false|nullable|date_format:H:i:s|after:start_time',
        ];

        if ($user->isAdmin()) {
            $rules['salon_id'] = 'required|exists:salons,id,deleted_at,NULL';
        }

        return $rules;
    }
}
