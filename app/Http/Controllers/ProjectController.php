<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Department;
use App\Models\FileUpload;
use App\Models\Project;
use App\Models\ProjectKpi;
use App\Models\ProjectRisk;
use App\Models\ProjectVersion;
use App\Models\User;
use App\Repositories\Department\DepartmentRepositoryInterface;
use App\Repositories\FileUpload\FileUploadRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\ProjectKpt\ProjectKptRepositoryInterface;
use App\Repositories\ProjectMember\ProjectMemberRepositoryInterface;
use App\Repositories\ProjectRisk\ProjectRiskRepositoryInterface;
use App\Repositories\ProjectVersion\ProjectVersionRepositoryInterface;
use App\Repositories\Ticket\TicketRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use DB;
use GuzzleHttp\json_decode;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\View\View;
use Redirect;
use Request as RequestStt;
use App\Repositories\Permission\PermissionRepositoryInterface;
use Session;

class ProjectController extends Controller {

    /**
     *  use ProjectRepositoryInterface
     *  @author ChauNM
     *  @param ProjectRepositoryInterface $project
     *  @return void()
     */
    public function __construct(ProjectRepositoryInterface $project,
                                UserRepositoryInterface $user,
                                FileUploadRepositoryInterface $fileupload,
                                ProjectKptRepositoryInterface $project_kpt,
                                ProjectRiskRepositoryInterface $project_risk,
                                TicketRepositoryInterface $project_ticket,
                                ProjectVersionRepositoryInterface $projectVersion,
                                DepartmentRepositoryInterface $department,
                                ProjectMemberRepositoryInterface $projectMember,
                                PermissionRepositoryInterface $permission)
    {
        $this->project        = $project;
        $this->user           = $user;
        $this->fileupload     = $fileupload;
        $this->project_kpt    = $project_kpt;
        $this->project_risk   = $project_risk;
        $this->project_ticket = $project_ticket;
        $this->projectVersion = $projectVersion;
        $this->department     = $department;
        $this->project_member = $projectMember;
        $this->permission     = $permission;
    }

    /**
     * @todo Get list Brse
     *
     * @author thanhnb6719
     * @return array $listBrse
     */
    private function getBrse() {
        $roleBseVn     = Sentinel::findRoleById(7);
        $bseVn         = $roleBseVn->users()
                                   ->select(DB::raw('CONCAT(last_name, " ", first_name) AS full_name, users.id'))
                                   ->lists('full_name','id')
                                   ->all();
        $roleBseJp     = Sentinel::findRoleById(8);
        $bseJp         = $roleBseJp->users()
                                   ->select(DB::raw('CONCAT(last_name, " ", first_name) AS full_name, users.id'))
                                   ->lists('full_name','id')
                                   ->all();
        $roleSubBse    = Sentinel::findRoleById(11);
        $subBse        = $roleSubBse->users()
                                    ->select(DB::raw('CONCAT(last_name, " ", first_name) AS full_name, users.id'))
                                    ->lists('full_name','id')
                                    ->all();
        $brseAsManager = User::select(DB::raw('CONCAT(last_name, " ", first_name) AS full_name, users.id'))
                                            ->join('departments', 'departments.manager_id','=','users.member_code')
                                            ->orderBy('first_name')
                                            ->lists('full_name','id')
                                            ->all();
        $listBrse           = $brseAsManager + $bseVn + $bseJp + $subBse;
        return $listBrse;
    }

