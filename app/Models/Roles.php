<?php

namespace App\Models;

use App\Updater;
use Cartalyst\Sentinel\Roles\EloquentRole;

class Roles extends EloquentRole
{
    use Updater;

    protected $table = 'roles';
    protected $fillable = [
                    'id',
                    'slug',
                    'name',
                    'permissions',
                    'deleted_by',
                    'updated_by',
                    'created_by',
                    'created_at',
                    'updated_at'
    ];
    protected $dates = ['deleted_at'];
}
