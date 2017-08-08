<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RootCause extends Model
{
    use SoftDeletes;
    protected $table = 'root_cause';
    protected $fillable=[
                    'name',
                    'source_id',
                    'integrated_root_id',
                    'related_id',
                    'created_at',
                    'updated_at',
    ];

    protected $dates = ['deleted_at'];
}
