<?php
namespace App\Http\Controllers;

use App\Repositories\ProjectMember\ProjectMemberRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use Illuminate\Support\Facades\Config;
use Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request as MyRequest;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\AssignFormRequest;
use Illuminate\Support\Facades\Input;
use App\Http\Requests\AssignedSearchRequest;
use App\Models\User;
use function GuzzleHttp\json_encode;
use App\Repositories\User\UserRepositoryInterface;
use App\Models\ProjectMember;
use App\Repositories\Permission\PermissionRepositoryInterface;
use Illuminate\Support\Facades\Redirect;
use Cartalyst\Sentinel\Laravel\Facades\Activation;

class MemberController extends Controller {

    static $removeRoles = ['Admin','General Director',
                           'Department Manager','Division Manager',
                           'Team Leader'];
    /**
     * @author tampt6722
     * @param ProjectRepositoryInterface $project
     * @param ProjectMemberRepositoryInterface $members
     */
    public function __construct(
        ProjectMemberRepositoryInterface $members,
        ProjectRepositoryInterface $project,
        UserRepositoryInterface $users,
        PermissionRepositoryInterface $permission)
    {
        $this->members = $members;
        $this->project = $project;
        $this->roles = Sentinel::getRoleRepository()->setModel('App\Models\Roles');
        $this->users = $users;
        $this->permission = $permission;
    }

    /**
     * @author SonNA
     * Get List Member in project
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index($id)
    {
        $checkPermission = $this->permission->checkPermissionForProject($id, "user.view_member");
        if ($checkPermission == true) {
            $user       = Sentinel::check();
            $costType   = Config::get('constant.cost_type');
            $keyword    = Request::get('key', '');
            $limit      = Request::get('limit', Config::get('constant.RECORD_PER_PAGE'));
            $data       = $this->members->getMembersAssigned($id)->paginate($limit);
            $project    = $this->project->findByAttribute('id', $id);
            $paginate   = Config::get('constant.paginate_number');
            $status     = Config::get('constant.status');
            $roles      = $this->roles->all()->toArray();

            //handler popup assign member
            $totalMember = Activation::select('users.id')
                        ->leftJoin('users', 'users.id', '=', 'activations.user_id')
                        ->whereColumn('users.id', 'users.related_id')
                        ->where('users.source_id', 5)
                        ->count();
            $limitMember    = 30;
            $paged          = ceil($totalMember / $limitMember) - 1;
            $users          = Activation::select('users.first_name', 'users.last_name', 'users.id', 'users.member_code')
                                        ->leftJoin('users', 'users.id', '=', 'activations.user_id')
                                        ->whereColumn('users.id', 'users.related_id')
                                        ->where('users.source_id', 5)
                                        ->take($limitMember)
                                        ->get()
                                        ->toArray();
            $assignedMem    = $this->members->checkAssignedMember($id)->get()->toArray();
            $fillter        = [];
            foreach ($assignedMem as $key => $value) {
                array_push($fillter, $value['user_id']);
            }
            foreach ($roles as $key => $item) {
                if (in_array($item['name'], self::$removeRoles)) {
                    unset($roles[$key]);
                }
            }
            $permission = $this->members->getPermissionOfAMemberInProject($user->id, $id);
            return view('project_member.members',
            [
                'data'       =>$data,
                'costType'   => $costType,
                'project_id' => $id,
                'paginate'   => $paginate,
                'status'     => $status,
                'project'    => $project,
                'roles'      => $roles,
                'users'      => $users,
                'paged'      => $paged,
                'fillter'    => $fillter,
                'user'       => $user,
                'limit'      => $limit,
                'permission' => $permission
            ]);
        } else {
            $errorsMessage = 'Sorry. You can not access this page because lack permission!';
            return Redirect::back()->with('errorsMessage', $errorsMessage);
        }
    }

    /**
     * @author SonNA
     * @todo Handler pagination with ajax
     * @param Request $request
     */
    public function paging()
    {
         $limit = 30;
         $offset = isset(Request::get('params')['pageTo']) ? (Request::get('params')['pageTo']) : '';
         $id = isset(Request::get('params')['project_id']) ? (Request::get('params')['project_id']) : '';
         if ($offset != 1) {
             if ($offset == '') {
                 $totalMember = Activation::select('users.id')
                     ->leftJoin('users', 'users.id', '=', 'activations.user_id')
                     ->whereColumn('users.id', 'users.related_id')
                     ->where('users.source_id', 5)
                     ->count();
                 $offset = ceil($totalMember / $limit);
             } else {
                 $offset = $offset;
             }
             $offset = ($offset  * $limit) - $limit;
             $users = Activation::select('users.first_name','users.last_name','users.id', 'users.member_code')
                ->leftJoin('users', 'users.id', '=', 'activations.user_id')
                ->where('users.source_id', 5)
                ->whereColumn('users.id', 'users.related_id')
                ->take($limit)
                ->skip($offset)
                ->get()
                ->toArray();
             $assignedMem = $this->members->checkAssignedMember($id)->get()->toArray();
             $fillter = [];
             foreach ($assignedMem as $key => $value) {
                 array_push($fillter, $value['user_id']);
             }
             foreach ($users as $key => $value) {
                 if (in_array($value['id'], $fillter)) {
                     $j = $users[$key];
                     $users[$key]['existed'] = true;
                 }
             }
         } else {
             $users = Activation::select('users.first_name','users.last_name','users.id', 'users.member_code')
                ->leftJoin('users', 'users.id', '=', 'activations.user_id')
                ->where('users.source_id', 5)
                ->whereColumn('users.id', 'users.related_id')
                ->take($limit)
                ->get()
                ->toArray();
             $assignedMem = $this->members->checkAssignedMember($id)->get()->toArray();
             $fillter = [];
             foreach ($assignedMem as $key => $value) {
                 array_push($fillter, $value['user_id']);
             }
             foreach ($users as $key => $value) {
                 if (in_array($value['id'], $fillter)) {
                     $j = $users[$key];
                     $users[$key]['existed'] = true;
                 }
             }
         }
         return json_encode($users);
    }