    /**
     * @todo Show list project
     *
     * @author chaunm8181
     * @param Request $request
     * @return view
     */
    public function index(Request $request) {
        $user              = Sentinel::check();
        $userId            = $user->id;
        $source_id         = Config::get('constant.stream_types');
        $limit             = $request->get('limit',Config::get('constant.RECORD_PER_PAGE'));
        $stt               = ( $request->get('page','1') - 1 ) * $limit;
        $type              = Config::get('constant.project_type');
        $status_id         = Config::get('constant.status');
        $language          = config::get('constant.project_language');
        $paginate          = Config::get('constant.paginate_number');
        $brse              = $this->getBrse();
        $role_id           = $this->permission->getRoleOfUser();

        $managerIds        = $this->user->getManagerId();

        // Get data to fill select box
        $department_list   = Department::all();
        $department_all    = $this->department->getDepDevTeam($department_list);
        $department        = $department_all['departments'];
        $division          = $department_all['divisions'];
        $team              = $department_all['teams'];
        $type_id           = $request->get('type_id','');
        $project_name      = $request->get('name','');
        $status            = $request->get('status','');
        $bse               = $request->get('bse','');
        $department_id     = $request->get('department','');
        $division_id       = $request->get('division','');
        $team_id           = $request->get('team','');
        $language_id       = $request->get('language_id','');
        $groupCheck        = $this->project->getGroupProjectMemberJoin("user.view_list_project");
        $departments       = $groupCheck['departments'];
        $projectMemberJoin = $groupCheck['projectJoin'];

        $searchGroup = $this->project->saveDeparamentSearch($department_id,$division_id,$team_id,$groupCheck['divisions'],$groupCheck['teams'],$groupCheck['projects']);
        $divisions         = $searchGroup['divisions'];
        $teams             = $searchGroup['teams'];
        $projects          = $searchGroup['projects'];

        $managerIds       = $this->user->getManagerId();

        $project           = $this->project->searchInProjectList
                            ($role_id, $projectMemberJoin,$type_id,
                             $project_name, $status, $bse, $department_id,
                             $division_id, $team_id, $language_id, $limit);

        return view('projects.index',
                compact('project','type','status_id','department_id',
                        'brse','language','stt','paginate','departments',
                        'divisions','teams','source_id','user','role_id',
                        'department','division', 'team','managerIds','userId','managerIds'));
    }

    /**
     * Show Form Create
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function create()
    {
        $managerIds       = $this->user->getManagerId();
        if (Sentinel::check()->hasAccess('user.create_project') || in_array(Sentinel::check()->id, $managerIds))
        {
            $data           = Department::all();
            $department_all = $this->department->getDepDevTeam($data);
            $departments    = $department_all['departments'];
            $divisions      = $department_all['divisions'];
            $teams          = $department_all['teams'];
            $brse           = $this->getBrse();
            $status         = Config::get('constant.status_project');
            $type_id        = Config::get('constant.project_type');
            $language_id    = config::get('constant.project_language');
            return view('projects.create', compact('type_id','status','departments',
                                                   'language_id','brse','divisions',
                                                   'teams'));
        }else return redirect(Route('projects.index'));
    }

    /**
     * Create New Project
     *
     * @param CreateProjectRequest $request
     */

    public function store(CreateProjectRequest $request)
    {
        if(!Sentinel::getUser()->inRole(Sentinel::findRoleById(1)->slug))
        {
            $teamCreated = $this->project->getDepartmentWhichManagerManage(Sentinel::getUser()->id);
            if(!in_array($request->get('department_id'), $teamCreated))
            {
                return redirect()->back()->with('errorsMessage','Do not permisson create team')->withInput();
            }
        }
        $data = $request->all();
        $project = $this->project->save($request->except('_token'));
        return redirect(Route('projects.index'))
            ->withSuccess(Lang::get('message.create_project_success'));
    }

    /**
     *  @todo Edit Project
     *  @author ChauNM
     *  @param $id integer
     */
    public function edit($id)
    {
        $checkPermission = $this->permission->checkPermissionForProject($id, "user.update_project_info");
        if($checkPermission == true){
            $project       = Project::find($id);
            $team_id       = $project->department_id;
            $team          = Department::find($team_id);
            $division_id   = $team->parent_id;
            $division      = Department::find($division_id);
            $department_id = $division['parent_id'];
            $department    = Department::find($department_id);
            $project_id    = $id;

            $fileUploaded  = $this->fileupload->findFileUploadByProjectId($id);
            $public_path   = '/uploads/';
            $notFileImages = ["doc","docx","xls","xlsx","xlsm","ppt","pptx","pdf","txt"];
            $resrouce_need = '';
            if(!empty($project->resource_need)){
                $resrouce_need = json_decode($project->resource_need, true);
            }

            $data           = Department::all();
            $department_all = $this->department->getDepDevTeam($data);
            $departments    = $department_all['departments'];
            $divisions      = $department_all['divisions'];
            $teams          = $department_all['teams'];
            $brse           = $this->getBrse();

            $status         = Config::get('constant.status_project');
            $type_id        = Config::get('constant.project_type');
            $stream_id      = Config::get('constant.stream_types');
            $language_id    = Config::get('constant.project_language');
            $process_apply  = Config::get('constant.process_apply');
            $resources      = Config::get('constant.position');

            return view('projects.edit',compact('project','status','type_id',
                    'brse','stream_id','language_id','departments','divisions',
                    'teams','process_apply','fileUploaded','public_path','resources',
                    'resrouce_need','notFileImages',
                    'department','divison','team','division_id', 'department_id',
                    'project_id'));
        } else {
            $errorsMessage = 'Sorry. You can not access this page because lack permission!';
            return Redirect::back()->with('errorsMessage', $errorsMessage);
        }
    }

