<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Config;
use App\Models\Project;
use App\Repositories\MemberReport\MemberReportRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use Helpers;
use function Faker\unique;
use App\Repositories\Ranking\RankingRepositoryInterface;
use App\Repositories\ProjectReport\ProjectReportRepositoryInterface;
use App\Models\MemberReport;
use DB;
use App\Models\ProjectReport;
use App\Repositories\ProjectKpi\ProjectKpiRepositoryInterface;
use App\Models\ProjectKpi;
use App\Repositories\ProjectVersion\ProjectVersionRepositoryInterface;

class RankController extends Controller
{
    public function __construct(
            ProjectRepositoryInterface $project,
            RankingRepositoryInterface $rank,
            ProjectReportRepositoryInterface $projectReport,
            MemberReportRepositoryInterface $memberReport,
            ProjectKpiRepositoryInterface $projeckpi
       
            )
    {
        $this->project = $project;
        $this->rank = $rank;
        $this->memberReport = $memberReport;
        $this->projectReport = $projectReport;
        $this->projectKpi = $projeckpi;
     
    }

    public function index(Request $request)
    {
        $years       = Config::get('constant.list_years_report');
        $dates       = Config::get('constant.ranking_date');
        $months      = Config::get('constant.list_months_report');
        $languages = Config::get('constant.project_language');
        $projectTypes = Config::get('constant.project_type');
        $getDepartment    = $request->get('department','');
        $getDivision      = $request->get('division','');
        $getTeam          = $request->get('team','');
        $projectId        = $request->get('project','');

        $monthCurrent = (int) date('m');
        $year = $request->get('year');
        $month = $request->get('month');

        $groupCheck = $this->project->getGroupProjectMemberJoin("user.view_quality_report_by_member");
        $departments       = $groupCheck['departments'];
        $projectMemberJoin = $groupCheck['projectJoin'];

        $searchGroup = $this->project->saveDeparamentSearch($getDepartment, $getDivision, $getTeam,
                $groupCheck['divisions'], $groupCheck['teams'], $groupCheck['projects']);
        $divisions         = $searchGroup['divisions'];
        $teams             = $searchGroup['teams'];
        $projects          = $searchGroup['projects'];

        $rankTop = Config::get('constant.rank_top');

        if($month != null && $year != null)
        {
            $dataDevs = MemberReport::select('user_id', 'name','email', 'user_name','department_name',
                    DB::raw('sum(workload)  as workload'),
                    DB::raw('sum(kloc)  as kloc'),
                    DB::raw('sum(task)  as task'),
                    DB::raw('sum(bug_weighted)  as bug_weighted'),
                    DB::raw('sum(madebug_weighted)  as madebug_weighted'))
                    ->where('year', $year)
                    ->where('position', 'Dev');

                    // If in_array(0, $month), search by months. Else search all
            if (!in_array(0, $month)) {
                $dataDevs= $dataDevs->whereIn('month', $month)->groupBy('user_id');
            } else {
                $dataDevs= $dataDevs->groupBy('user_id');
            }
            $dataDevs = $dataDevs->get()->toArray();

            $dataQas = MemberReport::select('user_id', 'name', 'email', 'user_name','department_name',
                    DB::raw('sum(workload)  as workload'),
                    DB::raw('sum(task)  as task'),
                    DB::raw('sum(foundbug_weighted)  as foundbug_weighted'),
                    DB::raw('sum(testcase_create)  as testcase_create'),
                    DB::raw('sum(testcase_test)  as testcase_test'),
                    DB::raw('sum(test_workload)  as test_workload'),
                    DB::raw('sum(createTc_workload)  as createTc_workload'))
                    ->where('year', $year)
                    ->where('position', 'QA');

                    // If in_array(0, $month), search by months. Else search all
                    if (!in_array(0, $month)) {
                        $dataQas= $dataQas->whereIn('month', $month)->groupBy('user_id');
                    } else {
                        $dataQas= $dataQas->groupBy('user_id');
                    }
            $dataQas = $dataQas->get()->toArray();

            $dataProjects = ProjectReport::select(
                    'project_report.project_id',
                    'project_report.project_name',
                    'project_report.department_id',
                    'project_report.department_name',
                    'projects.language_id',
                    'projects.type_id',
                    DB::raw('sum(actual_hour) as actual_hour'),
                    DB::raw('sum(loc)  as loc'),
                    DB::raw('sum(task)  as task'),
                    DB::raw('sum(tested_tc)  as tested_tc'),
                    DB::raw('sum(weighted_bug)  as weighted_bug'),
                    DB::raw('sum(weighted_uat_bug)  as weighted_uat_bug'))
                    ->where('year', $year)
                    ->join('projects','projects.id','=','project_report.project_id');
                    // If in_array(0, $month), search by months. Else search all
            if (!in_array(0, $month)) {
                $result = $dataProjects->whereIn('month', $month)->groupBy('project_id');
            } else {
                $result = $dataProjects->groupBy('project_id');
            }
            $dataProjects = $dataProjects->get();

            $resultDevs = $this->rank->rankingDev($dataDevs);
            $resultQas = $this->rank->rankingQA($dataQas);
            $resultProjects = $this->rank->rankingProject($dataProjects);
            $resultBres = $this->rank->rankingBres($year, $month);

            $resultQal = $this->rank->rankingQAL($year, $month);

            $resultDm = $this->rank->rankDm($year, $month);
            return view('rank.index',[
                'languages' =>$languages,
                'projectTypes' =>$projectTypes,
                'monthCurrent' => $monthCurrent,
                'years' => $years,
                'dates' => $dates,
                'months' => $months,
                'rankTop' => $rankTop,
                'resultDevs' => $resultDevs,
                'resultQas' => $resultQas,
                'resultProjects' => $resultProjects,
                'resultBres' => $resultBres,
                'resultQal' => $resultQal,
                'resultDm' => $resultDm,
            ]);
        }else{
            $monthC =  [];
            $year = 'this_year';
            array_push($monthC, $monthCurrent);
            $dataDevs = MemberReport::select('user_id', 'name','email', 'user_name','department_name',
                    DB::raw('sum(workload)  as workload'),
                    DB::raw('sum(kloc)  as kloc'),
                    DB::raw('sum(task)  as task'),
                    DB::raw('sum(bug_weighted)  as bug_weighted'),
                    DB::raw('sum(madebug_weighted)  as madebug_weighted'))
                    ->where('year', $year)
                    ->where('position', 'Dev');

            // If in_array(0, $month), search by months. Else search all
            if (!in_array(0, $monthC)) {
                $dataDevs= $dataDevs->groupBy('user_id');
            } else {
                $dataDevs= $dataDevs->groupBy('user_id');
            }
            $dataDevs = $dataDevs->get()->toArray();

            $dataQas = MemberReport::select('user_id', 'name', 'email', 'user_name','department_name',
                    DB::raw('sum(workload)  as workload'),
                    DB::raw('sum(task)  as task'),
                    DB::raw('sum(foundbug_weighted)  as foundbug_weighted'),
                    DB::raw('sum(testcase_create)  as testcase_create'),
                    DB::raw('sum(testcase_test)  as testcase_test'),
                    DB::raw('sum(test_workload)  as test_workload'),
                    DB::raw('sum(createTc_workload)  as createTc_workload'))
                    ->where('year', $year)
                    ->where('position', 'QA');

                    // If in_array(0, $month), search by months. Else search all
            if (!in_array(0, $monthC)) {
                $dataQas= $dataQas->groupBy('user_id');
            } else {
                $dataQas= $dataQas->groupBy('user_id');
            }
            $dataQas = $dataQas->get()->toArray();

            $dataProjects = ProjectReport::select(
                    'project_report.project_id',
                    'project_report.project_name',
                    'project_report.department_id',
                    'project_report.department_name',
                    'projects.language_id',
                    'projects.type_id',
                    DB::raw('sum(actual_hour) as actual_hour'),
                    DB::raw('sum(loc)  as loc'),
                    DB::raw('sum(task)  as task'),
                    DB::raw('sum(tested_tc)  as tested_tc'),
                    DB::raw('sum(weighted_bug)  as weighted_bug'),
                    DB::raw('sum(weighted_uat_bug)  as weighted_uat_bug'))
                    ->where('year', $year)
                    ->join('projects','projects.id','=','project_report.project_id');
            // If in_array(0, $month), search by months. Else search all
            if (!in_array(0, $monthC)) {
                $result = $dataProjects->groupBy('project_id');
            } else {
                $result = $dataProjects->groupBy('project_id');
            }
            $dataProjects = $dataProjects->get();

            $resultDevs = $this->rank->rankingDev($dataDevs);
            $resultQas = $this->rank->rankingQA($dataQas);
            $resultProjects = $this->rank->rankingProject($dataProjects);

            $resultBres = $this->rank->rankingBres('this_year', $monthC);

            $resultQal = $this->rank->rankingQAL('this_year', $monthC);

            $resultDm = $this->rank->rankDm('this_year', $monthC);
            return view('rank.index',[
                'languages' =>$languages,
                'projectTypes' =>$projectTypes,
                'monthCurrent' => $monthCurrent,
                'years' => $years,
                'dates' => $dates,
                'months' => $months,
                'rankTop' => $rankTop,
                'resultDevs' => $resultDevs,
                'resultQas' => $resultQas,
                'resultProjects' => $resultProjects,
                'resultBres' => $resultBres,
                'resultQal' => $resultQal,
                'resultDm' => $resultDm,
            ]);
        }
    }

