<?php
namespace App\Repositories\Loc;

interface LocRepositoryInterface
{
    public function all();

    public function paginate($quantity);

    public function find($id);

    public function delete($id);

    public function save($data);

    public function update($data, $id);

    public function findByAttribute($att, $name);

    public function getDataByAttribute($att, $name);

    public function findByAttributes($att1, $name1, $att2, $name2);

    public function findByTriAttributes($att1, $name1, $att2, $name2, $att3, $name3);

    public function updateLoc($projectId, $ticketId, $pUserId, $loc, $integratedCreatedAt, $integratedUpdatedAt);

    public function saveloc($projectId, $ticketId, $pUserId, $loc, $integratedCreatedAt, $integratedUpdatedAt);
}