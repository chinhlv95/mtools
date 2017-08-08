<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    protected $table = 'file_upload';
    protected $fillable=[
                    'id',
                    'name',
                    'project_id',
                    'created_at',
                    'updated_at',
    ];
}