    public function infoProject(Request $request)
    {
        $monthC = $request->get('month');
        $year = $request->get('year');
        $mm = Config::get('constant.men_month'); // Men month value
        $monthCurrent = (int) date('m');
        $listMonths = Config::get('constant.list_months_report');
        $status_id         = Config::get('constant.status');
        $language          = config::get('constant.project_language');
        $resource      = Config::get('constant.resource');


        $dataProjects = ProjectReport::select(
                'project_report.project_id',
                'project_report.project_name',
                'project_report.department_id',
                'project_report.department_name',
                'projects.status',
                'projects.plant_start_date',
                'projects.language_id',
                'projects.plant_end_date',
                'projects.plant_start_date',
                'projects.unit_test',
                'projects.test_first',
                'projects.detail_design',
                'users.last_name',
                'users.first_name',
                DB::raw('sum(actual_hour) as actual_hour'),
                DB::raw('sum(loc)  as loc'),
                DB::raw('sum(task)  as task'),
                DB::raw('sum(tested_tc)  as tested_tc'),
                DB::raw('sum(weighted_bug)  as weighted_bug'),
                DB::raw('sum(weighted_uat_bug)  as weighted_uat_bug'))
                ->where('projects.id',$request->get('id'))
                ->join('projects','projects.id','=','project_report.project_id')
                ->join('users','users.id','=','projects.brse')
        ;
        if(!empty($monthC))
        {
            if (!in_array(0, $monthC)) {
                $result = $dataProjects->where('year', $request->get('year'));
                $result = $dataProjects->groupBy('project_id');
            } else{
                $result = $dataProjects->where('year', $request->get('year'));
                $result = $dataProjects->groupBy('project_id');
            }
            if($year == 'this_year')
                $yearSearch = date('Y');
            else
                $yearSearch = date("Y",strtotime("-1 year"));

            $resultKpis = [];
            if(in_array(0, $monthC))
            {
                if($year == 'this_year')
                {
                    $firstDateDefault = date('Y-m-d', strtotime('first day of january this year'));
                    $endDateDefault   = date('Y-m-d', strtotime('last day of december this year'));
                }
                else{
                    $firstDateDefault = date('Y-m-d', strtotime('first day of january last year'));
                    $endDateDefault   = date('Y-m-d', strtotime('last day of december last year'));
                }
                $resultKpis = ProjectKpi::where('project_id', $request->get('id'))
                                    ->where('baseline_flag', 1)
                                    ->where('start_date', '>=', $firstDateDefault)
                                    ->where('start_date', '<=', $endDateDefault)
                                    ->orderBy('start_date','ASC')
                                    ->get()->toArray();
            }else{
                foreach ($monthC as $key=>$value)
                {
                    $firstDateDefault = date('Y-m-d', strtotime('first day of '.substr($listMonths[$value], 0, 3).' '.$yearSearch));
                    $endDateDefault   = date('Y-m-d', strtotime('last day of '.substr($listMonths[$value], 0, 3).' '.$yearSearch));
                    $kpis = ProjectKpi::where('project_id', $request->get('id'))
                                ->where('baseline_flag', 1)
                                ->where('start_date', '>=', $firstDateDefault)
                                ->where('start_date', '<=', $endDateDefault)
                                ->orderBy('start_date','ASC')
                                ->get()->first();
                    if($kpis != null)
                    {
                        $kpis = $kpis->toArray();
                        array_push($resultKpis, $kpis);
                        $kpis = null;
                    }
                }
            }
        }else{
            $result = $dataProjects->where('year', 'this_year');
            $result = $dataProjects->groupBy('project_id');

            $firstDateDefault = date('Y-m-d', strtotime('first day of January this year'));
            $endDateDefault   = date('Y-m-d', strtotime('last day of december this year'));

            $resultKpis = ProjectKpi::where('project_id', $request->get('id'))
                    ->where('baseline_flag', 1)
                    ->where('start_date', '>=', $firstDateDefault)
                    ->where('start_date', '<=', $endDateDefault)
                    ->orderBy('start_date','ASC')
                    ->get()
                    ->toArray();
        }
        $dataProjects = $dataProjects->get()->first();
        $estimate_project = $this->project->getDataPlanEffort($request->get('id'))->sum('estimate_time');
        $actual_project   = $this->project->getDataActualEffort($request->get('id'))->sum('actual_hour');
        return view('rank.detail_project',[
                        'dataProjects' => $dataProjects,
                        'mm' => $mm,
                        'month_project_kpi' => $resultKpis,
                        'status_id' => $status_id,
                        'language' => $language,
                        'estimate_project' => $estimate_project,
                        'actual_project' => $actual_project,
                        'resource' => $resource,

        ]);

    }

