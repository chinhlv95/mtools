<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    protected $table = 'categories';

    protected $fillable=[
                    'values',
                    'status',
                    'user_id',
                    'strategy',
                    'created_at',
                    'updated_at',
    ];

    protected $dates = ['deleted_at'];

    public function projectRisks()
    {
        return $this->hasMany('App\Models\ProjectRisk');
    }

    public function categoryKpt()
    {
        return $this->hasMany('App\Models\ProjectKpt');
    }
}