    /**
     * @author SonNA
     * @todo Handler search member with ajax
     * @param Request $request
     */
    public function searchByAjax()
    {
        $key = \Helpers::mst_trim(Request::get('term'));
        $id = Request::get('project_id');
        $users = [];
        if (!empty($key)) {
            $users = $this->users->findUserLike($key)->get()->toArray();
            $assignedMem = $this->members->checkAssignedMember($id)->get()->toArray();
            $fillter = [];
            foreach ($assignedMem as $key => $value) {
                if($value['user_id'] == $value['uid']) {
                    array_push($fillter, $value['user_id']);
                } else {
                    array_push($fillter, $value['uid']);
                }
            }
            foreach ($users as $key => $value) {
                if (in_array($value['id'], $fillter)) {
                    $j = $users[$key];
                    $users[$key]['existed'] = true;
                }
            }
        } else {
            $users = ['wrong_key' => true];
        }

        return json_encode($users);
    }

    /**
     * Handler assign member to current project function
     * @author SonNA
     * @param $myRequest
     * @param $params
     * @param $keys
     * @param $costType
     * @return resource\project_member\assign.blade.php
     */
    public function store()
    {
        $user_ids = Request::get('members');
        $project_id = Request::get('project_id');
        $roles = Request::get('roles');
        $data = [];
        if (!empty($user_ids)) {
            foreach ($user_ids as $k => $v) {
                array_push($data, [
                    'user_id' => $v,
                    'project_id' => $project_id,
                    'role_id' => $roles
                ]);
            }
            foreach ($data as $key => $value) {
                $this->members->save($value);
            }
            return redirect()->action('MemberController@index',$project_id)->with('success', 'Assign Member success');
        }
        return redirect()->action('MemberController@index',$project_id);
    }