    /**
     * @param UpdateProjectRequest $request
     * @param type $id
     * @return type
     */
    public function update($project_id,UpdateProjectRequest $request)
    {
        $project = $this->project->update($request->except('_token'),$project_id);
        return redirect(Route('project.show',['id'=>$project_id]))->withSuccess(Lang::get('message.update_project_success'));
    }

    /**
     * Fillter data selectbox aijax
     * @author ChauNM
     * @return array
     */
    public function fillterDataByGroupIdWithAjax(Request $request){
        $id     = $request->id;
        $groups = $this->project->apiDepartment();
        $result = $this->project->filterData($id, $groups);
        return $result;
    }

    /**
     * View Default Screen Project Info
     * @author SonNA
     * @return view project info
     */
    public function ProjectReport($id) {
        return view('projects.report', ['project_id' => $id]);
    }

    /**
     * Handler upload file ajax
     * @author SonNA
     * @return filesInfo
     */
    public function uploadFiles(Request $request,Filesystem $filesystem){
        $files          = $request->file('__files');
        $target_dir     = public_path().'/uploads/';
        $public_path    = '/uploads/';
        $project_id     = $request->input('project_id');
        $directoryFlag  = $filesystem->exists($target_dir);
        $notFileImages  = ["doc","docx","xls","xlsx","xlsm","ppt","pptx","pdf","txt"];
        if(!$directoryFlag){
            $filesystem->makeDirectory($target_dir,777,false,false);
        }

        foreach ($files as $file):
            $fileName = 'project_'.rand(11111,99999).'_'.$project_id.'_'.preg_replace('#[^\w-]#', "", $file->getClientOriginalName());
            $fileUploadId = $this->fileupload->save($fileName,
                                    $file->getClientSize(),
                                    $file->getClientOriginalExtension() , $project_id);
            $ArraysId[] = $fileUploadId;
            $file->move($target_dir,$fileName);
        endforeach;

        foreach ($ArraysId as $k):
            $fileUploaded[] = $this->fileupload->find($k);
        endforeach;
        return view('projects.upload',['files' => $files,'filePath' =>
                        $public_path,'fileUploaded' => $fileUploaded,'notFileImages' => $notFileImages] );
    }

    /**
     * Handler delete file ajax
     * @author SonNA
     * @return filesInfo
     */
    public function delete(Request $request,Filesystem $filesystem){
        $fileId         = !empty($request->input('id')) ? $request->input('id') : '';
        $project_id     = !empty($request->project_id) ? $request->project_id : '';
        $file_name      = !empty($request->input('file-name')) ? $request->input('file-name') : '';
        $deleteFileList = !empty($request->_checkedValue) ? $request->_checkedValue : '';
        $public_path    = '/uploads/';
        $notFileImages  = ["doc","docx","xls","xlsx","xlsm","ppt","pptx","pdf","txt"];

        //delete files
        if(!empty($deleteFileList)){
            foreach ($deleteFileList as $key => $value):
                $this->deleteItem($filesystem,$value['value'], $value['id'],$this->fileupload);
            endforeach;
        }else{
            //delete one file
            $this->deleteItem($filesystem,$file_name, $fileId,$this->fileupload);
        }
        $files = $this->fileupload->findFileUploadByProjectId($project_id);

        return view('projects.upload',['filePath' =>
                        $public_path,'fileUploaded' => $files,'notFileImages'
                            => $notFileImages]);
    }

    /**
     * Handler delete item file
     * @author SonNA
     * @return filesInfo
     */
    static function deleteItem($filesystem,$file_name,$fileId,$fileupload){
        $target_dir = public_path().'/uploads/';
        $filesystem->delete($target_dir.$file_name);
        $delete     = $fileupload->delete($fileId);
        return $delete;
    }

