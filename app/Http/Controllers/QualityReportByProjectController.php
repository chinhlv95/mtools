<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\Cost\CostRepositoryInterface;
use Illuminate\Support\Facades\Config;
use App\Repositories\ProjectMember\ProjectMemberRepositoryInterface;
use Illuminate\Support\Facades\Input;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use function GuzzleHttp\json_encode;
use App\Repositories\QualityReport\QualityReportByProjectRepositoryInterface;
use App\Repositories\ProjectReport\ProjectReportRepository;



class QualityReportByProjectController extends Controller
{
    public function __construct(ProjectRepositoryInterface $project,
                                ProjectMemberRepositoryInterface $pm,
                                QualityReportByProjectRepositoryInterface $report,
                                ProjectReportRepository $projectReport)
    {
        $this->project       = $project;
        $this->projectMember = $pm;
        $this->report        = $report;
        $this->projectReport = $projectReport;
    }

    public function index()
    {

        $years       = Config::get('constant.list_years_report');
        $months      = Config::get('constant.list_months_report');
        $paginate         = Config::get('constant.paginate_number');
        $mm = Config::get('constant.men_month');
        $status           = Config::get('constant.status');
        $reportType = Config::get('constant.qp_report_type');

        // Get data to fill select box
        $groupCheck = $this->project->getGroupProjectMemberJoin("user.view_quality_report_by_project");
        $departments       = $groupCheck['departments'];
        $projectMemberJoin = $groupCheck['projectJoin'];
        $searchGroup = $this->project->saveDeparamentSearch('','','',$groupCheck['divisions'],$groupCheck['teams'],$groupCheck['projects']);
        $divisions         = $searchGroup['divisions'];
        $teams             = $searchGroup['teams'];
        $projects          = $searchGroup['projects'];


        return view('quality_report.project',[
                        'projects'       => $projects,
                        'departments'    => $departments,
                        'divisions'      => $divisions,
                        'teams'          => $teams,
                        'mm'             => $mm,
                        'years'    => $years,
                        'months'       => $months,
                        'paginate'       => $paginate,
                        'reportType'     => $reportType,
                        'status'         => $status
        ]);

    }


    /**
     * Get quality and productivity report by project
     *
     * @author tampt6722
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function getReportByProject(Request $request)
    {
        $projectId        = $request->get('project','');
        $year               = $request->get('year','');
        $month              = $request->get('month','');
        $getDepartment    = $request->get('department','');
        $getDivision      = $request->get('division','');
        $getTeam          = $request->get('team','');
        $position         = $request->get('position', '');
        $getReportType    = $request->get('reportType','');
        $getStatus        = $request->get('status','');
        $years       = Config::get('constant.list_years_report');
        $months      = Config::get('constant.list_months_report');
        $paginate         = Config::get('constant.paginate_number');
        $limit            = $request->get('limit', Config::get('constant.RECORD_PER_PAGE'));
        $mm = Config::get('constant.men_month');
        $number           = ($request->get('page','1') - 1)* $limit;
        $status           = Config::get('constant.status');
        $reportType = Config::get('constant.qp_report_type');

        // Get data to fill select box
        $groupCheck = $this->project->getGroupProjectMemberJoin("user.view_quality_report_by_project");
        $departments       = $groupCheck['departments'];
        $projectMemberJoin = $groupCheck['projectJoin'];
        $searchGroup = $this->project->saveDeparamentSearch($getDepartment, $getDivision, $getTeam,
                        $groupCheck['divisions'], $groupCheck['teams'], $groupCheck['projects']);
        $divisions         = $searchGroup['divisions'];
        $teams             = $searchGroup['teams'];
        $projects          = $searchGroup['projects'];

        /// xem lại search all
        if (($year == -1)) {
            $parameters = [
                            'errorsMessage' => 'Please select Year!'
            ];
            return redirect()->back()->with($parameters);
        } elseif (($year != -1) && ($month == '')) {
            $parameters = [
                            'errorsMessage' => 'Please select Month!'
            ];
            return redirect()->back()->with($parameters);
        } else {
            $dataProjects = $this->projectReport->getDataToView($year, $month, $getDepartment, $getDivision,
            $getTeam, $projectId, $projectMemberJoin, $getStatus);

            // $getReportType = 1, report by table
            if ($getReportType == 1) {
                return view('quality_report.project',[
                                'dataProjects' => $dataProjects,
                                'projects'       => $projects,
                                'departments'    => $departments,
                                'divisions'      => $divisions,
                                'teams'          => $teams,
                                'mm'             => $mm,
                                'number'         => $number,
                                'years'    => $years,
                                'months'       => $months,
                                'paginate'       => $paginate,
                                'reportType'     => $reportType,
                                'status'         => $status
                ]);

            } elseif ($getReportType == 2) { //$getReportType = 2, report by graph

                return view('quality_report.project_graph',[
                                'dataProjects' => $dataProjects,
                                'projects'       => $projects,
                                'departments'    => $departments,
                                'divisions'      => $divisions,
                                'teams'          => $teams,
                                'mm'             => $mm,
                                'number'         => $number,
                                'years'    => $years,
                                'months'       => $months,
                                'paginate'       => $paginate,
                                'reportType'     => $reportType,
                                'status'         => $status
                ]);
            } else {
                return view('quality_report.project',[
                                'projects'       => $projects,
                                'departments'    => $departments,
                                'divisions'      => $divisions,
                                'teams'          => $teams,
                                'mm'             => $mm,
                                'number'         => $number,
                                'years'    => $years,
                                'months'       => $months,
                                'paginate'       => $paginate,
                                'reportType'     => $reportType,
                                'status'         => $status
                ]);
            }
        }

    }

    /**
     * Return project member data for api
     * @author tampt6722
     *
     */
    public function getProjectMemberApi()
    {
        return $this->report->getDataProjectMemberApi();
    }

    /**
     * Return project report data for api
     * @author tampt6722
     *
     */
    public function getProjectReportApi (Request $request){
        return $this->report->getDataProjectReportApi($request);
    }

    /**
     * Return max min data of project report for api
     * @author tampt6722
     *
     */
    public function getProjectMaxMin()
    {
        return $this->report->getMaxMinDataForApi();
    }


}
