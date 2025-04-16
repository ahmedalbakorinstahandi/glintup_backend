<?php

namespace App\Http\Requests\Rewards\GiftCard;

use App\Http\Requests\BaseFormRequest;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'code' => 'required|string|unique:gift_cards,code',
            'sender_id' => 'required|exists:users,id',
            'recipient_id' => 'nullable|exists:users,id',
            'phone_code' => 'required|string',
            'phone' => 'required|string',
            'type' => 'required|in:services,amount',
            'amount' => 'nullable|numeric',
            'currency' => 'nullable|string',
            'services' => 'nullable|array',
            'tax' => 'nullable|numeric',
            'message' => 'required|string',
        ];
    }
}