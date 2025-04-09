<?php

namespace App\Models\Salons;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalonCustomer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'salon_id',
        'user_id',
        'is_banned',
        'notes',
    ];

    protected $casts = [
        'is_banned'  => 'boolean',
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
