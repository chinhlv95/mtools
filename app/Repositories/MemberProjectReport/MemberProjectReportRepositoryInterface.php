<?php
namespace App\Repositories\MemberProjectReport;

/**
 *
 * Dec 20, 2016 3:25:36 PM
 * @author tampt6722
 *
 */
interface MemberProjectReportRepositoryInterface
{
    public function all();

    public function paginate($quantity);

    public function find($id);

    public function delete($id);

    public function save($data);

    public function update($data, $id);

    public function findByAttribute($att, $name);

    public function findByAttributes($att1, $name1, $att2, $name2);

    public function getDataReport($reportFlag, $position, $projectId,
            $startDate, $endDate, $getDepartment, $getDivision, $getTeam, $projectMemberJoin, $getStatus);

    public function getNameDate($reportFlag, $startDate, $endDate);

    public function getUser($reportFlag, $position, $startDate, $endDate);
}