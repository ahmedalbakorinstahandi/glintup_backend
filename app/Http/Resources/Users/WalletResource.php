<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'user_id'          => $this->user_id,
            'balance'          => $this->balance,
            'balance_formatted' => $this->balance_formatted,
            'created_at'       => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'       => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
