<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectKpt extends Model
{
    use SoftDeletes;
    protected $table = 'project_kpt';
    protected $fillable=[
                    'project_id',
                    'user_id',
                    'type_id',
                    'category_id',
                    'release_id',
                    'status',
                    'content',
                    'action',
                    'created_at',
                    'updated_at',
    ];

    protected $dates = ['deleted_at'];

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id','id');
    }

    public function release()
    {
        return $this->belongsTo('App\Models\ProjectRelease', 'release_id','id');
    }

    public function project()
    {
        return $this->belongsTo('App\Models\Project', 'project_id','id');
    }
}
