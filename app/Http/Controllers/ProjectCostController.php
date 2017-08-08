<?php

namespace App\Http\Controllers;

use App\Repositories\Api\ApiRepositoryInterface;
use App\Repositories\Entry\EntryRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\ProjectMember\ProjectMemberRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use DateTime;
use Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ProjectCostController extends Controller
{
    public function __construct(ApiRepositoryInterface $api,
                                UserRepositoryInterface $user,
                                EntryRepositoryInterface $entry,
                                ProjectRepositoryInterface $project,
                                ProjectMemberRepositoryInterface $projectMembers)
    {
        $this->api            = $api;
        $this->user           = $user;
        $this->entry          = $entry;
        $this->project        = $project;
        $this->projectMembers = $projectMembers;
    }

    /**
     * Display a listing of the resource.
     *
     * @author thanhnb6719
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Get data in url
        $getReportType    = $request->get('reportType');
        $time             = $request->get('check_time');
        $defaultTime      = $request->get('date');
        $requestStartDate = $request->get('start_date');
        $requestEndDate   = $request->get('end_date');
        $requestMonth     = $request->get('month');
        $requestYear      = $request->get('year');
        $getStatus        = $request->get('status','');
        $getDepartment    = $request->get('department',-1);
        $getDivision      = $request->get('division',-1);
        $getTeam          = $request->get('team',-1);
        $projectId        = $request->get('project',-1);
        $limit            = $request->get('limit', 5);
        $selectDate       = Config::get('constant.report_select_date');
        $paginate         = [
            5 => '5',
            10 => '10',
            20 => '20',
            30 => '30',
            50 => '50'
        ];
        $arr_role = [
                'General Director',
                'Department Manager',
                'Division Manager',
                'Team Leader',
                'PM',
                'DevL',
                'QAL',
                'BSE/VN',
                'BSE/JP',
                'Sub BSE'
        ];

        $reportType       = Config::get('constant.cost_report_type');
        $status           = Config::get('constant.status');
        $number           = ($request->get('page','1') - 1)* $limit;

        // Get data to fill select box
        $groupCheck = $this->project->getGroupProjectMemberJoin("user.view_project_cost");
        $departments       = $groupCheck['departments'];
        $projectMemberJoin = $groupCheck['projectJoin'];

        $searchGroup = $this->project->saveDeparamentSearch($getDepartment,$getDivision,$getTeam,$groupCheck['divisions'],$groupCheck['teams'],$groupCheck['projects']);
        $divisions         = $searchGroup['divisions'];
        $teams             = $searchGroup['teams'];
        $projects          = $searchGroup['projects'];

        $dateArray        = $this->project->getTimeSearch($time, $defaultTime, $requestStartDate, $requestEndDate);
        $startDate        = $dateArray['start'];
        $endDate          = $dateArray['end'];
        $firstDateDefault = date('d/m/Y', strtotime('first day of this month'));
        $endDateDefault   = date('d/m/Y', strtotime('last day of this month'));
        // Get list project after search
        $projectInSearch  = $this->project->getProjectInSearch($projectId, $getDepartment, $getDivision, $getTeam, $getStatus, $projectMemberJoin);
        $managerIds       = $this->user->getManagerId();
        if (Sentinel::check()) {
            $userId           = Sentinel::getUser()->id;
            if (in_array($userId, $managerIds)) {
                $roleUser = "manager";
            } else {
                $roleUser = "";
            }
        }
        // Get entry after search
        if ($getReportType == 'personal_report') {
            $allMember = $this->projectMembers->getMemberOrder($projectId, $getDepartment, $getDivision, $getTeam, $projectMemberJoin)->paginate($limit);
            $memberInProject = $this->projectMembers->getMemberInPersonalCost($projectId, $getDepartment, $getDivision, $getTeam, $projectMemberJoin)->get();
            $allEntry = $this->entry->getEntryOfPersonal($startDate, $endDate);
            $listProjectSearch = $projectInSearch->get();

            foreach ($allMember as $key => $member) {
                $allMember = $this->addItemUsers($allMember, $member, $memberInProject, $allEntry, $key);
                $subUsers = $this->projectMembers->getSubMemberOrder($member->id, $projectId, $getDepartment, $getDivision, $getTeam, $projectMemberJoin)->get();
                if (!empty($subUsers)) {
                    foreach ($subUsers as $keySub => $subUser) {
                        $subUsers = $this->addItemUsers($subUsers, $subUser, $memberInProject, $allEntry, $keySub);
                    }
                    foreach ($subUsers as $sub) {
                        $allMember[$key]->workTime += $sub->workTime;
                        $allMember[$key]->standardTime += $sub->standardTime;
                        $allMember[$key]->minTime += $sub->minTime;
                        $allMember[$key]->maxTime += $sub->maxTime;
                        $allMember[$key]->personalEntry += $sub->personalEntry;
                        $allMember[$key]->underTime += $sub->underTime;
                        $allMember[$key]->overTime += $sub->overTime;
                    }
                }
            }
            $countNumProject = 0;
            foreach ($allMember as $key => $value) {
                if ($value->countProject > $countNumProject) {
                    $countNumProject = $value->countProject;
                }
            }
            return view('cost.project.index', [
                'start_date'       => $startDate,
                'end_date'         => $endDate,
                'reportType'       => $reportType,
                'departments'      => $departments,
                'divisions'        => $divisions,
                'teams'            => $teams,
                'select_date'      => $selectDate,
                'paginate'         => $paginate,
                'memberInProject'  => $memberInProject,
                'allMember'        => $allMember,
                'number'           => $number,
                'projects'         => $projects,
                'projectInSearch'  => $listProjectSearch,
                'allEntry'         => $allEntry,
                'status'           => $status,
                'firstDateDefault' => $firstDateDefault,
                'endDateDefault'   => $endDateDefault,
                'countNumProject'  => $countNumProject,
                'roleUser'         => $roleUser,
                'isAdmin' => ((isset(Sentinel::check()->permissions) && in_array(array_keys(Sentinel::check()->permissions)[0], $arr_role)) || (Sentinel::getUser()->inRole(Sentinel::findRoleById(1)->slug) == 1)) ? 1 : 0
            ]);
        } else {
            if ($getReportType == 'graph_report') {
                $projectName    = null;
                $projectMembers = null;
                $listProjectSearch = $projectInSearch->get();
                $idProject      = $projectInSearch->pluck('id');
                $entry          = $this->entry->getEntryInTicket($idProject, $startDate, $endDate, $getReportType, $getStatus);
            } elseif ($getReportType == 'entries_detail_report') {
                $listProjectSearch = $projectInSearch->get();
                $projectName    = $projectInSearch->paginate($limit);
                if (count($projectName) > 0) {
                    if ((ceil(abs(strtotime($endDate) - strtotime($startDate)) / 86400) > 31)) {
                        $idProject      = $projectName->pluck('id');
                        $projectMembers = $this->projectMembers->getUserInProject($idProject)->get();
                        $entry          = $this->entry->getEntryInTicket($idProject, $startDate, $endDate, $getReportType, $getStatus);
                        $period = Helpers::findMonthInPeriodOfTime($startDate, $endDate);
                        if (!empty($projectName)) {
                            foreach ($projectName as $eachProject) {
                                foreach ($projectMembers as $key => $member) {
                                    if ($eachProject->id == $member->project_id) {
                                        $totalTime = 0;
                                        foreach ($entry['total'] as $total) {
                                            if ($total->all_project_id == $eachProject->id && $total->user_email == $member->email){
                                                $totalTime = $total->actual_hour;
                                            }
                                        }
                                        $projectMembers[$key]->totalTime = $totalTime;
                                        $entryHour = [];
                                        foreach ($period as $keyPeriod => $month) {
                                            $entryH = "";
                                            $entryHour[$keyPeriod] = $entryH;
                                            foreach ($entry['eachMonth'] as $e) {
                                                $entryDate = $e->spent_month.'/'.$e->spent_year;
                                                if (($entryDate == $month->format("m/Y")
                                                        || '0'.$entryDate == $month->format("m/Y"))
                                                        && $e->all_project_id == $eachProject->id
                                                        && $e->user_email == $member->email){
                                                    $entryH += $e->actual_hour;
                                                }
                                            }
                                            $entryHour[$keyPeriod] = $entryH;
                                        }
                                        $projectMembers[$key]->entryHour = $entryHour;
                                    }
                                }
                            }
                            $projectMembers = $this->mapUsers($projectMembers, 'totalTime', 'entryHour');
                        }
                    } elseif ((ceil(abs(strtotime($endDate) - strtotime($startDate)) / 86400) > 7) && ceil(abs(strtotime($endDate) - strtotime($startDate)) / 86400) <= 31) {
                        $idProject      = $projectName->pluck('id');
                        $projectMembers = $this->projectMembers->getUserInProject($idProject)->get();
                        $entry          = $this->entry->getEntryInTicket($idProject, $startDate, $endDate, $getReportType, $getStatus);
                        $period = Helpers::findWeekInPeriodOfTime($startDate, $endDate);
                        if (!empty($projectName)) {
                            foreach ($projectName as $eachProject) {
                                foreach ($projectMembers as $key => $member) {
                                    if ($eachProject->id == $member->project_id) {
                                        $totalTime = 0;
                                        foreach ($entry['total'] as $total) {
                                            if($total->all_project_id == $eachProject->id && $total->user_email == $member->email) {
                                                $totalTime = $total->actual_hour;
                                            }
                                        }
                                        $projectMembers[$key]->totalTime = $totalTime;
                                        $entryHour = [];
                                        foreach ($period as $keyPeriod => $week) {
                                            $entryH = "";
                                            $entryHour[$keyPeriod] = $entryH;
                                            foreach ($entry['eachWeek'] as $e) {
                                                $entryDate = $e->spent_week.'/'.$e->spent_year;
                                                if (($entryDate == $week->format("W/Y")
                                                        || '0'.$entryDate == $week->format("W/Y"))
                                                        && $e->all_project_id == $eachProject->id
                                                        && $e->user_email == $member->email){
                                                    $entryH += $e->actual_hour;
                                                }
                                            }
                                            $entryHour[$keyPeriod] = $entryH;
                                        }
                                        $projectMembers[$key]->entryHour = $entryHour;
                                    }
                                }
                            }
                            $projectMembers = $this->mapUsers($projectMembers, 'totalTime', 'entryHour');
                        }
                    } else {
                        if ($projectName != null) {
                            foreach ($projectName as $getProject) {
                                $pId            = array($getProject->id);
                                $projectMember = $this->projectMembers->getUserInProject($pId)->get();
                                $enTry = $this->entry->getEntryInTicket($pId, $startDate, $endDate, 3, $getStatus);
                                foreach ($projectMember as $key => $member) {
                                    $total = 0;
                                    $totalEachCell = [];
                                    for ($i = strtotime($startDate); $i <= strtotime($endDate); $i = strtotime("+1 day", $i)) {
                                        $totaleachcell = 0;
                                        $totalEachCell[$i] = $totaleachcell;
                                        if (!empty($enTry)) {
                                            if ($getProject->id == $getProject->id) {
                                                foreach ($enTry as $e) {
                                                    if ($e->email == $member->email) {
                                                        if (strtotime($e->personal_spent_at) == $i) {
                                                            $total += $e->personal_actual_hour;
                                                            $totaleachcell += $e->personal_actual_hour;
                                                            $totalEachCell[$i] = $totaleachcell;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        $projectMember[$key]->totalEachCell = $totalEachCell;
                                    }
                                    $projectMember[$key]->total = $total;
                                }
                                $projectMember = $this->mapUsers($projectMember, 'total', 'totalEachCell');
                                $projectMembers[] = ['project_id' => $getProject->id, 'member' => $projectMember];
                                $entry[]          = ['project_id' => $getProject->id, 'entry' => $enTry];
                            }
                        }
                    }
                } else {
                    $entry = [];
                    $projectMembers = [];
                }
            } elseif ($getReportType == 'personal_detail_report') {
                if ($getTeam == -1) {
                    return redirect()->back()->with('errorsMessage', 'Personal Detail Report must search with team!');
                } else {
                    $listUserName = $this->user->getListUserOfTeam($getTeam)->pluck('users.user_name','users.related_id');
                    $listUserId   = array_keys($listUserName->toArray());
                    $query_date   = $requestYear.'-'.$requestMonth.'-04';
                    $date = new DateTime($query_date);
                    //First day of month
                    $date->modify('first day of this month');
                    $firstday= $date->format('Y-m-d 00:00:00');
                    //Last day of month
                    $date->modify('last day of this month');
                    $lastday= $date->format('Y-m-d 23:59:59');
                    $datas = $this->entry->getEntryOfPersonalWithTickets($firstday, $lastday, $listUserId);
                    if (count($datas) > 0) {
                        foreach ($datas as $data) {
                            $listUser[$data->user_id] = $data->user_name;
                        }
                    } else {
                        $listUser = [];
                    }
                    return view('cost.project.index', [
                        'listUser'          => $listUser,
                        'reportType'        => $reportType,
                        'select_date'       => $selectDate,
                        'start_date'        => $firstday,
                        'end_date'          => $lastday,
                        'firstDateDefault'  => $firstDateDefault,
                        'endDateDefault'    => $endDateDefault,
                        'status'            => $status,
                        'projects'          => $projects,
                        'departments'       => $departments,
                        'divisions'         => $divisions,
                        'teams'             => $teams,
                        'datas'             => $datas,
                        'roleUser'          => $roleUser,
                        'isAdmin' => ((isset(Sentinel::check()->permissions) && in_array(array_keys(Sentinel::check()->permissions)[0], $arr_role)) || (Sentinel::getUser()->inRole(Sentinel::findRoleById(1)->slug) == 1)) ? 1 : 0
                    ]);
                }
            } else {
                $listProjectSearch = $projectInSearch->get();
                $projectName    = $projectInSearch->paginate($limit);
                $idProject      = $projectName->pluck('id');
                $projectMembers = $this->projectMembers->getUserInProject($idProject)->get();
                $entry          = $this->entry->getEntryInTicket($idProject, $startDate, $endDate, $getReportType, $getStatus);
                if ($getReportType == 'summary_report') {
                    if (!empty($projectName)) {
                        foreach ($projectName as $eachProject) {
                            foreach ($projectMembers as $key => $member) {
                                if ($eachProject->id == $member->project_id) {
                                    $personalTime[$key] = 0;
                                    foreach ($entry as $e) {
                                        if ($e->user_id == $member->user_id && $eachProject->id == $e->all_project_id) {
                                            $personalTime[$key] += $e->actual_hour;
                                        }
                                    }
                                    $projectMembers[$key]->personalTime = $personalTime[$key];
                                    $subUsers = $this->user->getSubUsers($member->user_id);
                                    foreach ($subUsers as $keySub => $subUser) {
                                        $personalTimeSub[$keySub] = 0;
                                        foreach ($entry as $e) {
                                            if ($e->user_id == $subUser->id && $eachProject->id == $e->all_project_id) {
                                                $personalTimeSub[$keySub] = $e->actual_hour;
                                            }
                                        }
                                        $projectMembers[$key]->personalTime += $personalTimeSub[$keySub];
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            return view('cost.project.index', [
                'entry'          => $entry,
                'number'         => $number,
                'projects'       => $projects,
                'projectInSearch'=> $listProjectSearch,
                'start_date'     => $startDate,
                'end_date'       => $endDate,
                'paginate'       => $paginate,
                'listProjects'   => $projectName,
                'projectMembers' => $projectMembers,
                'departments'    => $departments,
                'divisions'      => $divisions,
                'teams'          => $teams,
                'reportType'     => $reportType,
                'select_date'    => $selectDate,
                'status'         => $status,
                'firstDateDefault' => $firstDateDefault,
                'endDateDefault' => $endDateDefault,
                'roleUser'          => $roleUser,
                'isAdmin' => ((isset(Sentinel::check()->permissions) && in_array(array_keys(Sentinel::check()->permissions)[0], $arr_role)) || (Sentinel::getUser()->inRole(Sentinel::findRoleById(1)->slug) == 1)) ? 1 : 0
            ]);
        }
    }

    public function mapUsers($projectMembers, $totalTime, $entry)
    {
        for ($i = 0; $i < count($projectMembers); $i++) {
            if ($projectMembers[$i]->user_id != $projectMembers[$i]->related_id) {
                $mainUser = Sentinel::findById($projectMembers[$i]->related_id);
                $projectMembers[$i]->email = $mainUser->email;
                $projectMembers[$i]->user_position = $mainUser->position;
                $projectMembers[$i]->first_name = $mainUser->first_name;
                $projectMembers[$i]->last_name = $mainUser->last_name;
                $projectMembers[$i]->$totalTime = $projectMembers[$i]->$totalTime;
                if (!empty($entry)) {
                    $projectMembers[$i]->$entry = $projectMembers[$i]->$entry;
                }
            }
            for ($j = 0; $j < count($projectMembers); $j++) {
                if ($projectMembers[$i]->related_id == $projectMembers[$j]->related_id
                    && $i != $j && $projectMembers[$i]->project_id == $projectMembers[$j]->project_id
                    && $projectMembers[$i]->user_id == $projectMembers[$i]->related_id)
                {
                    $mainUser = Sentinel::findById($projectMembers[$i]->related_id);
                    $projectMembers[$i]->email = $mainUser->email;
                    $projectMembers[$i]->user_position = $mainUser->position;
                    $projectMembers[$i]->first_name = $mainUser->first_name;
                    $projectMembers[$i]->last_name = $mainUser->last_name;
                    $projectMembers[$i]->$totalTime = $projectMembers[$i]->$totalTime + $projectMembers[$j]->$totalTime;
                    if (!empty($entry)) {
                        $entryI = $projectMembers[$i]->$entry;
                        foreach ($projectMembers[$j]->$entry as $key => $value) {
                            $entryI[$key] += $value;
                        }
                        $projectMembers[$i]->$entry = $entryI;
                    }
                    $keyMaps[] = $j;
                }
            }
        }
        if (!empty($keyMaps)) {
            foreach ($keyMaps as $keyMap) {
                unset($projectMembers[$keyMap]);
            }
        }
        return $projectMembers;
    }

    public function addItemUsers($allMember, $member, $memberInProject, $allEntry, $key)
    {
        if (!empty($allMember)) {
            $workTime = 0;
            $countProject = 0;
            $projectName = [];
            foreach ($memberInProject as $memberData) {
                if ($memberData->id == $member->id) {
                    $workTime += 8*($memberData->assign);
                    $projectName[] = $memberData->project_name;
                    $countProject++;
                }
            }

            $allMember[$key]->workTime = $workTime;
            $allMember[$key]->projectName = $projectName;
            $standardTime = $workTime*21;
            $minTime      = $standardTime*0.9;
            $maxTime      = $standardTime*1.1;
            $allMember[$key]->standardTime = $standardTime;
            $allMember[$key]->minTime = $minTime;
            $allMember[$key]->maxTime = $maxTime;

            $personalEntry = 0;
            foreach ($allEntry as $entry){
                if ($entry->id == $member->id){
                    $personalEntry += $entry->actual_hour;
                }
            }
            $allMember[$key]->personalEntry = $personalEntry;
            $underTime = $standardTime - $personalEntry;
            $overTime  = $personalEntry - $maxTime;
            $allMember[$key]->underTime = $underTime;
            $allMember[$key]->overTime = $overTime;
            $allMember[$key]->countProject = $countProject;
        }
        return $allMember;
    }
}
