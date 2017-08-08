<?php
namespace App\Repositories\QualityReport;

interface QualityReportByPmRepositoryInterface
{
    public function getDataMemberReportWithProjectApi($request);
    public function countDataByMember($objs, $userId, $projectId, $roleName, $att);
    public function getWeightedBugOfMemberOnProjectsForApi($key, $startDate, $endDate, $userFlag);
    public function countWeightedBugsOfMember($bugs, $userId, $projectId, $roleName);
    public function getTimeSearchReport($defaultTime);
    public function getDataMemberInProjectsReport($reportFlag, $startDate, $endDate, $nameReport);
    public function getDistinctData($datas);
    public function getDistinctDataForApi($datas, $flag = 0);
}