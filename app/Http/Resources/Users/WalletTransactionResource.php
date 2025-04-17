<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'user_id'           => $this->user_id,
            'amount'            => $this->amount,
            'currency'          => $this->currency,
            'formatted_amount'  => number_format($this->amount, 2),
            'description'       => $this->description,
            'status'            => $this->status,
            'type'              => $this->type,
            'is_refund'         => $this->is_refund,
            'transactionable_id'=> $this->transactionable_id,
            'transactionable_type' => $this->transactionable_type,
            'direction'         => $this->direction,
            'user'            => new UserResource($this->whenLoaded('user')),
            'created_at'        => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'        => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
