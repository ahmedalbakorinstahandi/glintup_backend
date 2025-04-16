<?php

namespace App\Http\Requests\Rewards\GiftCard;

use App\Http\Requests\BaseFormRequest;

class CreateByUserRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'phone_code' => 'required|string',
            'phone' => 'required|string',
            'type' => 'required|in:services,amount',
            'amount' => 'required_if:type,amount|numeric',
            'currency' => 'required_if:type,amount|string',
            'salon_id' => 'required_if:type,services|exists:salons,id,deleted_at,NULL',
            'services' => 'required_if:type,services|array|max:3',
            'services.*' => 'required_if:type,services|exists:services,id,deleted_at,NULL',
            'message' => 'required|string',
        ];
    }
}