    public function infoDev(Request $request)
    {
        $monthC = $request->get('month');
        $year = $request->get('year');
        $mm = Config::get('constant.men_month'); // Men month value
        $dataDevs = MemberReport::select(
                'user_id',
                'name',
                'email',
                'user_name',
                'department_name',
                DB::raw('sum(workload)  as workload'),
                DB::raw('sum(kloc)  as kloc'),
                DB::raw('sum(task)  as task'),
                DB::raw('sum(bug_weighted)  as bug_weighted'),
                DB::raw('sum(madebug_weighted)  as madebug_weighted'))
                ->where('position', 'Dev')
                ->where('user_id',$request->id);

        if(!empty($monthC))
        {
            if (!in_array(0, $monthC)) {
                $dataDevs = $dataDevs->where('year',$year)
                                     ->whereIn('month',$monthC)
                                     ->get()
                                     ->first();
            }else{
                $dataDevs = $dataDevs->where('year',$year)->get()->first();
            }
        }else{
            if(empty($year))
            {
                $dataDevs = $dataDevs->where('year','this_year')->get()->first();
            }else
                $dataDevs = $dataDevs->where('year',$year)->get()->first();
        }
        return view('rank.detail_developer',[
            'dataDevs' => $dataDevs,
            'mm' => $mm,
        ]);
    }

