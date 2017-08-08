<?php
namespace App\Repositories\MemberReport;
interface MemberReportRepositoryInterface
{
    public function all();

    public function paginate($quantity);

    public function find($id);

    public function delete($id);

    public function deleteAllData();

    public function save($data);

    public function update($data, $id);

    public function checkExistedData($departmentId, $projectId, $userId, $year, $month);

    public function getDevData($year, $months, $departmentId, $divisionId,
                                            $teamId, $projectId,$projectMemberJoin);
    public function getQAData($year, $months, $departmentId, $divisionId,
            $teamId, $projectId,$projectMemberJoin);

    public function getDataMemberToReport($year, $month, $position, $departmentId, $divisionId, $teamId, $projectId, $projectMemberJoin);

    public function saveDataMember ($startDate, $endDate, $year, $month);
}