    /**
     *  @todo download uploaded file
     *  @author SonNA
     *  @return response a link download file
     */
    public function download($file_name){
        $pathToFile = public_path('/uploads/'.$file_name);
        return response()->download($pathToFile);
    }

    /**
     * chauNM
     * @param Request $request
     */
    public function Inprogress(Request $request)
    {
        $id = $request->id;
        $project = Project::find($id);
        if($project)
        {
            if($project->sync_flag == 0){
                $project->sync_flag = "1";
            }elseif($project->sync_flag == 1){
                $project->sync_flag = "0";
            }
            $project->update();
        }
        $flag = $project->sync_flag;
        return $flag;
    }

    /**
     * ChauNM
     * @param Request $request
     */
    public function active(Request $request)
    {
        $id = $request->id;
        $project = Project::find($id);
        if($project->active == 0){
            $project->active = "1";
            $project->sync_flag = "0";
        }elseif($project->active == 1){
            $project->active = "0";
            $project->sync_flag = "0";
        }
        $project->update();
        $flag = $project->active;
        return $flag;
    }

    /**
     * Add project_key, project_id sync data project
     * ChauNM
     * @param Request $request
     */
    public function sync(Request $request)
    {
        $input = $request->all();

        if(isset($input['project_id']))
        {
            $this->validate($request, [
                            'project_id' => 'required|numeric',
                            'source_id' => 'required',
            ]);
        }
        if(isset($input['project_key']))
        {
            $this->validate($request, [
                            'project_key' => 'required',
                            'source_id' => 'required',
            ]);
        }

        $check = Project::where('active','=',1)
                            ->where('project_key','=',$input['project_key']);
        if(empty($check->get()))
            return Redirect::back()->with('message','Wrong Project KEY isset');
        $id = $input['project'];
        $project = Project::find($id);
        $project->sync_flag = 1;
        $project->update($input);
        if ($project->sync_flag == 2)
        {
            return Redirect::back()->with('message','Wrong Project ID, Key');
        }
        return redirect(route('projects.index'))
                ->withSuccess(Lang::get('message.sync_data_success'));
    }

    /**
     * @author ChauNM
     * @param unknown $id
     */
    public function crawleredit($id)
    {
        $project = Project::find($id);
        $source_id = config::get('constant.stream_types');
        return view('projects.crawler', compact('source_id','project'));
    }

    /**
     * @author ChauNM
     * @param unknown $id
     * @param Request $request
     */
    public function crawlerupdate($id, Request $request)
    {
        $project = $this->project->saveDataCrawler($request->except('_token'),$id);
        return redirect(route('projects.index'))
            ->withSuccess(Lang::get('message.update_project_success'));
    }

