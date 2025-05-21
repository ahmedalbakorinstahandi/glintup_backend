<?php

namespace App\Models\Statistics;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdStatistic extends Model
{

    protected $table = 'ad_statistics';

    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'promotion_ad_id',
        'clicked',
        'viewed',
    ];

    protected $casts = [
        'clicked'     => 'boolean',
        'viewed'      => 'boolean',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function promotionAd()
    {
        return $this->belongsTo(PromotionAd::class)->withTrashed();
    }
}
