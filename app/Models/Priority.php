<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Priority extends Model
{
    use SoftDeletes;
    protected $table = 'priority';
    protected $fillable=[
        'name',
        'source_id',
        'integrated_bug_id',
        'related_id',
        'created_at',
        'updated_at',
    ];

    protected $dates = ['deleted_at'];

}
