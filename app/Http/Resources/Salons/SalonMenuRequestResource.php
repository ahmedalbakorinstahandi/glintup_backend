<?php

namespace App\Http\Resources\Salons;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalonMenuRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'salon_id' => $this->salon_id,
            'notes' => $this->notes,
            'cost' => $this->cost,
            'status' => $this->status,
            'approved_at' => $this->approved_at,
            'rejected_at' => $this->rejected_at,
            'admin_note' => $this->admin_note,
            'salon' => new SalonResource($this->whenLoaded('salon')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

        ];
    }
}
