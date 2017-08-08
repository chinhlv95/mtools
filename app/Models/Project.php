<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;
    protected $table = 'projects';
    protected $fillable = [
                    'id',
                    'project_id',
                    'project_key',
                    'source_id',
                    'name',
                    'department_id',
                    'brse',
                    'plant_start_date',
                    'plant_end_date',
                    'status',
                    'actual_start_date',
                    'actual_end_date',
                    'type_id',
                    'flag',
                    'customer_name',
                    'language_id',
                    'active',
                    'process_apply',
                    'plant_total_effort',
                    'actual_effort',
                    'member_assign',
                    'description',
                    'resource_need',
                    'user_id',
                    'created_at',
                    'updated_at'
    ];
    protected $dates = ['deleted_at'];

    public function projectKpt()
    {
        return $this->hasMany('App\Models\ProjectKpt');
    }

    public function projectRelease()
    {
        return $this->hasMany('App\Models\ProjectRelease');
    }

    public function projectMember()
    {
        return $this->hasMany('App\Models\ProjectMember');
    }

    public function crawler_urls()
    {
        return $this->hasOne('App\Models\CrawlerUrl');
    }
}
