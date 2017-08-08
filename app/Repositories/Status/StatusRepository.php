<?php
namespace App\Repositories\Status;

use App\Models\Status;
use App\Models\Ticket;
use App\Models\Department;
use DB;

/**
 *
 * @author tampt6722
 *
 */
class StatusRepository implements StatusRepositoryInterface
{

    public function all(){
        return Status::all();
    }

    public function paginate($quantity){
        return Status::paginate($quantity);
    }

    public function find($id){
        return Status::find($id);
    }
    /**
     *
     * @author tampt6722
     *
     * @param array $data
     * @return integer
     */
    public function save($data){
        $status = new Status();
        if (isset($data['source_id'])) {
            $status->source_id = $data['source_id'];
        } else {
            $status->source_id = 0;
        }
        if (isset($data['name'])) {
            $status->name = $data['name'];
        } else {
            $status->name = "";
        }
        if (isset($data['integrated_status_id'])) {
            $status->integrated_status_id = $data['integrated_status_id'];
        } else {
            $status->integrated_status_id = 0;
        }
        $status->save();
        return $status->id;
    }

    public function delete($id){
        Status::find($id)->delete();
    }

    public function update($data, $id){
        $status = Status::find($id);

        $status->save();
        return true;
    }

    public function findByAttribute($att, $name){
        return Status::where($att, $name)->first();
    }

    public function findByAttributes($att1, $name1, $att2, $name2){
        return Status::where($att1, $name1)
                        ->where($att2,$name2)->first();
    }

    /**
     * @author thangdv8182
     * {@inheritDoc}
     * @see \App\Repositories\Status\StatusRepositoryInterface::getByStatus()
     */
    public function getTicketsByStatus($start_date, $end_date,$getDepartment, $getDivision, $getTeam, $projectMemberJoin, $project_id){
        $tickets =  Ticket::select(
                'tickets.*',
                'status.name as name_status',
                'bugs_weight.related_id as bug_weight_related',
                'ticket_type.name as ticket_type_name',
                'root_cause.name as root_cause_name',
                'ticket_type.related_id as ticket_type_related',
                'status.related_id as status_related',
                'root_cause.related_id as root_cause_related'
                )
                ->join('ticket_type','tickets.ticket_type_id','=','ticket_type.id')
                ->join('projects','tickets.project_id','=','projects.id')
                ->join('status','tickets.status_id','=','status.id')
                ->join('root_cause','tickets.root_cause_id','=','root_cause.id')
                ->join('bugs_weight','tickets.bug_weight_id','=','bugs_weight.id')
                ->whereBetween('tickets.integrated_created_at', [$start_date, $end_date]);
//         if ($project_id == -1) {
//             if (($getTeam != null) && ($getTeam != -1)) {
//                 $tickets = $tickets->whereIn('tickets.project_id',$projectMemberJoin)
//                                    ->where('projects.department_id', $getTeam);
//             } elseif (($getDivision != null) && ($getDivision != -1)){
//                 $listTeam = Department::where('parent_id', $getDivision)->pluck('id')->toArray();
//                 $tickets = $tickets->whereIn('tickets.project_id',$projectMemberJoin)
//                                 ->where(function ($query) use ($listTeam, $getDivision) {
//                                     $query->whereIn('projects.department_id', $listTeam)
//                                           ->orWhere('projects.department_id', $getDivision);
//                                 });
//             } elseif (($getDepartment != null) && ($getDepartment != -1)) {
//                 $listDivision = Department::where('parent_id', $getDepartment)->pluck('id')->toArray();
//                 $listTeam     = Department::whereIn('parent_id', $listDivision)->pluck('id')->toArray();
//                 $tickets = $tickets->whereIn('tickets.project_id',$projectMemberJoin)
//                                     ->where(function ($query) use ($listTeam, $listDivision, $getDepartment) {
//                                         $query->whereIn('projects.department_id', $listTeam)
//                                         ->orWhereIn('projects.department_id', $listDivision)
//                                         ->orWhere('projects.department_id', $getDepartment);
//                                     });
//             } else {
//                 $tickets = $tickets->whereIn('tickets.project_id',$projectMemberJoin);
//             }
//         } else {
            $tickets = $tickets->where('tickets.project_id','=',$project_id);
//         }
        return $tickets->where(function($q){
                            $q->where('ticket_type.related_id','=',9)
                            ->orWhere('ticket_type.related_id','=',10);
                        })
                        ->get()
                        ->toArray();
    }

