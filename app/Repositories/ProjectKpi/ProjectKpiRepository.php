<?php

namespace App\Repositories\ProjectKpi;

use DateTime;
use App\Models\Project;
use App\Models\ProjectKpi;
use Illuminate\Support\Facades\Config;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\QualityReport\QualityReportByProjectRepositoryInterface;

class ProjectKpiRepository implements ProjectKpiRepositoryInterface
{
    public function __construct(ProjectRepositoryInterface $project,
                                QualityReportByProjectRepositoryInterface $report)
    {
        $this->project    = $project;
        $this->report     = $report;
    }

    /**
     * @todo Get first Base line KPI
     * @author thanhnb6719
     * @param int $projectId
     */
    public function getFirstBaselineKpi($projectId) {
        $baseKpi = ProjectKpi::where('project_id', $projectId)
                           ->where('baseline_flag', 2)
                           ->orderBy('end_date','DESC')
                           ->first();
        return $baseKpi;
    }

    /**
     * @todo Get data of Kpi with id of project and type of kpi
     *
     * @author thanhnb6719
     * @param int $projectId
     * @param int $baseLineFlag
     * @param date $startDate
     * @param date $endDate
     * @see \App\Repositories\ProjectKpi\ProjectKpiRepositoryInterface::getDataOfKpiFollowFlag()
     */
    public function getDataOfKpiFollowFlag($projectId, $baseLineFlag, $startDate, $endDate){
        $query = ProjectKpi::where('project_id', $projectId)
                            ->where('baseline_flag', $baseLineFlag);
        if ($baseLineFlag == 2) {
            $data = $query->where(function ($date) use ($startDate, $endDate) {
                             $date->where('start_date', '>=', $startDate)
                                  ->orWhere('end_date', '<=', $endDate);
                          })
                          ->orderBy('start_date','ASC')
                          ->get();
        } else {
            $data = $query->where('start_date', '>=', $startDate)
                          ->where('start_date', '<=', $endDate)
                          ->orderBy('start_date','ASC')
                          ->get();
        }
         return $data;
    }

    /**
     * @todo Get metric follow date
     *
     * @author thanhnb6719
     * @param int $projectIds
     * @param date $startDate
     * @param date $endDate
     * @return array $metric
     */
    public function getMetricFollowDate($projectIds, $startDate, $endDate)
    {
        $mm                        = Config::get('constant.men_month');
        // Total cost from project start to end of this week
        $totalPlanEffort           = $this->project->getPlanEffortWithDate($projectIds, $startDate, $endDate)->estimate_time;
        $totalActualEffort         = $this->project->getActualHour($projectIds, $startDate, $endDate)->actual_hour;
        $totalMmCost               = $totalActualEffort/$mm;
        // Total cost of each tracker or activities relate bug
        $bugCost                   = $this->project->getActualHourWithTracker($projectIds, $startDate, $endDate, 9)->actual_hour;
        $uatBugCost                = $this->project->getActualHourWithTracker($projectIds, $startDate, $endDate, 10)->actual_hour;
        $actualFixBug              = $this->project->getActualHourWithActivityWithoutTracker($projectIds, $startDate, $endDate, 27, 9, 10)->actual_hour;
        $actualConfirmTest         = $this->project->getActualHourWithActivityWithoutTracker($projectIds, $startDate, $endDate, 25, 9, 10)->actual_hour;
        $totalBugCost              = $bugCost + $uatBugCost + $actualFixBug + $actualConfirmTest;
        // Bug weight
        $bugs                      = $this->report->getWeightedBugsForProject(9, $startDate, $endDate, $projectIds);
        $bugsUAT                   = $this->report->getWeightedBugsForProject(10, $startDate, $endDate, $projectIds);
        $bugWeight                 = $this->countWeightedBugsForProject($bugs);
        $uatWeight                 = $this->countWeightedBugsForProject($bugsUAT);
        $totalBugWeight            = $bugWeight + $uatWeight;
        // Loc
        $totalLoc                  = $this->project->getLocOfAProject($projectIds, $startDate, $endDate)->loc;
        if ($totalActualEffort == null || $totalActualEffort == 0) {
            $actualCostEfficiency       = null;
            $actualFixingCost           = null;
            $leakage                    = null;
            $defectRate                 = null;
            $codeProductivity           = null;
        } else {
            // Cost efficiency          = (total plan cost / total actual cost )*100
            $actualCostEfficiency       = $this->roundDataPercent($totalPlanEffort, $totalActualEffort);
            // Fixing cost              = ((cost of tracker(bug + bug after release) + cost of activities(fix bug + confirm bug)) / total actual cost) * 100
            $actualFixingCost           = $this->roundDataPercent($totalBugCost, $totalActualEffort);
            // Leakage                  = (weighted of bug after release / mmCost)
            $leakage                    = $uatWeight/$totalMmCost;
            // Defect rate              = ((weighted of bug after release + weighted of bug) / mmCost)
            $defectRate                 = $totalBugWeight/$totalMmCost;
            // Code productivity        = Total LOC / mmCost
            $codeProductivity           = round($totalLoc/$totalMmCost,2);
        }
        // Number bug of release
        $numberBugOfRelease         = $this->project->countTicketsWithTicketType($projectIds, 10, $startDate, $endDate)->countId;
        // Weighted bug of release  = $uatWeight
        // Custom survey            = $actualCustomerSurvey
        // Defect remove efficiency = (weight of bug / ( weight of bug + weight of UAT bug))*100
        $defectRemoveEfficiency     = $this->roundDataPercent($bugWeight, $totalBugWeight);
        if ($defectRemoveEfficiency == "NA") {
            $defectRemoveEfficiency = null;
        }
        // Testcase productivity    = Total number testcase / mmCost ( activities = Make testcase)
        $testCaseProductivity       = $this->project->getTestcaseWithActivity($projectIds, $startDate, $endDate, 19);
        // Tested producactivity    = Total number testcase / mmCost ( activities= Testing)
        $testedProductivity         = $this->project->getTestcaseWithActivity($projectIds, $startDate, $endDate, 21);
        $metric = [
            'costEfficiency'       => $actualCostEfficiency,
            'fixingBugCost'        => $actualFixingCost,
            'leakage'              => $leakage,
            'numberUATBug'         => $numberBugOfRelease,
            'weightUATBug'         => $uatWeight,
            'defectRate'           => $defectRate,
            'defectRemove'         => $defectRemoveEfficiency,
            'codeProductivity'     => $codeProductivity,
            'testCaseProductivity' => $testCaseProductivity,
            'testedProductivity'   => $testedProductivity
        ];
        return $metric;
    }

