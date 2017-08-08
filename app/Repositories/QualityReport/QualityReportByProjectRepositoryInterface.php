<?php
namespace App\Repositories\QualityReport;
interface QualityReportByProjectRepositoryInterface
{
    public function getDataProjectMemberApi();
    public function getDataProjectReportApi ($request);
    public function roundData($numerator, $denominator, $percent = 0);
    public function countWeightedBugsForProject($bugs, $id);
    public function getWeightedBugsForProject ($key, $startDate, $endDate, $projectIds);
    public function count($objs, $id, $att);
    public function getDataProjectList($projects, $startDate, $endDate, $mm);
}