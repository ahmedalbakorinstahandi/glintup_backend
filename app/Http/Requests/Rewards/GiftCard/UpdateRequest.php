<?php

namespace App\Http\Requests\Rewards\GiftCard;

use App\Http\Requests\BaseFormRequest;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'code' => 'sometimes|string|unique:gift_cards,code,' . $this->route('id'),
            'sender_id' => 'sometimes|exists:users,id',
            'recipient_id' => 'nullable|exists:users,id',
            'phone_code' => 'sometimes|string',
            'phone' => 'sometimes|string',
            'type' => 'sometimes|in:services,amount',
            'amount' => 'nullable|numeric',
            'currency' => 'nullable|string',
            'services' => 'nullable|array',
            'tax' => 'nullable|numeric',
            'message' => 'sometimes|string',
            'theme_id' => 'nullable|integer',
        ];
    }
}
