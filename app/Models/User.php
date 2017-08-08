<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\SoftDeletes;

use Cartalyst\Sentinel\Users\EloquentUser;
class User extends EloquentUser
{

    //use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
            'id',
            'email',
            'user_name',
            'password',
            'permissions',
            'position',
            'member_code',
            'department_id',
            'source_id',
            'related_id',
            'last_login',
            'first_name',
            'last_name',
            'created_at',
            'updated_at'
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function projectMember()
    {
        return $this->hasMany('App\Models\ProjectMember');
    }
}