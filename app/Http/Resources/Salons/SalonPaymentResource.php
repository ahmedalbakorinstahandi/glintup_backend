<?php

namespace App\Http\Resources\Salons;

use Illuminate\Http\Resources\Json\JsonResource;

class SalonPaymentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'amount'            => $this->amount,
            'currency'          => $this->currency,
            'method'            => $this->method,
            'status'            => $this->status,
            'is_refund'         => $this->is_refund,
            'system_percentage' => $this->system_percentage,
            'paymentable_id'    => $this->paymentable_id,
            'paymentable_type'  => $this->paymentable_type,

            'created_at'        => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'        => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
