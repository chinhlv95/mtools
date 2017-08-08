<?php

namespace App\Http\Controllers;

use App\Repositories\Api\ApiRepositoryInterface;
use App\Repositories\Entry\EntryRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\ProjectMember\ProjectMemberRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Repositories\Cost\CostRepositoryInterface;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class PersonalCostController extends Controller
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
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Get data in url
        $projectId        = $request->get('project','');
        $time             = $request->get('check_time','');
        $defaultTime      = $request->get('date','');
        $getDepartment    = $request->get('department','');
        $getDivision      = $request->get('division','');
        $getTeam          = $request->get('team','');
        $requestStartDate = $request->get('start_date','');
        $requestEndDate   = $request->get('end_date','');
        $selectDate       = Config::get('constant.select_date');
        $paginate         = Config::get('constant.paginate_number');
        $limit            = $request->get('limit', Config::get('constant.RECORD_PER_PAGE'));
        $number           = ($request->get('page','1') - 1)* $limit;
        // Get date
        $dateArray        = $this->project->getTimeSearch($time, $defaultTime, $requestStartDate, $requestEndDate);
        $startDate        = $dateArray['start'];
        $endDate          = $dateArray['end'];

        // Get data to fill select box
        $groupCheck = $this->project->getGroupProjectMemberJoin("user.view_personal_cost");
        $departments       = $groupCheck['departments'];
        $divisions         = $groupCheck['divisions'];
        $teams             = $groupCheck['teams'];
        $projects          = $groupCheck['projects'];
        $projectMemberJoin = $groupCheck['projectJoin'];

        $allMember = $this->projectMembers->getMemberOrder($projectId, $getDepartment, $getDivision, $getTeam, $projectMemberJoin)->paginate($limit);
        $memberInProject = $this->projectMembers->getMemberInPersonalCost($projectId, $getDepartment, $getDivision, $getTeam, $projectMemberJoin)->get();
        $allEntry = $this->entry->getEntryOfPersonal($startDate, $endDate);

        return view('cost.personal.index',['start_date'     => $startDate,
                                           'end_date'       => $endDate,
                                           'departments'    => $departments,
                                           'divisions'      => $divisions,
                                           'teams'          => $teams,
                                           'select_date'    => $selectDate,
                                           'paginate'       => $paginate,
                                           'memberInProject'=> $memberInProject,
                                           'allMember'      => $allMember,
                                           'number'         => $number,
                                           'projects'       => $projects,
                                           'allEntry'       => $allEntry
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
