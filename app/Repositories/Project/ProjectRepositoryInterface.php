<?php

namespace App\Repositories\Project;

interface ProjectRepositoryInterface
{
    public function all();

    public function get();

    public function paginate($quantity);

    public function find($id);

    public function save($data);

    public function saveProjectFromCrawler($data);

    public function delete($id);

    public function update($data, $id);

    public function updateProjectFromCrawler($data, $id);

    public function findByAttribute($attr, $data);

    public function searchInProjectList($role_id, $projectMemberJoin, $type_id, $project_name, $status, $bse, $department_id, $division_id, $team_id, $language_id,$limit);

    public function apiDepartment();

    public function filterData($id,$fillterData);

    public function saveDataCrawler($data, $id);

    public function findByAttributes($att1, $name1, $att2, $name2);

    public function getProjectsByAttribute($attr, $data, $flag);

    public function getProjectsToUpdateFirstly($attr, $data);

    public function getDataPlanEffort($project_id);

    public function getDataActualEffort($project_id);

    public function getLocOfAProject($projectIds, $startDate, $endDate);

    public function getActualHour($projectIds, $startDate, $endDate);

    public function getTicketsWithBugWeight($projectIds, $typeRelatedId, $weightRelatedId, $startDate, $endDate);

    public function getProjectMemberJoin($userId, $checkGroup);

    public function getDepDevTeam($listDepartmentId);

    public function getGroupProjectMemberJoin($permissionNeedCheck);

    public function countTicketsWithTicketType($projectIds, $typeRelatedId, $startDate, $endDate);

    public function getPlanEffortWithDate($projectIds, $startDate, $endDate);

    public function getActualHourWithTracker($projectIds, $startDate, $endDate, $typeRelatedId);

    public function getActualHourWithActivity($projectIds, $startDate, $endDate, $activityRelatedId);

    public function getActualHourWithActivityWithoutTracker($projectIds, $startDate, $endDate, $activityRelatedId, $withOutId1, $withOutId2);

    public function getDatasByAttribute($params,$attribute,$iterite);

    public function getTestcaseWithActivity($projectIds, $startDate, $endDate, $activityRelatedId);

    public function getProjectInSearch($projectIdSearch, $getDepartment, $getDivision, $getTeam, $getStatus, $projectMemberJoin);

    public function getTimeSearch($time, $defaultTime, $requestStartDate, $requestEndDate);

    public function saveDeparamentSearch($getDepartment,$getDivision,$getTeam,$divisions,$teams,$projects);

    public function getDepartmentWhichManagerManage($managerId);

    public function getProjectRole($project_id, $userId);
}