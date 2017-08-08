<?php
namespace App\Repositories\Status;

/**
 *
 * @author tampt6722
 *
 */
interface StatusRepositoryInterface
{
    public function all();

    public function paginate($quantity);

    public function find($id);

    public function delete($id);

    public function save($data);

    public function update($data, $id);

    public function findByAttribute($att, $name);

    public function findByAttributes($att1, $name1, $att2, $name2);

    public function getTicketsByStatus($start_date, $end_date,$getDepartment, $getDivision, $getTeam, $projectMemberJoin, $project_id);

    public function getTicketsByUser($start_date, $end_date,$getDepartment, $getDivision, $getTeam, $projectMemberJoin, $project_id);

    public function getStatusName($tickets_status,$name_unique);

    public function getStatusIdDefault();

    public function getStatusId($integratedStatusId, $statusName, $sourceId);
}