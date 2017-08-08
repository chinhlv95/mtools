<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExportMonthProject;
use App\Models\Project;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Entry\EntryRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\ProjectMember\ProjectMemberRepositoryInterface;
use Carbon\Carbon;
use Config;
use DateTime;
use App\Http\Controllers\ProjectCostController;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PHPExcel_Exception;
use WebDriver\back;

class ExportController extends Controller {

    public function __construct(EntryRepositoryInterface $entry, ProjectRepositoryInterface $project, UserRepositoryInterface $user, ProjectMemberRepositoryInterface $projectMembers) {
        $this->entry = $entry;
        $this->user = $user;
        $this->project = $project;
        $this->projectMembers = $projectMembers;
    }

    /**
     * Export excel template file with data
     *
     * @author thangdv
     * @return
     */
    public function export(ExportMonthProject $request) {
        $s_date = '1-' . str_replace('/', '-', $request->get('month'));
        $start_date = date('Y-m-d 00:00:00', strtotime($s_date));
        $start_date = Carbon::parse($start_date)->startOfMonth()->format("Y-m-d 00:00:00");
        $end_date = Carbon::parse($start_date)->lastOfMonth()->format("Y-m-d 23:59:59");
        $projectId = $request->get('project_id', '');
        $project = Project::find($projectId);
        $devison = $this->project->findDepartment($project->division_id);
        $projectMembers = $this->projectMembers
                ->getUserInProject($projectId)
                ->get();
        $entries = $this->entry
                        ->getEntryInTicket($projectId, $start_date, $end_date)->get();
        $excelTemplate = 'resources/views/xls_template/CostSummaryMonth.xlsx';
        $sheetName = 'Sheet1';
        try {
            Excel::load($excelTemplate, function($reader) use($project, $devison, $start_date, $end_date, $projectMembers, $entries, $sheetName) {
                $reader->sheet($sheetName, function($sheet) use($project, $devison, $start_date, $end_date, $projectMembers, $entries) {
                    $sheet->cell('C2', function($cell) use ($devison, $project) {
                        // manipulate the cell
                        if (!empty($devison))
                            $cell->setValue($devison['value']);
                    });
                    $sheet->cell('C2', function($cell) use ($devison) {
                        // manipulate the cell
                        if (!empty($devison))
                            $cell->setValue($devison['value']);
                    });
                    $sheet->cell('C3', function($cell) use($start_date) {
                        // manipulate the cell
                        $cell->setValue(date('Y-m-d', strtotime($start_date)));
                    });
                    $sheet->cell('C4', function($cell) use($project) {
                        // manipulate the cell
                        $cell->setValue($project->name);
                    });
                    $stt = 9;
                    foreach ($projectMembers as $member) {
                        $array = [];
                        $explodeEmail = explode('@', $member->email);
                        $idMemmber = substr($explodeEmail[0], -4, 4);
                        array_push($array, $idMemmber);
                        array_push($array, $member->last_name . " " . $member->first_name);
                        array_push($array, '');
                        array_push($array, "=E3*8");
                        array_push($array, '=SUM(F' . $stt . ":AP" . $stt . ")");
                        for ($i = strtotime($start_date); $i <= strtotime($end_date); $i = strtotime("+1 day", $i)) {
                            if (!empty($entries)) {
                                $check = false;
                                $total_cost = 0;
                                foreach ($entries as $entry) {
                                    if ($entry->assign_to_email == $member->email) {
                                        if (strtotime($entry->spent_at) == $i) {
                                            $check = true;
                                            $total_cost += $entry->actual_hour;
                                        }
                                    }
                                }
                                if (!$check)
                                    array_push($array, " ");
                                else
                                    array_push($array, $total_cost);
                            }
                        }
                        $sheet->row($stt++, $array);
                    }
                });
            })->export('xlsx');
        } catch (Exception $e) {
            return back()->with('message', 'Export error!');
        }
    }