    /**
     * @todo Prepare for save Kpi data
     *
     * @author thanhnb6719
     * @param int $project_id
     * @param date $startProject
     * @see \App\Repositories\ProjectKpi\ProjectKpiRepositoryInterface::saveSyncKpi()
     */
    public function saveSyncKpi($project_id, $startProject) {
        if (ceil(abs(strtotime("today") - strtotime($startProject)) / 86400) < 730) {
            // If period time < 2 year. Sync up all KPI data in the past.
            $startDate  = $startProject;
            $endDate    = date('Y-m-d', strtotime('today'));
            $months     = \Helpers::findMonthInPeriodOfTime($startDate, $endDate);
            foreach ($months as $m) {
                $startMonth   = $m->format('Y-m-d');
                $endMonth     = date('Y-m-t', strtotime($startMonth));
                $metricMonth  = $this->getMetricFollowDate($project_id, $startProject, $endMonth);
                $saveMonth    = $this->saveKpi(1, $project_id, $startMonth, $endMonth, $metricMonth);
            }
            $weeks     = \Helpers::findWeekInPeriodOfTime($startDate, $endDate);
            foreach ($weeks as $w) {
                $startWeek   = $w->format('Y-m-d');
                $endWeek     = date('Y-m-d', strtotime($startWeek.'+ 6 days'));
                $metricWeek  = $this->getMetricFollowDate($project_id, $startProject, $endWeek);
                $saveWeek    = $this->saveKpi(0, $project_id, $startWeek, $endWeek, $metricWeek);
            }
        } else {
            // Else Sync up KPI data in 2 year period.
            // Save month
            for ($i = 1; $i < 24; $i++) {
                $checkDate  = 23 - $i;
                $sLastMonth = date('Y-m-d', strtotime('first day of last month'));
                $eLastMonth = date('Y-m-d', strtotime('last day of last month'));
                if ($i==23) {
                    $startMonth = $sLastMonth;
                    $endMonth   = $eLastMonth;
                } else {
                    $startMonth = date('Y-m-d', strtotime('-'.$checkDate.' month',strtotime($sLastMonth)));
                    $endMonth   = date('Y-m-d', strtotime('-'.$checkDate.' month',strtotime($eLastMonth)));
                }
                $metricMonth  = $this->getMetricFollowDate($project_id, $startProject, $endMonth);
                $saveMonth    = $this->saveKpi(1, $project_id, $startMonth, $endMonth, $metricMonth);
            }
            // Save weeks
            for ($i = 1; $i < 104; $i++) {
                $wCheckDate = 103-$i;
                $sLastWeek   = date('Y-m-d', strtotime('monday last week'));
                $eLastWeek   = date('Y-m-d', strtotime('sunday last week'));
                if ($i==103) {
                    $startWeek  = $sLastWeek;
                    $endWeek    = $eLastWeek;
                } else {
                    $startWeek  = date('Y-m-d', strtotime('-'.$wCheckDate.' week',strtotime($sLastWeek)));
                    $endWeek    = date('Y-m-d', strtotime('-'.$wCheckDate.' week',strtotime($eLastWeek)));
                }
                $metricWeek  = $this->getMetricFollowDate($project_id, $startProject, $endWeek);
                $saveWeek    = $this->saveKpi(0, $project_id, $startWeek, $endWeek, $metricWeek);
            }
        }
    }

