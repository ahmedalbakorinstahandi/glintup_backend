<?php

namespace App\Models\Salons;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalonStaff extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'salon_id',
        'user_id',
        'position',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function salon()
    {
        return $this->belongsTo(Salon::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
