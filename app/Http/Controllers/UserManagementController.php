<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Config;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\EditUserRequest;
use App\Models\CrawlerType;

class UserManagementController extends Controller
{
    public function __construct(UserRepositoryInterface $user)
    {
        $this->user = $user;
    }
    public function index(Request $request)
    {
        $status_user = Config::get('constant.status_user');
        $paginate_number = Config::get('constant.paginate_number');
        $limit     = $request->get('limit',Config::get('constant.RECORD_PER_PAGE'));
        $stt       = ( $request->get('page','1') - 1 ) * $limit;
        $roleSearch = $request->get('role_id','');
        $name = $request->get('name','');
        $status = $request->get('status','');
        $page = $request->get('page',1);
        $type = $request->get('type');
        $result = $this->user->getUserManagement($roleSearch,\Helpers::mst_trim($name),$limit,$status,$page,$request, $type);
        $users = $result['users'];
        $count = $result['count'];
        $sources = Config::get('constant.stream_types');
        foreach ($users as $key => $user) {
            $mainUser = Sentinel::findById($user['related_id']);
            $users[$key]['main_member_code'] = $mainUser['member_code'];
            if (!empty($mainUser)) {
                $subUsers = $this->user->getSubUsers($mainUser->id);
                $subSources = [];
                if (!empty($subUsers)) {
                    foreach ($subUsers as $subUser) {
                        $subSources[] = $subUser->user_name.' - '.$sources[$subUser->source_id];
                    }
                }
                $users[$key]['subSources'] = $subSources;
            }
        }
        $roles = Roles::all();
        $types = [
            [
                'id' => 1,
                'name' => 'Has'
            ],
            [
                'id' => 2,
                'name' => 'Hasn\'t'
            ]
        ];

        return view('user_management.index',[
            'status_user' => $status_user,
            'paginate_number' => $paginate_number,
            'users' => $users,
            'stt' => $stt,
            'roles' => $roles,
            'count' => $count,
            'types' => $types,
            'sources' => $sources
        ]);
    }
    public function edit(\Illuminate\Http\Request $request,$id)
    {
        $user = Sentinel::findById($id);
        foreach ($user->roles as $role) {
            $role->users()->detach($user);
        }
        if ($user->inRole(Sentinel::findRoleById(1)->slug)) {
            $role = Sentinel::findRoleById(2);
            $role->users()->attach($user);
        } else {
            $role = Sentinel::findRoleById(1);
            $role->users()->attach($user);
        }

        return redirect()->back()->with('success','Edit success!');
    }
    public function lockUser($id)
    {
        $user = Sentinel::findById($id);

        if ($activation = Activation::completed($user)) {
            Activation::remove($user);
        } else {
            $activation = Activation::complete($user,Activation::create($user)->code);
        }

        return redirect()->back()->with('success','Edit success!');
    }

