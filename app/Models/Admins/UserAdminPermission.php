<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAdminPermission extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'permission_id'];
}