    /**
     * @todo Show project info
     *
     * @author chaunm8181
     * @param unknown $id
     * @param request $request
     */
    public function show($id,Request $request)
    {
        $checkPermission = $this->permission->checkPermissionForProject($id, "user.view_project_info");
        if($checkPermission == true){
            $process_apply = Config::get('constant.process_apply');
            $resource      = Config::get('constant.resource');
            $status        = Config::get('constant.status');
            $types         = Config::get('constant.kpt_type');
            $limit         = Config::get('constant.RECORD_PER_PAGE');
            $risk_strategy = Config::get('constant.risk_strategy');
            $language      = Config::get('constant.project_language');
            $type          = Config::get('constant.project_type');
            $stt           = ( RequestStt::get('paginate-version','1') - 1 ) * $limit;
            $sttKpt        = ( RequestStt::get('paginate-kpt','1') - 1 ) * $limit;
            $sttRisk       = ( RequestStt::get('paginate-risk','1') - 1 ) * $limit;
            $sttCategory   = ( RequestStt::get('page','1') - 1 ) * $limit;
            $brse          = $this->getBrse();
            $project       = Project::find($id);
            $project_id    = $project['id'];

            $list_kpt      = $this->project_kpt->getKptList('','','', $project_id)
                            ->paginate($limit, ['*'], 'paginate-kpt');
            $list_risk     = ProjectRisk::where('project_id','=',$project_id)
                                        ->paginate($limit, ['*'], 'paginate-risk');
            $list_ticket   = $this->project_ticket->ticket_project($project_id,$request->get('page',1),$request);

            $project_kpi = ProjectKpi::where('project_id',$project_id)
                                           ->where('baseline_flag','0')
                                           ->orderBy('start_date','desc')
                                           ->take(4)
                                           ->get();
            $week_project_kpi = array_reverse($project_kpi->toArray());
            $dataVersion = $this->projectVersion->getDataVersionAndEntriesTicket($id,$request->get('paginate-version',1),$request);
            // get data resource_need
            $versionEstimate = $dataVersion['versions'];
            $versionActual = $dataVersion['versionActual'];

            if(!empty($project->resource_need)){
                $resource_need = json_decode($project->resource_need, true);
            }
            // count version_task and version_defect
            $version_task     = $this->projectVersion->getDataTaskInVersion()->where('ticket_type_id','=',1)->get();
            $version_defect   = $this->projectVersion->getDataTaskInVersion()->where('ticket_type_id','=',2)->get();
            $estimate_project = $this->project->getDataPlanEffort($id)->sum('estimate_time');
            $actual_project   = $this->project->getDataActualEffort($id)->sum('actual_hour');


            return view('projects.project_info',
                    compact('project','status','list_kpt','list_risk',
                            'list_ticket','types','stt','sttKpt','sttRisk','sttCategory',
                            'risk_strategy','brse','language','type','resources','resource_need',
                            'number_tasks','number_bug','project_id','versionEstimate','versionActual',
                            'version_task','version_defect','estimate_project','actual_project',
                            'week_project_kpi','process_apply','resource','test','test1'));
        } else {
            $errorsMessage = 'Sorry. You can not access this page because lack permission!';
            return Redirect::back()->with('errorsMessage', $errorsMessage);
        }

        $process_apply = Config::get('constant.process_apply');
        $resource      = Config::get('constant.resource');
        $status        = Config::get('constant.status');
        $types         = Config::get('constant.kpt_type');
        $limit         = Config::get('constant.RECORD_PER_PAGE');
        $risk_strategy = Config::get('constant.risk_strategy');
        $language      = Config::get('constant.project_language');
        $type          = Config::get('constant.project_type');
        $stt           = ( RequestStt::get('paginate-version','1') - 1 ) * $limit;
        $sttKpt        = ( RequestStt::get('paginate-kpt','1') - 1 ) * $limit;
        $sttRisk       = ( RequestStt::get('paginate-risk','1') - 1 ) * $limit;
        $sttCategory   = ( RequestStt::get('page','1') - 1 ) * $limit;
        $brse          = $this->getBrse();
        $project       = Project::find($id);
        $project_id    = $project['id'];
        $version       = ProjectVersion::where('project_id','=',$project_id)
                                       ->groupBy('name')
                                       ->orderBy('name', 'desc')
                                       ->paginate($limit, ['*'], 'paginate-version');
        $list_kpt      = $this->project_kpt->getKptList('','','', $project_id)
                        ->paginate($limit, ['*'], 'paginate-kpt');
        $list_risk     = ProjectRisk::where('project_id','=',$project_id)
                                    ->paginate($limit, ['*'], 'paginate-risk');
        $list_ticket   = $this->project_ticket->ticket_project($project_id,$request->get('page',1),$request);

        $week_project_kpi = ProjectKpi::where('project_id',$project_id)
                                       ->where('baseline_flag','0')
                                       ->get();
        // get data resource_need
        if(!empty($project->resource_need)){
            $resource_need = json_decode($project->resource_need, true);
        }
        // count estima_time in ticket
        $estimate         = $this->projectVersion->getDataJoinTicketAndVersion($id)->get();
        // count actual_hour in entries
        $actual_hour      = $this->projectVersion->getDataJoinTicketAndEntries($id)->get();
        // count version_task and version_defect
        $version_task     = $this->projectVersion->getDataTaskInVersion()->where('ticket_type_id','=',1)->get();
        $version_defect   = $this->projectVersion->getDataTaskInVersion()->where('ticket_type_id','=',2)->get();
        $estimate_project = $this->project->getDataPlanEffort($id)->sum('estimate_time');
        $actual_project   = $this->project->getDataActualEffort($id)->sum('actual_hour');

        return view('projects.project_info',
                compact('project','version','status','list_kpt','list_risk',
                        'list_ticket','types','stt','sttKpt','sttRisk','sttCategory',
                        'risk_strategy','brse','language','type','resources','resource_need',
                        'number_tasks','number_bug','project_id','estimate','actual_hour',
                        'version_task','version_defect','estimate_project','actual_project',
                        'week_project_kpi','process_apply','resource'));

    }

}