    /**
     * Export total entry of project from a template
     *
     * @author thanhnb6719
     *
     */
    public function exportTotal(Request $request) {
        // Get data in url
        $getReportType = $request->get('reportType');
        $time = $request->get('check_time');
        $defaultTime = $request->get('date');
        $requestStartDate = $request->get('start_date');
        $requestEndDate = $request->get('end_date');
        $projectId = $request->get('project');
        $getDepartment = $request->get('department');
        $getDivision = $request->get('division');
        $getTeam = $request->get('team');
        $limit = $request->get('limit');

        // Get date
        $dateArray = $this->project->getTimeSearch($time, $defaultTime, $requestStartDate, $requestEndDate);
        $startDate = $dateArray['start'];
        $endDate = $dateArray['end'];

        // Get data for both case (no project ID - having project ID)
        $projectName = $this->project->getProjectInSearch($projectId, $getDepartment, $getDivision, $getTeam)->paginate($limit);
        $idProject = $projectName->pluck('id');
        $projectMembers = $this->projectMembers->getUserInProject($idProject)->get();

        $entries = $this->entry->getEntryInTicket($idProject, $startDate, $endDate, $getReportType);
        $excelTemplate = 'resources/views/xls_template/ExportTotalTemplate.xlsx';
        $sheetName = 'Sheet1';
        try {
            Excel::load($excelTemplate, function($reader) use($projectName, $projectMembers, $entries, $sheetName) {
                $reader->sheet($sheetName, function($sheet) use($projectName, $projectMembers, $entries) {
                    $rowStart = 6;
                    $stt = 0;
                    foreach ($projectName as $project) {
                        foreach ($projectMembers as $projectMems) {

                        }
                        $rowStartMerged = 6;
                        $rowEndMerged = 11;
                        $sheet->row($rowStart++, array(++$stt, $project->name, '3', '2'));
                        $sheet->setMergeColumn(array(
                            'columns' => array('A', 'B'),
                            'rows' => array(array($rowStartMerged, $rowEndMerged))
                        ));
                    }
                });
            })->export('xlsx');
        } catch (PHPExcel_Exception $e) {
            $errors = 'Error: ' . $e->getMessage();
            return $errors;
        }
    }

