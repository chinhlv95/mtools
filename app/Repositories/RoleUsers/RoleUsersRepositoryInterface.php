<?php
namespace App\Repositories\RoleUsers;

/**
 *
 * Sep 22, 201610:19:11 AM
 * @author SonNA6229
 *
 */

interface RoleUsersRepositoryInterface
{
    public function all();

    public function create($data);

    public function update($data, $id);

    public function delete($id);

    public function find($id);

    public function findBy($field, $value);

}