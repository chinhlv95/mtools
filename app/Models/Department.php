<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';
    protected $fillable=[
                    'parent_id',
                    'name',
                    'manager_id',
                    'status',
                    'description',
                    'created_at',
                    'updated_at',
    ];

    protected $dates = ['deleted_at'];
}
