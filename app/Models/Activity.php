<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;
    protected $table = 'activities';
    protected $fillable=[
                    'activity_id',
                    'name',
                    'source_id',
                    'created_at',
                    'updated_at',
    ];

    protected $dates = ['deleted_at'];
}
