<?php

namespace App\Http\Requests\Booking\Booking;

use App\Http\Requests\BaseFormRequest;
use App\Models\Users\User;
use App\Services\MessageService;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $phone = $this->input('phone');
        $phoneCode = $this->input('phone_code');

        if (!$phone || !$phoneCode) {
            MessageService::abort(422, 'messages.phone_code_or_phone_required');
        }

        $phone = str_replace(' ', '', $phoneCode) . str_replace(' ', '', $phone);

        $user = User::whereRaw("REPLACE(CONCAT(phone_code, phone), ' ', '') = ?", [$phone])
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
                'phone_code' => 'required|string',
                'phone'      => 'required|string',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'gender' => 'required|string|in:male,female',
            ];
        }

        $rules2 = [
            'date'           => 'required|date',
            'time'           => 'required|date_format:H:i',
            'status'         => 'required|in:pending,confirmed',
            // 'payment_status' => 'required|in:unpaid,partially_paid,paid',
            // 'notes'          => 'nullable|string',
            'salon_notes'    => 'nullable|string',
            'services' => 'required|array|min:1',
            'services.*.id' => 'required|exists:services,id,deleted_at,NULL',
        ];

        if ($user) {
            $rules2['phone_code'] = 'required|string';
            $rules2['phone'] = 'required|string';
        }


        $creator = User::auth();

        if (!$creator->isUserSalon()) {
            $rules2['salon_id'] = 'required|exists:salons,id,deleted_at,NULL';
        }

        return array_merge($rules, $rules2);
    }
}
