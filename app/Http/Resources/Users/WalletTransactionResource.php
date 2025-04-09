<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'wallet_id'         => $this->wallet_id,
            'amount'            => $this->amount,
            'formatted_amount'  => $this->formatted_amount,
            'currency'         => $this->currency,
            'description'       => $this->description,
            'transaction_type'  => $this->transaction_type,
            'short_type'        => $this->short_type,
            'direction'         => $this->direction,
            'created_at'        => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'        => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
