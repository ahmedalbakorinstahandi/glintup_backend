<?php

namespace App\Models\Salons;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalonMenuRequest extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'salon_id',
        'notes',
        'cost',
        'status',
        'approved_at',
        'rejected_at',
        'admin_note',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    protected $dates = [
        'approved_at',
        'rejected_at',
        'deleted_at',
    ];

    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }
}
