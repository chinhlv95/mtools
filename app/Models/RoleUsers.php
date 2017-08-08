<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleUsers extends Model
{
    protected $table = 'role_users';
    protected $fillable = [
                    'user_id',
                    'role_id',
                    'created_at',
                    'updated_at'
    ];
    protected $dates = ['deleted_at'];
}
