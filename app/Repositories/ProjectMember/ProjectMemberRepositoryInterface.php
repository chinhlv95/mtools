<?php
namespace App\Repositories\ProjectMember;

interface ProjectMemberRepositoryInterface
{
    public function getMembersAssigned($project_id);

    public function checkAssignedMember($id);

    public function emailAutocomplete($query);

    public function find($id);

    public function delete($id);

    public function update($data, $id);

    public function restoreOrRemove($id, $status);

    public function findByAttribute($att, $name);

    public function findByAttributes($att1, $name1,$att2, $name2);

    public function save($data);

    public function getUserInProject($listProjectId);

    public function getMemberOrder($projectId, $getDepartment, $getDivision, $getTeam, $projectMemberJoin);

    public function getMemberInPersonalCost($projectId, $getDepartment, $getDivision, $getTeam, $projectMemberJoin);

    public function updatePmFromCrawler($data, $id);

    public function checkWhetherProjectIsNull($query, $projectId, $getDepartment, $getDivision, $getTeam, $projectMemberJoin);

    public function getApiProjectMember();

    public function getAllMembersInProjects();

    public function getAllMembersInAProject();

    public function countTicketsWithBugWeightOfMember($typeRelatedId,
                    $startDate, $endDate, $prositions, $userFlag = 0);

    public function getWorkLoadOfAMember($startDate, $endDate, $positions, $activityRelatedId = 0);

    public function getLocOfDevs($startDate, $endDate);

    public function countTasksOfMembers($startDate, $endDate, $potision);

    public function getLocOfAMemberForApi($startDate, $endDate);

    public function getWorkLoadOfAMemberForApi($startDate, $endDate,  $activityRelatedId = 0);

    public function countTicketsWithBugWeightOfMemberForApi($typeRelatedId,
            $weightRelatedId, $startDate, $endDate, $userFlag = 0);

    public function countTicketsWithTicketTypeOfMemberForApi($startDate, $endDate);

    public function countWeightBugsOfMemberOnProjectsForApi($typeRelatedId,
            $weightRelatedId, $startDate, $endDate, $userFlag);

    public function countTicketsWithTicketTypeOfMemberOnProjectsForApi($typeRelatedId, $startDate, $endDate);

    public function getLocOfAMemberOnProjectsForApi($startDate, $endDate);

    public function getWorkLoadOfAMemberOnProjectsForApi($startDate, $endDate, $activityRelatedId = 0);

    public function getLocOfAMemberInProjects($startDate, $endDate);

    public function getWorkLoadOfAMemberInProjects($startDate, $endDate, $activityRelatedId = 0);

    public function countTicketsWithBugWeightOfMemberInProjects($typeRelatedId,
            $weightRelatedId, $startDate, $endDate);

    public function countTicketsWithTicketTypeOfMemberInProjects($typeRelatedId,
            $startDate, $endDate);

    public function saveProjectMember($projectId, $userId, $roleId);

    public function getTestCaseOfAMemberForApi($startDate, $endDate, $activityRelatedId);

    public function getTestCaseOfAMember($startDate, $endDate, $activityRelatedId);

    public function getTestCaseOfAMemberOnProjectForApi($startDate, $endDate, $activityRelatedId);

    public function getSubMemberOrder($userId, $projectId, $getDepartment, $getDivision, $getTeam, $projectMemberJoin);

    public function getPermissionOfAMemberInProject($userId, $projectId);

}