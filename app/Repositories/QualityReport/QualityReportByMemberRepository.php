<?php
namespace App\Repositories\QualityReport;

use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\ProjectMember\ProjectMemberRepositoryInterface;
use Illuminate\Support\Facades\Config;
use App\Repositories\MemberReport\MemberReportRepositoryInterface;
use App\Models\MemberReport;
use Helpers;
use DB;
use Illuminate\Http\Request;

class QualityReportByMemberRepository
                implements QualityReportByMemberRepositoryInterface {

    public function __construct(ProjectRepositoryInterface $project,
            ProjectMemberRepositoryInterface $pm,
            QualityReportByProjectRepositoryInterface $report,
            MemberReportRepositoryInterface $memberReport)
    {
        $this->project = $project;
        $this->report = $report;
        $this->projectMember = $pm;
        $this->memberReport = $memberReport;
    }


    /**
     * Get total data loc or entries or bugs for a member
     *
     * @author tampt6722
     *
     * @param Collection $objs
     * @param integer $id
     * @param string $att
     * @return integer
     */
    public function countDataByMember($objs, $id, $att, $position)
    {
        $count = 0;
        if (count($objs) > 0) {
            foreach ($objs as $obj) {
                if (($obj->user_id == $id) && ($obj->role_name == $position)) {
                    $count = $obj->$att;
                }
            }
        }

        return $count;
    }

    /**
     * Get total data loc or entries or bugs for a member
     *
     * @author tampt6722
     *
     * @param Collection $objs
     * @param integer $id
     * @param string $att
     * @return integer
     */
    public function sumTestCase($objs,  $id, $att, $position)
    {
        $count = 0;
        if (count($objs) > 0) {
            foreach ($objs as $obj) {
                if (($obj->user_id == $id) && ($obj->role_name == $position))
                {
                    $count += $obj->$att;
                }
            }
        }

        return $count;
    }

    /**
     *
     * @author tampt6722
     *
     * @param integer $key
     * @param integer $projectId
     * @param Date $startDate
     * @param Date $endDate
     * @param integer $getDepartment
     * @param integer $getDivision
     * @param integer $getTeam
     * @return array
     */
    public function getWeightedBugOfMemberForApi($key, $startDate, $endDate, $userFlag)
    {
        $weight = [];
        for ($i = 1; $i <= 5; $i++) {
            $weight[$i] = $this->projectMember->countTicketsWithBugWeightOfMemberForApi(
                    $key, $i, $startDate, $endDate, $userFlag);
        }

        return $weight;
    }

    /**
     * Caculate weighted bugs of a member
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\QualityReport\QualityReportByMemberRepositoryInterface::countWeightedBugsOfMember()
     */
     public function countWeightedBugsOfMember($bugs, $userId, $position) {
        $count = [];
        $bugWeight = 0;
        $weight = Config::get('constant.bug_weight');
        for ($i = 1; $i <= 5; $i++) {
            $count[$i] = $this->countDataByMember($bugs[$i], $userId, 'countId', $position);
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
     * Return data report Q&P by member
     *
     * @author tampt6722
     *
     * @return array
     */
    public function getDataMemberReportApi($request)
    {
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
        $mm = Config::get('constant.men_month');
        $memberReport = MemberReport::select(
                'email',
                'year',
                'position',
                DB::raw('sum(workload) as workload'),
                DB::raw('sum(task) as task'),
                DB::raw('sum(kloc) as kloc'),
                DB::raw('sum(bug_weighted) as bug_weighted'),
                DB::raw('sum(madebug_weighted) as madebug_weighted'),
                DB::raw('sum(foundbug_weighted) as foundbug_weighted'),
                DB::raw('sum(testcase_create) as testcase_create'),
                DB::raw('sum(testcase_test) as testcase_test'),
                DB::raw('sum(test_workload) as test_workload'),
                DB::raw('sum(createTc_workload) as createTc_workload')
        );

        if(isset($year))
        {
            $memberReport = $memberReport->where('year','=',$year);
        }
        if($months != null)
        {
            $memberReport = $memberReport->whereIn('month',$months);
        }
        $memberReport = $memberReport->groupBy('year','user_id','position')->get()->toArray();
        $resultDevs = [];
        $resultQas = [];

        $maxKlogMmDev = 10000;
        $maxAssignBugMmDev = 10000;
        $maxTaskMmDev = 10000;
        $maxMadeBugKlocDev = 10000;
        $maxMadeBugmmDev = 10000;
        $minKlogMmDev = 0;
        $minAssignBugMmDev = 0;
        $minTaskMmDev = 0;
        $minMadeBugKlocDev = 0;
        $minMadeBugmmDev = 0;

        $minTestCaseCreQA =  Helpers::writeNum($memberReport[0]['testcase_create'], $memberReport[0]['createTc_workload']/$mm);
        $minTestCaseExeQA = Helpers::writeNum($memberReport[0]['testcase_test'], $memberReport[0]['test_workload']/$mm);
        $minTaskMmQA = 10000;
        $minBug1000TcQA = Helpers::writeNum($memberReport[0]['foundbug_weighted'], ($memberReport[0]['testcase_test']/1000));
        $minBugMmQA = 10000;
        $maxTestCaseCreQA = 0;
        $maxTestCaseExeQA = 0;
        $maxTaskMmQA = 0;
        $maxBug1000TcQA = 0;
        $maxBugMmQA = 0;

        foreach ($memberReport as $member)
        {
            if ($member['year'] == 'last_year')
            {
                $year = date("Y",strtotime("last year"));
            } else
                $year = date("Y",strtotime("this year"));

            if ($member['position'] == 'Dev')
            {
                $email = $member['email'];
                $workload =  Helpers::writeNum($member['workload'], $mm);
                $klogMm = Helpers::writeNum($member['kloc'], $member['workload']/$mm);
                $assignBugMm = Helpers::writeNum($member['bug_weighted'], $member['workload']/$mm);
                $taskMm = Helpers::writeNum($member['task'], $member['workload']/$mm);
                $madeBugKloc = Helpers::writeNum($member['madebug_weighted'], $member['kloc']);
                $madeBugmm = Helpers::writeNum($member['madebug_weighted'], $member['workload']/$mm);
                
                if($klogMm < $maxKlogMmDev && $klogMm != 0)
                {
                    $maxKlogMmDev= $klogMm;
                }
                if($assignBugMm < $maxAssignBugMmDev && $assignBugMm != 0)
                {
                    $maxAssignBugMmDev= $assignBugMm;
                }
                if($taskMm < $maxTaskMmDev && $taskMm != 0)
                {
                    $maxTaskMmDev = $taskMm;
                }
                if($madeBugKloc < $maxMadeBugKlocDev && $madeBugKloc!= 0)
                {
                    $maxMadeBugKlocDev = $madeBugKloc;
                }
                if($madeBugmm < $maxMadeBugmmDev && $madeBugmm != 0)
                {
                    $maxMadeBugmmDev = $madeBugmm;
                }

                if($klogMm > $minKlogMmDev)
                {
                    $minKlogMmDev= $klogMm;
                }
                if($assignBugMm > $minAssignBugMmDev)
                {
                    $minAssignBugMmDev = $assignBugMm;
                }
                if($taskMm > $minTaskMmDev)
                {
                    $minTaskMmDev = $taskMm;
                }
                if($madeBugKloc > $minMadeBugKlocDev)
                {
                    $minMadeBugKlocDev = $madeBugKloc;
                }
                if($madeBugmm > $minMadeBugmmDev)
                {
                    $minMadeBugmmDev = $madeBugmm;
                }
                array_push($resultDevs, array(
                    'email' => $email,
                    'klogMm' => $klogMm,
                    'assignBugMm' => $assignBugMm,
                    'taskMm' => $taskMm,
                    'madeBugKloc' => $madeBugKloc,
                    'madeBugmm' => $madeBugmm,
                    'year' => $year,
                ));
            } elseif ($member['position']== 'QA') {
                $email = $member['email'];
                $testCaseCre =  Helpers::writeNum($member['testcase_create'], $member['createTc_workload']/$mm);
                $testCaseExe = Helpers::writeNum($member['testcase_test'], $member['test_workload']/$mm);
                $taskMm = Helpers::writeNum($member['task'], $member['workload']/$mm);
                $bug1000Tc = Helpers::writeNum($member['foundbug_weighted'], ($member['testcase_test']/1000));
                $bugMm = Helpers::writeNum($member['foundbug_weighted'], $member['workload']/$mm);

                if($minTestCaseCreQA > $testCaseCre)
                {
                    $minTestCaseCreQA = $testCaseCre;
                }
                if($maxTestCaseCreQA < $testCaseCre)
                {
                    $maxTestCaseCreQA = $testCaseCre;
                }
                
                if($minTestCaseExeQA > $testCaseExe)
                {
                    $minTestCaseExeQA = $testCaseExe;
                }
                if($maxTestCaseExeQA < $testCaseExe)
                {
                    $maxTestCaseExeQA = $testCaseExe;
                }
                
                if($minTaskMmQA > $taskMm && $taskMm != 0)
                {
                    $minTaskMmQA = $taskMm;
                }
                if($maxTaskMmQA < $taskMm)
                {
                    $maxTaskMmQA = $taskMm;
                }
                
                if($minBug1000TcQA > $bug1000Tc)
                {
                    $minBug1000TcQA = $bug1000Tc;
                }
                if($maxBug1000TcQA < $bug1000Tc)
                {
                    $maxBug1000TcQA = $bug1000Tc;
                }
                
                if($minBugMmQA > $bugMm && $bugMm != 0)
                {
                    $minBugMmQA = $bugMm;
                }
                if($maxBugMmQA < $bugMm)
                {
                    $maxBugMmQA = $bugMm;
                }
                array_push($resultQas, array (
                    'email' => $email,
                    'testCaseCre' => $testCaseCre,
                    'testCaseExe' => $testCaseExe,
                    'taskMm' => $taskMm,
                    'bug1000Tc' => $bug1000Tc,
                    'bugMm' => $bugMm,
                    'year' => $year,
                ));
            }
        }

        $resultMemberReport = [
            'dev' => $resultDevs,
            'qa' => $resultQas,
            'minDev'=>[
                'klogMm' => $maxKlogMmDev,
                'assignBugMm' => $maxAssignBugMmDev,
                'taskMm' => $maxTaskMmDev,
                'madeBugKloc' => $maxMadeBugKlocDev,
                'madeBugmm' => $maxMadeBugmmDev,
            ],
            'maxDev'=>[
                    'klogMm' => $minKlogMmDev,
                    'assignBugMm' => $minAssignBugMmDev,
                    'taskMm' => $minTaskMmDev,
                    'madeBugKloc' => $minMadeBugKlocDev,
                    'madeBugmm' => $minMadeBugmmDev,
            ],
            'minQa'=>[
                'testCaseCre' => $minTestCaseCreQA,
                'testCaseExe' => $minTestCaseExeQA,
                'taskMm' => $minTaskMmQA,
                'bug1000Tc' => $minBug1000TcQA,
                'bugMm' => $minBugMmQA,
            ],
            'maxQa'=>[
                    'testCaseCre' => $maxTestCaseCreQA,
                    'testCaseExe' => $maxTestCaseExeQA,
                    'taskMm' => $maxTaskMmQA,
                    'bug1000Tc' => $maxBug1000TcQA,
                    'bugMm' => $maxBugMmQA,
            ],
        ];

        return $resultMemberReport;
        // Return json_encode($mReportData, JSON_UNESCAPED_UNICODE);
    }
}
