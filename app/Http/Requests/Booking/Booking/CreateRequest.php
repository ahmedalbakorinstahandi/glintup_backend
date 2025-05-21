<?php

namespace App\Http\Requests\Booking\Booking;

use App\Http\Requests\BaseFormRequest;
use App\Models\Users\User;
use App\Services\MessageService;
use App\Services\PhoneService;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $phone = $this->input('phone');


        if (!$phone) {
            // TODO: Phone is required translation
            MessageService::abort(422, 'messages.phone_is_required');
        }

        $phoneParts = PhoneService::parsePhoneParts($phone);
        $countryCode = $phoneParts['country_code'];
        $phoneNumber = $phoneParts['national_number'];


        $user = User::where('phone', $phoneNumber)
            ->where('phone_code', $countryCode)
            ->where('role', 'customer')
            ->first();

        $rules = [];


        if ($user) {
            if ($user->is_active == 0) {
                // TODO: User is banned
                MessageService::abort(422, 'messages.user.is_banned');
            }
        } else {
            $rules = [
                'phone' => ['required', 'phone:AUTO'],
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'gender' => 'required|string|in:male,female',
            ];
        }

        $rules2 = [
            'date'           => 'required|date',
            'time'           => 'required|date_format:H:i',
            'status'         => 'required|in:pending,confirmed',
            // 'notes'          => 'nullable|string',
            'salon_notes'    => 'nullable|string',
            'services' => 'required|array|min:1',
            'services.*.id' => 'required|exists:services,id,deleted_at,NULL',
        ];

        if ($user) {
            $rules2['phone'] = ['required', 'phone:AUTO'];
        }


        $creator = User::auth();

        if (!$creator->isUserSalon()) {
            $rules2['salon_id'] = 'required|exists:salons,id,deleted_at,NULL';
        }

        return array_merge($rules, $rules2);
    }
}
