<?php
namespace App\Repositories\MemberProjectReport;

use App\Models\MemberProjectReport;
use App\Repositories\ProjectMember\ProjectMemberRepositoryInterface;
use DB;
/**
 *
 * Dec 20, 2016 3:21:55 PM
 * @author tampt6722
 *
 */
class MemberProjectReportRepository implements MemberProjectReportRepositoryInterface
{
    public function __construct(ProjectMemberRepositoryInterface $pm)
    {
        $this->pm = $pm;
    }
    public function all(){
        return MemberProjectReport::all();
    }

    public function paginate($quantity){
        return MemberProjectReport::paginate($quantity);
    }

    public function find($id){
        return MemberProjectReport::find($id);
    }

    public function save($data){
        $report = new MemberProjectReport();
        $report->user_id = $data['user_id'];
        $report->position = $data['position'];
        $report->project_id = $data['project_id'];
        $report->report_flag = $data['report_flag'];
        $report->common_data = $data['common_data'];
        $report->quality = $data['quality'];
        $report->productivity = $data['productivity'];
        $report->time_name = $data['time_name'];
        $report->start_date = $data['start_date'];
        $report->end_date = $data['end_date'];
        $report->save();
        return $report->id;
    }

    public function delete($id){
        MemberProjectReport::find($id)->delete();
    }

    public function update($data, $id){
        $report = MemberProjectReport::find($id);

        $report->save();
        return true;
    }

    public function findByAttribute($att, $name){
        return MemberProjectReport::where($att, $name)->first();
    }

    public function findByAttributes($att1, $name1, $att2, $name2){
        return MemberProjectReport::where($att1, $name1)
                        ->where($att2,$name2)->first();
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\MemberProjectReport\MemberProjectReportRepositoryInterface::getDataReport()
     */
    public function getDataReport($reportFlag, $position, $projectId,
            $startDate, $endDate, $getDepartment, $getDivision, $getTeam, $projectMemberJoin, $getStatus)
    {
        $query = MemberProjectReport::select(DB::raw('CONCAT(users.last_name," ",users.first_name) AS full_name'),
                'projects.name as project_name', 'member_project_report.*')
                ->join('users', 'users.id', '=', 'member_project_report.user_id')
                ->join('projects', 'projects.id', '=', 'member_project_report.project_id')
                ->where('member_project_report.report_flag', $reportFlag)
                ->where('member_project_report.position', $position)
                ->where('member_project_report.start_date','>=', $startDate)
                ->where('member_project_report.end_date', '<=', $endDate);
        if (!empty($getStatus)) {
            $query1 = $query->where('projects.status', $getStatus);
            $result = $this->pm->checkWhetherProjectIsNull($query1, $projectId,
                    $getDepartment, $getDivision, $getTeam, $projectMemberJoin)->get();
        } else {
            $result = $this->pm->checkWhetherProjectIsNull($query, $projectId,
                    $getDepartment, $getDivision, $getTeam, $projectMemberJoin)->get();
        }

        return $result;
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\MemberProjectReport\MemberProjectReportRepositoryInterface::getNameDate()
     */
    public function getNameDate($reportFlag, $startDate, $endDate)
    {
            $query = MemberProjectReport::select('member_project_report.time_name')
            ->where('report_flag','=', $reportFlag)
            ->where('start_date','>=', $startDate)
            ->where('end_date', '<=', $endDate)
            ->distinct()->get();
        return $query;
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\MemberProjectReport\MemberProjectReportRepositoryInterface::getUser()
     */
    public function getUser($reportFlag, $position, $startDate, $endDate)
    {
        $query = MemberProjectReport::select('member_project_report.user_id',
                'users.first_name', 'users.last_name')
            ->join('users', 'users.id', '=', 'member_project_report.user_id')
            ->where('member_project_report.report_flag', $reportFlag)
            ->where('member_project_report.position', $position)
            ->where('member_project_report.start_date','>=', $startDate)
            ->where('member_project_report.end_date', '<=', $endDate)
            ->distinct()->orderBy('member_project_report.user_id', 'asc')->get();
        return $query;
    }
}