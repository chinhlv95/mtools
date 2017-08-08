<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
/**
 *
 * Sep 22, 201610:19:40 AM
 * @author tampt6722
 *
 */
class ProjectVersion extends Model
{
//    use SoftDeletes;
    protected $table = 'project_versions';
    protected $fillable = [
                    'id',
                    'integrated_version_id',
                    'project_id',
                    'source_id',
                    'name',
                    'status',
                    'description',
                    'start_date',
                    'end_date'
    ];
    //protected $dates = ['deleted_at'];
}
