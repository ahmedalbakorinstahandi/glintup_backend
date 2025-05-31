<?php

namespace App\Models\General;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{
    use SoftDeletes;

    protected $table = 'statuses';

    // timestamps
    public $timestamps = false;

    protected $fillable = [
        'name',
        'statusable_id',
        'statusable_type',
        'created_by',
    ];

    protected $casts = [

        'created_at' => 'datetime',
    ];


    public function statusable()
    {
        return $this->morphTo();
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    
}
