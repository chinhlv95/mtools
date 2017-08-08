<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogProject extends Model
{
    use SoftDeletes;
    protected $table = 'log_projects';
    protected $fillable = [
            'id',
            'project_id',
            'crawled_date',
            'status_code',
            'errors_message',
            'ticket_count',
            'crawler_type_id',
            'created_at',
            'updated_at',
    ];
    protected $dates = ['deleted_at'];
}
