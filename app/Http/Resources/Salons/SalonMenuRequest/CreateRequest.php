<?php

namespace App\Http\Resources\Salons\SalonMenuRequest;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreateRequest extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'notes' => ['nullable', 'string'],
            'cost' => ['sometimes', 'numeric', 'min:0'],
            'status' => ['sometimes', 'in:pending,approved,rejected'],
            'approved_at' => ['nullable', 'date'],
            'rejected_at' => ['nullable', 'date'],
            'admin_note' => ['nullable', 'string'],
        ];
    }
}
