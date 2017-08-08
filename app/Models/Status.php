<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{
  //  use SoftDeletes;
    protected $table = 'status';
    protected $fillable=[
                    'id',
                    'name',
                    'source_id',
                    'integrated_status_id',
                    'created_at',
                    'updated_at',
    ];

   // protected $dates = ['deleted_at'];
}
