<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAdminPermission extends Model
{


    protected $fillable = ['user_id', 'permission_id'];
}