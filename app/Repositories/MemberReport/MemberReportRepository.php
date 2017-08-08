<?php
namespace App\Repositories\MemberReport;

use DB;
use App\Models\MemberReport;
use App\Models\Department;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\ProjectMember\ProjectMemberRepositoryInterface;
use Illuminate\Support\Facades\Config;
use App\Repositories\QualityReport\QualityReportByProjectRepositoryInterface;

class MemberReportRepository implements MemberReportRepositoryInterface{


    public function __construct(ProjectRepositoryInterface $project,
            ProjectMemberRepositoryInterface $pm,
            QualityReportByProjectRepositoryInterface $projectReport)
    {
        $this->project = $project;
        $this->projectReport = $projectReport;
        $this->projectMember = $pm;
    }

    public function all(){
        return MemberReport::all();
    }

    public function paginate($quantity) {
        return MemberReport::paginate($quantity);
    }

    public function find($id) {
        return MemberReport::find($id);
    }

    public function delete($id) {
        MemberReport::delete($id);
    }

    public function deleteAllData() {
        MemberReport::truncate();
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\MemberReport\MemberReportRepositoryInterface::save()
     */
    public function save($data) {
        $report = new MemberReport();
        $report->department_id = $data['department_id'];
        $report->project_id = $data['project_id'];
        $report->user_id = $data['user_id'];
        $report->name = $data['name'];
        $report->email = $data['email'];
        $report->position = $data['position'];
        $report->year = $data['year'];
        $report->month = $data['month'];
        $report->user_name = $data['user_name'];
        if (isset($data['department_name'])) {
            $report->department_name = $data['department_name'];
        }
        if (isset($data['workload'])) {
            $report->workload = $data['workload'];
        }
        if (isset($data['task'])) {
            $report->task = $data['task'];
        }
        if (isset($data['kloc'])) {
            $report->kloc = $data['kloc'];
        }
        if (isset($data['bug_weighted'])) {
            $report->bug_weighted = $data['bug_weighted'];
        }
        if (isset($data['madebug_weighted'])) {
            $report->madebug_weighted = $data['madebug_weighted'];
        }
        if (isset($data['foundbug_weighted'])) {
            $report->foundbug_weighted = $data['foundbug_weighted'];
        }
        if (isset($data['testcase_create'])) {
            $report->testcase_create = $data['testcase_create'];
        }
        if (isset($data['testcase_test'])) {
            $report->testcase_test = $data['testcase_test'];
        }
        if (isset($data['test_workload'])) {
            $report->test_workload = $data['test_workload'];
        }
        if (isset($data['createTc_workload'])) {
            $report->createTc_workload = $data['createTc_workload'];
        }
        $report->save();
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\MemberReport\MemberReportRepositoryInterface::update()
     */
    public function update($data, $id) {
        $report = MemberReport::find($id);

        if (isset($data['department_id'])) {
            $report->department_id = $data['department_id'];
        }
        if (isset($data['department_name'])) {
            $report->department_name = $data['department_name'];
        }
        if (isset($data['project_id'])) {
            $report->project_id = $data['project_id'];
        }
        if (isset($data['user_id'])) {
            $report->user_id = $data['user_id'];
        }
        if (isset($data['name'])) {
            $report->name = $data['name'];
        }
        if (isset($data['email'])) {
            $report->email = $data['email'];
        }
        if (isset($data['user_name'])) {
            $report->user_name = $data['user_name'];
        }
        if (isset($data['position'])) {
            $report->position = $data['position'];
        }
        if (isset($data['year'])) {
            $report->year = $data['year'];
        }
        if (isset($data['month'])) {
            $report->month = $data['month'];
        }
        if (isset($data['workload'])) {
            $report->workload = $data['workload'];
        }
        if (isset($data['task'])) {
            $report->task = $data['task'];
        }
        if (isset($data['kloc'])) {
            $report->kloc = $data['kloc'];
        }
        if (isset($data['bug_weighted'])) {
            $report->bug_weighted = $data['bug_weighted'];
        }
        if (isset($data['madebug_weighted'])) {
            $report->madebug_weighted = $data['madebug_weighted'];
        }
        if (isset($data['foundbug_weighted'])) {
            $report->foundbug_weighted = $data['foundbug_weighted'];
        }
        if (isset($data['testcase_create'])) {
            $report->testcase_create = $data['testcase_create'];
        }
        if (isset($data['testcase_test'])) {
            $report->testcase_test = $data['testcase_test'];
        }
        if (isset($data['test_workload'])) {
            $report->test_workload = $data['test_workload'];
        }
        if (isset($data['createTc_workload'])) {
            $report->createTc_workload = $data['createTc_workload'];
        }
        $report->save();
    }

    /**
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\MemberReport\MemberReportRepositoryInterface::checkExistedData()
     */
    public function checkExistedData($departmentId, $projectId, $userId, $year, $month)
    {
        $query = MemberReport::where('department_id', $departmentId)
                            ->where('project_id', $projectId)
                            ->where('year', $year)
                            ->where('month', $month)
                            ->where('user_id', $userId)
                            ->first();
        return $query;
    }

    /**
     *  @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\MemberReport\MemberReportRepositoryInterface::getDevData()
     */
    public function getDevData($year, $month, $departmentId, $divisionId,
                                            $teamId, $projectId,$projectMemberJoin)
    {
            $query1 = MemberReport::select('user_id', 'name','email', 'user_name','department_name',
                    DB::raw('sum(workload)  as workload'),
                    DB::raw('sum(kloc)  as kloc'),
                    DB::raw('sum(task)  as task'),
                    DB::raw('sum(bug_weighted)  as bug_weighted'),
                    DB::raw('sum(madebug_weighted)  as madebug_weighted'))
                ->where('year', $year)
                ->where('position', 'Dev');
            $query2 = $this->checkWhetherProjectIsNull($query1, $projectId, $departmentId,
                                    $divisionId, $teamId, $projectMemberJoin);

            // If in_array(0, $month), search by months. Else search all
            if (!in_array(0, $month)) {
                $result = $query2->whereIn('month', $month)->groupBy('user_id');
            } else {
                $result = $query2->groupBy('user_id');
            }
            return $result->get()->toArray();
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\MemberReport\MemberReportRepositoryInterface::getQAData()
     */
    public function getQAData($year, $month, $departmentId, $divisionId,
            $teamId, $projectId,$projectMemberJoin){
                $query1 = MemberReport::select('user_id', 'name', 'email', 'user_name','department_name',
                        DB::raw('sum(workload)  as workload'),
                        DB::raw('sum(task)  as task'),
                        DB::raw('sum(foundbug_weighted)  as foundbug_weighted'),
                        DB::raw('sum(testcase_create)  as testcase_create'),
                        DB::raw('sum(testcase_test)  as testcase_test'),
                        DB::raw('sum(test_workload)  as test_workload'),
                        DB::raw('sum(createTc_workload)  as createTc_workload'))
                        ->where('year', $year)
                        ->where('position', 'QA');
                        $query2 = $this->checkWhetherProjectIsNull($query1, $projectId, $departmentId,
                                $divisionId, $teamId, $projectMemberJoin);

                        // If in_array(0, $month), search by months. Else search all
                        if (!in_array(0, $month)) {
                            $result = $query2->whereIn('month', $month)->groupBy('user_id');
                        } else {
                            $result = $query2->groupBy('user_id');
                        }
                        return $result->get()->toArray();
    }

    /**
     *
     * @author tampt6722
     *
     * @param $query
     * @param unknown $projectId
     * @param integer $getDepartment
     * @param integer $getDivision
     * @param integer $getTeam
     * @param array $projectMemberJoin
     * @return Query
     */
    public function checkWhetherProjectIsNull($query, $projectId, $getDepartment,
            $getDivision, $getTeam, $projectMemberJoin)
    {
        if (($projectId == null) || ($projectId == -1)) {
            if (($getTeam != null) && ($getTeam != -1)) {
                $result = $query->whereIn('project_id', $projectMemberJoin)
                ->where('department_id', $getTeam);
            } elseif (($getDivision != null) && ($getDivision != -1)) {
                $listTeam = Department::where('parent_id', $getDivision)->pluck('id')->toArray();
                $result = $query->whereIn('project_id', $projectMemberJoin)
                ->where(function ($query) use ($listTeam, $getDivision) {
                    $query->whereIn('department_id', $listTeam)
                    ->orWhere('department_id', $getDivision);
                });
            } elseif (($getDepartment != null) && ($getDepartment != -1)) {
                $listDivision = Department::where('parent_id', $getDepartment)->pluck('id')->toArray();
                $listTeam     = Department::whereIn('parent_id', $listDivision)->pluck('id')->toArray();
                $result = $query->whereIn('project_id', $projectMemberJoin)
                ->where(function ($query) use ($listTeam, $listDivision, $getDepartment) {
                    $query->whereIn('department_id', $listTeam)
                    ->orWhereIn('department_id', $listDivision)
                    ->orWhere('department_id', $getDepartment);
                });
            } else {
                $result = $query->whereIn('project_id', $projectMemberJoin);
            }
        } else {
            $result = $query->where('project_id', $projectId)
            ->whereIn('project_id', $projectMemberJoin);
        }

        return $result;
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\MemberReport\MemberReportRepositoryInterface::getDataMemberToReport()
     */
    public function getDataMemberToReport($year, $month, $position, $departmentId, $divisionId, $teamId, $projectId, $projectMemberJoin)
    {
        $datas = [];
        if($position == 'Dev') {
            $datas = $this->getDevData($year, $month, $departmentId, $divisionId, $teamId, $projectId, $projectMemberJoin);

        } elseif ($position == 'QA') {
            $datas = $this->getQAData($year, $month, $departmentId, $divisionId, $teamId, $projectId, $projectMemberJoin);
        }

        return $datas;
    }

    /**
     *
     * @author tampt6722
     *
     * @param integer $projectId
     * @param Datetime $startDate
     * @param Datetime $endDate
     * @param integer $getDepartment
     * @param integer $getDivision
     * @param integer $getTeam
     */
    public function saveDataMember ($startDate, $endDate, $year, $month)
    {
        $mm = Config::get('constant.men_month');

        //----------------------FOR DEV------------------------------//

        $devPositions = ['Dev', 'DevL'];
        $devIds = $this->projectMember->getMembersWithPositions($devPositions);
        
        $mainDevs = $this->projectMember->getMainMembers($devIds,$devPositions)->toArray();
//         file_put_contents("result.txt",print_r($mainDevs,TRUE));
//         die;
        // Get line of code of members in a project
        $locs = $this->projectMember->getLocOfDevs($startDate, $endDate)->toArray();
        // Get number of member tasks
        $tasks = $this->projectMember->countTasksOfMembers($startDate, $endDate, $devPositions)->toArray();
        $bugs = $this->projectMember->countTicketsWithBugWeightOfMember(9,
                $startDate, $endDate, $devPositions, 0)->toArray();

        $madeBugs = $this->projectMember->countTicketsWithBugWeightOfMember(9,
                    $startDate, $endDate, $devPositions, 1)->toArray();
        // Get actual hour for dev members in a project
        $entries = $this->projectMember->getWorkLoadOfAMember($startDate, $endDate, $devPositions);

        if (!empty($mainDevs)) {
            foreach ($mainDevs as $dev) {
                $devData = [];
                $userName = $dev['user_name'];
                $userId = $dev['user_id'];
                $departmentId = $dev['department_id'];
                $projectId = $dev['project_id'];
                $name = $dev['last_name'] .' '.$dev['first_name'];
                $loc = $this->countDataByMember($locs, $projectId, $userId , 'loc');
                $kLoc = $loc/1000;
                $actualHour = $this->countDataByMember($entries, $projectId, $userId, 'actual_hour');
               // $workload = $this->projectReport->roundData($actualHour, $mm);
                $task = $this->countDataByMember($tasks, $projectId, $userId, 'countId');
                $weightedBug = $this->countWeightedBugsOfMember($bugs, $projectId, $userId);
                $weightedMadeBug = $this->countWeightedBugsOfMember($madeBugs, $projectId, $userId);

                $devData = [
                                'department_id' => $departmentId,
                                'department_name' => $dev['department_name'],
                                'project_id' => $projectId,
                                'user_id' => $userId,
                                'name' => $name,
                                'user_name' => $userName,
                                'email'=> $dev['email'],
                                'position' => 'Dev',
                                'year' => $year,
                                'month' => $month,
                                'workload' => $actualHour,
                                'kloc' => $kLoc,
                                'task' => $task,
                                'bug_weighted' => $weightedBug,
                                'madebug_weighted' => $weightedMadeBug
                ];
                $checkDev = $this->checkExistedData($departmentId, $projectId, $userId, $year, $month);
                if (count($checkDev) == 0) {
                    $this->save($devData);
                } else {
                    $this->update($devData, $checkDev->id);
                }
            }
        }

        //--------------------FOR QA------------------------------
        $qaPositions = ['QA', 'QAL'];
        $qaIds = $this->projectMember->getMembersWithPositions($qaPositions);
        $mainQas = $this->projectMember->getMainMembers($qaIds, $qaPositions)->toArray();

        //Get number of test case with making test case activity
        $createTcs = $this->projectMember->getTestCaseOfAMember($startDate, $endDate, 19)->toArray();

        // Get number of test case with testing activity
        $testingTcs = $this->projectMember->getTestCaseOfAMember($startDate, $endDate, 21)->toArray();

        $qaTasks = $this->projectMember->countTasksOfMembers($startDate, $endDate, $qaPositions)->toArray();
        $qaEntries = $this->projectMember->getWorkLoadOfAMember($startDate, $endDate, $qaPositions)->toArray();

        // Get actual hour for test of qa members in a project
        $testEntries = $this->projectMember->getWorkLoadOfAMember($startDate, $endDate, $devPositions, 21);
        // Get actual hour for make test case of qa members in a project
        $mTestEntries = $this->projectMember->getWorkLoadOfAMember($startDate, $endDate, $devPositions, 19);

        // Count members' tickets which have ticket type is bug with 5 kinds of weight in a project
        $foundBugs = $this->projectMember->countTicketsWithBugWeightOfMember(9,
                $startDate, $endDate, $qaPositions, 2)->toArray();

        if (!empty($mainQas)) {
            foreach ($mainQas as $qa) {
                $qaData = [];
                $userName = $qa['user_name'];
                $userId = $qa['user_id'];
                $departmentId = $qa['department_id'];
                $projectId = $qa['project_id'];
                $name = $qa['last_name'] .' '.$qa['first_name'];

                $createTc = $this->sumTestCase($createTcs, $projectId, $userId, 'test_case');
                $testingTc = $this->sumTestCase($testingTcs, $projectId, $userId, 'test_case');

                $actualHour = $this->countDataByMember($qaEntries, $projectId, $userId, 'actual_hour');
              //  $workload = $this->projectReport->roundData($actualHour, $mm); // Workload per men month

                $actualHourTest = $this->countDataByMember($testEntries,  $projectId, $userId , 'actual_hour');
               // $tWorkLoad = $this->projectReport->roundData($actualHourTest, $mm); // Work load for test per men month

                $actualHourMakeTc = $this->countDataByMember($mTestEntries,  $projectId, $userId , 'actual_hour');
              //  $makeTcWorkLoad = $this->projectReport->roundData($actualHourMakeTc, $mm); //  Work load for make test case per men month

                $weightedBug = $this->countWeightedBugsOfMember($foundBugs, $projectId, $userId);

                $task = $this->countDataByMember($qaTasks, $projectId, $userId, 'countId');
                $qaData = [
                                'department_id' => $departmentId,
                                'department_name' => $qa['department_name'],
                                'project_id' => $projectId,
                                'user_id' => $userId,
                                'name' => $name,
                                'user_name' => $userName,
                                'email'=> $qa['email'],
                                'position' => 'QA',
                                'year' => $year,
                                'month' => $month,
                                'task' => $task,
                                'testcase_create' => $createTc,
                                'testcase_test' => $testingTc,
                                'workload' => $actualHour,
                                'test_workload' =>$actualHourTest,
                                'createTc_workload' => $actualHourMakeTc,
                                'foundbug_weighted' => $weightedBug
                ];

                $checkQA = $this->checkExistedData($departmentId, $projectId, $userId, $year, $month);
                if (count($checkQA) == 0) {
                    $this->save($qaData);
                } else {
                    $this->update($qaData, $checkQA->id);
                }
            }
        }
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
    public function countDataByMember($objs,$projectId, $id, $att)
    {
        $count = 0;
        if (count($objs) > 0) {
            foreach ($objs as $obj) {
                if (($obj['related_id'] == $id) && ($obj['project_id'] == $projectId)) {
                    $count = $obj[$att];
                }
            }
        }

        return $count;
    }

    /**
     * Get number of tickets with weightId
     *
     * @author tampt6722
     *
     * @param array $objs
     * @param integer $projectId
     * @param integer $id
     * @param integer $weightId
     * @param string $att
     * @return integer
     */
    public function countTicketsWithWeight($objs,$projectId, $id, $weightId, $att)
    {
        $count = 0;
        if (count($objs) > 0) {
            foreach ($objs as $obj) {
                if (($obj['related_id'] == $id) && ($obj['project_id'] == $projectId)
                        && ($obj['weight_related_id'] == $weightId))
                {
                    $count = $obj[$att];
                }
            }
        }

        return $count;
    }

    /**
     * Sum number of a member test cases
     * @author tampt6722
     *
     * @param array $obs
     * @param integer $projectId
     * @param integer $id
     * @param string $att
     * @return integer
     */
    public function sumTestCase($objs, $projectId, $userId, $att)
    {
        $count = 0;
        if (count($objs) > 0) {
            foreach ($objs as $obj) {
                if (($obj['related_id'] == $userId) && ($obj['project_id'] == $projectId))
                {
                    $count += $obj[$att];
                }
            }
        }

        return $count;
    }


    /**
     * Caculate weighted bugs of a member
     * @author tampt6722
     *
     * @param array $bugs
     * @param integer $projectId
     * @param integer $userId
     * @return integer
     */
    public function countWeightedBugsOfMember($bugs, $projectId, $userId) {
        $count = [];
        $bugWeight = 0;
        $weight = Config::get('constant.bug_weight');
        for ($i = 1; $i <= 5; $i++) {
            $count[$i] = $this->countTicketsWithWeight($bugs, $projectId, $userId, $i, 'countId');
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
}