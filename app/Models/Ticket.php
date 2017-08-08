<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;
    protected $table = 'tickets';
    protected $fillable = [
                    'id',
                    'integrated_ticket_id',
                    'integrated_parent_id',
                    'source_id',
                    'ticket_type_id',
                    'status_id',
                    'title',
                    'category',
                    'version',
                    'description',
                    'estimate_time',
                    'start_date',
                    'due_date',
                    'progress',
                    'completed_date',
                    'created_by_user',
                    'assign_to_user',
                    'made_by_user',
                    'bug_type_id',
                    'loc',
                    'impact_analysis',
                    'test_case',
                    'impact_analysis',
                    'project_id',
    ];
    protected $dates = ['deleted_at'];
}
