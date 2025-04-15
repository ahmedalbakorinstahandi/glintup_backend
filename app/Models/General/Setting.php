<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'key',
        'value',
        'type',
        'allow_null',
        'is_settings',
    ];

    protected $casts = [
        'allow_null' => 'boolean',
        'is_settings' => 'boolean',
    ];
}