    /**
     * @todo Save Kpi to database
     *
     * @author thanhnb6719
     * @param unknown $check
     * @param unknown $project_id
     * @param unknown $startDate
     * @param unknown $endDate
     * @param unknown $metric
     * @return string
     */
    public function saveKpi($check, $project_id, $startDate, $endDate, $metric) {
        $date        = new DateTime($startDate);
        $year        = $date->format("Y");
        if ($check == 1) {
            $month       = $date->format("M");
            $nameKpi     = $month."/".$year;
        } elseif ($check == 0) {
            $week        = $date->format("W");
            $nameKpi     = "W".$week."/".$year;
        }
        $checkData   = ProjectKpi::where('project_id', $project_id)
                                 ->where('name', $nameKpi)
                                 ->first();
        $checkProject = Project::where('id', $project_id)
                                ->where('active', 1)
                                ->first();
        $getBaseline = ProjectKpi::where('project_id', $project_id)
                                 ->where('baseline_flag', 2)
                                 ->where('start_date', '<', $startDate)
                                 ->where('end_date', '>', $endDate)
                                 ->orderBy('created_at', 'desc')
                                 ->first();
        if ($getBaseline == null) {
            $planCostEfficiency         = 0;
            $planFixCost                = 0;
            $planLeakage                = 0;
            $planCustomerSurvey         = 0;
            $actualCustomerSurvey       = 0;
            $planBugAfterReleaseNumber  = 0;
            $planBugAfterReleaseWeight  = 0;
            $planDefectRemoveEfficiency = 0;
            $planDefectRate             = 0;
            $planCodeProductivity       = 0;
            $planTestCaseProductivity   = 0;
            $planTestedProductivity     = 0;
            $description                = "Baseline is not exist!";
        } else {
            $planCostEfficiency         = $getBaseline->plan_cost_efficiency;
            $planFixCost                = $getBaseline->plan_fix_code;
            $planLeakage                = $getBaseline->plan_leakage;
            $planCustomerSurvey         = $getBaseline->plan_customer_survey;
            $actualCustomerSurvey       = $getBaseline->actual_customer_survey;
            $planBugAfterReleaseNumber  = $getBaseline->plan_bug_after_release_number;
            $planBugAfterReleaseWeight  = $getBaseline->plan_bug_after_release_weight;
            $planDefectRemoveEfficiency = $getBaseline->plan_defect_remove_efficiency;
            $planDefectRate             = $getBaseline->plan_defect_rate;
            $planCodeProductivity       = $getBaseline->plan_code_productivity;
            $planTestCaseProductivity   = $getBaseline->plan_test_case_productivity;
            $planTestedProductivity     = $getBaseline->plan_tested_productivity;
            $description                = $getBaseline->description;
        }
        if ($checkProject != null) {
            try {
                if ($checkData == null) {
                    $projectKpi                              = new ProjectKpi();
                } else {
                    $projectKpi                              = ProjectKpi::find($checkData->id);
                }
                $projectKpi->name                            = $nameKpi;
                $projectKpi->project_id                      = $project_id;
                $projectKpi->baseline_flag                   = $check;
                $projectKpi->start_date                      = $startDate;
                $projectKpi->end_date                        = $endDate;
                $projectKpi->actual_cost_efficiency          = $metric['costEfficiency'];
                $projectKpi->plan_cost_efficiency            = $planCostEfficiency;
                $projectKpi->actual_fix_code                 = $metric['fixingBugCost'];
                $projectKpi->plan_fix_code                   = $planFixCost;
                $projectKpi->actual_leakage                  = $metric['leakage'];
                $projectKpi->plan_leakage                    = $planLeakage;
                $projectKpi->actual_customer_survey          = $actualCustomerSurvey;
                $projectKpi->plan_customer_survey            = $planCustomerSurvey;
                $projectKpi->actual_bug_after_release_number = $metric['numberUATBug'];
                $projectKpi->plan_bug_after_release_number   = $planBugAfterReleaseNumber;
                $projectKpi->actual_bug_after_release_weight = $metric['weightUATBug'];
                $projectKpi->plan_bug_after_release_weight   = $planBugAfterReleaseWeight;
                $projectKpi->actual_defect_remove_efficiency = $metric['defectRemove'];
                $projectKpi->plan_defect_remove_efficiency   = $planDefectRemoveEfficiency;
                $projectKpi->actual_defect_rate              = $metric['defectRate'];
                $projectKpi->plan_defect_rate                = $planDefectRate;
                $projectKpi->actual_code_productivity        = $metric['codeProductivity'];
                $projectKpi->plan_code_productivity          = $planCodeProductivity;
                $projectKpi->actual_test_case_productivity   = $metric['testCaseProductivity'];
                $projectKpi->plan_test_case_productivity     = $planTestCaseProductivity;
                $projectKpi->actual_tested_productivity      = $metric['testedProductivity'];
                $projectKpi->plan_tested_productivity        = $planTestedProductivity;
                $projectKpi->description                     = $description;
                $projectKpi->save();
            } catch (Exception $e) {
                $error = "Error: ".$project_id." - ".$e->getMessage();
                return $error;
            }
        }
    }

