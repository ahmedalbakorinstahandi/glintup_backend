<?php

namespace App\Http\Requests\Booking\Booking;

use App\Http\Requests\BaseFormRequest;
use App\Models\Users\User;
use App\Services\MessageService;

class RescheduleServicesRequest extends BaseFormRequest
{
    public function rules(): array
    {



        $rules = [

            'services' => 'required|array|min:1',
            'services.*.id' => 'required|exists:booking_services,id,deleted_at,NULL',
            'services.*.start_time' => 'required|date_format:H:i',
            'services.*.end_time' => 'required|date_format:H:i',

        ];


        return $rules;
    }
}
