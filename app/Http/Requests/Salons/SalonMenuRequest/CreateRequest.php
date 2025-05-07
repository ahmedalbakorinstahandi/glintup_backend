<?php

namespace App\Http\Requests\Salons\SalonMenuRequest;

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
            'notes' => 'required|string',
            'success_url' => 'required|string',
            'cancel_url' => 'required|string',
        ];
    }
}
