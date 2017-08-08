<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProjectMember extends Model {

    protected $table = 'project_member';
    protected $fillabel = [
                    'id',
                    'user_id',
                    'project_id',
                    'assign',
                    'note',
                    'start_date',
                    'end_date',
                    'position',
                    'role_id',
                    'created_at',
                    'updated_at',
                    'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id','id');
    }

    public function project()
    {
        return $this->belongsTo('App\Models\Project', 'project_id','id');
    }
}