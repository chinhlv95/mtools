<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberReport extends Model
{
    protected $table = 'members_report';
    protected $fillable=[
                    'department_id',
                    'division_id',
                    'team_id',
                    'project_id',
                    'member_id',
                    'email',
                    'name',
                    'position',
                    'year',
                    'data_T1',
                    'data_T2',
                    'data_T3',
                    'data_T4',
                    'data_T5',
                    'data_T6',
                    'data_T7',
                    'data_T8',
                    'data_T9',
                    'data_T10',
                    'data_T11',
                    'data_T12',
                    'created_at',
                    'updated_at',
    ];
}
