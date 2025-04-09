<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'booking_id',
        'amount',
        'tax',
        'discount',
        'status',
    ];

    protected $casts = [
        'amount'      => 'double',
        'tax'         => 'double',
        'discount'    => 'double',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class)->withTrashed();
    }

    // ✅ خصائص مخصصة
    public function getTotalAmountAttribute(): float
    {
        return $this->amount + $this->tax - ($this->discount ?? 0);
    }
}
