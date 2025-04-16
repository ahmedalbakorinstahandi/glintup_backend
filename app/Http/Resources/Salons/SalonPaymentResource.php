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
            // 'amount_formatted'  => $this->amount_formatted,
            'currency'          => $this->currency,
            'method'            => $this->method,
            'status'            => $this->status,
            // 'status_label'      => $this->status_label,
            'is_refund'         => $this->is_refund,
            'paymentable_id'    => $this->paymentable_id,
            'paymentable_type'  => $this->paymentable_type,

            'created_at'        => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'        => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
