<?php

namespace App\Http\Resources\General;

use App\Http\Resources\Users\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatusResource extends JsonResource
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
            'name' => $this->name,
            'created_by' => $this->created_by,
            'statusable_id' => $this->statusable_id,
            'statusable_type' => $this->statusable_type,
            'statusable' => $this->statusable,
            'created_by_user' => new UserResource($this->whenLoaded('createdByUser')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
