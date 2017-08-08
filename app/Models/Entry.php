<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entry extends Model
{
    use SoftDeletes;
    protected $table = 'entries';
    protected $fillable = [
                    'id',
                    'integrated_entry_id',
                    'project_id',
                    'ticket_id',
                    'user_id',
                    'actual_hour',
                    'integrated_activity_id',
                    'spent_at',
                     ];
    protected $dates = ['deleted_at'];
}