    /**
     * @author thangdv8182
     * Export total entry of project from a template
     * @param Request $request
     */
    public function exportCost(Request $request) {
        $reportType = Config::get('constant.cost_report_type');

        $getReportType = $request->get('reportType');
        $time = $request->get('check_time');
        $defaultTime = $request->get('date');
        $requestStartDate = $request->get('start_date');
        $requestEndDate = $request->get('end_date');

        $getDepartment = $request->get('department');
        $getDivision = $request->get('division');
        $getTeam = $request->get('team');
        $projectId = $request->get('project');
        $getStatus = $request->get('status');

        $limit = $request->get('limit', Config::get('constant.RECORD_PER_PAGE'));
        $number = ($request->get('page', '1') - 1) * $limit;

        $reportType       = Config::get('constant.cost_report_type');
//         dd($reportType);

        // Get date
        $dateArray = $this->project->getTimeSearch($time, $defaultTime, $requestStartDate, $requestEndDate);
        $startDate = $dateArray['start'];
        $endDate = $dateArray['end'];
        $entry = [];
        $projectName = [];
        $projectMembers = [];
        $allMember = [];
        $memberInProject = [];
        $allEntry = [];

        $groupCheck = $this->project->getGroupProjectMemberJoin("user.view_project_cost");
        $projectMemberJoin = $groupCheck['projectJoin'];
        $projectInSearch = $this->project->getProjectInSearch($projectId, $getDepartment, $getDivision, $getTeam, $getStatus, $projectMemberJoin);

        if ($getReportType == 'personal_report') {
            $allMember = $this->projectMembers->getMemberOrder($projectId, $getDepartment, $getDivision, $getTeam, $projectMemberJoin)->paginate($limit);
            $memberInProject = $this->projectMembers->getMemberInPersonalCost($projectId, $getDepartment, $getDivision, $getTeam, $projectMemberJoin)->get();
            $allEntry = $this->entry->getEntryOfPersonal($startDate, $endDate);
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
            Excel::create('Project_Cost', function($excel) use ($number, $startDate,
                    $endDate, $projectName, $getReportType, $projectMembers,$reportType,
                    $allMember, $memberInProject, $allEntry) {
                        $excel->sheet($reportType['personal_report'], function($sheet) use ($number, $startDate,
                        $endDate, $projectName, $getReportType, $projectMembers,
                        $allMember, $memberInProject, $allEntry) {
                    $sheet->loadView('cost.project.export')
                            ->with('number', $number)
                            ->with('start_date', $startDate)
                            ->with('end_date', $endDate)
                            ->with('listProjects', $projectName)
                            ->with('reportType', $getReportType)
                            ->with('projectMembers', $projectMembers)
                            ->with('allMember', $allMember)
                            ->with('memberInProject', $memberInProject)
                            ->with('allEntry', $allEntry)
                    ;
                });
            })->export('xls');
        } else {
            if ($getReportType == 'graph_report') {
                $projectName = null;
                $projectMembers = null;
                $idProject = $projectInSearch->pluck('id');
                $entry = $this->entry->getEntryInTicket($idProject, $startDate, $endDate, $getReportType, $getStatus);
            } elseif ($getReportType == 3) {

                $projectName = $projectInSearch->paginate($limit);
                if (count($projectName) > 0) {
                    if (ceil(abs(strtotime($endDate) - strtotime($startDate)) / 86400) > 31) {
                        $idProject = $projectName->pluck('id');
                        $projectMembers = $this->projectMembers->getUserInProject($idProject)->get();
                        $entry = $this->entry->getEntryInTicket($idProject, $startDate, $endDate, $getReportType, $getStatus);
                    } elseif ((ceil(abs(strtotime($endDate) - strtotime($startDate)) / 86400) > 7) && ceil(abs(strtotime($endDate) - strtotime($startDate)) / 86400) <= 31) {
                        $idProject = $projectName->pluck('id');
                        $projectMembers = $this->projectMembers->getUserInProject($idProject)->get();
                        $entry = $this->entry->getEntryInTicket($idProject, $startDate, $endDate, $getReportType, $getStatus);
                    } else {
                        if ($projectName != null) {
                            foreach ($projectName as $getProject) {
                                $pId = array($getProject->id);
                                $projectMembers[] = ['project_id' => $getProject->id, 'member' => $this->projectMembers->getUserInProject($pId)->get()];
                                $entry[] = ['project_id' => $getProject->id, 'entry' => $this->entry->getEntryInTicket($pId, $startDate, $endDate, 3, $getStatus)];
                            }
                        }
                    }
                } else {
                    $entry = [];
                    $projectMembers = [];
                }
            } elseif ($getReportType == 'personal_detail_report') {

                $listUserName = $this->user->getListUserOfTeam($getTeam)->pluck('users.user_name', 'users.related_id');
                $listUserId = array_keys($listUserName->toArray());
                $requestMonth = $request->get('month');
                $requestYear = $request->get('year');
                $query_date = $requestYear . '-' . $requestMonth . '-04';
                $date = new DateTime($query_date);
                //First day of month
                $date->modify('first day of this month');
                $firstday = $date->format('Y-m-d 00:00:00');
                //Last day of month
                $date->modify('last day of this month');
                $lastday = $date->format('Y-m-d 23:59:59');

                $projects = [];

                $datas = $this->entry->getEntryOfPersonalWithTickets($firstday, $lastday, $listUserId);

                Excel::create('Project_Cost', function($excel) use ($number, $firstday,
                        $lastday, $listUserName, $getReportType, $projectMembers,
                        $allMember, $projects, $datas) {
                    foreach ($listUserName as $key => $user) {
                        $excel->sheet($user, function($sheet) use ($number, $firstday,
                                $lastday, $user, $key, $getReportType, $projectMembers,
                                $allMember, $projects, $datas) {
                            $sheet->loadView('cost.project.export')
                                    ->with('number', $number)
                                    ->with('start_date', $firstday)
                                    ->with('end_date', $lastday)
                                    ->with('user', $user)
                                    ->with('key', $key)
                                    ->with('reportType', $getReportType)
                                    ->with('projectMembers', $projectMembers)
                                    ->with('allMember', $allMember)
                                    ->with('projects', $projects)
                                    ->with('datas', $datas)
                            ;
                        });
                    }
                })->export('xls');
                return view('cost.project.export', ['listUser' => $listUser,
                    'reportType' => $reportType,
                    'start_date' => $firstday,
                    'end_date' => $lastday,
                    'projects' => $projects,
                    'datas' => $datas,
                ]);
            } else {

                $projectName = $projectInSearch->paginate($limit);
                $idProject = $projectName->pluck('id');
                $projectMembers = $this->projectMembers->getUserInProject($idProject)->get();
                $entry = $this->entry->getEntryInTicket($idProject, $startDate, $endDate, $getReportType, $getStatus);
                if ($getReportType == 'summary_report') {

                    if (!empty($projectName)) {
                        foreach ($projectName as $eachProject) {
                            foreach ($projectMembers as $key => $member) {
                                if ($eachProject->id == $member->project_id) {
                                    $personalTime[$key] = 0;
                                    foreach ($entry as $e) {
                                        if ($e->user_id == $member->user_id && $eachProject->id == $e->all_project_id) {
                                            $personalTime[$key] = $e->actual_hour;
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
            try {
                Excel::create('Project_Cost', function($excel) use ($entry, $number, $startDate,$reportType,
                        $endDate, $projectName, $getReportType, $projectMembers,
                        $memberInProject, $allEntry) {
                            $excel->sheet($reportType[$getReportType], function($sheet) use ($entry, $number, $startDate,
                            $endDate, $projectName, $getReportType, $projectMembers,
                            $memberInProject, $allEntry) {
                        $sheet->loadView('cost.project.export')
                                ->with('entry', $entry)
                                ->with('number', $number)
                                ->with('start_date', $startDate)
                                ->with('end_date', $endDate)
                                ->with('listProjects', $projectName)
                                ->with('reportType', $getReportType)
                                ->with('projectMembers', $projectMembers)
                                ->with('memberInProject', $memberInProject)
                                ->with('allEntry', $allEntry)
                        ;
                    });
                })->export('xls');
            } catch (Exception $e) {
                return back()->with('message', 'Export error!');
            }
        }
    }

    public function addItemUsers($allMember, $member, $memberInProject, $allEntry, $key) {
        if (!empty($allMember)) {
            $workTime = 0;
            $projectName = [];
            foreach ($memberInProject as $memberData) {
                if ($memberData->id == $member->id) {
                    $workTime += 8 * ($memberData->assign);
                    $projectName[] = $memberData->project_name;
                }
            }

            $allMember[$key]->workTime = $workTime;
            $allMember[$key]->projectName = $projectName;
            $standardTime = $workTime * 21;
            $minTime = $standardTime * 0.9;
            $maxTime = $standardTime * 1.1;
            $allMember[$key]->standardTime = $standardTime;
            $allMember[$key]->minTime = $minTime;
            $allMember[$key]->maxTime = $maxTime;

            $personalEntry = 0;
            foreach ($allEntry as $entry) {
                if ($entry->id == $member->id) {
                    $personalEntry += $entry->actual_hour;
                }
            }
            $allMember[$key]->personalEntry = $personalEntry;
            $underTime = $standardTime - $personalEntry;
            $overTime = $personalEntry - $maxTime;
            $allMember[$key]->underTime = $underTime;
            $allMember[$key]->overTime = $overTime;
        }
        return $allMember;
    }

}
