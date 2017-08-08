<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Project\ProjectRepositoryInterface;
use Illuminate\Support\Facades\Config;
use App\Repositories\ProjectMember\ProjectMemberRepositoryInterface;
use Illuminate\Support\Facades\Input;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use function GuzzleHttp\json_encode;
use App\Repositories\Cost\CostRepositoryInterface;
use App\Repositories\QualityReport\QualityReportByProjectRepositoryInterface;
use App\Repositories\QualityReport\QualityReportByMemberRepositoryInterface;
use Illuminate\Support\Collection;
use App\Repositories\MemberReport\MemberReportRepositoryInterface;

class QualityReportByMemberController extends Controller
{
    public function __construct(ProjectRepositoryInterface $project,
            ProjectMemberRepositoryInterface $pm,
            QualityReportByMemberRepositoryInterface $qpMember,
            MemberReportRepositoryInterface $memberReport)
    {
        $this->project = $project;
        $this->projectMember = $pm;
        $this->qpMember = $qpMember;
        $this->memberReport = $memberReport;
    }

    public function index()
    {
        $years       = Config::get('constant.list_years_report');
        $months      = Config::get('constant.list_months_report');
        $paginate         = Config::get('constant.paginate_number');
        $limit            = Config::get('constant.RECORD_PER_PAGE');
        $mm = Config::get('constant.men_month');
        // Get data to fill select box
        $groupCheck = $this->project->getGroupProjectMemberJoin("user.view_quality_report_by_member");
        $departments       = $groupCheck['departments'];
        $projectMemberJoin = $groupCheck['projectJoin'];

        $searchGroup = $this->project->saveDeparamentSearch('', '', '',
                $groupCheck['divisions'], $groupCheck['teams'], $groupCheck['projects']);
        $divisions         = $searchGroup['divisions'];
        $teams             = $searchGroup['teams'];
        $projects          = $searchGroup['projects'];

        return view('quality_report.member', [
                        'projects' => $projects,
                        'departments' => $departments,
                        'divisions' => $divisions,
                        'teams' => $teams,
                        'years'    => $years,
                        'months'       => $months
        ]);
    }

    /**
     * Get members' data served report quality and productivity
     * @author tampt6722
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function getReportByMember (Request $request)
    {
        $projectId        = $request->get('project','');
        $year               = $request->get('year','');
        $month              = $request->get('month','');
        $getDepartment    = $request->get('department','');
        $getDivision      = $request->get('division','');
        $getTeam          = $request->get('team','');
        $position         = $request->get('position', '');
        $years       = Config::get('constant.list_years_report');
        $months      = Config::get('constant.list_months_report');
        $paginate         = Config::get('constant.paginate_number');
        $limit            = $request->get('limit', Config::get('constant.RECORD_PER_PAGE'));
        $mm = Config::get('constant.men_month');
        //Get data to fill select box
        $groupCheck = $this->project->getGroupProjectMemberJoin("user.view_quality_report_by_member");
        $departments       = $groupCheck['departments'];
        $projectMemberJoin = $groupCheck['projectJoin'];

        $searchGroup = $this->project->saveDeparamentSearch($getDepartment, $getDivision, $getTeam,
                        $groupCheck['divisions'], $groupCheck['teams'], $groupCheck['projects']);
        $divisions         = $searchGroup['divisions'];
        $teams             = $searchGroup['teams'];
        $projects          = $searchGroup['projects'];

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
            $flag = 0;
            $devDatas = [];
            $qaDatas = [];
            $datas = $this->memberReport->getDataMemberToReport($year, $month, $position, $getDepartment,
                                                $getDivision, $getTeam, $projectId, $projectMemberJoin);
            if($position == 'Dev') {
                $devDatas = $datas;
                $flag = 1;

            } elseif ($position == 'QA') {
                $qaDatas = $datas;
                $flag = 2;
            }
            return view('quality_report.member', [
                            'projects' => $projects,
                            'departments' => $departments,
                            'divisions' => $divisions,
                            'teams' => $teams,
                            'years'    => $years,
                            'months'       => $months,
                            'mm' => $mm,
                            'devDatas' => $devDatas,
                            'qaDatas' => $qaDatas,
                            'flag' => $flag
            ]);
        }

    }

    /**
     * Return api summary members report
     * @author tampt6722
     *
     */
    public function getMemberReportApi(Request $request)
    {
        return $this->qpMember->getDataMemberReportApi($request);
    }

}