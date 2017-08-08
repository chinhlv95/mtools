<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberProjectReport extends Model
{
    use SoftDeletes;
    protected $table = 'member_project_report';
    protected $fillable = [
                    'id',
                    'report_flag',
                    'user_id',
                    'position',
                    'project_id',
                    'department_id',
                    'common_data',
                    'quality',
                    'productivity',
                    'start_date',
                    'end_date',
                    'time_name'
    ];
    protected $dates = ['deleted_at'];
}
