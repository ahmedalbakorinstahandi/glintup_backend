<?php

namespace App\Models\Salons;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalonSocialMidiaSite extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'salon_id',
        'social_media_site_id',
        'link',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function salon()
    {
        return $this->belongsTo(Salon::class)->withTrashed();
    }

    public function socialMediaSite()
    {
        return $this->belongsTo(SocialMidiaSite::class, 'social_media_site_id')->withTrashed();
    }
}
