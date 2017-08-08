<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileImport extends Model
{
    protected $table = 'file_import';
    protected $fillable=[
                    'id',
                    'name',
                    'user_id',
                    'status',
                    'project_id',
                    'type',
                    'parent_id',
                    'created_at',
                    'updated_at',
    ];
}
