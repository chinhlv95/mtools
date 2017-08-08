<?php
namespace App\Repositories\ProjectReport;

use App\Models\ProjectReport;
use App\Repositories\Project\ProjectRepositoryInterface;
use Illuminate\Support\Facades\Config;
use DB;
use App\Models\Department;
use App\Repositories\Entry\EntryRepositoryInterface;

/**
 *
 * @author tampt6722
 *
 */
class ProjectReportRepository implements ProjectReportRepositoryInterface
{
    public function __construct(ProjectRepositoryInterface $project,
            EntryRepositoryInterface $entry)
    {
        $this->project = $project;
        $this->entry = $entry;

    }

    public function all(){
        return ProjectReport::all();
    }

    public function paginate($quantity){
        return ProjectReport::paginate($quantity);
    }

    public function find($id){
        return ProjectReport::find($id);
    }

    public function save($data){
        $report = new ProjectReport();
        $report->department_id = $data['department_id'];
        $report->department_name = $data['department_name'];
        $report->project_id = $data['project_id'];
        $report->project_name = $data['project_name'];
        $report->status = $data['status'];
        $report->year = $data['year'];
        $report->month = $data['month'];
        $report->tested_tc = $data['tested_tc'];
        $report->loc = $data['loc'];
        $report->task = $data['task'];
        $report->weighted_bug = $data['weighted_bug'];

        $report->weighted_uat_bug = $data['weighted_uat_bug'];
        $report->actual_hour = $data['actual_hour'];
        $report->save();
        return $report->id;
    }

    public function delete($id){
        ProjectReport::find($id)->delete();
    }

    public function update($data, $id){
        $report = ProjectReport::find($id);
        $report->department_id = $data['department_id'];
        $report->department_name = $data['department_name'];
        $report->project_id = $data['project_id'];
        $report->project_name = $data['project_name'];
        $report->year = $data['year'];
        $report->month = $data['month'];
        $report->status = $data['status'];
        $report->tested_tc = $data['tested_tc'];
        $report->loc = $data['loc'];
        $report->task = $data['task'];
        $report->weighted_bug = $data['weighted_bug'];
        $report->weighted_uat_bug = $data['weighted_uat_bug'];
        $report->actual_hour = $data['actual_hour'];
        $report->save();
        return $report->id;
    }

    public function findByAttribute($att, $name){
        return ProjectReport::where($att, $name)->first();
    }

    public function findByAttributes($att1, $name1, $att2, $name2){
        return ProjectReport::where($att1, $name1)
                        ->where($att2,$name2)->first();
    }


    /**
     *
     * @author Tampt
     *
     */
    public function deleteAllData() {
        ProjectReport::truncate();
    }

    public function saveDataProject($startDate, $endDate, $year, $month)
    {
        $projects = $this->project->getActiveProjects();
        $tasks = $this->project->countTasksOfProject($startDate, $endDate);
        $testCases = $this->project->getTestcasesForPQ($startDate, $endDate, 21);
        $locs = $this->project->getLocsForPQ($startDate, $endDate); // Get tickets have loc in a project
        $entries = $this->entry->getActualHourForPQ( $startDate, $endDate); // Get actual hour of tickets in a project
        $bugs = $this->project->getBugsForPQ(9, $startDate, $endDate);
        $bugsUat = $this->project->getBugsForPQ(10, $startDate, $endDate);
        if (count($projects) > 0) {
            foreach ($projects as $project) {
                $projectId = $project->id;
                $departmentId = $project->department_id;
                $testCase = $this->sumTestCase($testCases, $projectId, 'test_case'); // get total test case
                $loc = $this->count($locs, $projectId, 'loc'); // Get line of code of a project
                $task = $this->count($tasks, $projectId, 'countId'); // Get number of tasks of a project
                $actualHour = $this->count($entries, $projectId, 'actual_hour'); // Get workload of a project
                $weightedBug = $this->countWeightedBugs($bugs, $projectId);
                $weightedUatBug = $this->countWeightedBugs($bugsUat, $projectId);
                $data = [
                                'department_id' => $departmentId,
                                'department_name' => $project->department_name,
                                'project_id' => $projectId,
                                'status' => $project->status,
                                'project_name' => $project->name,

                                'year' => $year,
                                'month' => $month,
                                'tested_tc' => $testCase,
                                'task' => $task,
                                'loc' => $loc,
                                'weighted_bug' => $weightedBug,
                                'weighted_uat_bug' => $weightedUatBug,
                                'actual_hour' => $actualHour,
                ];
                $checkProject = $this->checkExistedData($departmentId, $projectId, $year, $month);
                if (count($checkProject) > 0) {
                    $this->update($data, $checkProject->id);
                } else {
                    $this->save($data);
                }
            }
        }
    }

