<?php

namespace App\Models\Users;

use App\Models\Admins\AdminPermission;
use App\Models\Booking\Booking;
use App\Models\General\Notification;
use App\Models\Rewards\FreeService;
use App\Models\Salons\Salon;
use App\Models\Salons\SalonStaff;
use App\Models\Salons\UserSalonPermission;
use App\Models\User\UserAdminPermission;
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
        'balance',
        'gender',
        'birth_date',
        'avatar',
        'phone_code',
        'phone',
        'password',
        'email',
        'email_offers',
        'role',
        'is_active',
        'latitude',
        'longitude',
        'address',
        'notes',
        'otp',
        'stripe_customer_id',
        'otp_expire_at',
        'is_verified',
        'register_at',
    ];

    protected $casts = [
        'id'              => 'integer',
        'first_name'      => 'string',
        'last_name'       => 'string',
        'balance'        => 'double',
        'email'          => 'string',
        'email_offers'    => 'boolean',
        'birth_date'      => 'date',
        'is_active'       => 'boolean',
        'latitude'        => 'double',
        'longitude'       => 'double',
        'address'         => 'string',
        'notes'           => 'string',
        'otp_expire_at'   => 'datetime',
        'is_verified'     => 'boolean',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
        'register_at'     => 'datetime',
    ];


    public static function auth()
    {
        if (Auth::guard('sanctum')->check()) {
            $user =  Auth::guard('sanctum')->user();
            return User::where('id', $user->id)->first();
        }

        return null;
    }


    // // salon
    // public function salon()
    // {
    //     $staff = SalonStaff::where('user_id', $this->id)->first();

    //     if (!$staff) {
    //         return null;
    //     }

    //     $salon = Salon::where('id', $this->staff->salon_id)->first();

    //     if (!$salon) {
    //         return null;
    //     }

    //     return $salon;

    // }


    // public function staff()
    // {
    //     return $this->hasOne(\App\Models\Salons\SalonStaff::class, 'user_id')->withTrashed();
    // }


    public function adminPermissions()
    {
        return $this->belongsToMany(
            AdminPermission::class,
            'user_admin_permissions',
            'user_id',
            'permission_id'
        )->withTimestamps()->withTrashed();
    }




    public function salon()
    {
        return $this->hasOneThrough(
            Salon::class,   // النهاية المطلوبة
            SalonStaff::class, // الجدول الوسيط
            'user_id',     // Foreign key in SalonStaff pointing to User
            'id',          // Foreign key in Salon pointing to Salon
            'id',          // Local key on User
            'salon_id'     // Local key on SalonStaff pointing to Salon
        )->withTrashed();
    }

    //bookings
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }


    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isSalonOwner()
    {
        return $this->role === 'salon_owner';
    }

    public function isUserSalon()
    {
        return $this->isSalonOwner() || $this->isStaff();
    }

    public function getSalonId()
    {

        if ($this->isUserSalon()) {
            return $this->staff->salon_id;
        }

        return null;
    }

    // free services
    public function freeServices()
    {
        return $this->hasMany(FreeService::class, 'user_id');
    }

    // salon owner
    public function salonOwner()
    {
        return $this->hasMany(Salon::class, 'owner_id');
    }



    // user salon permissions
    public function salonPermissions()
    {
        return $this->hasMany(UserSalonPermission::class, 'user_id');
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
        return abs(now()->diffInYears($this->birth_date));
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


    public static function notificationsUnreadCount()
    {
        if (Auth::check()) {
            $user = User::auth();
            return  Notification::where('user_id', $user->id)->whereNull('read_at')->count();
        } else {
            return  Notification::whereNull('user_id')->count();
        }
    }
}
