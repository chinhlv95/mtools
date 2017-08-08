<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loc extends Model
{
    use SoftDeletes;
    protected $table = 'locs';
    protected $fillable = [
                        'id',
                        'project_id',
                        'ticket_id',
                        'user_id',
                        'loc',
                        'created_at',
                        'updated_at'
                     ];
    protected $dates = ['deleted_at'];
}
