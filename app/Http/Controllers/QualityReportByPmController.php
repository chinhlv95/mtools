<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Repositories\MemberProjectReport\MemberProjectReportRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\ProjectMember\ProjectMemberRepositoryInterface;
use App\Repositories\QualityReport\QualityReportByPmRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;


class QualityReportByPmController extends Controller
{
    public function __construct(ProjectRepositoryInterface $project,
            ProjectMemberRepositoryInterface $pm,
            QualityReportByPmRepositoryInterface $report,
            UserRepositoryInterface $user,
            MemberProjectReportRepositoryInterface $memberReport)
    {
        $this->project = $project;
        $this->projectMember = $pm;
        $this->report = $report;
        $this->user = $user;
        $this->memberReport = $memberReport;
    }

    /**
     *
     * @author tampt6722
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function getReportByMemberInProjects (Request $request)
    {
        $qaData = [];
        $devData = [];
        $timeName = '';
        $devIds = [];
        $qaIds = [];
        $getReportType    = $request->get('reportType','');
        $projectId        = $request->get('project','');
        $defaultTime      = $request->get('date','');
        $getDepartment    = $request->get('department','');
        $getDivision      = $request->get('division','');
        $getTeam          = $request->get('team','');
        $getStatus        = $request->get('status','');
        $dateArray        = $this->report->getTimeSearchReport($defaultTime);
        $startDate        = $dateArray['start'];
        $endDate          = $dateArray['end'];

        $firstDateDefault = date('d/m/Y', strtotime('first day of this month'));
        $endDateDefault   = date('d/m/Y', strtotime('last day of this month'));

        $reportType       = Config::get('constant.qp_time_report_type');
        $selectDate       = Config::get('constant.report_select_date');
        $paginate         = Config::get('constant.paginate_number');
        $status           = Config::get('constant.status');
        $limit            = $request->get('limit', Config::get('constant.RECORD_PER_PAGE'));
        $number           = ($request->get('page','1') - 1) * $limit;
        $groupCheck = $this->project->getGroupProjectMemberJoin("user.view_quality_report_by_project_member");
        $departments       = $groupCheck['departments'];
        $divisions         = $groupCheck['divisions'];
        $teams             = $groupCheck['teams'];
        $projects          = $groupCheck['projects'];
        $projectMemberJoin = $groupCheck['projectJoin'];
        $limit            = $request->get('limit', Config::get('constant.RECORD_PER_PAGE'));
        $number           = ($request->get('page','1') - 1)* $limit;
        if ($getReportType == 1) {
                $timeName = $this->memberReport->getNameDate(1, $startDate, $endDate);
                $qaDatas = $this->memberReport->getDataReport(1, 'QA', $projectId,
                            $startDate, $endDate, $getDepartment, $getDivision,
                            $getTeam, $projectMemberJoin, $getStatus)->toArray();
                $devDatas = $this->memberReport->getDataReport(1, 'Dev', $projectId,
                            $startDate, $endDate, $getDepartment, $getDivision,
                            $getTeam, $projectMemberJoin, $getStatus)->toArray();
                $devData =  $this->report->getDistinctData($devDatas);
                $qaData = $this->report->getDistinctData($qaDatas);
        } elseif ($getReportType == 2) {
            $qaData = $this->memberReport->getDataReport(2, 'QA', $projectId,
                        $startDate, $endDate, $getDepartment, $getDivision, $getTeam,
                        $projectMemberJoin, $getStatus)->toArray();
            $devData = $this->memberReport->getDataReport(2, 'Dev', $projectId,
                        $startDate, $endDate, $getDepartment, $getDivision, $getTeam,
                        $projectMemberJoin, $getStatus)->toArray();
            $timeName = $this->memberReport->getNameDate(2, $startDate, $endDate);
            $devData =  $this->report->getDistinctData($devDatas);
            $qaData = $this->report->getDistinctData($qaDatas);
        }

        return view('quality_report.project_member',
                [
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'projects'      => $projects,
                            'departments'   => $departments,
                            'divisions'     => $divisions,
                            'teams'         => $teams,
                            'select_date'   => $selectDate,
                            'number'         => $number,
                            'paginate'       => $paginate,
                            'reportType'     => $reportType,
                            'status'        => $status,
                            'timeName' => $timeName,
                            'qaData' => $qaData,
                            'devData' => $devData,
                            'firstDateDefault' => $firstDateDefault,
                            'endDateDefault' => $endDateDefault
                ]);
    }

    /**
     * Get data member assigned in projects for api
     * @author tampt6722
     *
     */
    public function getMemberReportWithProjectApi(Request $request)
    {
        return $this->report->getDataMemberReportWithProjectApi($request);
    }
}