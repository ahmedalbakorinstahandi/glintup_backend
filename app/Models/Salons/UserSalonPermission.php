<?php

namespace App\Models\Salons;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSalonPermission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'permission_id',
        'salon_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function permission()
    {
        return $this->belongsTo(SalonPermission::class, 'permission_id')->withTrashed();
    }

    public function salon()
    {
        return $this->belongsTo(Salon::class)->withTrashed();
    }
}
