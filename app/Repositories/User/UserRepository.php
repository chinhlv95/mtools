<?php
namespace App\Repositories\User;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Repositories\Api\ApiRepositoryInterface;
use Config;
use Illuminate\Support\Facades\DB;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Roles;
use App\Models\Project;

class UserRepository implements UserRepositoryInterface
{

    public function __construct(ApiRepositoryInterface $api)
    {
        $this->api = $api;
    }
    public function all(){
        return User::all();
    }

    public function paginate($quantity){
        return User::paginate($quantity);
    }

    public function find($id){
        return User::find($id);
    }

    public function save($data){
        $user = new User();
        $user->email = $data['name'];
        $user->first_name = $data['first_name'];
        $user->password = $data['password'];
        $user->permissions = serialize($data['permissions']);
        $user->save();
        return true;
    }

    public function delete($id){
        return User::find($id)->delete();
    }

    public function update($data, $id){
        $user = User::find($id);
        if(isset($data['position_value'])) {
            $user->position = $data['position_value'];
        }
        if(isset($data['member_code'])) {
            $user->member_code = $data['member_code'];
        }

        if (isset($data['group_team'])) {
            $user->department_id = $data['group_team'];
        }
        if (isset($data['related_id'])) {
            $user->related_id = $data['related_id'];
        }

        if (isset($data['source_id'])) {
            $user->source_id = $data['source_id'];
        }
        $user->save();
        return $user->id;
    }
    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\User\UserRepositoryInterface::findByAttribute()
     */
    public function findByAttribute($att, $name){
        return User::where($att, $name)->first();
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\User\UserRepositoryInterface::saveUserByUsername()
     */
    public function saveUserByUsername($username, $email, $firstName, $lastName, $sourceId = 0){
        $checkUser = $this->findByAttributes('user_name', $username, 'source_id', $sourceId);
        if(count($checkUser) == 0) {
            // Create user with member-role
            $user_data = [
                            'email'    => $email,
                            'user_name' => $username,
                            'password' => '12345678',
                            'permissions' => [
                                            'member' => true,
                            ],
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'source_id' => $sourceId,
                            'position' => 'Member',
            ];
            $user = Sentinel::register($user_data);
            $role = Sentinel::findRoleById(2);
            $role->users()->attach($user);
            $userId = $user->id;
            $dataUpdate['related_id'] = $userId;
            $this->update($dataUpdate, $userId);
            return $userId;
        } else {
            return $checkUser->id;
        }
    }

    /**
    * @author tampt6722
    *
    * {@inheritDoc}
    * @see \App\Repositories\User\UserRepositoryInterface::findByAttributes()
    */
    public function findByAttributes($att1, $name1, $att2, $name2){
        return User::where($att1, $name1)
        ->where($att2, $name2)->first();
    }

    public function apiMember()
    {
        try{
            $timeout = 30;
            $urlActive = env('PORTAL_URL')."member_position.php";
            $activeUser = $this->api->getApi($urlActive, $timeout);
            $urlInactive = env('PORTAL_URL')."member_position.php?status=1";
            $inActiveUser = $this->api->getApi($urlInactive, $timeout);
            return array_merge($activeUser, $inActiveUser);
        } catch (Exception $e) {
            $this->error('Error!');
            print_r( $e->getResponse());
        }
    }
    /**
     * @author thangdv8182, tampt
     * @param unknown $data
     */
    public function saveUserPortal($data)
    {
        if (!empty($data['user_name'])) {
            $user_data = [];
            $roles = Config::get('constant.role');
            $position = $data['position_value'];
            $roleUser = "Member";
            try{
                foreach ($roles as $key => $value)
                {
                    if(strpos($position, $key) !== false)
                    {
                        $roleUser = $value;
                    }
                }
                // Source id of the portal is 5
                $checkUser = $this->findByAttributes('user_name', $data['user_name'], 'source_id', 5);
                $user_data = [
                        'first_name' => $data['name'],
                        'last_name' => '',
                        'email'    => $data['email'],
                        'user_name' => $data['user_name'],
                        'password' => '12345678',
                        'source_id' => '5',
                        'permissions' => [
                                        $roleUser => true,
                        ],
                        'position' => $data['position_value'],
                        'member_code' => $data['member_code'],
                        'department_id' => $data['group_team'],

                ];
                if((count($checkUser) == 0) && ($data['position_value'] != 'BO')) {
                    if ($data['status'] == 1) {
                        $user = Sentinel::register($user_data);
                    } else {
                        $user = Sentinel::registerAndActivate($user_data);
                    }
                    $role = Sentinel::findRoleByName($roleUser);
                    $role->users()->attach($user);
                    $userId = $user->id;
                    $dataUpdate['related_id'] = $userId;
                    $this->update($dataUpdate, $userId);
                } elseif ((count($checkUser) > 0) && ($data['position_value'] != 'BO')) {
                    $userUpdate = $this->find($checkUser->id);
                    Sentinel::update($userUpdate, $user_data);
                }
            } catch (Exception $e) {
                $this->error('Error!');
                print_r( $e->getResponse());
            }
        }
    }

    public function getBseInUserTable(){
        $query = DB::table('users')->where('permissions', 'LIKE', '%"BSE":true%')
                                   ->orwhere('permissions', 'LIKE', '%"PM":true%')
                                   ->orderBy('first_name')
                                   ->get();
        return $query;
    }

    /**
     * @todo get User By key Search
     * @param string $key
     * @return Users array
     */
    public function findUserLike($key = '')
    {
     $query = Activation::select('users.first_name','users.last_name','users.id', 'users.member_code')
        ->leftJoin('users', 'users.id', '=', 'activations.user_id')
         ->where(DB::raw('CONCAT(users.first_name, " ", users.last_name)'),'LIKE',"%$key%")
         ->whereColumn('users.id', 'users.related_id')
         ->where('users.source_id', 5)
         ->orderBy('users.created_at','DESC');
        return $query;
    }

    public function getUserManagement($roleSearch,$name,$limit,$status,$page = 1,$request, $type)
    {
        if(!empty($roleSearch))
        {
            $role  = Sentinel::findRoleById($roleSearch);
            $users = $role->users()
                ->with('roles')
                ->where(function ($query) use($name){
                      $query->where(DB::raw('CONCAT(last_name, " ", first_name)'),'LIKE',"%$name%")
                      ->orWhere('email', 'LIKE',"%$name%")
                      ->orWhere('member_code', 'LIKE',"%$name%");
                  });
        }
        else {
            $users = User::where(function ($query) use($name){
                $query->where(DB::raw('CONCAT(last_name, " ", first_name)'),'LIKE',"%$name%")
                ->orWhere('email', 'LIKE',"%$name%")
                ->orWhere('member_code', 'LIKE',"%$name%");
            });
        }
        if (!empty($type)) {
            if($type == 1) {
                $users = $users->whereColumn('id', 'related_id')
                     ->whereIn('id', function($query) {
                            $query->select('related_id')
                                ->from('users')
                                ->whereColumn('id', '<>', 'related_id');
                });
            } elseif ($type == 2) {
                $users = $users->whereColumn('id', 'related_id')
                    ->whereNotIn('id', function($query) {
                        $query->select('related_id')
                        ->from('users')
                        ->whereColumn('id', '<>', 'related_id');
                });
            }
        }
        $users = $users->whereColumn('id', 'related_id')->get();
        if ($status == 1) {
            foreach ($users as $key=>$user) {
                if (!($activation = Activation::completed($user))) {
                    $users->forget($key);
                }
            }
        }
        if ($status == 2) {
            foreach ($users as $key=>$user) {
                if ($activation = Activation::completed($user)) {
                    $users->forget($key);
                }
            }
        }
        $count = count($users);
        $perPage = $limit; // Number of items per page
        $offset = ($page * $perPage) - $perPage;
        $result = new LengthAwarePaginator(
                $users->forPage($page, $perPage),
                count($users), // Total items
                $perPage, // Items per page
                $page, // Current page
                ['path' => $request->url(), 'query' => $request->query()] // We need this so we can keep all old query parameters from the url
                );
        return ['users'=>$result,'count'=>$count];
    }

    /**
     * @todo Get Admin or Directory Id
     *
     * @author tampt6722
     * @see \App\Repositories\User\UserRepositoryInterface::getAdminOrDirectorId()
     */
    public function getAdminOrDirectorId()
    {
        $ids          = [];
        $roleAdmin    = Sentinel::findRoleById(1);
        $admins       = $roleAdmin->users()->with('roles')->get();
        if (count($admins) > 0) {
            foreach ($admins as $admin) {
                $ids [] = $admin->id;
            }
        }
        $roleDirector = Sentinel::findRoleById(13);
        $directors    = $roleDirector->users()->with('roles')->get();
        if (count($directors) > 0) {
            foreach ($directors as $director) {
                $ids [] = $director->id;
            }
        }
        return $ids;
    }

    /**
     * get all manager in company
     *
     * @author thanhnb6719
     * @return array Id
     * @see \App\Repositories\User\UserRepositoryInterface::getManagerId()
     */
    public function getManagerId()
    {
        return DB::table('departments')
                    ->join('users','departments.manager_id','=','users.member_code')
                    ->groupBy('users.related_id')
                    ->pluck('users.related_id');
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\User\UserRepositoryInterface::getUserMapping()
     */
    public function getUserMapping($sourceId, $name)
    {
        $result = User::select('id', 'member_code', 'email', 'source_id', 'related_id',
                DB::raw('CONCAT(last_name, " ", first_name) as full_name'))
                ->where('source_id', '=', $sourceId)
                ->where(function ($query) use($name){
                    $query->where(DB::raw('CONCAT(last_name, " ", first_name)'),'LIKE',"%$name%")
                    ->orWhere('email', 'LIKE',"%$name%")
                    ->orWhere('member_code', 'LIKE',"%$name%");
                });
        return $result;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\User\UserRepositoryInterface::getAllEmails()
     */
    public function getAllEmails()
    {
        $result = User::select('email')
            ->pluck('email');

        return $result;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\User\UserRepositoryInterface::getFullName()
     */
    public function getFullName()
    {
        $result = User::select(DB::raw('CONCAT(last_name," ",first_name) as full_name'))
                ->where('source_id', '=', 5)
                ->pluck('full_name');

        return $result;
    }

    public function getSubUsers($userId) {
        return User::Where('related_id', $userId)
                    ->where('id', '!=', $userId)->get();
    }

    public function getUsersNotSub($userId) {
        $users = User::query();
        $users->select('users.user_name', 'users.email', 'users.first_name', 'users.related_id', 'users.id', 'users.source_id');
        $users->where('users.id', '<>', $userId);
        $users->whereNotIn('users.id', function($query) {
            $query->select('related_id')
            ->from('users')
            ->whereColumn('id', '<>', 'related_id');
        });
        return $users->get()->toArray();
    }

    public function getListUserOfTeam($teamId) {
        return Project::where('projects.department_id', $teamId)
                ->join('project_member', 'project_member.project_id', '=', 'projects.id')
                ->join('users', 'project_member.user_id', '=', 'users.id');
//              ->whereColumn('users.id', 'users.related_id');
    }
}
