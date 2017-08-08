<?php
namespace App\Repositories\RoleUsers;

use App\Models\RoleUsers;

/**
 *
 * Sep 22, 201610:19:11 AM
 * @author SonNA6229
 *
 */

class RoleUsersRepository implements RoleUsersRepositoryInterface
{

    public function __construct()
    {
    }

    /**
     * @todo get all Role member
     * @author SonNA
     * @see \App\Repositories\RoleUsers\RoleUsersRepositoryInterface::all()
     */
    public function all(){

        $result = RoleUsers::all();
        return $result;
    }

    /**
     * @todo Assign Members to Role
     * @author SonNA
     * @see \App\Repositories\RoleUsers\RoleUsersRepositoryInterface::create()
     */
    public function create($data){
        $query = RoleUsers::create($data);
        $query->save();
        return $query;
    }

    /**
     * @todo update Members to Role
     * @author SonNA
     * @see \App\Repositories\RoleUsers\RoleUsersRepositoryInterface::update()
     */
    public function update($data, $id , $attribute = 'id'){
        $query = RoleUsers::where($attribute,$id)->update($data);
        return $query;
    }

    /**
     * @todo Delete Members in Role
     * @author SonNA
     * @see \App\Repositories\RoleUsers\RoleUsersRepositoryInterface::delete()
     */
    public function delete($id){
        $query = RoleUsers::where('role_id', $id);
        $query->delete();
        return $query;
    }

    /**
     * @todo find Member by Role id
     * @author SonNA
     * @see \App\Repositories\RoleUsers\RoleUsersRepositoryInterface::find()
     */
    public function find($id){
        $query = RoleUsers::find($id);
        return $query;
    }

    /**
     * @todo find role by other field
     * @author SonNA
     * @see \App\Repositories\RoleUsers\RoleUsersRepositoryInterface::findBy()
     */
    public function findBy($attribute, $value){
        $query = RoleUsers::where($attribute,$value);
        return $query;
    }
}