    public function infoQa(Request $request)
    {
        $monthC = $request->get('month');
        $year = $request->get('year');
        $mm = Config::get('constant.men_month'); // Men month value
        $dataQas = MemberReport::select(
                'user_id',
                'name',
                'email',
                'user_name',
                'department_name',
                DB::raw('sum(workload)  as workload'),
                DB::raw('sum(kloc)  as kloc'),
                DB::raw('sum(task)  as task'),
                DB::raw('sum(bug_weighted)  as bug_weighted'),
                DB::raw('sum(madebug_weighted)  as madebug_weighted'))
                ->where('position', 'QA')
                ->where('user_id',$request->id);

                if(!empty($monthC))
                {
                    if (!in_array(0, $monthC)) {
                        $dataQas = $dataQas->where('year',$year)
                        ->whereIn('month',$monthC)
                        ->get()
                        ->first();
                    }else{
                        $dataQas = $dataQas->where('year',$year)->get()->first();
                    }
                }else{
                    if(empty($year))
                    {
                        $dataQas = $dataQas->where('year','this_year')->get()->first();
                    }else
                        $dataQas = $dataQas->where('year',$year)->get()->first();
                }
                return view('rank.detail_qa',[
                                'dataQas' => $dataQas,
                                'mm' => $mm,
                ]);
    }
}
