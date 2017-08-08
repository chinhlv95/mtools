<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProjectKpiRequest;
use App\Models\Project;
use App\Models\ProjectKpi;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\ProjectKpi\ProjectKpiRepositoryInterface;
use App\Repositories\QualityReport\QualityReportByProjectRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;

class ProjectKpiController extends Controller
{
    public function __construct(ProjectRepositoryInterface $project,
                                QualityReportByProjectRepositoryInterface $report,
                                ProjectKpiRepositoryInterface $projeckpi
            )
    {
        $this->project    = $project;
        $this->report     = $report;
        $this->projectKpi = $projeckpi;
    }

    /**
     * @todo Display a listing of the Kpi.
     *
     * @author thanhnb6719
     * @param request $request
     * @param $project_id
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $project_id)
    {
        // Get data in url
        $getReportType    = $request->get('reportType');
        $time             = $request->get('check_time');
        $defaultTime      = $request->get('date');
        $requestStartDate = $request->get('start_date');
        $requestEndDate   = $request->get('end_date');

        // Get data default in constant
        $selectDate       = Config::get('constant.report_select_date');
        $reportType       = Config::get('constant.kpi_report_type');

        // Format date
        $dateArray        = $this->project->getTimeSearch($time, $defaultTime, $requestStartDate, $requestEndDate);
        $startDate        = $dateArray['start'];
        $endDate          = $dateArray['end'];
        $firstDateDefault = date('d/m/Y', strtotime('first day of this month'));
        $endDateDefault   = date('d/m/Y', strtotime('last day of this month'));

        // Set default data for project kpi and get it
        $week_project_kpi  = [];
        $month_project_kpi = [];
        $base_project_kpi  = [];

        if ($getReportType == 1) {
            $week_project_kpi  = $this->projectKpi->getDataOfKpiFollowFlag($project_id, 0, $startDate, $endDate);
        } elseif ($getReportType == 2) {
            $month_project_kpi = $this->projectKpi->getDataOfKpiFollowFlag($project_id, 1, $startDate, $endDate);
        } else {
            $base_project_kpi  = $this->projectKpi->getDataOfKpiFollowFlag($project_id, 2, $startDate, $endDate);
        }

        return view('project_kpi.index', ['week_project_kpi'    => $week_project_kpi,
                                          'month_project_kpi'   => $month_project_kpi,
                                          'base_project_kpi'    => $base_project_kpi,
                                          'project_id'          => $project_id,
                                          'start_date'          => $startDate,
                                          'end_date'            => $endDate,
                                          'select_date'         => $selectDate,
                                          'reportType'          => $reportType,
                                          'firstDateDefault'    => $firstDateDefault,
                                          'endDateDefault'      => $endDateDefault,
        ]);
    }

    /**
     * @todo Show the form for creating a new resource
     *
     * @author thanhnb6719
     * @param int $project_id
     * @return \Illuminate\Http\Response
     */
    public function create($project_id)
    {
        $project      = $this->project->find($project_id);
        $projectName  = $project->name;
        $startProject = $project->actual_start_date;
        $endDate      = date('Y-m-d',strtotime("today"));
        $endSearch    = date('d/m/Y',strtotime("today"));
        if ($startProject == null || $startProject == "0000-00-00") {
            return redirect()->back()->with('errorsMessage', 'To create a Baseline KPI for project: '.$projectName.', you need to edit Actual start date first.');
        } else {
            $baseLine     = $this->projectKpi->getFirstBaselineKpi($project_id);
            if ($baseLine == null) {
                $startSearch = date('d/m/Y', strtotime($startProject));
            } else {
                $nextDate    = date('Y-m-d', strtotime($baseLine->end_date .' +1 day'));
                $today       = date('Y-m-d', strtotime('today'));
                $startSearch = date('d/m/Y', strtotime($nextDate));
                if ($nextDate > $today) {
                    $endSearch = $startSearch;
                }
            }
        }

        $metric       = $this->projectKpi->getMetricFollowDate($project_id, $startProject, $endDate);
        return view('project_kpi.create', ['project'    => $project,
                                           'start_date' => $startSearch,
                                           'end_date'   => $endSearch,
                                           'project_id' => $project_id,
                                           'metric'     => $metric]);
    }

