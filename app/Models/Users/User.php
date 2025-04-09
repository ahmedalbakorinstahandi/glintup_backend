<?php

namespace App\Models\Users;

use App\Models\Salons\SalonStaff;
use App\Models\Salons\UserSalonPermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'birth_date',
        'avatar',
        'phone_code',
        'phone',
        'password',
        'role',
        'is_active',
        'latitude',
        'longitude',
        'otp',
        'otp_expire_at',
        'is_verified',
    ];

    protected $casts = [
        'birth_date'      => 'date',
        'is_active'       => 'boolean',
        'latitude'        => 'double',
        'longitude'       => 'double',
        'otp_expire_at'   => 'datetime',
        'is_verified'     => 'boolean',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];


    public static function auth()
    {
        if (Auth::check()) {
            return User::find(Auth::user()->id);
        }

        // MessageService::abort(503, 'messages.unauthorized');

        abort(
            401,
            response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ]),
        );
    }

    // user salon permissions
    public function salonPermissions()
    {
        return $this->hasMany(UserSalonPermission::class, 'user_id');
    }

    // salon staff
    // public function salonStaff()
    // {
    //     return $this->hasMany(SalonStaff::class, 'user_id');
    // }

    public function wallet()
    {
        return $this->hasOne(Wallet::class)->withTrashed();
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function getAgeAttribute(): ?int
    {
        if (is_null($this->birth_date)) {
            return null;
        }

        return now()->diffInYears($this->birth_date);
    }



    public function getLocation()
    {
        if (is_null($this->latitude) || is_null($this->longitude)) {
            return null;
        }

        return [
            'lat' => $this->latitude,
            'lng' => $this->longitude,
        ];
    }
}
