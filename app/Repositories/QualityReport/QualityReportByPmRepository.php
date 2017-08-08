<?php
namespace App\Repositories\QualityReport;

use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\ProjectMember\ProjectMemberRepositoryInterface;
use Illuminate\Support\Facades\Config;
use DateTime;
use App\Repositories\MemberProjectReport\MemberProjectReportRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\MemberReport;
use Helpers;
use Illuminate\Http\Request;
use App\Models\ProjectMember;

class QualityReportByPmRepository implements QualityReportByPmRepositoryInterface {

    public function __construct(ProjectRepositoryInterface $project,
            ProjectMemberRepositoryInterface $pm,
            QualityReportByProjectRepositoryInterface $report,
            MemberProjectReportRepositoryInterface $memberReport)
    {
        $this->project = $project;
        $this->projectMember = $pm;
        $this->report = $report;
        $this->memberReport = $memberReport;
    }

    public function getDataMemberReportWithProjectApi($request)
    {
        $requestStartDate = $request->get('start_date');
        $requestEndDate = $request->get('end_date');
        if (($requestStartDate == "")&&($requestEndDate == "")) {
            $startDate   = date('1970-01-01 00:00:00');
            $endDate     = date('9999-01-01 23:59:59');
        } elseif (($requestStartDate == "")&&(!$requestEndDate == "")) {
            $startDate   = date('1970-01-01 00:00:00');
            $eDate       = str_replace('/', '-', $requestEndDate);
            $endDate     = date('Y-m-d 23:59:59', strtotime($eDate));
        } elseif ((!$requestStartDate == "")&&($requestEndDate == "")) {
            $sDate       = str_replace('/', '-', $requestStartDate);
            $startDate   = date('Y-m-d 00:00:00', strtotime($sDate));
            $endDate     = date('Y-m-d 23:59:59');
        } else {
            $sDate       = str_replace('/', '-', $requestStartDate);
            $startDate   = date('Y-m-d 00:00:00', strtotime($sDate));
            $eDate       = str_replace('/', '-', $requestEndDate);
            $endDate     = date('Y-m-d 23:59:59', strtotime($eDate));
        }

        $projectMembers = ProjectMember::select(
            'project_member.project_id',
            'u2.email',
            'u2.id',
            'project_member.start_date',
            'project_member.end_date'
        )
        ->join('users as u1','u1.id','=','project_member.user_id')
        ->join('users as u2','u1.related_id','=','u2.id');

        if ($requestStartDate != "" && $requestEndDate != "")
        {
            $projectMembers = $projectMembers
                ->whereBetween('project_member.start_date',[$startDate,$endDate])
                ->orWhereBetween('project_member.end_date',[$startDate,$endDate]);
        }

        $resultProjectMember = $projectMembers->get()->groupBy('email');

        return $resultProjectMember;
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
    public function countDataByMember($objs, $userId, $projectId, $roleName, $att)
    {
        $count = 0;
        if (count($objs) > 0) {
            foreach ($objs as $obj) {
                if ($obj->user_id == $userId && $obj->project_id == $projectId
                        && $obj->role_name == $roleName)
                {
                    $count = $obj->$att;
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
    * @param Date $startDate
    * @param Date $endDate
    * @return array
    */
    public function getWeightedBugOfMemberOnProjectsForApi($key, $startDate, $endDate, $userFlag)
    {
        $weight = [];
        for ($i = 1; $i <= 5; $i++) {
            $weight[$i] = $this->projectMember->countWeightBugsOfMemberOnProjectsForApi(
                    $key, $i, $startDate, $endDate, $userFlag);
        }

        return $weight;
    }

    /**
     *
     * @author tampt6722
     *
     * @param integer $key
     * @param Date $startDate
     * @param Date $endDate
     * @return array
     */
    public function getWeightedBugOfMemberInProjects($key, $startDate, $endDate)
    {
        $weight = [];
        for ($i = 1; $i <= 5; $i++) {
            $weight[$i] = $this->projectMember->countTicketsWithBugWeightOfMemberInProjects(
                    $key, $i, $startDate, $endDate);
        }

        return $weight;
    }

    /**
     *
     * @author tampt6722
     *
     * @param array $bugs
     * @param integer $userId
     * @return number
     */
    public function countWeightedBugsOfMember($bugs, $userId, $projectId, $roleName) {
        $count = [];
        $bugWeight = 0;
        $weight = Config::get('constant.bug_weight');
        for ($i = 1; $i <= 5; $i++) {
            $count[$i] = $this->countDataByMember($bugs[$i], $userId, $projectId, $roleName, 'countId');
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
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\QualityReport\QualityReportByPmRepositoryInterface::getTimeSearchReport()
     */
    public function getTimeSearchReport($defaultTime)
    {
        if ($defaultTime != null) {
            switch ($defaultTime)
            {
                case 'this_month':
                    $startDate = date('Y-m-d 00:00:00', strtotime('first day of this month'));
                    $endDate   = date('Y-m-d 23:59:59', strtotime('last day of this month'));
                    break;
                case 'last_month':
                    $startDate = date('Y-m-d 00:00:00', strtotime('first day of last month'));
                    $endDate   = date('Y-m-d 23:59:59', strtotime('last day of last month'));
                    break;
                case 'last_three_month':
                    $startDate = date('Y-m-d 00:00:00', strtotime('-3 month'));
                    $endDate   = date('Y-m-d 23:59:59', strtotime('today'));
                    break;
                case 'last_six_month':
                    $startDate = date('Y-m-d 00:00:00', strtotime('-6 month'));
                    $endDate   = date('Y-m-d 23:59:59', strtotime('today'));
                    break;
                case 'this_year':
                    $startDate = date('Y-01-01 00:00:00');
                    $endDate   = date('Y-12-31 23:59:59');
                    break;
                case 'last_year':
                    $year = date('Y') - 1;
                    $start = "January 1st, {$year}";
                    $end = "December 31st, {$year}";
                    $startDate = date('Y-m-d 00:00:00', strtotime($start));
                    $endDate   = date('Y-m-d 00:00:00', strtotime($end));
                    break;
            }
        } else {
            $startDate  = date('Y-m-d 00:00:00', strtotime('first day of this month'));
            $endDate    = date('Y-m-d 23:59:59', strtotime('today'));
        }

        return ['start' => $startDate, 'end' => $endDate];
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\QualityReport\QualityReportByPmRepositoryInterface::getDataMemberInProjectsReport()
     */
    public function getDataMemberInProjectsReport($reportFlag, $startDate, $endDate, $nameReport)
    {
        $mm = Config::get('constant.men_month');
        // Get line of code of members in a project
        $tLoc = $this->projectMember->getLocOfAMemberInProjects($startDate, $endDate);

        // Get actual hour for dev members in a project
        $tEntries = $this->projectMember->getWorkLoadOfAMemberInProjects($startDate, $endDate);

        // Get actual hour for test of qa members in a project
        $testEntries = $this->projectMember->getWorkLoadOfAMemberInProjects($startDate, $endDate, 21);

        // Get actual hour for make test case of qa members in a project
        $mTestEntries = $this->projectMember->getWorkLoadOfAMemberInProjects($startDate, $endDate, 19);
        // Count members' tickets which have ticket type is task in a project
        $tasks = $this->projectMember->countTicketsWithTicketTypeOfMemberInProjects(11, $startDate, $endDate);
        // Count members' tickets which have ticket type is bug with 5 kinds of weight in a project
        $bugs = $this->getWeightedBugOfMemberInProjects(9, $startDate, $endDate);

        if (count($tLoc) > 0) {

            foreach ($tLoc as $user) {
                $roleName = $user->role_name;
                if ($roleName == 'Dev' || $roleName == 'DevL') {
                    $dev = [];
                    $common = [];
                    $quality = [];
                    $productivity = [];
                    $userId = $user->user_id;
                    $projectId = $user->project_id;

                    $loc = $user->loc;
                    $position = 'Dev';
                    $kLoc = $loc/1000;
                    $actualHour = $this->countDataByMember($tEntries, $userId, $projectId, $roleName, 'actual_hour');
                    $workload = round($actualHour/$mm, 2);
                    $task = $this->countDataByMember($tasks, $userId, $projectId, $roleName, 'countId');
                    $taskPerMm = round($task/$mm, 2); // number task per men month
                    $weightedBug = $this->countWeightedBugsOfMember($bugs, $userId, $projectId, $roleName);
                    $bWPerMm = round($weightedBug/$mm, 2); // weighted bug per men month
                    $kLocPerMm = round($kLoc/$mm, 2); // kLOC per men month
                    $bWPerWorkLoad = $this->report->roundData($weightedBug, $workload);
                    $bWPerKloc = $this->report->roundData($weightedBug, $kLoc);
                    $quality= [   'weightedBugPerKloc' => $bWPerKloc,
                                  'weightedBugperWl' => $bWPerWorkLoad
                    ];
                    $productivity = ['kLocPerMm' => $kLocPerMm,
                                    'weightedBugPerMm' => $bWPerMm,
                                    'taskPerMm' => $taskPerMm
                    ];
                    $common = [     'loc' => $loc,
                                    'workload' => $workload,
                                    'bug' => $weightedBug,
                                    'task' => $task,
                    ];
                    $dev = [
                                    'user_id' => $userId,
                                    'position' => $position,
                                    'project_id' => $projectId,
                                    'report_flag' => $reportFlag,
                                    'common_data' => serialize($common),
                                    'quality' => serialize($quality),
                                    'productivity' => serialize($productivity),
                                    'time_name' => $nameReport,
                                    'start_date' => $startDate,
                                    'end_date' => $endDate,

                    ];
                    $this->memberReport->save($dev);

                } elseif ($roleName == 'QA' || $roleName == 'QAL') {

                    $common = [];
                    $quality = [];
                    $productivity = [];
                    $userId = $user->user_id;
                    $projectId = $user->project_id;

                    $position = 'QA';
                    $actualHour = $this->countDataByMember($tEntries, $userId, $projectId, $roleName, 'actual_hour');
                    $workload = round($actualHour/$mm, 2);
                    $actualHourTest = $this->countDataByMember($testEntries, $userId , $projectId, $roleName, 'actual_hour');
                    $tWorkLoad = round($actualHourTest/$mm, 2); // Work load for test per men month
                    $actualHourMakeTc = $this->countDataByMember($mTestEntries, $userId , $projectId, $roleName, 'actual_hour');
                    $makeTcWorkLoad = round($actualHourMakeTc/$mm, 2); //  Work load for make test case per men month

                    $weightedBug = $this->countWeightedBugsOfMember($bugs, $userId, $projectId, $roleName);
                    $bWPerWorkLoad = $this->report->roundData($weightedBug, $workload);
                    $bWPerMm = round($weightedBug/$mm, 2); // weighted bug per men month
                    $common = [     'tWorkLoad' => $tWorkLoad,
                                    'makeTcWorkLoad' => $makeTcWorkLoad,
                                    'bug' => $weightedBug,
                    ];
                    $quality= ['weightedBugperWl' => $bWPerWorkLoad];
                    $productivity = ['weightedBugPerMm' => $bWPerMm];
                    $qa  = [
                                   'user_id' => $userId,
                                    'position' => $position,
                                    'project_id' => $projectId,
                                    'report_flag' => $reportFlag,
                                    'common_data' => serialize($common),
                                    'quality' => serialize($quality),
                                    'productivity' => serialize($productivity),
                                    'time_name' => $nameReport,
                                    'start_date' => $startDate,
                                    'end_date' => $endDate,
                    ];
                    $this->memberReport->save($qa);
                }
            }
        }
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\QualityReport\QualityReportByPmRepositoryInterface::getDistinctData()
     */
    public function getDistinctData($datas)
    {
        $a = [];
        foreach ($datas as $key => $value) {
            $a[] = $value['user_id'];
        }
        $reportData = [];
        $reportDatas = [];
        foreach ($a as $key => $id) {
            foreach ($datas as $key1 => $value) {
                if($id == $value['user_id'] && $key != $key1){
                    $reportData[$value['full_name']][$key1] = [
                                    'project_name' => $value['project_name'],
                                    'common_data' => unserialize($value['common_data']),
                                    'quality' => unserialize($value['quality']),
                                    'productivity' => unserialize($value['productivity']),
                                    'time_name' => $value['time_name']

                    ];
                    unset($datas[$key1]);
                } else {
                    $reportDatas[$value['full_name']][$key1] = [
                                    'project_name' => $value['project_name'],
                                    'common_data' => unserialize($value['common_data']),
                                    'quality' => unserialize($value['quality']),
                                    'productivity' => unserialize($value['productivity']),
                                    'time_name' => $value['time_name']
                    ];
                }
            }
        }
        return $reportDatas;

    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\QualityReport\QualityReportByPmRepositoryInterface::getDistinctDataForApi()
     */
    public function getDistinctDataForApi($datas, $flag = 0)
    {
        $b = [];
        foreach ($datas as $key => $value) {
            $b[] = $value['email'];
        }
        $reportData = [];
        $reportDatas = [];
        foreach ($b as $key => $email) {
            foreach ($datas as $key1 => $value) {

                if($email == $value['email'] && $key != $key1){
                    if ($flag == 1) {
                        $reportData[$value['email']][$key1] = [
                                        'projectId' => $value['projectId'],
                                        'projectName' => $value['projectName']
                        ];
                    } else {
                        $reportData[$value['email']][$key1] = [
                                        'projectId' => $value['projectId'],
                                        'projectName' => $value['projectName'],
                                        'quality' => $value['quality'],
                                        'productivity' => $value['productivity'],
                        ];
                    }
                    unset($datas[$key1]);
                } else {
                    if ($flag == 1) {
                        $reportDatas[$value['email']][$key1] = [
                                        'projectId' => $value['projectId'],
                                        'projectName' => $value['projectName']
                        ];
                    } else {
                        $reportDatas[$value['email']][$key1] = [
                                        'projectId' => $value['projectId'],
                                        'projectName' => $value['projectName'],
                                        'quality' => $value['quality'],
                                        'productivity' => $value['productivity'],
                        ];
                    }
                }
            }
        }

        return $reportDatas;
    }
}