    /**
     * Count the number of test case of a project
     * @author Tampt6722
     *
     * @param Object $objs
     * @param integer $projectId
     * @param string $att
     * @return number
     */
    public function sumTestCase($objs, $projectId, $att)
    {
        $count = 0;
        if (count($objs) > 0) {
            foreach ($objs as $obj) {
                if ($obj->project_id == $projectId)
                {
                    $count += $obj->$att;
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
    public function countWeightedBugs($bugs, $projectId) {
        $count = [];
        $bugWeight = 0;
        $weight = Config::get('constant.bug_weight');
        for ($i = 1; $i <= 5; $i++) {
            $count[$i] = $this->countTicketsWithWeight($bugs, $projectId, $i, 'countId');
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

    public function countTicketsWithWeight($objs, $projectId, $weightId, $att)
    {
        $count = 0;
        if (count($objs) > 0) {
            foreach ($objs as $obj) {
                if (($obj->project_id == $projectId) && ($obj->bug_weight_id == $weightId))
                {
                    $count = $obj->$att;
                    break;
                }
            }
        }

        return $count;
    }


    public function checkExistedData($departmentId, $projectId, $year, $month)
    {
        $query = ProjectReport::where('department_id', $departmentId)
        ->where('project_id', $projectId)
        ->where('year', $year)
        ->where('month', $month)
        ->first();
        return $query;
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
                if ($obj->project_id == $id) {
                    $count = $obj->$att;
                    break;
                }
            }
        }
        return $count;
    }

    /**
     *
     * @author tampt6722
     *
     * @param integer $year
     * @param array $month
     * @param integer $departmentId
     * @param integer $divisionId
     * @param integer $teamId
     * @param integer $projectId
     * @param array $projectMemberJoin
     * @param integer $status     *
     */
    public function getDataToView($year, $month, $departmentId, $divisionId,
            $teamId, $projectId, $projectMemberJoin, $status)
    {
        $query1 = ProjectReport::select('project_id','project_name','department_id','department_name',
                    DB::raw('sum(actual_hour) as actual_hour'),
                    DB::raw('sum(loc)  as loc'),
                    DB::raw('sum(task)  as task'),
                    DB::raw('sum(tested_tc)  as tested_tc'),
                    DB::raw('sum(weighted_bug)  as weighted_bug'),
                    DB::raw('sum(weighted_uat_bug)  as weighted_uat_bug'))
                    ->where('year', $year);
        $query2 = $this->__checkWhetherProjectIsNull($query1, $projectId, $departmentId,
                $divisionId, $teamId, $projectMemberJoin, $status);

        // If in_array(0, $month), search by months. Else search all
        if (!in_array(0, $month)) {
            $result = $query2->whereIn('month', $month)->groupBy('project_id');
        } else {
            $result = $query2->groupBy('project_id');
        }
        return $result->get();
    }

    /**
     *
     * @author tampt6722
     *
     * @param Query $query
     * @param integer $projectId
     * @param integer $departmentId
     * @param integer $divisionId
     * @param integer $teamId
     * @param array $projectMemberJoin
     * @param integer $status
     * @return QueryBuilder
     */
    private function __checkWhetherProjectIsNull($query, $projectId, $getDepartment,
                $getDivision, $getTeam, $projectMemberJoin, $status)
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
        // if status = 0, search all without status. Else, search by status
        if ($status != 0) {
            $result = $result->where('status', $status);
        }
        return $result;
    }

}