    /**
     * Count weighted bugs in a project
     *
     * @author tampt6722
     *
     * @param array $bugs
     * @param integer $id
     * @return number
     */
    public function countWeightedBugsForProject($bugs)
    {
        $count = [];
        $bugWeight = 0;
        $weight = Config::get('constant.bug_weight');
        for ($i = 1; $i <= 5; $i++) {
            $count[$i] = $bugs[$i]['countId'];
        }
        foreach ($weight as $key => $value) {
            switch ($key) {
                case 1:
                    $bugWeight += $count[1] * $value;
                    break;
                case 2:
                    $bugWeight += $count[2] * $value;
                    break;
                case 3:
                    $bugWeight += $count[3] * $value;
                    break;
                case 4:
                    $bugWeight += $count[4] * $value;
                    break;
                case 5:
                    $bugWeight += $count[5] * $value;
                    break;
                default: break;
            }
        }
        return $bugWeight;
    }

    /**
     *
     * @author tampt6722
     *
     * @param number $numerator
     * @param number $denominator
     * @param integer $mm
     * @return number|string
     */
    public function roundDataMm($numerator, $denominator, $mm)
    {
        if ($denominator != 0) {
            $data = round($numerator/$denominator, 2)/$mm;
        } else {
            $data = 'NA';
        }
        return $data;
    }

    /**
     *
     * @author tampt6722
     *
     * @param number $numerator
     * @param number $denominator
     * @return number|string
     */
    public function roundDataPercent($numerator, $denominator)
    {
        if ($denominator != 0) {
            $data = round((($numerator/$denominator) * 100), 2);
        } else {
            $data = 'NA';
        }
        return $data;
    }

