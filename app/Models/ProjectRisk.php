<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectRisk extends Model
{
    use SoftDeletes;
    protected $table = 'project_risk';
    protected $fillable=[
                    'status',
                    'impact',
                    'propability',
                    'strategy',
                    'mitigration_plan',
                    'risk_title',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                    'category_id',
    ];
    protected $dates = ['deleted_at'];

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id','id');
    }
}
