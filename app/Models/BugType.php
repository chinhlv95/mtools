<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BugType extends Model
{
    use SoftDeletes;
    protected $table = 'bugs_type';
    protected $fillable=[
                    'key',
                    'name',
                    'source_id',
                    'integrated_bug_id',
                    'related_id',
                    'created_at',
                    'updated_at',
    ];

    protected $dates = ['deleted_at'];

}
