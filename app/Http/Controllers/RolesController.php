<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use \Illuminate\Support\Facades\Config;
use App\Http\Requests\CreateRoleRequest;
use App\Models\Roles;
use App\Models\User;
use App\Repositories\RoleUsers\RoleUsersRepositoryInterface;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use GuzzleHttp\json_encode;

class RolesController extends Controller
{
    static $staticRoleNames = ['Admin','Member','Dev','DevL','QA','QAL','BSE/VN',
                            'BSE/JP','Comtor','JP Supporter','Sub BSE','PM',
                            'General Director',
                            'Department Manager',
                            'Division Manager',
                            'Team Leader'];
    public function __construct(RoleUsersRepositoryInterface $roleUsers){
        $this->roles = Sentinel::getRoleRepository()->setModel('App\Models\Roles');
        $this->users = Sentinel::getUserRepository();
        $this->RoleUsers = $roleUsers;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = $this->roles->all()->toArray();
        $log_info = [];
        foreach ($roles as $key => $value){
            $created_user = $this->users->findById($value['created_by']);
            $updated_user = $this->users->findById($value['updated_by']);
            array_push($log_info,[
                $created_user['last_name'].' '.$created_user['first_name'],
                $updated_user['last_name'].' '.$updated_user['first_name']
             ]);
        }
        return view('roles.index',['roles' => $roles,
                                   'log' => $log_info,
                                   'staticRoleNames' => self::$staticRoleNames]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $structerGroup = Config::get('constant.STRUCTURE_PERMISSION');
        $costGroup = Config::get('constant.COST_PERMISSION');
        $defectGroup = Config::get('constant.DEFECT_PERMISSION');
        $pqGroup = Config::get('constant.PQ_PERMISSION');
        $adminGroup = Config::get('constant.ADMINISTRATION_PERMISSION');
        $fileGroup = Config::get('constant.FILE_MANAGEMENT');
        return view('roles.create',['structerGroup' => $structerGroup,
                                    'costGroup' => $costGroup,
                                    'defectGroup' => $defectGroup,
                                    'pqGroup' => $pqGroup,
                                    'adminGroup' => $adminGroup,
                                    'fileGroup' => $fileGroup
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRoleRequest $request)
    {
        $name = \Helpers::mst_trim($request->roleName);
        $slug = \Helpers::mst_trim($request->slug,"_");
        $checkExist = $this->roles->findByName($name);
        if(!empty($name)){
            if(empty($checkExist)){
                $permissions = [];
                if(!empty($request->permission)){
                    foreach ($request->permission as $key => $value){
                        $permissions['user.'.$value] = true;
                    }
                }
                $this->roles->createModel()->create([
                                'name' => $name,
                                'slug' => $slug.rand(0,1000),
                                'permissions' => $permissions
                ]);

                return redirect()->action('RolesController@index')->with('success', 'Add new roles success');
            }else{
                $uniqueMsg = 'This role already exists.';
                return redirect()->back()->with('uniqueMsg', $uniqueMsg);
            }
        }else{
            $requedMsg = 'The role name field is required.';
            return redirect()->back()->with('requedMsg', $requedMsg);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Sentinel::findRoleById($id);
        $structerGroup = Config::get('constant.STRUCTURE_PERMISSION');
        $costGroup = Config::get('constant.COST_PERMISSION');
        $defectGroup = Config::get('constant.DEFECT_PERMISSION');
        $pqGroup = Config::get('constant.PQ_PERMISSION');
        $adminGroup = Config::get('constant.ADMINISTRATION_PERMISSION');
        $fileGroup = Config::get('constant.FILE_MANAGEMENT');
        if(!empty($role)){
            $permission = $role->permissions;
            return view('roles.update',['role' => $role,
                                        'role_id' => $id,
                                        'permission' => $permission,
                                        'structerGroup' => $structerGroup,
                                        'costGroup' => $costGroup,
                                        'defectGroup' => $defectGroup,
                                        'pqGroup' => $pqGroup,
                                        'adminGroup' => $adminGroup,
                                        'fileGroup' => $fileGroup,
                                        'staticRoleNames' => self::$staticRoleNames
            ]);
        }else{
            return redirect()->action('RolesController@index')->with('errorsMessage', 'This ID not found in system');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateRoleRequest $request)
    {
        $role = Sentinel::findRoleById($request->role_id);
        $roleName = \Helpers::mst_trim($request->roleName);
        $roleSlug = \Helpers::mst_trim($request->slug,"_");
        $checkExist = $this->roles->findByName($roleName);
        $permissions = [];
        if(!empty($request->permission)){
            foreach ($request->permission as $key => $value){
                $permissions['user.'.$value] = true;
            }
        }
        if(!empty($roleName)){
            if(!empty($role)){
                if($role->name != $roleName && !empty($checkExist)){
                    $uniqueMsg = 'This role already exists.';
                    return redirect()->back()->with('uniqueMsg', $uniqueMsg);
                }else{
                     $role->name = $roleName;
                     $role->slug = $roleSlug.rand(0,1000);
                     $role->permissions = $permissions;
                     $role->save();
                     return redirect()->action('RolesController@index')->with('success', 'Update roles success');
                }
            }else{
                return redirect()->action('RolesController@index')->with('errorsMessage', 'This ID not found in system');
            }
        }else{
            $requedMsg = 'The role name field is required.';
            return redirect()->back()->with('requedMsg', $requedMsg);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @author SonNA
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
      $roleId = !empty($request->id) ? $request->id : '';
      $deleteAll = !empty($request->ids) ? $request->ids : '';
      $checkId = !empty($request->_check_id) ? $request->_check_id : '';
      $roles_id = !empty($request->_roles_id) ? $request->_roles_id : '';

      if(!empty($checkId)){
          $role = $this->roles->findById($checkId);
          $users = $role->getUsers()->toArray();
          return count($users);
      }
      if(!empty($roleId)){
          $role = $this->roles->findById($roleId);
          if(!empty($role)){
             $role->delete();
             return redirect()->back();
          }else{
              return redirect()->action('RolesController@index')->with('errorsMessage', 'This ID not found in system');
          }
      }
      if(!empty($deleteAll)){
          $ids = explode(",", $deleteAll);
          foreach ($ids as $k => $v){
              $role = $this->roles->findById($v);
              $role->delete();
          }
          return redirect()->back();
      }
      if(!empty($roles_id)){
          $roles = $this->roles->all()->toArray();
          $roleUsers = $this->RoleUsers->all()->toArray();
          $roleNames = [];
          $totalUsers = [];
          $roleCombine = [];

          foreach ($roleUsers as $key => $value){
              if(in_array($value['role_id'],$roles_id)){
                  array_push($totalUsers, $value['role_id']);
              }
          }
          $totalUsers = array_values(array_count_values($totalUsers));

          foreach($roles as $key => $value){
            if(in_array($value['id'],$roles_id)){
                array_push($roleNames,$value['name']);
            }
          }

          foreach ($totalUsers as $k => $v){
            array_push($roleCombine, ['names' => $roleNames[$k], 'users' => $v]);
          }

          return json_encode($roleCombine);
      }
    }
}
