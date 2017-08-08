<?php
namespace App\Repositories\ProjectVersion;
/**
 *
 * Sep 22, 201610:19:11 AM
 * @author tampt6722
 *
 */
interface ProjectVersionRepositoryInterface
{

    public function all();

    public function paginate($quantity);

    public function find($id);

    public function delete($id);

    public function save($data);

    public function update($data, $id);

    public function findByAttribute($att, $name);

    public function findByAttributes($att1, $name1, $att2, $name2);

    public function getDataJoinTicketAndVersion();

    public function getDataJoinTicketAndEntries();

    public function getDataTaskInVersion();

    public function getVersionByAttribute($attribute, $name);

    public function getVersionId ($inteVersionId, $inteVersionName, $projectId, $sourceId);

    public function getDataVersionAndEntriesTicket($project_id, $page, $request);

    public function  paginateCollection($perPage,$page,$request,$collection);
}