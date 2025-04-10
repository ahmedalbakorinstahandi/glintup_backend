<?php

namespace App\Http\Requests\Booking\Booking;

use App\Http\Requests\BaseFormRequest;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'code'           => 'required|string|unique:bookings,code',
            'user_id'        => 'required|exists:users,id',
            'salon_id'       => 'required|exists:salons,id',
            'date'           => 'required|date',
            'time'           => 'required|date_format:H:i:s',
            'status'         => 'required|in:pending,confirmed,completed,cancelled',
            'payment_status' => 'required|in:unpaid,partially_paid,paid',
            'notes'          => 'nullable|string',
            'salon_notes'    => 'nullable|string',
        ];
    }
}
