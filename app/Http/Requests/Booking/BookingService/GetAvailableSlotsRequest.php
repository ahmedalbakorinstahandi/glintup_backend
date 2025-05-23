<?php


namespace App\Http\Requests\Booking\BookingService;

use App\Http\Requests\BaseFormRequest;

class GetAvailableSlotsRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'date' => 'required|date:format:Y-m-d',
        ];
    }
}
