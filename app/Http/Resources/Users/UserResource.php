<?php

namespace App\Http\Resources\Users;

use App\Http\Resources\Salons\UserSalonPermissionResource;
use App\Http\Resources\Users\RefundResource;
use App\Http\Resources\Users\WalletResource;
use App\Http\Resources\Users\WalletTransactionResource;
use Illuminate\Http\Resources\Json\JsonResource;


class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'first_name'    => $this->first_name,
            'last_name'     => $this->last_name,
            'full_name'     => $this->first_name . ' ' . $this->last_name,
            'full_phone'    => $this->phone_code . ' ' . $this->phone,
            'gender'        => $this->gender,
            'birth_date'    => $this->birth_date?->format('Y-m-d'),
            'age' => $this->birth_date ? $this->birth_date->diff(now())->format('%y years, %m months, %d days') : null,
            'avatar'        => $this->avatar != null ? asset('storage/' . $this->avatar) : null,
            'phone_code'    => $this->phone_code,
            'phone'         => $this->phone,
            'role'          => $this->role,
            'is_active'     => $this->is_active,
            'is_verified'   => $this->is_verified,
            // 'location'      => $this->getLocation(),
            'otp_expire_at' => $this->otp_expire_at,

            'wallet'              => new WalletResource($this->whenLoaded('wallet')),
            'wallet_transactions' => WalletTransactionResource::collection($this->whenLoaded('walletTransactions')),
            'refunds'             => RefundResource::collection($this->whenLoaded('refunds')),
            'salon_permissions'   => UserSalonPermissionResource::collection($this->whenLoaded('salonPermissions')),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
