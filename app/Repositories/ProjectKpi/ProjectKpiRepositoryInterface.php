<?php
namespace App\Repositories\ProjectKpi;

interface ProjectKpiRepositoryInterface
{
    public function getFirstBaselineKpi($projectId);

    public function getDataOfKpiFollowFlag($projectId, $baseLineFlag, $startDate, $endDate);

    public function getMetricFollowDate($projectIds, $startDate, $endDate);

    public function saveSyncKpi($project_id, $startProject);

    public function updateBaselineKpi($id, $project_id, $data);

    public function saveKpi($check, $project_id, $startDate, $endDate, $metric);

    public function updateBaselineForProjectKpi($baselineId, $projectId);
}