    /**
     * @author thuynv6723
     * @param unknown $userId
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function editUser($userId)
    {
        $listSources = Config::get('constant.stream_types');
        $users = Sentinel::findById($userId);
        $role = '';
        $subUsers = '';
        $mainUsers = '';
        if ($users->related_id > 0) {
            $mainUsers = Sentinel::findById($users->related_id);
        } else {
            $mainUsers = $users;
        }
        $subUsers = $this->user->getSubUsers($mainUsers->id);

        foreach ($mainUsers->roles as $role) {
            $role = $role['id'];
        }
        $emails = $this->user->getAllEmails()->toArray();
        $autoData = json_encode($emails);
        $userNotSub = $this->user->getUsersNotSub($mainUsers->id);
        $sources = Config::get('constant.stream_types');

        // get source by user
        if (!empty($userNotSub)) {
            foreach ($userNotSub as $key => $value) {
                $nameSource = $sources[$value['source_id']];
                $userNotSub[$key]['source'] = $nameSource;
                if($userNotSub[$key]['related_id'] != $users['id'] && $userNotSub[$key]['related_id'] != $userNotSub[$key]['id']) {
                    unset($userNotSub[$key]);
                }
            }
        }

        return view('user_management.editUser', [
            'user' => $users,
            'role' => $role,
            'mainUsers' => $mainUsers,
            'subUsers' => $subUsers->pluck('id')->toArray(),
            'autoData' => $autoData,
            'userNotSub' => $userNotSub,
            'listSources' => $listSources
        ]);
    }

    /**
     * @author thuynv6723
     * @param EditUserRequest $request
     * @param unknown $userId
     * @return unknown
     */
    public function processEditUser(EditUserRequest $request, $userId)
    {
        $sources = Config::get('constant.stream_types');
        $password = $request->get('password');
        $password_confirm = $request->get('password_confirmation');
        $first_name = $request->get('first_name');
        $last_name = $request->get('last_name');
        $main_email = $request->get('main_email');
        $admin = $request->get('administrator');
        $sub_users = $request->get('sub_user');
        $data = [
            'last_name' => $last_name,
            'first_name' => $first_name,
        ];

        if ($password != $password_confirm) {
            return Redirect::back()
                ->withErrors('The confirmed password don\'t match. Try again?')
                ->withInput();
        }
        if (ctype_space($password)) {
            return Redirect::back()
                ->withErrors('Password should include some characters: a-z,A-Z,0-9,@.')
                ->withInput();
        }
        if($password != '')
        {
            $data['password'] = $password;
        }
        $user = Sentinel::findById($userId);
        $mainUser = Sentinel::findById($user->related_id);
        $subUsers = '';
        $subUsers = $this->user->getSubUsers($mainUser->id);
        if (!empty($subUsers)) {
            foreach ($subUsers as $subUser) {
                Sentinel::update($subUser, array('related_id'=> $subUser->id));
                Activation::complete($subUser,Activation::create($subUser)->code);
            }
        }
        $data['related_id'] = $mainUser->id;
        Sentinel::update($mainUser, $data);
        // update sub user
        if (!empty($sub_users)) {
            $sub_users = array_unique($sub_users);
            $keyMain = array_search($mainUser->id, $sub_users);
            if($keyMain != null) {
                unset($sub_users[$keyMain]);
            }
            foreach ($sub_users as $sub_user) {
                if ($sub_user != null) {
                    $subUser = Sentinel::findById($sub_user);
                    $sourceSub = $sources[$subUser->source_id];
                    $getListSubUsers = $this->user->getSubUsers($subUser->id);
                    if ($subUser->id != $subUser->related_id
                            && $subUser->related_id != $mainUser->id) {
                        $mainUserOther = Sentinel::findById($subUser->related_id);
                        $mainSourceOther = $sources[$mainUserOther->source_id];

                        return Redirect::back()
                            ->withErrors($subUser->user_name.' - '.$sourceSub. ' is a child of user '.$mainUserOther->user_name.' - '.$mainSourceOther.' !')
                            ->withInput();
                    } elseif (count($getListSubUsers) > 0) {
                        return Redirect::back()
                            ->withErrors($subUser->user_name.' - '.$sourceSub. ' is a main of another user !')
                            ->withInput();
                    } else {
                        Activation::remove($subUser);
                        Sentinel::update($subUser, array('related_id' => $mainUser->id));
                    }
                }
            }
        }
        // update role user
        foreach ($mainUser->roles as $role) {
            $role->users()->detach($mainUser);
        }
        if ($admin == 1) {
            $role = Sentinel::findRoleById(1);
            $role->users()->attach($mainUser);
        } else {
            if ($role['id'] == 1) {
                $role = Sentinel::findRoleById(2);
            } else {
                $role = Sentinel::findRoleById($role['id']);
            }
            $role->users()->attach($mainUser);
        }

        return Redirect::route('user-management.index',[
            'status' => $request->get('status'),
            'role_id' => $request->get('role_id'),
            'name' => $request->get('name'),
            'limit' => $request->get('limit', 10),
            'page' => $request->get('page', 1),
            'type' => $request->get('type')
            ])->withSuccess("Update user ". $mainUser->last_name." ".$mainUser->first_name ." success!");
    }
}
