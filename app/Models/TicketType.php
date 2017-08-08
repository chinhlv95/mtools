<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketType extends Model
{
    use SoftDeletes;
    protected $table = 'ticket_type';
    protected $fillable = [
                    'id',
                    'name',
                    'source_id',
                    'integrated_ticket_type_id',

    ];
    protected $dates = ['deleted_at'];
}
