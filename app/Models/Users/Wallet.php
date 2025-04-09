<?php

namespace App\Models\Users;

use App\Services\HelperService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'balance',
    ];

    protected $casts = [
        'balance'    => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function getBalanceFormattedAttribute(): string
    {
        return number_format($this->balance, 2) . HelperService::getCurrencySymbol('AED');
    }
}
