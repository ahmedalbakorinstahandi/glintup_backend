<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'path',
        'type',
        'imageable_id',
        'imageable_type',
    ];

    protected $casts = [
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    protected $appends = ['url'];

    public function imageable()
    {
        return $this->morphTo()->withTrashed();
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}
