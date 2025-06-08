<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'address_secondary',
        'city',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'addressable_id',
        'addressable_type',
        'city_place_id',
    ];

    public function addressable()
    {
        return $this->morphTo();
    }
    
}
