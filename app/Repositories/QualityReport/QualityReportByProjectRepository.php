<?php
namespace App\Repositories\QualityReport;

use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\Cost\CostRepositoryInterface;
use App\Repositories\ProjectMember\ProjectMemberRepositoryInterface;
use Illuminate\Support\Facades\Config;
use App\Models\ProjectReport;
use Helpers;
use DB;

class QualityReportByProjectRepository
                implements QualityReportByProjectRepositoryInterface {

    public function __construct(ProjectRepositoryInterface $project,
            ProjectMemberRepositoryInterface $pm,
            ProjectReport $report)
    {
        $this->project = $project;
        $this->projectMember = $pm;
        $this->report = $report;
    }

    /**
     * Get projects' data served report quality and productivity
     * @author tampt6722
     *
     * @param CostRepositoryInterface $projects
     * @param \DateTime $startDate
     * @param DateTime $endDate
     * @return number[][]
     */
    public function getDataProjectList($projects, $startDate, $endDate, $mm) {
        $pQuality = [];
        if (count($projects) > 0) {
            foreach ($projects as $project) {
                $projectIds[] = $project->id;
            }
        } else {
            $projectIds = [];
        }
        if (!empty($projectIds)) {
            $testCases = $this->project->getTestcaseWithActivity1($projectIds, $startDate, $endDate, 21);
            $tLoc = $this->project->getLocOfAProject($projectIds, $startDate, $endDate); // Get tickets have loc in a project
            $tEntries = $this->project->getActualHour($projectIds, $startDate, $endDate); // Get actual hour of tickets in a project
            $bugs = $this->getWeightedBugsForProject(9, $startDate, $endDate, $projectIds);
            $bugsUAT = $this->getWeightedBugsForProject(10, $startDate, $endDate, $projectIds);

            foreach ($projectIds as $id) {
                $countLoc = $this->count($tLoc, $id, 'loc'); // Get line of code of a project
                $kLoc = $countLoc/1000;
                $countEntry = $this->count($tEntries, $id, 'actual_hour'); // Get workload of a project
                $workload = $this->roundData($countEntry, $mm); // workload per men month
                $testCase = $this->count($testCases, $id, 'test_case'); // get total test case
                $bugWeight = $this->countWeightedBugsForProject($bugs, $id); // Get weighted bug
                $uatWeight = $this->countWeightedBugsForProject($bugsUAT, $id); // Get weighted uat bug
                $totalBugs = $bugWeight + $uatWeight;
                $bWPerMm = $this->roundData($bugWeight, $workload); // weighted bug per men month
                $tCPerMm = $this->roundData($testCase, $workload);
                $bugPerTc = $this->roundData($bugWeight, ($testCase/1000));
                $kLocPermm = $this->roundData($kLoc, $workload); // kLOC per men month
                $bugBeforeRelease = $this->roundData($bugWeight, $totalBugs, 100); // bugs before release (in percent)
                $bWPerKloc = $this->roundData($bugWeight, $kLoc); //weighted bug per kloc
                $bUatPerKloc = $this->roundData($uatWeight, $kLoc); // weighted uat bug per kloc
                $pQuality[] = [
                                'projectId' => $id,
                                'countEntry' => $countEntry,
                                'testCase' => $testCase,
                                'loc' => $countLoc,
                                'workload' => $workload,
                                'bugWeight' => $bugWeight,
                                'uatWeight' => $uatWeight,
                                'bWPerKloc' => $bWPerKloc,
                                'bUatPerKloc' => $bUatPerKloc,
                                'bugPerTc' => $bugPerTc,
                                'bWPerMm' => $bWPerMm,
                                'kLocPermm' => $kLocPermm,
                                'bugBeforeRelease' => $bugBeforeRelease,
                                'tCPerMm' => $tCPerMm,
                ];
            }
        }

        return $pQuality;
    }

    /**
     * Get total data loc or entries or bugs for a project
     *
     * @author tampt6722
     *
     * @param array $objs
     * @param integer $id
     * @param string $att
     * @return number
     */
    public function count($objs, $id, $att)
    {
        $count = 0;
        if (count($objs) > 0) {
            foreach ($objs as $obj) {
                if (count($obj) > 0) {
                    if ($obj->project_id == $id) {
                        $count = $obj->$att;
                    }
                }
            }
        }
        return $count;
    }

    /**
     * Get weighted bugs with 5 kind of weights
     *
     * @author tampt6722
     *
     * @param integer $key
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param array $projectIds
     * @return array
     */
    public function getWeightedBugsForProject ($key, $startDate, $endDate, $projectIds)
    {
        $weight = [];
        for ($i = 1; $i <= 5; $i++) {
            $weight[$i] = $this->project->getTicketsWithBugWeight($projectIds,
                    $key, $i, $startDate, $endDate);
        }

        return $weight;
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
    public function countWeightedBugsForProject($bugs, $id)
    {
        $count = [];
        $bugWeight = 0;
        $weight = Config::get('constant.bug_weight');
        for ($i = 1; $i <= 5; $i++) {
            $count[$i] = $this->count($bugs[$i], $id, 'countId');
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
     * @return string|number
     */
    public function roundData($numerator, $denominator, $percent = 0)
    {
        if ($denominator != 0 && $percent != 0) {
            $data = round($numerator/$denominator, 3) * 100;
        } elseif ($denominator != 0 && $percent == 0) {
            $data = round($numerator/$denominator, 3);
        } else {
            $data = 'NA';
        }

        return $data;
    }

    /**
     * Get api for projects report
     *
     * @author tampt6722
     *
     * @return array
     */
    public function getDataProjectReportApi ($request) {
        $mm = Config::get('constant.men_month');
        $months = $request->get('months');
        $yearRequest = $request->get('year');

        if($yearRequest != null)
        {
            if($yearRequest == date("Y",strtotime("this year")))
            {
                $year = 'this_year';
            }elseif ($yearRequest == date("Y",strtotime("last year")))
            {
                $year = 'last_year';
            }
            else
                return "api not data";
        }

        $projectReports = ProjectReport::select(
                'project_id',
                'project_name',
                DB::raw('sum(tested_tc) as tested_tc'),
                DB::raw('sum(loc) as loc'),
                DB::raw('sum(task) as task'),
                DB::raw('sum(weighted_bug) as weighted_bug'),
                DB::raw('sum(weighted_uat_bug) as weighted_uat_bug'),
                DB::raw('sum(actual_hour) as actual_hour'),
                'year'
                );
        if(isset($year))
        {
            $projectReports = $projectReports->where('year','=',$year);
        }
        if($months != null)
        {
            $projectReports = $projectReports->whereIn('month',$months);
        }
        $projectReports = $projectReports->groupBy('project_id')->get()->toArray();
        $resultProjectReports = [];

        $kloc = Helpers::writeNum($projectReports[0]['loc'], 1000);
        $kTestcase = Helpers::writeNum($projectReports[0]['tested_tc'], 1000);
        $workload = Helpers::writeNum($projectReports[0]['actual_hour'], $mm);
        $bugBeforeRelease = Helpers::writeNumberInPer($projectReports[0]['weighted_bug'], ($projectReports[0]['weighted_bug']+$projectReports[0]['weighted_uat_bug']));

        $minKlogMm = Helpers::writeNum($kloc, $workload);
        $minTcMm = Helpers::writeNum($projectReports[0]['tested_tc'], $workload);
        $minTaskMm = Helpers::writeNum($projectReports[0]['task'], $workload);
        $minBugKlog = Helpers::writeNum($projectReports[0]['weighted_bug'], $kloc);
        $minBugAfterReKlog = Helpers::writeNum($projectReports[0]['weighted_uat_bug'], $kloc);
        $minBug1000Tc = Helpers::writeNum($projectReports[0]['weighted_bug'], $kTestcase);
        $minBugBeforeRe = $bugBeforeRelease;
        $minBugMm = Helpers::writeNum($projectReports[0]['weighted_bug'], $workload);
        
        $maxKlogMm = Helpers::writeNum($kloc, $workload);
        $maxTcMm = Helpers::writeNum($projectReports[0]['tested_tc'], $workload);
        $maxTaskMm = Helpers::writeNum($projectReports[0]['task'], $workload);
        $maxBugKlog = Helpers::writeNum($projectReports[0]['weighted_bug'], $kloc);
        $maxBugAfterReKlog = Helpers::writeNum($projectReports[0]['weighted_uat_bug'], $kloc);
        $maxBug1000Tc = Helpers::writeNum($projectReports[0]['weighted_bug'], $kTestcase);
        $maxBugBeforeRe = $bugBeforeRelease;
        $maxBugMm = Helpers::writeNum($projectReports[0]['weighted_bug'], $workload);
        
        $resultProjects = [];
        foreach($projectReports as $data)
        {
            $kloc = Helpers::writeNum($data['loc'], 1000);
            $kTestcase = Helpers::writeNum($data['tested_tc'], 1000);
            $workload = Helpers::writeNum($data['actual_hour'], $mm);
            $bugBeforeRelease = Helpers::writeNumberInPer($data['weighted_bug'], ($data['weighted_bug']+$data['weighted_uat_bug']));
            if($data['year'] == 'last_year')
            {
                $year = date("Y",strtotime("last year"));
            }else
                $year = date("Y",strtotime("this year"));

            if($minKlogMm > Helpers::writeNum($kloc, $workload))
            {
                $minKlogMm = Helpers::writeNum($kloc, $workload);
            }
            if($maxKlogMm < Helpers::writeNum($kloc, $workload))
            {
                $maxKlogMm = Helpers::writeNum($kloc, $workload);
            }
            
            if($minTcMm > Helpers::writeNum($data['tested_tc'], $workload))
            {
                $minTcMm = Helpers::writeNum($data['tested_tc'], $workload);
            }
            if($maxTcMm < Helpers::writeNum($data['tested_tc'], $workload))
            {
                $maxTcMm = Helpers::writeNum($data['tested_tc'], $workload);
            }
            
            if($minBugKlog > Helpers::writeNum($data['weighted_bug'], $kloc))
            {
                $minBugKlog = Helpers::writeNum($data['weighted_bug'], $kloc);
            }
            if($maxBugKlog < Helpers::writeNum($data['weighted_bug'], $kloc))
            {
                $maxBugKlog = Helpers::writeNum($data['weighted_bug'], $kloc);
            }
            
            if($minBugAfterReKlog > Helpers::writeNum($data['weighted_uat_bug'], $kloc))
            {
                $minBugAfterReKlog = Helpers::writeNum($data['weighted_uat_bug'], $kloc);
            }
            if($maxBugAfterReKlog < Helpers::writeNum($data['weighted_uat_bug'], $kloc))
            {
                $maxBugAfterReKlog = Helpers::writeNum($data['weighted_uat_bug'], $kloc);
            }
            
            if($minBug1000Tc > Helpers::writeNum($data['weighted_bug'], $kTestcase))
            {
                $minBug1000Tc = Helpers::writeNum($data['weighted_bug'], $kTestcase);
            }
            if($maxBug1000Tc < Helpers::writeNum($data['weighted_bug'], $kTestcase))
            {
                $maxBug1000Tc = Helpers::writeNum($data['weighted_bug'], $kTestcase);
            }
            
            if($minBugBeforeRe > $bugBeforeRelease)
            {
                $minBugBeforeRe = $bugBeforeRelease;
            }
            if($maxBugBeforeRe > $bugBeforeRelease)
            {
                $maxBugBeforeRe = $bugBeforeRelease;
            }
            
            if($minBugMm > Helpers::writeNum($data['weighted_bug'], $workload))
            {
                $minBugMm = Helpers::writeNum($data['weighted_bug'], $workload);
            }
            if($maxBugMm > Helpers::writeNum($data['weighted_bug'], $workload))
            {
                $maxBugMm = Helpers::writeNum($data['weighted_bug'], $workload);
            }
            array_push($resultProjectReports, array(
                'project_id' => $data['project_id'],
                'name' => $data['project_name'],
                'klogMm' => Helpers::writeNum($kloc, $workload),
                'tcMm' => Helpers::writeNum($data['tested_tc'], $workload),
                'taskMm' => Helpers::writeNum($data['task'], $workload),
                'bugKlog' => Helpers::writeNum($data['weighted_bug'], $kloc),
                'bugAfterReKlog' => Helpers::writeNum($data['weighted_uat_bug'], $kloc),
                'bug1000Tc' => Helpers::writeNum($data['weighted_bug'], $kTestcase),
                'bugBeforeRe' => $bugBeforeRelease,
                'bugMm' => Helpers::writeNum($data['weighted_bug'], $workload),
                'year' => $year,
            ));
        }

        $resultProjects = [
            'projects' => $resultProjectReports,
            'min' => [
                'klogMm' => $minKlogMm,
                'tcMm' => $minTcMm,
                'taskMm' => $minTaskMm,
                'bugKlog' => $minBugKlog,
                'bugAfterReKlog' => $minBugAfterReKlog,
                'bug1000Tc' => $minBug1000Tc,
                'bugBeforeRe' => $minBugBeforeRe,
                'bugMm' => $minBugMm,
            ],
            'max' => [
                    'klogMm' => $maxKlogMm,
                    'tcMm' => $maxTcMm,
                    'taskMm' => $maxTaskMm,
                    'bugKlog' => $maxBugKlog,
                    'bugAfterReKlog' => $maxBugAfterReKlog,
                    'bug1000Tc' => $maxBug1000Tc,
                    'bugBeforeRe' => $maxBugBeforeRe,
                    'bugMm' => $maxBugMm,
            ],
        ];
        return $resultProjects;
    }

    /**
     * Return all project member data
     * @author tampt6722
     *
     * @return array
     */
    public function getDataProjectMemberApi()
    {
        $dataPM = $this->projectMember->getApiProjectMember();
        return $dataPM;
    }
}