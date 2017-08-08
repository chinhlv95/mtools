<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ProjectReport extends Model
{
    protected $table = 'project_report';
    protected $fillable=[
                    'department_id',
                    'project_id',
                    'status',
                    'year',
                    'month',
                    'tested_tc',
                    'loc',
                    'weighted_bug',
                    'uat_bug',
                    'actual_hour'
    ];

}