    /**
     * @todo Store a newly created resource in storage.
     *
     * @author thanhnb6719
     * @param int $project_id
     * @param request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($project_id, CreateProjectKpiRequest $request)
    {
        $project                      = Project::find($project_id);
        $project_kpi                  = $request->all();
        $project_kpi['start_date']    = \Helpers::formatDateYmd($project_kpi['kpi_start_date']);
        $project_kpi['end_date']      = \Helpers::formatDateYmd($project_kpi['kpi_end_date']);
        $project_kpi['project_id']    = $project->id;
        $project_kpi['baseline_flag'] = 2;
        $lastBaseline = ProjectKpi::create($project_kpi);
        $this->projectKpi->updateBaselineForProjectKpi($lastBaseline->id, $project_id);
        return redirect(Route('kpi.index',$project_id))->withSuccess(Lang::get('message.create_project_kpi_success'));
    }

    /**
     * @todo Display the specified resource.
     *
     * @author thanhnb6719
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('project_kpi.show');
    }

    /**
     * @todo Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @param  int  $project_id
     * @return \Illuminate\Http\Response
     */
    public function edit($project_id, $id)
    {
        $project = Project::find($project_id);
        $kpi     = ProjectKpi::find($id);
        return view('project_kpi.edit', compact('kpi','project','project_id'));
    }

    /**
     * @todo Update the specified resource in storage.
     *
     * @author thanhnb6719
     * @param  request $request
     * @param  int  $project_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $project_id, $id)
    {
        $data               = $request->all();
        $start_date = implode('-', array_reverse(explode('/', $data['start_date'])));
        $end_date   = implode('-', array_reverse(explode('/', $data['end_date'])));
        $checkStartBaseline = ProjectKpi::where('project_id', $project_id)
                                        ->where('baseline_flag', 2)
                                        ->where('start_date', '<=', $start_date)
                                        ->where('end_date', '>=', $start_date)
                                        ->where('id', '<>', $id)
                                        ->first();
        if ($checkStartBaseline != null) {
            return redirect()->back()->with('errorsMessage', 'Start date of this Baseline KPI was created before. Start date was located outside of the start date and end date of the nearest baseline.');
        }

        $checkEndBaseline = ProjectKpi::where('project_id', $project_id)
                                        ->where('baseline_flag', 2)
                                        ->where('start_date', '<=', $end_date)
                                        ->where('end_date', '>=', $end_date)
                                        ->where('id', '<>', $id)
                                        ->first();
        if ($checkEndBaseline != null) {
            return redirect()->back()->with('errorsMessage', 'End date of this Baseline KPI was created before. End date was located outside of the start date and end date of the nearest baseline.');
        }

        $updateBaseLineKpi  = $this->projectKpi->updateBaselineKpi($id, $project_id, $data);
        $this->projectKpi->updateBaselineForProjectKpi($id, $project_id);
        return redirect(Route('kpi.index', $project_id))->withSuccess(Lang::get('message.update_project_kpi_success'));
    }

    /**
     * @todo Remove the specified resource from storage.
     *
     * @author thanhnb6719
     * @param  int  $project_id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $project_id)
    {
        $id = $request->id;
        $project_kpi = ProjectKpi::find($id);
        $this->projectKpi->updateBaselineForProjectKpi($id, $project_id, 1);
        $project_kpi->delete();
        return redirect(Route('kpi.index',['project_id' => $project_id]))->withSuccess(Lang::get('message.delete_project_kpi_success'));
    }

    /**
     * @todo Show data in baseline create (using ajax)
     *
     * @author chaunm8181
     * @param Request $request
     * @return array $matric
     */
    public function selectDate(Request $request)
    {
        $metric         = [];
        $mm             = Config::get('constant.men_month');
        $id             = $request->project_id;
        $project        = $this->project->find($id);
        $endDate        = \Helpers::formatDateYmd($request->end_date);
        $startProject   = $project->actual_start_date;
        if (!empty($startProject) && !empty($endDate)) {
            $metric = $this->projectKpi->getMetricFollowDate($id, $startProject, $endDate);
        };
        return $metric;
    }

    /**
     * @todo Sync data function (Save/update kpi data with long term)
     *
     * @author thanhnb6719
     * @param unknown $project_id
     */
    public function sync($project_id) {
        $startProject  = Project::where('id', $project_id)->first()->actual_start_date;
        if ($startProject == null || $startProject == '0000-00-00') {
            return Redirect::back()->withErrors(['Cannot sync up KPI data. Please add actual time of project first!']);
        } else {
            $save = $this->projectKpi->saveSyncKpi($project_id, $startProject);
            return Redirect::back();
        }
    }
}
