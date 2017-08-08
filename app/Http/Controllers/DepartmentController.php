<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDepartmentRequest;
use App\Models\Department;
use App\Models\Project;
use App\Models\User;
use App\Repositories\Department\DepartmentRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class DepartmentController extends Controller
{

    public function __construct(DepartmentRepositoryInterface $department,
        ProjectRepositoryInterface $project) {
        $this->department = $department;
        $this->project = $project;
    }

    /**
     * @todo Show list of departments.
     *
     * @author thanhnb6719
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index(Request $request)
    {
        $dep             = Department::all();
        $companyArray    = $this->department->getDepDevTeam($dep);
        $departments     = $companyArray['departments'];
        $divisions       = $companyArray['divisions'];
        $teams           = $companyArray['teams'];
        return view('department.list', ['departments' => $departments,
            'divisions'   => $divisions,
            'teams'       => $teams]);
    }

    /**
     * @todo Show form of create new deparment function.
     *
     * @author chaunm8181
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function create()
    {
        $department = Department::all();
        $manager_id = User::select(DB::raw('CONCAT(last_name, " ", first_name) AS full_name,member_code'))
        ->where('member_code','!=', 0)
        ->lists('full_name','member_code')
        ->all();
        return view('department.create', compact('department','manager_id'));
    }

    /**
     * @todo Save new department function
     *
     * @author chaunm8181
     * @param CreateDepartmentRequest $request
     */
    public function store(CreateDepartmentRequest $request)
    {
        $department = $this->department->save($request->except('_token'));
        return redirect(Route('department.index'))
        ->withSuccess(Lang::get('message.create_department_success'));
    }

    /**
     * @todo Show the form for editing the department.
     *
     * @author chaunm8181
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function edit($id)
    {
        $department = Department::find($id);
        $parent_id  = $department->parent_id;
        $parent     = Department::find($parent_id);
        $manager_id = User::select(DB::raw('CONCAT(last_name, " ", first_name) AS full_name,member_code'))
        ->where('member_code','!=', 0)
        ->lists('full_name','member_code')
        ->all();
        return view('department.edit', compact('department','parent','manager_id'));
    }

    /**
     * @todo Update department after edit.
     *
     * @author chaunm8181
     * @param CreateDepartmentRequest $request
     * @param int $id
     */
    public function update(CreateDepartmentRequest $request, $id)
    {
        $department = $this->department->update($request->except('_token'),$id);
        return redirect(Route('department.index'))
        ->withSuccess(Lang::get('message.update_department_success'));
    }

    /**
     * @todo Remove the specified resource from storage.
     *
     * @author chaunm8181
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $id           = $request->id;
        $parent       = Department::where('parent_id', $id)->count();
        $current_page = $request->page;
        $keyword      = $request->keyword;
        if ($parent == 0) {
            $project    = Project::all();
            foreach ($project as $value)
            {
                if($id == $value->department_id )
                {
                    return redirect()->route('department.index',
                        ['keyword' => $keyword,'page' => $current_page ])
                    ->withErrors(Lang::get('message.delete_department_fail'));
                }else {
                    $department = Department::find($id);
                    $department->delete($id);
                    return redirect()->route('department.index',
                        ['keyword' => $keyword,'page' => $current_page ])
                    ->withSuccess(Lang::get('message.delete_department_success'));
                }
            }
        } else {
            return redirect()->route('department.index',
                ['keyword' => $keyword,'page' => $current_page ])
            ->withErrors(Lang::get('message.delete_department_fail'));
        }
    }

    /**
     * @todo fill data in to combobox search department, division, team, project, brse
     *
     * @author sonna
     * @return array
     */
    public function fillcombobox(){
        // Get data to fill select box
        $groupCheck  = $this->project->getGroupProjectMemberJoin("");
        $users       = User::all('id','first_name','last_name')->toArray();
        $user_brse   = [];
        $companys    = [];
        foreach ($users as $key => $value){
            if(in_array($value['id'], $groupCheck['brses'])){
                array_push($user_brse, ['id' => $value['id'],
                 'name' =>$value['first_name'].$value['last_name']]);
            }
        }
        array_push($companys, ['departmenrts' => $groupCheck['departments'],
         'divisions'    => $groupCheck['divisions'],
         'teams'        => $groupCheck['teams'],
         'projects'     => $groupCheck['projects'],
         'brses'        => $user_brse
         ]);
        return $companys;
    }

    /**
     * @author SonNA
     * @todo get project item by team
     * @see department_id field in project table is team_id
     * @return json
     */
    public function getProject(Request $request){
        $team_id = $request->team_id;
        if(!empty($team_id)){
            $fileds = ['id','name'];
            $projects = $this->project->getDatasByAttribute($fileds, 'department_id', $team_id)
            ->get()->toArray();
            return $projects;
        }else{
            return $projects = [];
        }
    }
}
