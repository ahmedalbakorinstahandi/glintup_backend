<?php

namespace App\Models\Services;

use App\Models\Salons\Salon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupService extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'group_id',
        'service_id',
        'salon_id',
        'order',
    ];

    protected $casts = [
        'order'      => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class)->withTrashed();
    }

    public function service()
    {
        return $this->belongsTo(Service::class)->withTrashed();
    }

    public function salon()
    {
        return $this->belongsTo(Salon::class)->withTrashed();
    }
}
