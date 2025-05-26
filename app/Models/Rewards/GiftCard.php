<?php

namespace App\Models\Rewards;

use App\Models\Services\Service;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class GiftCard extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'sender_id',
        'recipient_id',
        'phone_code',
        'phone',
        'type',
        'amount',
        'currency',
        'salon_id',
        'services',
        'tax',
        'message',
        'theme_id',
        'is_used',
        'received_at',
    ];

    protected $casts = [
        'code'          => 'string',
        'sender_id'     => 'integer',
        'recipient_id'  => 'integer',
        'phone_code'    => 'string',
        'phone'         => 'string',
        'type'          => 'string',
        'currency'      => 'string',
        'salon_id'      => 'integer',
        'message'       => 'string',
        'amount'        => 'decimal:2',
        'tax'           => 'double',
        'services'      => 'array',
        'theme_id'      => 'integer',
        'is_used'       => 'boolean',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
        'received_at'   => 'datetime',
    ];


    public function getServicesData()
    {
        $services = is_array($this->services) ? $this->services : [];

        $services_data = [];

        foreach ($services as $service_id) {
            $service = Service::find($service_id);
            if ($service) {
                $services_data[] = $service;
            }
        }

        return $services_data;
    }


    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id')->withTrashed();
    }


    // salon
    public function salon()
    {
        return $this->belongsTo(User::class, 'salon_id')->withTrashed();
    }


    public static function generateCode(): string
    {
        return 'GIFT-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
    }


    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id')->withTrashed();
    }

    public function getFullPhoneAttribute(): string
    {
        return $this->phone_code . $this->phone;
    }

    // public function getTypeLabelAttribute(): string
    // {
    //     return ucfirst($this->type);
    // }

    protected function services(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => json_decode($value, true),
            set: fn($value) => json_encode($value),
        );
    }
}
