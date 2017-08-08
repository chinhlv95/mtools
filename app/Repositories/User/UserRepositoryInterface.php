<?php
namespace App\Repositories\User;

interface UserRepositoryInterface
{
    public function all();

    public function paginate($quantity);

    public function find($id);

    public function save($data);

    public function delete($id);

    public function findByAttribute($att, $name);

    public function saveUserByUsername($username, $email, $firstName, $lastName, $sourceId = 0);

    public function findByAttributes($att1, $name1, $att2, $name2);

    public function apiMember();

    public function getBseInUserTable();

    public function update($data, $id);

    public function findUserLike($key);

    public function getUserManagement($roleSearch,$name,$limit,$status,$page,$request, $type);

    public function getAdminOrDirectorId();

    public function getManagerId();

    public function getUserMapping($sourceId, $name);

    public function getAllEmails();

    public function getFullName();

    public function getSubUsers($userId);

    public function getUsersNotSub($userId);

    public function getListUserOfTeam($teamId);
}