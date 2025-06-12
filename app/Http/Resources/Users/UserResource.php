<?php

namespace App\Http\Resources\Users;

use App\Http\Resources\Admins\AdminPermissionResource;
use App\Http\Resources\Salons\SalonResource;
use App\Http\Resources\Salons\UserSalonPermissionResource;
use App\Http\Resources\Users\RefundResource;
use App\Http\Resources\Users\WalletResource;
use App\Http\Resources\Users\WalletTransactionResource;
use App\Models\Users\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
{
    public function toArray($request)
    {

        $is_admin = false;
        $show_user_location = false;
        if (Auth::check()) {
            $user = User::auth();

            if ($user->isAdmin()) {
                $is_admin = true;
                $show_user_location = true;
            } else if ($user->isUserSalon()) {
                $is_home_service_salon = $user->salon->isHomeServiceSalon();
                if ($is_home_service_salon) {
                    $show_user_location = true;
                }
            } elseif ($user->isCustomer() && $user->id == $this->id) {
                $show_user_location = true;
            }
        }

        return [
            'id'            => $this->id,
            'first_name'    => $this->first_name,
            'last_name'     => $this->last_name,
            'full_name'     => $this->first_name . ' ' . $this->last_name,
            'balance'       => $this->balance ?? 0,
            'full_phone'    => '+' . $this->phone_code . ' ' . $this->phone,
            'gender'        => $this->gender,
            'birth_date'    => $this->birth_date?->format('Y-m-d'),
            'age' => $this->birth_date ? $this->birth_date->diff(now())->format('%y years, %m months, %d days') : null,
            'avatar'        => $this->avatar != null ? asset('storage/' . $this->avatar) : null,
            'phone_code'    => '+' . $this->phone_code,
            'phone'         => $this->phone,
            'email'         => $this->email,
            'email_offers'  => $this->email_offers,
            'role'          => $this->role,
            'latitude'     => $show_user_location ? $this->latitude : null,
            'longitude'    => $show_user_location ? $this->longitude : null,
            'address'       => $this->address,
            'is_active'     => $this->is_active,
            'is_verified'   => $this->is_verified,
            'otp_expire_at' => $this->otp_expire_at,
            'notes'         => $this->when($is_admin, $this->notes),

            'salon' => new SalonResource($this->whenLoaded('salon')),
            'salon_owner' => new SalonResource($this->whenLoaded('salonOwner')),

            'wallet_transactions' => WalletTransactionResource::collection($this->whenLoaded('walletTransactions')),
            'refunds'             => RefundResource::collection($this->whenLoaded('refunds')),
            'salon_permissions'   => UserSalonPermissionResource::collection($this->whenLoaded('salonPermissions')),

            'admin_permissions' => AdminPermissionResource::collection($this->whenLoaded('adminPermissions')),

            'register_at' => $this->register_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