    public function getTicketsByUser($start_date, $end_date,$getDepartment, $getDivision, $getTeam, $projectMemberJoin, $project_id){
        $tickets =  Ticket::select(
                'tickets.*',
                'status.name as name_status',
                'bugs_weight.related_id as bug_weight_related',
                'ticket_type.name as ticket_type_name',
                'root_cause.name as root_cause_name',
                'ticket_type.related_id as ticket_type_related',
                'status.related_id as status_related',
                'root_cause.related_id as root_cause_related',
                'users.related_id as users_related_id',
                DB::raw('(CASE tickets.source_id
                               WHEN 0 THEN 5
                               WHEN 1 THEN 1
                               WHEN 2 THEN 2
                               WHEN 3 THEN 3
                               WHEN 4 THEN 4
                             END ) AS tickets_source_id')
                )
                ->join('ticket_type','tickets.ticket_type_id','=','ticket_type.id')
                ->join('projects','tickets.project_id','=','projects.id')
                ->join('status','tickets.status_id','=','status.id')
                ->join('root_cause','tickets.root_cause_id','=','root_cause.id')
                ->join('bugs_weight','tickets.bug_weight_id','=','bugs_weight.id')
                ->whereBetween('tickets.integrated_created_at', [$start_date, $end_date]);
//                 if ($project_id == -1) {
//                     if (($getTeam != null) && ($getTeam != -1)) {
//                         $tickets = $tickets->whereIn('tickets.project_id',$projectMemberJoin)
//                         ->where('projects.department_id', $getTeam);
//                     } elseif (($getDivision != null) && ($getDivision != -1)){
//                         $listTeam = Department::where('parent_id', $getDivision)->pluck('id')->toArray();
//                         $tickets = $tickets->whereIn('tickets.project_id',$projectMemberJoin)
//                         ->where(function ($query) use ($listTeam, $getDivision) {
//                             $query->whereIn('projects.department_id', $listTeam)
//                             ->orWhere('projects.department_id', $getDivision);
//                         });
//                     } elseif (($getDepartment != null) && ($getDepartment != -1)) {
//                         $listDivision = Department::where('parent_id', $getDepartment)->pluck('id')->toArray();
//                         $listTeam     = Department::whereIn('parent_id', $listDivision)->pluck('id')->toArray();
//                         $tickets = $tickets->whereIn('tickets.project_id',$projectMemberJoin)
//                         ->where(function ($query) use ($listTeam, $listDivision, $getDepartment) {
//                             $query->whereIn('projects.department_id', $listTeam)
//                             ->orWhereIn('projects.department_id', $listDivision)
//                             ->orWhere('projects.department_id', $getDepartment);
//                         });
//                     } else {
//                         $tickets = $tickets->whereIn('tickets.project_id',$projectMemberJoin);
//                     }
//                 } else {
                    $tickets = $tickets->where('tickets.project_id','=',$project_id);
//                 }
                return $tickets->where(function($q){
                    $q->where('ticket_type.related_id','=',9)
                    ->orWhere('ticket_type.related_id','=',10);
                });
    }
    /**
     * @author thangdv8182
     * get bug uat
     * @param unknown $tickets_status
     * @param unknown $startDate
     * @param unknown $endDate
     * @param unknown $units_date
     */
    public function getBugUat($tickets_status,$startDate, $endDate,$units_date,$type_bug = 1,$bug_weight)
    {
        $array_bug = [];
        $array_uat = [];
        switch ($units_date)
        {
            case 'day':
                for ($i = strtotime($startDate); $i <= strtotime($endDate); $i = strtotime("+1 day", $i))
                {
                    $countBug = 0;
                    $countBugUat = 0;
                    foreach ($tickets_status as $ticket)
                    {
                        if($ticket['ticket_type_related'] == 9 && strtotime(date('Y-m-d',strtotime($ticket['integrated_created_at']))) == $i)
                        {
                            if($type_bug == 2)
                            {
                                $countBug += $bug_weight[$ticket['bug_weight_related']];
                            }else
                                $countBug++;
                        }
                        if($ticket['ticket_type_related'] == 10 && strtotime(date('Y-m-d',strtotime($ticket['integrated_created_at']))) == $i)
                        {
                            if($type_bug == 2)
                            {
                                $countBugUat += $bug_weight[$ticket['bug_weight_related']];
                            }else
                                $countBugUat++;
                        }
                    }
                    array_push($array_bug, $countBug);
                    array_push($array_uat, $countBugUat);
                }
                break;
            case 'week':
                $period = \Helpers::findWeekInPeriodOfTime($startDate, $endDate);
                for ($i = strtotime($period->start->format('Y-m-d H:i:s')); $i <= strtotime($period->end->format('Y-m-d H:i:s')); $i = strtotime("+1 week", $i))
                {
                    $week_start = strtotime('monday this week', $i);
                    $week_end = strtotime('sunday this week', $i);
                    $countBug = 0;
                    $countBugUat = 0;
                    foreach ($tickets_status as $ticket)
                    {
                        if($ticket['ticket_type_related'] == 9
                                && strtotime(date('Y-m-d',strtotime($ticket['integrated_created_at']))) <= $week_end
                                && strtotime(date('Y-m-d',strtotime($ticket['integrated_created_at']))) >= $week_start)
                        {
                            if($type_bug == 2)
                            {
                                $countBug += $bug_weight[$ticket['bug_weight_related']];
                            }else
                                $countBug++;
                        }
                        if($ticket['ticket_type_related'] == 10
                                && strtotime(date('Y-m-d',strtotime($ticket['integrated_created_at']))) <= $week_end
                                && strtotime(date('Y-m-d',strtotime($ticket['integrated_created_at']))) >= $week_start)
                        {
                            if($type_bug == 2)
                            {
                                $countBugUat += $bug_weight[$ticket['bug_weight_related']];
                            }else
                                $countBugUat++;
                        }
                    }
                    array_push($array_bug, $countBug);
                    array_push($array_uat, $countBugUat);
                }
                break;
            case 'month':
                for ($i = strtotime($startDate); $i <= strtotime($endDate); $i = strtotime("+1 month", $i))
                {
                    $month = date("M Y", $i);
                    $start_month = strtotime('first day of '.$month);
                    $end_month  = strtotime(date('Y-m-d 23:59:59',strtotime('last day of '.$month)));
                    $countBug = 0;
                    $countBugUat = 0;

                    foreach ($tickets_status as $ticket)
                    {
                        if($ticket['ticket_type_related'] == 9
                                && strtotime($ticket['integrated_created_at']) <= $end_month
                                && strtotime($ticket['integrated_created_at']) >= $start_month)
                        {
                            if($type_bug == 2)
                            {
                                $countBug += $bug_weight[$ticket['bug_weight_related']];
                            }else
                                $countBug++;
                        }
                        if($ticket['ticket_type_related'] == 10
                                && strtotime(date('Y-m-d',strtotime($ticket['integrated_created_at']))) <= $end_month
                                && strtotime(date('Y-m-d',strtotime($ticket['integrated_created_at']))) >= $start_month)
                        {
                            if($type_bug == 2)
                            {
                                $countBugUat += $bug_weight[$ticket['bug_weight_related']];
                            }else
                                $countBugUat++;
                        }
                    }
                    array_push($array_bug, $countBug);
                    array_push($array_uat, $countBugUat);
                }
                break;
            case 'year':
                for ($i = strtotime($startDate); $i <= strtotime($endDate); $i = strtotime("+1 year", $i))
                {
                    $year = date("Y", $i);
                    $start_year = strtotime('first day of Jan '.$year);
                    $end_year  = strtotime(date('Y-m-d 23:59:59',strtotime('last day of Dec '.$year)));
                    $countBug = 0;
                    $countBugUat = 0;
                    foreach ($tickets_status as $ticket)
                    {
                        if($ticket['ticket_type_related'] == 9
                                && strtotime($ticket['integrated_created_at']) <= $end_year
                                && strtotime($ticket['integrated_created_at']) >= $start_year)
                        {
                            if($type_bug == 2)
                            {
                                $countBug += $bug_weight[$ticket['bug_weight_related']];
                            }else
                                $countBug++;
                        }
                        if($ticket['ticket_type_related'] == 10
                                && strtotime($ticket['integrated_created_at']) <= $end_year
                                && strtotime($ticket['integrated_created_at']) >= $start_year)
                        {
                            if($type_bug == 2)
                            {
                                $countBugUat += $bug_weight[$ticket['bug_weight_related']];
                            }else
                                $countBugUat++;
                        }
                    }
                    array_push($array_bug, $countBug);
                    array_push($array_uat, $countBugUat);
                }
                break;
        }
        return [
            'array_bug'=>$array_bug,
            'array_uat'=>$array_uat,
        ];
    }
    public function getOpenClose($tickets_status,$startDate, $endDate,$units_date,$type_bug = 1,$bug_weight)
    {
        $array_found = [];
        $array_close = [];
        switch ($units_date)
        {
            case 'day':
                for ($i = strtotime($startDate); $i <= strtotime($endDate); $i = strtotime("+1 day", $i))
                {
                    $countFound = 0;
                    $countClose = 0;
                    foreach ($tickets_status as $ticket)
                    {
                        if(strtotime(date('Y-m-d',strtotime($ticket['integrated_created_at']))) == $i)
                        {
                            if($type_bug == 2)
                            {
                                $countFound += $bug_weight[$ticket['bug_weight_related']];
                            }else
                                $countFound++;
                        }
                        if(strtotime(date('Y-m-d',strtotime($ticket['completed_date']))) == $i)
                        {
                            if($type_bug == 2)
                            {
                                $countClose += $bug_weight[$ticket['bug_weight_related']];
                            }else
                                $countClose++;
                        }
                    }
                    array_push($array_found, $countFound);
                    array_push($array_close, $countClose);
                }
                break;
            case 'week':
                $period = \Helpers::findWeekInPeriodOfTime($startDate, $endDate);
                for ($i = strtotime($period->start->format('Y-m-d H:i:s')); $i <= strtotime($period->end->format('Y-m-d H:i:s')); $i = strtotime("+1 week", $i))
                {
                    $week_start = strtotime('monday this week', $i);
                    $week_end = strtotime('sunday this week', $i);
                    $countFound = 0;
                    $countClose = 0;
                    foreach ($tickets_status as $ticket)
                    {
                        if( strtotime(date('Y-m-d',strtotime($ticket['integrated_created_at']))) <= $week_end
                                && strtotime(date('Y-m-d',strtotime($ticket['integrated_created_at']))) >= $week_start)
                        {
                            if($type_bug == 2)
                            {
                                $countFound += $bug_weight[$ticket['bug_weight_related']];
                            }else
                                $countFound++;
                        }
                        if( strtotime(date('Y-m-d',strtotime($ticket['completed_date']))) <= $week_end
                                && strtotime(date('Y-m-d',strtotime($ticket['completed_date']))) >= $week_start)
                        {
                            if($type_bug == 2)
                            {
                                $countClose += $bug_weight[$ticket['bug_weight_related']];
                            }else
                                $countClose++;
                        }
                    }
                    array_push($array_found, $countFound);
                    array_push($array_close, $countClose);
                }
                break;
            case 'month':
                for ($i = strtotime($startDate); $i <= strtotime($endDate); $i = strtotime("+1 month", $i))
                {
                    $month = date("M Y", $i);
                    $start_month = strtotime('first day of '.$month);
                    $end_month  = strtotime(date('Y-m-d 23:59:59',strtotime('last day of '.$month)));
                    $countFound = 0;
                    $countClose = 0;
                    foreach ($tickets_status as $ticket)
                    {
                        if( strtotime($ticket['integrated_created_at']) <= $end_month
                                && strtotime($ticket['integrated_created_at']) >= $start_month)
                        {
                            if($type_bug == 2)
                            {
                                $countFound += $bug_weight[$ticket['bug_weight_related']];
                            }else
                                $countFound++;
                        }
                        if( strtotime(date('Y-m-d',strtotime($ticket['completed_date']))) <= $end_month
                                && strtotime(date('Y-m-d',strtotime($ticket['completed_date']))) >= $start_month)
                        {
                            if($type_bug == 2)
                            {
                                $countClose += $bug_weight[$ticket['bug_weight_related']];
                            }else
                                $countClose++;
                        }
                    }
                    array_push($array_found, $countFound);
                    array_push($array_close, $countClose);
                }
                break;
            case 'year':
                for ($i = strtotime($startDate); $i <= strtotime($endDate); $i = strtotime("+1 year", $i))
                {
                    $year = date("Y", $i);
                    $start_year = strtotime('first day of Jan '.$year);
                    $end_year  = strtotime('last day of Dec '.$year);
                    $countFound = 0;
                    $countClose = 0;
                    foreach ($tickets_status as $ticket)
                    {
                        if( strtotime($ticket['integrated_created_at']) <= $end_year
                                && strtotime($ticket['integrated_created_at']) >= $start_year)
                        {
                            if($type_bug == 2)
                            {
                                $countFound += $bug_weight[$ticket['bug_weight_related']];
                            }else
                                $countFound++;
                        }
                        if( strtotime($ticket['completed_date']) <= $end_year
                                && strtotime($ticket['completed_date']) >= $start_year)
                        {
                            if($type_bug == 2)
                            {
                                $countClose += $bug_weight[$ticket['bug_weight_related']];
                            }else
                                $countClose++;
                        }
                    }
                    array_push($array_found, $countFound);
                    array_push($array_close, $countClose);
                }
                break;
        }
        return [
            'array_found'=>$array_found,
            'array_close'=>$array_close,
        ];
    }
    /**
     * @author thangdv8182
     * {@inheritDoc}
     * @see \App\Repositories\Status\StatusRepositoryInterface::getStatusName()
     */
    public function getStatusName($tickets_status,$name_unique)
    {

        $array_temp = [];
        $tickets_status_name = [];

        foreach ($tickets_status as $key=>$value)
        {
            array_push($array_temp, $value["$name_unique"]);
        }
        $tickets_status_name = array_unique($array_temp);

        return $tickets_status_name;
    }

    /**
     * Get the id of the default status
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Status\StatusRepositoryInterface::getStatusIdDefault()
     */
    public function getStatusIdDefault() {
        $status = $this->findByAttributes('source_id', 0, 'key', 1);
        return $status->id;
    }

    /**
     * Get statues
     * @author tampt6722
     *
     * @param integer $integratedStatusId
     * @param string $statusName
     * @return integer
     */
    public function getStatusId($integratedStatusId, $statusName, $sourceId) {
        $existedStatus = $this->findByAttributes(
                'source_id', $sourceId, 'integrated_status_id', $integratedStatusId);
        if (count($existedStatus) == 0) {
            $dataStatus['source_id'] = $sourceId;
            $dataStatus['integrated_status_id'] = $integratedStatusId;
            $dataStatus['name'] = $statusName;
            $statusId = $this->save($dataStatus);
        } else {
            $statusId = $existedStatus->id;
        }
        return $statusId;
    }
}