    /**
     * @todo Update project Kpi Baseline
     *
     * @author thanhnb6719
     *
     */
    public function updateBaselineKpi($id, $project_id, $data){
        $projectKpi                                  = ProjectKpi::find($id);
        $projectKpi->name                            = $data['name'];
        $projectKpi->project_id                      = $project_id;
        $projectKpi->baseline_flag                   = 2;
        $projectKpi->start_date                      = \Helpers::formatDateYmd($data['start_date']);
        $projectKpi->end_date                        = \Helpers::formatDateYmd($data['end_date']);
        $projectKpi->actual_cost_efficiency          = $data['actual_cost_efficiency'];
        $projectKpi->plan_cost_efficiency            = $data['plan_cost_efficiency'];
        $projectKpi->actual_fix_code                 = $data['actual_fix_code'];
        $projectKpi->plan_fix_code                   = $data['plan_fix_code'];
        $projectKpi->actual_leakage                  = $data['actual_leakage'];
        $projectKpi->plan_leakage                    = $data['plan_leakage'];
        $projectKpi->actual_customer_survey          = $data['plan_customer_survey'];
        $projectKpi->plan_customer_survey            = $data['plan_customer_survey'];
        $projectKpi->actual_bug_after_release_number = $data['actual_bug_after_release_number'];
        $projectKpi->plan_bug_after_release_number   = $data['plan_bug_after_release_number'];
        $projectKpi->actual_bug_after_release_weight = $data['actual_bug_after_release_weight'];
        $projectKpi->plan_bug_after_release_weight   = $data['plan_bug_after_release_weight'];
        $projectKpi->actual_defect_remove_efficiency = $data['actual_defect_remove_efficiency'];
        $projectKpi->plan_defect_remove_efficiency   = $data['plan_defect_remove_efficiency'];
        $projectKpi->actual_defect_rate              = $data['actual_defect_rate'];
        $projectKpi->plan_defect_rate                = $data['plan_defect_rate'];
        $projectKpi->actual_code_productivity        = $data['actual_code_productivity'];
        $projectKpi->plan_code_productivity          = $data['plan_code_productivity'];
        $projectKpi->actual_test_case_productivity   = $data['actual_test_case_productivity'];
        $projectKpi->plan_test_case_productivity     = $data['plan_test_case_productivity'];
        $projectKpi->actual_tested_productivity      = $data['actual_tested_productivity'];
        $projectKpi->plan_tested_productivity        = $data['plan_tested_productivity'];
        $projectKpi->description                     = $data['description'];
        $projectKpi->save();
    }

    /**
     * @todo Update KPI weeks or months after create new Kpi Baseline
     *
     * @author thanhnb6719
     * @param int $baselineId
     * @param int $projectId
     * @see \App\Repositories\ProjectKpi\ProjectKpiRepositoryInterface::updateBaselineForProjectKpi()
     */

    public function updateBaselineForProjectKpi($baselineId, $projectId, $delete = null){
        $lastBaseline = ProjectKpi::find($baselineId);
        if ($delete == null) {
            $listProjectKpi = ProjectKpi::where('project_id',$projectId)
                                        ->whereIn('baseline_flag', [0,1])
                                        ->where('start_date', '>=', $lastBaseline->start_date)
                                        ->where('end_date', '<=', $lastBaseline->end_date)
                                        ->update(['plan_cost_efficiency'          => $lastBaseline->plan_cost_efficiency,
                                                 'plan_fix_code'                  => $lastBaseline->plan_fix_code,
                                                 'plan_leakage'                   => $lastBaseline->plan_leakage,
                                                 'plan_customer_survey'           => $lastBaseline->plan_customer_survey,
                                                 'plan_bug_after_release_number'  => $lastBaseline->plan_bug_after_release_number,
                                                 'plan_bug_after_release_weight'  => $lastBaseline->plan_bug_after_release_weight,
                                                 'plan_defect_remove_efficiency'  => $lastBaseline->plan_defect_remove_efficiency,
                                                 'plan_defect_rate'               => $lastBaseline->plan_defect_rate,
                                                 'plan_code_productivity'         => $lastBaseline->plan_code_productivity,
                                                 'plan_test_case_productivity'    => $lastBaseline->plan_test_case_productivity,
                                                 'plan_tested_productivity'       => $lastBaseline->plan_tested_productivity,
                                                 'description'                    => $lastBaseline->description,
                                        ]);
        } else {
            $listProjectKpi = ProjectKpi::where('project_id',$projectId)
                                        ->whereIn('baseline_flag', [0,1])
                                        ->where('start_date', '>=', $lastBaseline->start_date)
                                        ->where('end_date', '<=', $lastBaseline->end_date)
                                        ->update(['plan_cost_efficiency'          => '0',
                                                  'plan_fix_code'                 => '0',
                                                  'plan_leakage'                  => '0',
                                                  'plan_customer_survey'          => '0',
                                                  'plan_bug_after_release_number' => '0',
                                                  'plan_bug_after_release_weight' => '0',
                                                  'plan_defect_remove_efficiency' => '0',
                                                  'plan_defect_rate'              => '0',
                                                  'plan_code_productivity'        => '0',
                                                  'plan_test_case_productivity'   => '0',
                                                  'plan_tested_productivity'      => '0',
                                                  'description'                   => 'Baseline is not exist!',
                                        ]);
        }
    }
}
