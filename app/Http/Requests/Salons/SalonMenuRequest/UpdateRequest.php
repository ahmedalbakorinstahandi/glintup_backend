<?php

namespace App\Http\Requests\Salons\SalonMenuRequest;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UpdateRequest extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'status' => 'nullable|in:approved,rejected',
            'approved_at' => 'nullable|date',
            'rejected_at' => 'nullable|date',
            'admin_note' => 'nullable|string',
        ];
    }
}
