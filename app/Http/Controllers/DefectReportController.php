<?php

namespace App\Http\Controllers;

use Config;

use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Ticket\TicketRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\TicketType\TicketTypeRepositoryInterface;
use App\Repositories\ProjectMember\ProjectMemberRepositoryInterface;
use App\Repositories\Api\ApiRepositoryInterface;
use App\Models\Ticket;
use App\Models\BugWeight;
use DB;
use App\Repositories\BugWeight\BugWeightRepositoryInterface;
use App\Repositories\Status\StatusRepositoryInterface;
use App\Repositories\Cost\CostRepositoryInterface;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\DefectReportSearchRequest;
use Illuminate\Http\Request;
use Validator;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\Project;
use App\Models\Department;
use PHPExcel_Worksheet_Drawing;

class DefectReportController extends Controller
{
    /**
     * @author thangdv8182
     * @param ApiRepositoryInterface $api
     * @param UserRepositoryInterface $user
     * @param TicketRepositoryInterface $ticket
     * @param ProjectRepositoryInterface $project
     * @param TicketTypeRepositoryInterface $ticketType
     * @param ProjectMemberRepositoryInterface $projectMembers
     * @param BugWeightRepositoryInterface $bugWeight
     * @param StatusRepositoryInterface $status
     */
    public function __construct(ApiRepositoryInterface $api,
            UserRepositoryInterface $user,
            TicketRepositoryInterface $ticket,
            ProjectRepositoryInterface $project,
            TicketTypeRepositoryInterface $ticketType,
            ProjectMemberRepositoryInterface $projectMembers,
            BugWeightRepositoryInterface $bugWeight,
            StatusRepositoryInterface $status,
            DepartmentController $dptCon
            )
    {
        $this->api            = $api;
        $this->user           = $user;
        $this->ticket         = $ticket;
        $this->project        = $project;
        $this->ticketType     = $ticketType;
        $this->bugWeight      = $bugWeight;
        $this->projectMembers = $projectMembers;
        $this->status         = $status;
        $this->dptCon         = $dptCon;
    }
    public function index(Request $request)
    {
        if(!empty($project_id))
        {
            if(!in_array($project_id, $projectMemberJoin))
            {
                $errorsMessage = 'Wrong Project ID';
                return redirect()->back()->with('errorsMessage', $errorsMessage);
            }
        }
        $select_date = Config::get('constant.report_select_date');
        $report_type = Config::get('constant.report_type');
        $units_date  = Config::get('constant.units_time');
        $desToolTips = Config::get('constant.description_tool_tip');
        $bug_weight  = Config::get('constant.bug_weight');
        $bug_type    = Config::get('constant.bug_type');

        $getDepartment    = $request->get('department',-1);
        $getDivision      = $request->get('division',-1);
        $getTeam          = $request->get('team',-1);

        $project_id       = $request->get('project',-1);
        $time             = $request->get('check_time','');
        $defaultTime      = $request->get('date','');
        $requestStartDate = $request->get('start_date','');
        $requestEndDate   = $request->get('end_date','');

        $groupCheck = $this->project->getGroupProjectMemberJoin("user.view_defect");
        $departments       = $groupCheck['departments'];
        $projectMemberJoin = $groupCheck['projectJoin'];

        $searchGroup = $this->project->saveDeparamentSearch($getDepartment,$getDivision,$getTeam,$groupCheck['divisions'],$groupCheck['teams'],$groupCheck['projects']);
        $divisions         = $searchGroup['divisions'];
        $teams             = $searchGroup['teams'];
        $projects          = $searchGroup['projects'];

        $userId            = Sentinel::check()->id;
        if($project_id != -1 && Sentinel::getUser()->inRole(Sentinel::findRoleById(1)->slug) == false){
            $projectImport = $this->project->getProjectRole($project_id, $userId);
//             $projectImport = array(
//                 $this->project->find($project_id)
//             );

        }  else {
            $projectImport = $projects;

        }

        $requestReportType = $request->get('report_type');
        $dateArray        = $this->project->getTimeSearch($time, $defaultTime, $requestStartDate, $requestEndDate);
        $startDate        = $dateArray['start'];
        $endDate          = $dateArray['end'];
        $firstDateDefault = date('d/m/Y', strtotime('first day of this month'));
        $endDateDefault   = date('d/m/Y', strtotime('last day of this month'));

        $tickets_status_name = [];
        $array_bug = [];
        $array_uat = [];
        $array_found = [];
        $array_close = [];
        $name_make_bug = [];
        $name_found_bug = [];
        $name_fix_bug= [];
        $name_root_cause = [];
        $ticketsMakeBug = [];
        $ticketsFoundBug = [];
        $ticketsFixBug = [];

        $tickets = $this->status->getTicketsByStatus($startDate, $endDate, $getDepartment, $getDivision, $getTeam, $projectMemberJoin,$project_id);
//         dd($tickets);
        if($request->get('report_type') == 'summary' || empty($request->get('report_type')))
        {
            //by status
            $tickets_status_name = $this->status->getStatusName($tickets,'status_related');
            //by who make

            $ticketsMakeBug = DB::select("
                    SELECT
                     `tickets`.*,
                     `status`.`name` AS name_status,
                     `bugs_weight`.`related_id` AS bug_weight_related,
                     `ticket_type`.`name` AS ticket_type_name,
                     `root_cause`.`name` AS root_cause_name,
                     `ticket_type`.`related_id` AS ticket_type_related,
                     `status`.`related_id` AS status_related,
                     `root_cause`.`related_id` AS root_cause_related,
                     `users`.`related_id` AS users_related_id
                    FROM
                     tickets
                    JOIN `ticket_type` ON tickets.ticket_type_id = ticket_type.id
                    JOIN `projects` ON tickets.project_id = projects.id
                    JOIN `status` ON tickets.status_id = status.id
                    JOIN `root_cause` ON tickets.root_cause_id = root_cause.id
                    JOIN `bugs_weight` ON tickets.bug_weight_id = bugs_weight.id
                    JOIN `users` ON tickets.made_by_user = users.user_name
                     AND
                     `users`.`source_id` =
                     (SELECT
                      CASE tickets.source_id
                       WHEN 0 THEN 5
                       WHEN 1 THEN 1
                       WHEN 2 THEN 2
                       WHEN 3 THEN 3
                       WHEN 4 THEN 4
                     END AS tickets_source_id)
                    WHERE
                     tickets.project_id = '{$project_id}'
                    AND tickets.integrated_created_at BETWEEN '{$startDate}' AND '{$endDate}'
                    AND ticket_type.related_id IN (9, 10)
            ");

            foreach($ticketsMakeBug as $key=>$item)
            {
                $ticketsMakeBug[$key] = (array) $item;
            }
            $name_make_bug = $this->status->getStatusName($ticketsMakeBug,'users_related_id');
// //             //by who found

            $ticketsFoundBug= DB::select("
                    SELECT
                    `tickets`.*,
                    `status`.`name` AS name_status,
                    `bugs_weight`.`related_id` AS bug_weight_related,
                    `ticket_type`.`name` AS ticket_type_name,
                    `root_cause`.`name` AS root_cause_name,
                    `ticket_type`.`related_id` AS ticket_type_related,
                    `status`.`related_id` AS status_related,
                    `root_cause`.`related_id` AS root_cause_related,
                    `users`.`related_id` AS users_related_id
                    FROM
                    tickets
                    JOIN `ticket_type` ON tickets.ticket_type_id = ticket_type.id
                    JOIN `projects` ON tickets.project_id = projects.id
                    JOIN `status` ON tickets.status_id = status.id
                    JOIN `root_cause` ON tickets.root_cause_id = root_cause.id
                    JOIN `bugs_weight` ON tickets.bug_weight_id = bugs_weight.id
                    JOIN `users` ON tickets.created_by_user = users.user_name
                    AND
                    `users`.`source_id` =
                    (SELECT
                    CASE tickets.source_id
                    WHEN 0 THEN 5
                    WHEN 1 THEN 1
                    WHEN 2 THEN 2
                    WHEN 3 THEN 3
                    WHEN 4 THEN 4
                    END AS tickets_source_id)
                    WHERE
                    tickets.project_id = '{$project_id}'
                    AND tickets.integrated_created_at BETWEEN '{$startDate}' AND '{$endDate}'
                    AND ticket_type.related_id IN (9, 10)
                    ");

            foreach($ticketsFoundBug as $key=>$item)
            {
                $ticketsFoundBug[$key] = (array) $item;
            }
            $name_found_bug = $this->status->getStatusName($ticketsFoundBug,'users_related_id');

            //by who fix
            $ticketsFixBug= DB::select("
                    SELECT
                    `tickets`.*,
                    `status`.`name` AS name_status,
                    `bugs_weight`.`related_id` AS bug_weight_related,
                    `ticket_type`.`name` AS ticket_type_name,
                    `root_cause`.`name` AS root_cause_name,
                    `ticket_type`.`related_id` AS ticket_type_related,
                    `status`.`related_id` AS status_related,
                    `root_cause`.`related_id` AS root_cause_related,
                    `users`.`related_id` AS users_related_id
                    FROM
                    tickets
                    JOIN `ticket_type` ON tickets.ticket_type_id = ticket_type.id
                    JOIN `projects` ON tickets.project_id = projects.id
                    JOIN `status` ON tickets.status_id = status.id
                    JOIN `root_cause` ON tickets.root_cause_id = root_cause.id
                    JOIN `bugs_weight` ON tickets.bug_weight_id = bugs_weight.id
                    JOIN `users` ON tickets.assign_to_user = users.user_name
                    AND
                    `users`.`source_id` =
                    (SELECT
                    CASE tickets.source_id
                    WHEN 0 THEN 5
                    WHEN 1 THEN 1
                    WHEN 2 THEN 2
                    WHEN 3 THEN 3
                    WHEN 4 THEN 4
                    END AS tickets_source_id)
                    WHERE
                    tickets.project_id = '{$project_id}'
                    AND tickets.integrated_created_at BETWEEN '{$startDate}' AND '{$endDate}'
                    AND ticket_type.related_id IN (9, 10)
                    ");

            foreach($ticketsFixBug as $key=>$item)
            {
                $ticketsFixBug[$key] = (array) $item;
            }
            $name_fix_bug = $this->status->getStatusName($ticketsFixBug,'users_related_id');

// //             //by root cause
            $name_root_cause = $this->status->getStatusName($tickets, 'root_cause_related');
        }

        if($request->get('report_type') == 'time' || empty($request->get('report_type')))
        {
            //by uat and bug
            $bugArray = $this->status->getBugUat($tickets,$startDate,$endDate,$request->get('units_time'),$request->get('type_bug'),$bug_weight);
            $array_bug = $bugArray['array_bug'];
            $array_uat = $bugArray['array_uat'];

            //by status open or close
            $bugFoundClose = $this->status->getOpenClose($tickets,$startDate,$endDate,$request->get('units_time'),$request->get('type_bug'),$bug_weight);
            $array_found = $bugFoundClose['array_found'];
            $array_close = $bugFoundClose['array_close'];
        }
        $managerIds       = $this->user->getManagerId();
        if (Sentinel::check()) {
            $userId           = Sentinel::getUser()->id;
            if (in_array($userId, $managerIds)) {
                $roleUser = "manager";
            } else {
                $roleUser = "";
            }
        }
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

        return view('defect_report.index',[
            'select_date'=>$select_date,
            'report_type'=>$report_type,
            'departments'    => $departments,
            'divisions'      => $divisions,
            'teams'          => $teams,
            'projects' =>$projects,
            'projectImport' =>$projectImport,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'units_date' => $units_date,
            'tickets_status' => $tickets,
            'tickets_status_name' => $tickets_status_name,
            'array_bug' => $array_bug,
            'array_uat' => $array_uat,
            'name_make_bug' => $name_make_bug,
            'name_found_bug' => $name_found_bug,
            'name_fix_bug' => $name_fix_bug,
            'name_root_cause' => $name_root_cause,
            'array_found' => $array_found,
            'array_close' => $array_close,
            'requestReportType' => $requestReportType,
            'desToolTips'=>$desToolTips,
            'bug_weight' => $bug_weight,
            'bug_type' => $bug_type,
            'firstDateDefault' => $firstDateDefault,
            'endDateDefault' => $endDateDefault,
            'ticketsMakeBug' => $ticketsMakeBug,
            'ticketsFoundBug' => $ticketsFoundBug,
            'ticketsFixBug' => $ticketsFixBug,
            'roleUser' => $roleUser,
            'isAdmin' => ((isset(Sentinel::check()->permissions) && in_array(array_keys(Sentinel::check()->permissions)[0], $arr_role)) || (Sentinel::getUser()->inRole(Sentinel::findRoleById(1)->slug) == 1)) ? 1 : 0
        ]);
    }
    public function export(Request $request)
    {

        $select_date = Config::get('constant.select_date');
        $report_type = Config::get('constant.report_type');
        $units_date = Config::get('constant.units_time');
        $desToolTips = Config::get('constant.description_tool_tip');
        $bug_weight = Config::get('constant.bug_weight');
        $bug_type = Config::get('constant.bug_type');

        $time             = $request->get('check_time','');
        $defaultTime      = $request->get('date','');
        $requestStartDate = $request->get('start_date','');
        $requestReportType = $request->get('report_type');
        $requestEndDate   = $request->get('end_date','');
        $unitsDate = $request->get('units_time','');

        $dateArray        = $this->project->getTimeSearch($time, $defaultTime, $requestStartDate, $requestEndDate);
        $startDate        = $dateArray['start'];
        $endDate          = $dateArray['end'];
        $firstDateDefault = date('d/m/Y', strtotime('first day of this month'));
        $endDateDefault   = date('d/m/Y', strtotime('last day of this month'));

        $getDepartment = $request->get('department','');
        $getDivision = $request->get('division','');
        $getTeam = $request->get('team','');
        $project_id = $request->get('project','');
        $groupCheck = $this->project->getGroupProjectMemberJoin("user.view_defect");
        $projectMemberJoin = $groupCheck['projectJoin'];

        $tickets_status_name = [];
        $array_bug = [];
        $array_uat = [];
        $array_found = [];
        $array_close = [];
        $name_make_bug = [];
        $name_found_bug = [];
        $name_fix_bug= [];
        $name_root_cause = [];
        $ticketsMakeBug = [];
        $ticketsFoundBug = [];
        $ticketsFixBug = [];

        $tickets = $this->status->getTicketsByStatus($startDate, $endDate, $getDepartment, $getDivision, $getTeam, $projectMemberJoin,$project_id);
        if($request->get('report_type') == 'summary' || empty($request->get('report_type')))
        {
            //by status
            $tickets_status_name = $this->status->getStatusName($tickets,'status_related');
            //by who make

            $ticketsMakeBug = $this->status->getTicketsByUser($startDate, $endDate, $getDepartment, $getDivision, $getTeam, $projectMemberJoin,$project_id);
            $ticketsMakeBug = $ticketsMakeBug->join('users',function($join)
            {
                $join->on('tickets.made_by_user', '=', 'users.user_name');
                $join->on('tickets.source_id', '=', 'users.source_id');
            })->get()->toArray();
            $name_make_bug = $this->status->getStatusName($ticketsMakeBug,'users_related_id');
// //             //by who found

            $ticketsFoundBug = $this->status->getTicketsByUser($startDate, $endDate, $getDepartment, $getDivision, $getTeam, $projectMemberJoin,$project_id);
            $ticketsFoundBug = $ticketsFoundBug->join('users',function($join)
            {
                $join->on('tickets.created_by_user', '=', 'users.user_name');
                $join->on('tickets.source_id', '=', 'users.source_id');
            })->get()->toArray();
            $name_found_bug = $this->status->getStatusName($ticketsFoundBug,'users_related_id');

            //by who fix
            $ticketsFixBug = $this->status->getTicketsByUser($startDate, $endDate, $getDepartment, $getDivision, $getTeam, $projectMemberJoin,$project_id);
            $ticketsFixBug = $ticketsFixBug->join('users',function($join)
            {
                $join->on('tickets.assign_to_user', '=', 'users.user_name');
                $join->on('tickets.source_id', '=', 'users.source_id');
            })->get()->toArray();
            $name_fix_bug = $this->status->getStatusName($ticketsFixBug,'users_related_id');

// //             //by root cause
            $name_root_cause = $this->status->getStatusName($tickets, 'root_cause_related');
        }

        if($request->get('report_type') == 'time' || empty($request->get('report_type')))
        {
            //by uat and bug
            $bugArray = $this->status->getBugUat($tickets,$startDate,$endDate,$request->get('units_time'),$request->get('type_bug'),$bug_weight);
            $array_bug = $bugArray['array_bug'];
            $array_uat = $bugArray['array_uat'];

            //by status open or close
            $bugFoundClose = $this->status->getOpenClose($tickets,$startDate,$endDate,$request->get('units_time'),$request->get('type_bug'),$bug_weight);
            $array_found = $bugFoundClose['array_found'];
            $array_close = $bugFoundClose['array_close'];
        }
        try
        {
            Excel::create('Export', function($excel) use ($startDate,$endDate,$tickets,
                                                            $tickets_status_name,$array_bug,$array_uat,
                                                            $name_make_bug,$name_found_bug,$name_fix_bug,
                                                            $name_root_cause,$array_found,$ticketsMakeBug,
                                                            $array_close,$requestReportType,$ticketsFoundBug,
                                                            $desToolTips,$bug_weight,$bug_type,$ticketsFixBug) {
                $excel->sheet('Report', function($sheet) use ($startDate,$endDate,$tickets,
                                                            $tickets_status_name,$array_bug,$array_uat,
                                                            $name_make_bug,$name_found_bug,$name_fix_bug,
                                                            $name_root_cause,$array_found,$array_close,$ticketsMakeBug,
                                                            $desToolTips,$bug_weight,$bug_type,$ticketsFoundBug,
                                                            $requestReportType,$ticketsFixBug) {
                    $sheet->loadView('defect_report.export')
                        ->with('start_date',$startDate)
                        ->with('end_date',$endDate)
                        ->with('tickets_status',$tickets)
                        ->with('tickets_status_name',$tickets_status_name)
                        ->with('array_bug',$array_bug)
                        ->with('array_uat',$array_uat)
                        ->with('name_make_bug',$name_make_bug)
                        ->with('name_found_bug',$name_found_bug)
                        ->with('name_fix_bug',$name_fix_bug)
                        ->with('name_root_cause',$name_root_cause)
                        ->with('array_found',$array_found)
                        ->with('array_close',$array_close)
                        ->with('desToolTips',$desToolTips)
                        ->with('bug_weight',$bug_weight)
                        ->with('bug_type',$bug_type)
                        ->with('ticketsFoundBug', $ticketsFoundBug)
                        ->with('ticketsMakeBug', $ticketsMakeBug)
                        ->with('ticketsFixBug', $ticketsFixBug)
                        ->with('requestReportType',$requestReportType)
                    ;
                });
            })->export('xls');
        }
        catch(Exception $e){
            return back()->with('message', 'Export error!');
        }
    }
}