    /**
     * Handler Edit project member info
     * @author SonNA
     * @return resource\project_member\members.blade.php
     */
    public function update()
    {
        $role           = Request::get('roles');
        $exRole           = Request::get('ex_role');
        $id             = Request::get('code');
        $page           = Request::get('page');
        $limit          = Request::get('limit', 10);
        $project_id     = Request::get('project_id');
        $projectMember  = ProjectMember::select(
                    'u2.id',
                    'u2.related_id',
                    'pm.id as project_mem_id'
                )->join('users as u1','u1.id','=','project_member.user_id')
                ->join('users as u2','u2.related_id','=','u1.related_id')
                ->join('project_member as pm','u2.id','=','pm.user_id')
                ->where('project_member.id','=',$id)
                ->where('pm.project_id','=',$project_id)
                ->where('pm.role_id','=',$exRole)
                ->get()
                ;
        foreach ($projectMember as $pm)
        {
            $projectMemberEdit = ProjectMember::find($pm->project_mem_id);
            $projectMemberEdit->role_id = $role;
            $projectMemberEdit->save();
        }

        return redirect()->route('projects.members.assign.index', [
            'project_id' => $project_id,
            'page' => $page,
            'limit' => $limit
         ])->with('success', 'Update roles success');
    }

    /**
     * Handler search member when assign function
     * @author SonNA
     * @param $request
     * @param $project_id
     * @return resource\project_member\assign.blade.php
     */
    public function searchMember(MyRequest $request, $project_id)
    {
        return view('project_member.assign' , ['project_id' => $project_id]);
    }

    /**
     * Handler Delete assigned member
     * @author SonNA
     * @return resource\project_member\assign.blade.php
     */
    public function delete($project_id)
    {
        $id            = Request::get('id');
        $page          = Request::get('page');
        $limit         = Request::get('limit', 10);
        $actionData    = Request::get('action');
        $project_id    = Request::get('project_id');
        $exRole        = Request::get('ex_role');

        $projectMember  = ProjectMember::select(
                        'u2.id',
                        'u2.related_id',
                        'pm.id as project_mem_id'
                        )->join('users as u1','u1.id','=','project_member.user_id')
                        ->join('users as u2','u2.related_id','=','u1.related_id')
                        ->join('project_member as pm','u2.id','=','pm.user_id')
                        ->where('project_member.id','=',$id)
                        ->where('pm.project_id','=',$project_id)
                        ->where('pm.role_id','=',$exRole)
                        ->get()
                        ;
        if ($projectMember != null) {
            if ($actionData == 2) {
                foreach ($projectMember as $pm)
                {
                    $this->members->delete($pm->project_mem_id);
                }

            } else {
                foreach ($projectMember as $pm)
                {
                    $this->members->restoreOrRemove($pm->project_mem_id, $actionData);
                }
            }

            $last_record = Request::get('last_record');

            if ($last_record == 1 && $page != 1) {
                $page = $page - 1;
            }
            if ($actionData == 0) {
                $successMessage = 'This member has been removed!';
            } elseif ($actionData == 1) {
                $successMessage = 'This member has been restore!';
            } else {
                $successMessage = 'This member has been delete!';
            }

            return redirect()->route('projects.members.assign.index',[
                'project_id' => $project_id,
                'page' => $page,
                'limit' => $limit,
            ])->with('DeleteAssignedUserSuccess', $successMessage);
        } else {
            if ($actionData == 0) {
                $errorMessage = "Having error when remove this member!";
            } elseif ($actionData == 1) {
                $errorMessage = "Having error when restore this member!";
            } else {
                $errorMessage = "Having error when delete this member!";
            }
            return redirect()->route('projects.members.assign.index',[
                'project_id' => $project_id,
                'page' => $page,
                'limit' => $limit,
            ])->with('errorsMessage', $errorMessage);
        }
    }

    /**
     * Handler autocomplete search email
     * @author SonNA
     * @return response json data of email list
     */
    public function emailAutocomplete(MyRequest $request)
    {
        $data = [];
        $result = [];
        $queries  = $this->members->emailAutocomplete($request['query']);
        foreach ($queries as $query):
            $data[] = [
                'value' => $query->email . ' - ' . $query->first_name . '
                ' . $query->last_name , 'data' => "$query->id" ,
                'email' => $query->email ];
        endforeach;
        $result = [ 'query' => 'Unit','suggestions' => $data ];
        return Response::json($result);
    }
}
