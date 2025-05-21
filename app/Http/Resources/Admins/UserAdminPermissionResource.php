<?php

namespace App\Http\Resources\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAdminPermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
