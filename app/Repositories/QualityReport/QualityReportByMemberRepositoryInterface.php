<?php
namespace App\Repositories\QualityReport;
interface QualityReportByMemberRepositoryInterface
{

    public function countDataByMember($objs,$projectId, $id, $att);

    public function getWeightedBugOfMemberForApi($key, $startDate, $endDate, $userFlag);

    public function countWeightedBugsOfMember($bugs, $projectId, $userId);

    public function getDataMemberReportApi($request);

}