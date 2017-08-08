<?php
namespace App\Repositories\Project;

use App\Models\Department;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use App\Repositories\Api\ApiRepositoryInterface;
use App\Repositories\ProjectMember\ProjectMemberRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use DB;
use GuzzleHttp\json_decode;
use GuzzleHttp\json_encode;
use Helpers;

class ProjectRepository implements ProjectRepositoryInterface
{

    public function __construct(ApiRepositoryInterface $api,
            UserRepositoryInterface $user,
            ProjectMemberRepositoryInterface $projectMember)
    {
        $this->api = $api;
        $this->user = $user;
        $this->projectMember = $projectMember;
    }

    /**
     * @todo Filter data project
     *
     * @author chaunm8181
     * @param int $role_id
     * @param $projectMemberJoin
     * @param int $type_id
     * @param int $project_name
     * @param int $status
     * @param int $bse
     * @param int $department_id
     * @param int $division_id
     * @param int $team_id
     * @param int $language_id
     * @param int $limit
     * @see \App\Repositories\Project\ProjectRepositoryInterface::search()
     */
    public function searchInProjectList($role_id, $projectMemberJoin, $type_id, $project_name, $status,
            $bse, $department_id, $division_id, $team_id, $language_id, $limit)
    {
        if ($role_id == 1 || $role_id == 13) {
            $query     = $this->getProjectMemberForProjectSearch('', 0, '');
            if (($team_id != null) && ($team_id != -1)) {
                $result = $query->where('projects.department_id', $team_id);
            }
            if (($division_id != null) && ($division_id != -1)) {
                $listTeam = Department::where('parent_id', $division_id)->pluck('id')->toArray();
                $query->where(function ($result) use ($listTeam, $division_id) {
                    $result->whereIn('projects.department_id', $listTeam)
                           ->orWhere('projects.department_id', $division_id);
                });
            }
            if (($department_id != null) && ($department_id != -1)) {
                $listDivision = Department::where('parent_id', $department_id)->pluck('id')->toArray();
                $listTeam     = Department::whereIn('parent_id', $listDivision)->pluck('id')->toArray();
                $query->where(function ($result) use ($listTeam, $listDivision, $department_id) {
                    $result->whereIn('projects.department_id', $listTeam)
                           ->orWhereIn('projects.department_id', $listDivision)
                           ->orWhere('projects.department_id', $department_id);
                });
            }
            if(!empty($project_name)){
                $query->where('projects.name', 'LIKE', "%$project_name%");
            }
            if(!empty($type_id)){
                $query->where('projects.type_id', '=', $type_id);
            }
            if($status != 0 && $status != ''){
                $query->where('projects.status', '=', $status);
            }
            if($status == '')
            {
                $query->where('projects.status', '=', 2);
            }
            if (($bse != null)){
                $query->where('projects.brse', '=', $bse);
            }
            if(!empty($language_id)){
                $query->where('language_id', '=', $language_id);
            }
            return $query->orderBy('created_at','DESC')->paginate($limit);
        } else {
            $userId           = Sentinel::getUser()->id;
            $managerIds       = $this->user->getManagerId();
            if (in_array($userId, $managerIds)) {
                $teamId       = $this->getDepartmentWhichManagerManage($userId);
                $query        = $this->getProjectMemberForProjectSearch($userId, 1, $teamId);
            } else {
                $query        = $this->getProjectMemberForProjectSearch($userId,3,'');
            }
            if (($team_id != null) && ($team_id != -1)) {
                $query ->where('projects.department_id', $team_id);
            }
            if (($division_id != null) && ($division_id != -1)) {
                $listTeam = Department::where('parent_id', $division_id)->pluck('id')->toArray();
                $query->where(function ($result) use ($listTeam, $division_id) {
                    $result->whereIn('projects.department_id', $listTeam)
                           ->orWhere('projects.department_id', $division_id);
                });
            }
            if (($department_id != null) && ($department_id != -1)) {
                $listDivision = Department::where('parent_id', $department_id)->pluck('id')->toArray();
                $listTeam     = Department::whereIn('parent_id', $listDivision)->pluck('id')->toArray();
                $query->where(function ($result) use ($listTeam, $listDivision, $department_id) {
                    $result->whereIn('projects.department_id', $listTeam)
                           ->orWhereIn('projects.department_id', $listDivision)
                           ->orWhere('projects.department_id', $department_id);
                });
            }
            if(!empty($project_name)){
                $query->where('projects.name', 'LIKE', "%$project_name%");
            }
            if(!empty($type_id)){
                $query->where('projects.type_id', '=', $type_id);
            }
            if($status != 0 && $status != ''){
                $query->where('projects.status', '=', $status);
            }
            if($status == '')
            {
                $query->where('projects.status', '=', 2);
            }
            if (($bse != null) && ($bse != -1)){
                $query->where('projects.brse', '=', $bse);
            }
            if(!empty($language_id)){
                $query->where('language_id', '=', $language_id);
            }
            return $query->orderBy('created_at','DESC')->paginate($limit);
        }
    }

    /**
     * all data
     * {@inheritDoc}
     * @see \App\Repositories\Project\ProjectRepositoryInterface::all()
     */
    public function all(){
        return Project::all();
    }

    public function get(){
        return $this->model->get();
    }

    public function paginate($quantity){
        return Project::orderBy('created_at','DESC')->paginate($quantity);
    }

    public function find($id){
        return Project::find($id);
    }

    /**
     * sava new data
     * {@inheritDoc}
     * @see \App\Repositories\Project\ProjectRepositoryInterface::save()
     */
    public function save($data){
        $project = Project::all();
        $data['plant_start_date'] = \Helpers::formatDateYmd($data['plant_start_date']);
        $data['plant_end_date'] = \Helpers::formatDateYmd($data['plant_end_date']);
        $current_user = Sentinel::getUser();
        $data['active'] = '1';
        $project = Project::create($data);
        $project_id = $project->id;
        $user_brse = $project->brse;
        $project_member = $this->projectMember->saveProjectMember($project_id, $user_brse, 7);
        return true;
    }

    /**
     * Save a project was got from backlog/redmine/gdo
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Project\ProjectRepositoryInterface::saveProjectFromCrawler()
     */
    public function saveProjectFromCrawler($data){
        $project = new Project();
        if (!empty($data['project_id']) && !empty($data['name'])) {
            $project->project_id = $data['project_id'];
            $project->name = $data['name'];
            $project->project_key = $data['project_key'];
            if (!empty($data['description'])) {
                $project->description = $data['description'];
            }
            else {
                $project->description = "";
            }
            if (!empty($data['department_id'])) {
                $project->department_id = $data['department_id'];
            } else {
                $project->department_id = 0;
            }
            $project->source_id = $data['source_id'];
            $project->save();
            return true;
        }
        return false;
    }

    public function delete($id){
        Project::find($id)->delete();
    }

    public function update($data, $id){
        $project = Project::find($id);
        $project_id = $project->id;
        $department = $project->department_id;
        if ($project->brse != $data['brse'])
        {
            $result = $project->brse;
            $this->updateOldBrse($result,$project_id);
            $user_brse = $data['brse'];
            $project_member = $this->saveUpdateProjectMember($user_brse, $project_id);
        }
        $project->plant_start_date  = \Helpers::formatDateYmd($data['plant_start_date']);
        $project->plant_end_date    = \Helpers::formatDateYmd($data['plant_end_date']);
        $project->actual_start_date = \Helpers::formatDateYmd($data['actual_start_date']);
        $project->actual_end_date   = \Helpers::formatDateYmd($data['actual_end_date']);
        $project->name              = $data['name'];
        $project->department_id     = $data['department_id'];
        $project->brse              = $data['brse'];
        $project->status            = $data['status'];
        $project->language_id       = $data['language_id'];
        $project->process_apply     = $data['process_apply'];
        $project->description       = $data['description'];
        $project->type_id           = $data['type_id'];
        $project->detail_design     = $data['detail_design'];
        $project->test_first        = $data['test_first'];
        $project->unit_test         = $data['unit_test'];
        $project->scenario          = $data['scenario'];
        $project->save();
        return true;
    }

    /**
     * Update a project got from backlog/redmine
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Project\ProjectRepositoryInterface::updateProjectFromCrawler()
     */
    public function updateProjectFromCrawler($data, $id){
        $project = Project::find($id);
        if (isset($data['project_id'])) {
            $project->project_id = $data['project_id'];
        }
        if (isset($data['project_key'])) {
            $project->project_key = $data['project_key'];
        }
        if (isset($data['name'])) {
            $project->name = $data['name'];
        }
        if (isset($data['flag'])) {
            $project->crawler_flag = $data['flag'];
        }
        if (isset($data['sync_flag'])) {
            $project->sync_flag = $data['sync_flag'];
        }
        if (isset($data['active'])) {
            $project->active = $data['active'];
        }
        $project->save();
        return true;
    }

    /**
     * Get projects with one condition
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Project\ProjectRepositoryInterface::findByAttribute()
     */
    public function findByAttribute($attr, $data){
        return Project::where($attr,$data)->first();
    }

    /**
     * @todo get data group form api form potal
     * @author ChauNM
     * @return json data
     */
    public function apiDepartment()
    {
        try {
            $json_data = Department::all();
            $removeKeys = [3,4,28];
            if($json_data != null){
                foreach ($json_data as $key => $department) {
                    foreach ($removeKeys as $k => $value){
                        if($department['id'] == $value ||
                           $department['parent_id'] == $value){
                            $key == $k;
                            unset($json_data[$key]);
                        }
                    }
                }
                return $json_data;
            }
        }catch (\Exceprion $e){
            print_r('data notfound');
        }
    }

    /**
     * @author thangdv8182
     * Get team devision department
     * @param array $data
     */
    public function getDepDevTeam($listDepartmentId)
    {
        $departments = [];
        $divisions   = [];
        $teams       = [];
        if($listDepartmentId != null){
            foreach ($listDepartmentId as $key => $id) {
                if($id > 0){
                    $groupCompany = Department::find($id);
                    if ($groupCompany->parent_id == 0) {
                        if(!in_array($groupCompany->toArray(), $departments)){
                            $departments[] = $groupCompany->toArray();
                        }
                    } else {
                        $groupDepartment = Department::find($groupCompany->parent_id);
                        if ($groupDepartment->parent_id == 0) {
                            if(!in_array($groupDepartment->toArray(), $departments)){
                                $departments[] = $groupDepartment->toArray();
                            }
                            if(!in_array($groupCompany->toArray(), $divisions)){
                                $divisions[]   = $groupCompany->toArray();
                            }
                        } else {
                            $groupDivision   = Department::find($groupCompany->parent_id);
                            $groupDepartment = Department::find($groupDivision->parent_id);
                            if(!in_array($groupDepartment->toArray(), $departments)){
                                $departments[]   = $groupDepartment->toArray();
                            }
                            if(!in_array($groupDivision->toArray(), $divisions)){
                                $divisions[]     = $groupDivision->toArray();
                            }
                            if(!in_array($groupCompany->toArray(), $teams)){
                                $teams[]         = $groupCompany->toArray();
                            }
                        }
                    }
                }
            }
        }
        return [
            'departments'=> $departments,
            'teams'      => $teams,
            'divisions'  => $divisions
        ];
    }


    /**
     * @todo fillter data when select department
     * @author ChauNM
     * @return filltered data
     */
    public function filterData($id, $fillterData)
    {
        $result = [];
        $teams = [];
        $project = [];
        foreach ($fillterData as $value){
            if( $value['parent_id'] == $id ) {
                array_push($result, $value);
            }
        }
        foreach ($result as $v){
            if($v['parent_id'] == $id) {
                array_push($teams, $v);
            }
        }
        return json_encode($result);
    }

    /**
     * @todo Save Data From Crawler
     * @author ChauNM
     * @return return boolean
     */
    public function saveDataCrawler($data, $id)
    {
        $project = Project::find($id);
        $project->sync_flag = "1";
        $project->update($data);

        return true;
    }

    /**
     * @todo Get projects with two conditions
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Project\ProjectRepositoryInterface::findByAttributes()
     */
    public function findByAttributes($att1, $name1, $att2, $name2){

        return Project::where($att1, $name1)
            ->where($att2, $name2)->first();
    }

   /**
    * @todo Get projects with flag not equal 0
    *
    * @author tampt6722    *
    * {@inheritDoc}
    * @see \App\Repositories\Project\ProjectRepositoryInterface::getProjectsByAttribute()
    */
    public function getProjectsByAttribute($attr, $data, $flag){
        return Project::where($attr, $data)
                         ->where('crawler_flag', $flag)
                         ->where('sync_flag','=', 1)
                         ->get();
    }

    /**
     * @todo Get projects to update at first time
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Project\ProjectRepositoryInterface::getProjectsToUpdateFirstly()
     */
    public function getProjectsToUpdateFirstly($attr, $data){
        return Project::where($attr, $data)
                        ->where('crawler_flag','=', 0)
                        ->where('sync_flag','=', 1)
                        ->get();
    }

    /**
     * get name department
     * @param int $id
     * @return \App\Repositories\Project\json
     */
    public function findDepartment($id)
    {
        $departments = $this->apiDepartment();
        foreach ($departments as $department)
        {
            if($department['id'] == $id)
                return $department;
        }
    }

    /**
     * @author chaunm8181
     * Get plan effort project
     * {@inheritDoc}
     * @see \App\Repositories\Project\ProjectRepositoryInterface::getDataPlanEffort()
     */
    public function getDataPlanEffort($project_id){
        return $query = Project::select(
                'projects.id as id_project',
                'tickets.id as ticket_id',
                'tickets.estimate_time')
                ->join('tickets','tickets.project_id','=','projects.id')
                ->where('tickets.deleted_at', null)
                ->where('projects.id','=',$project_id);
    }

    /**
     * @author chaunm8181
     * Get actual effort project
     * {@inheritDoc}
     * @see \App\Repositories\Project\ProjectRepositoryInterface::getDataPlanEffort()
     */
    public function getDataActualEffort($project_id){
        return $query = Project::select(
                    'projects.id as id_project',
                    'entries.id as entry_id',
                    'entries.actual_hour')
                    ->join('entries','entries.project_id', '=' ,'projects.id')
                    ->where('entries.deleted_at', null)
                    ->where('projects.id','=',$project_id);
    }

    /**
     * Get actual hour of a project
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Project\ProjectRepositoryInterface::getActualHour()
     */
    public function getActualHour($projectIds, $startDate, $endDate)
    {
        $query = Project::select('projects.id as project_id',
                DB::raw('sum(entries.actual_hour) as actual_hour'))
                ->join('entries',  function($join) use($startDate, $endDate) {
                    $join->on('entries.project_id', '=', 'projects.id')
                    ->where('entries.spent_at', '>=', $startDate)
                    ->where('entries.spent_at', '<=', $endDate);
                })
                ->where('entries.deleted_at', null);
        if (is_array($projectIds)) {
           $result = $query->whereIn('projects.id', $projectIds)
                                        ->groupBy('projects.id')->get();
        } else {
            $result = $query->where('projects.id', $projectIds)->first();
        }

        return $result;
    }

    /**
     * Get line of code of a project
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Project\ProjectRepositoryInterface::getLocOfAProject()
     */
    public function getLocOfAProject($projectIds, $startDate, $endDate)
    {
        $query =  Project::select('projects.id as project_id', DB::raw('sum(locs.loc)  as loc'))
            ->join('tickets', function($join) use($startDate, $endDate) {
                $join->on( 'projects.id', '=', 'tickets.project_id')
                ->where('tickets.start_date', '>=', $startDate)
                ->where('tickets.start_date', '<=', $endDate);
            })
            ->join('locs','tickets.id', '=', 'locs.ticket_id')
            ->where('locs.deleted_at', null)
            ->where('tickets.deleted_at', null);
        if (is_array($projectIds)) {
           $result = $query->whereIn('projects.id', $projectIds)->groupBy('projects.id')->get();
        } else {
            $result = $query->where('projects.id', $projectIds)->first();
        }
        return $result;
    }

    /**
     * Count tickets which have ticket type is bug or uat bug with weight bug of a project
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Project\ProjectRepositoryInterface::getTicketsWithBugWeight()
     */
    public function getTicketsWithBugWeight($projectIds, $typeRelatedId, $weightRelatedId, $startDate, $endDate) {
        $query = Project::select('projects.id as project_id',
                DB::raw('count(tickets.id)  as countId'))
                ->join('tickets', function($join) use($startDate, $endDate) {
                    $join->on( 'tickets.project_id', '=', 'projects.id')
                    ->where('tickets.integrated_created_at', '>=', $startDate)
                    ->where('tickets.integrated_created_at', '<=', $endDate);
                })
                ->join('ticket_type', function($join) use($typeRelatedId) {
                    $join->on('tickets.ticket_type_id', '=', 'ticket_type.id')
                    ->where('ticket_type.related_id','=', $typeRelatedId);
                })
                ->join('bugs_weight', function($join) use($weightRelatedId) {
                    $join->on('tickets.bug_weight_id', '=', 'bugs_weight.id')
                    ->where('bugs_weight.related_id','=',$weightRelatedId);
                })
                ->join('status', function($join) {
                    $join->on('status.id', '=', 'tickets.status_id')
                    ->where('status.related_id','<>', 6); // 6: rejected
                })
                ->where('tickets.deleted_at', null);
        if (is_array($projectIds)) {
           $result = $query->whereIn('projects.id', $projectIds)
                                        ->groupBy('projects.id')->get();
        } else {
            $result = $query->where('projects.id', $projectIds)->first();
        }

        return $result;
    }

    /**
     * Count tickets with a ticket type of a project
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Project\ProjectRepositoryInterface::countTicketsWithTicketType()
     */
    public function countTicketsWithTicketType($projectIds, $typeRelatedId, $startDate, $endDate) {
        $query = Project::select('projects.id as project_id',
                DB::raw('count(tickets.id)  as countId'))
                ->join('tickets', function($join) use($startDate, $endDate) {
                    $join->on( 'tickets.project_id', '=', 'projects.id')
                    ->where('tickets.integrated_created_at', '>=', $startDate)
                    ->where('tickets.integrated_created_at', '<=', $endDate);
                })
                ->join('ticket_type', function($join) use($typeRelatedId) {
                    $join->on('tickets.ticket_type_id', '=', 'ticket_type.id')
                    ->where('ticket_type.related_id','=',$typeRelatedId);
                })
                ->where('tickets.deleted_at', null);

        if (is_array($projectIds)) {
            $result = $query->whereIn('projects.id', $projectIds)
            ->groupBy('projects.id')->get();
        } else {
            $result = $query->where('projects.id', $projectIds)->first();
        }

        return $result;
    }

    /**
     * Get all project joined by member
     * @author thanhnb6719
     * @param int $userId
     * @param int $adminIds
     * @see \App\Repositories\Project\ProjectRepositoryInterface::getProjectMemberJoin()
     */
    public function getProjectMemberJoin($userId, $checkGroup){
        if ($checkGroup == 1) {
            $query = Project::select('projects.id','projects.name','projects.brse','projects.department_id')
                                ->leftJoin('departments','departments.id','=','projects.department_id')
                                ->where('projects.department_id','<>',0)
                                ->where('projects.active', 1)
                                ->get();
        } elseif ($checkGroup == 2) {
            // get departments which manage by manager
            $teamId = $this->getDepartmentWhichManagerManage($userId);
            // get project which manage by manager
            $query = Project::select('projects.id','projects.name','projects.brse','projects.department_id')
                                ->join('departments','departments.id','=','projects.department_id')
                                ->join('users','users.member_code','=','departments.manager_id')
                                ->whereIn('projects.department_id', $teamId)
                                ->where('projects.department_id','<>',0)
                                ->where('projects.active', 1)
                                ->get();
        } else {
            $query = Project::select('projects.id','projects.name','projects.brse','projects.department_id','roles.permissions')
                                ->join('project_member','projects.id','=','project_member.project_id')
                                ->join('users','users.id','=','project_member.user_id')
                                ->join('departments','departments.id','=','projects.department_id')
                                ->join('roles','roles.id','=','project_member.role_id')
                                ->where('projects.department_id','<>',0)
                                ->where('users.id', $userId)
                                ->where('projects.active', 1)
                                ->where('project_member.status', 1)
                                ->get();
        }
        return $query;
    }

    /**
     * @todo Get group project which member join
     *
     * @author thanhnb6719
     * @param string $permissionNeedCheck
     * @return array data
     */
    public function getGroupProjectMemberJoin($permissionNeedCheck){
        $projects          = [];
        $projectMemberJoin = [];
        $listDepartmentId  = [];
        $departments       = [];
        $divisions         = [];
        $teams             = [];
        $bse               = [];
        $adminIds          = $this->user->getAdminOrDirectorId();
        $managerIds        = $this->user->getManagerId();
        // Get data to fill select box
        if (Sentinel::check()) {
            $userId           = Sentinel::getUser()->id;
            if (in_array($userId, $adminIds)) {
                $projectsJoin     = $this->getProjectMemberJoin($userId, 1);
                if (!empty($projectsJoin)) {
                    foreach($projectsJoin as $pJ){
                        $projects[]          = $pJ;
                        $projectMemberJoin[] = $pJ->id;
                        $listDepartmentId[]  = $pJ->department_id;
                        $bse[]               = $pJ->brse;
                    }
                    $companyArray     = $this->getDepDevTeam($listDepartmentId);
                    $departments      = $companyArray['departments'];
                    $divisions        = $companyArray['divisions'];
                    $teams            = $companyArray['teams'];
                }
            } elseif (in_array($userId, $managerIds)) {
                $projectsJoin     = $this->getProjectMemberJoin($userId, 2);
                if (!empty($projectsJoin)) {
                    foreach($projectsJoin as $pJ){
                        $projects[]          = $pJ;
                        $projectMemberJoin[] = $pJ->id;
                        $listDepartmentId[]  = $pJ->department_id;
                        $bse[]               = $pJ->brse;
                    }
                    $companyArray     = $this->getDepDevTeam($listDepartmentId);
                    $departments      = $companyArray['departments'];
                    $divisions        = $companyArray['divisions'];
                    $teams            = $companyArray['teams'];
                }
            } else {
                if ($permissionNeedCheck == null) {
                    $projectsJoin     = $this->getProjectMemberJoin($userId, 3)->toArray();
                } else {
                    $projectsJoin     = $this->getProjectMemberJoin($userId, 3);
                }
                if (!empty($projectsJoin)) {
                    foreach($projectsJoin as $pJ){
                        if ($permissionNeedCheck == null) {
                            $projects[]          = $pJ;
                            $projectMemberJoin[] = $pJ['id'];
                            $listDepartmentId[]  = $pJ['department_id'];
                            $bse[]               = $pJ['brse'];
                        } elseif ($pJ->permissions != null) {
                            $projectPermission = json_decode($pJ->permissions);
                            if (array_key_exists($permissionNeedCheck, $projectPermission)) {
                                $projects[]          = $pJ;
                                $projectMemberJoin[] = $pJ->id;
                                $listDepartmentId[]  = $pJ->department_id;
                            }
                        }
                    }
                    $companyArray     = $this->getDepDevTeam($listDepartmentId);
                    $departments      = $companyArray['departments'];
                    $divisions        = $companyArray['divisions'];
                    $teams            = $companyArray['teams'];
                }
            }
        }
        usort($divisions, function ($item1, $item2) {
            return $item1['name'] > $item2['name'];
        });
        usort($teams, function ($item1, $item2) {
            return $item1['name'] > $item2['name'];
        });
        usort($departments, function ($item1, $item2) {
            return $item1['name'] > $item2['name'];
        });
        return [
            'projectJoin' => $projectMemberJoin,
            'projects'    => $projects,
            'departments' => $departments,
            'teams'       => $teams,
            'divisions'   => $divisions,
            'brses'       => $bse
        ];
    }

    /**
     * @todo Get the plan effort of projects with date time conditions
     *
     * @author tampt6722
     * @param int $projectIds
     * @param date $startDate
     * @param date $endDate
     * @see \App\Repositories\Project\ProjectRepositoryInterface::getPlanEffortWithDate()
     */
    public function getPlanEffortWithDate($projectIds, $startDate, $endDate)
    {
        $query =  Project::select('projects.id as project_id',
                DB::raw('sum(tickets.estimate_time) as estimate_time'))
        ->join('tickets', function($join) use($startDate, $endDate) {
            $join->on( 'tickets.project_id', '=', 'projects.id')
                ->where('tickets.start_date', '>=', $startDate)
                ->where('tickets.start_date', '<=', $endDate);
        })
        ->where('tickets.deleted_at', null);
        
        if (is_array($projectIds)) {
            $result = $query->whereIn('projects.id', $projectIds)->groupBy('projects.id')->get();
        } else {
            $result = $query->where('projects.id', $projectIds)->first();
        }
        return $result;
    }

    /**
     * Get actual cost of projects with a ticket type
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Project\ProjectRepositoryInterface::getActualHourWithTracker()
     */
    public function getActualHourWithTracker($projectIds, $startDate, $endDate, $typeRelatedId)
    {
        $query =  Project::select('projects.id as project_id',
                DB::raw('sum(entries.actual_hour) as actual_hour'))
                ->join('tickets','projects.id', '=', 'tickets.project_id')
                ->join('entries',  function($join) use($startDate, $endDate) {
                    $join->on('entries.ticket_id', '=', 'tickets.id')
                    ->where('entries.spent_at', '>=', $startDate)
                    ->where('entries.spent_at', '<=', $endDate);
                })
                ->join('ticket_type', function($join) use($typeRelatedId) {
                    $join->on('tickets.ticket_type_id', '=', 'ticket_type.id')
                    ->where('ticket_type.related_id','=',$typeRelatedId);
                })
                ->where('tickets.deleted_at', null)
                ->where('entries.deleted_at', null);
        if (is_array($projectIds)) {
            $result = $query->whereIn('projects.id', $projectIds)->groupBy('projects.id')->get();
        } else {
            $result = $query->where('projects.id', $projectIds)->first();
        }

        return $result;
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Project\ProjectRepositoryInterface::getActualHourWithActivity()
     */
    public function getActualHourWithActivity($projectIds, $startDate, $endDate, $activityRelatedId)
    {
        $query =  Project::select('projects.id as project_id',
                DB::raw('sum(entries.actual_hour) as actual_hour'))
                ->join('tickets','projects.id', '=', 'tickets.project_id')
                ->join('entries',  function($join) use($startDate, $endDate) {
                    $join->on('entries.ticket_id', '=', 'tickets.id')
                         ->where('entries.spent_at', '>=', $startDate)
                         ->where('entries.spent_at', '<=', $endDate);
                })
                ->join('activities', function($join) use($activityRelatedId) {
                        $join->on('activities.id', '=', 'entries.activity_id')
                             ->where('activities.related_id','=', $activityRelatedId);
                })
                ->where('tickets.deleted_at', null)
                ->where('entries.deleted_at', null);;
        if (is_array($projectIds)) {
            $result = $query->whereIn('projects.id', $projectIds)->groupBy('projects.id')->get();
        } else {
            $result = $query->where('projects.id', $projectIds)->first();
        }
        return $result;
    }

    /**
     * @todo Get actual
     * {@inheritDoc}
     * @see \App\Repositories\Project\ProjectRepositoryInterface::getActualHourWithActivityWithoutTracker()
     */
    public function getActualHourWithActivityWithoutTracker($projectIds, $startDate, $endDate, $activityRelatedId, $withOutId1, $withOutId2)
    {
        $query =  Project::select('projects.id as project_id',
                DB::raw('sum(entries.actual_hour) as actual_hour'))
                ->join('tickets','projects.id', '=', 'tickets.project_id')
                ->join('entries',  function($join) use($startDate, $endDate) {
                    $join->on('entries.ticket_id', '=', 'tickets.id')
                         ->where('entries.spent_at', '>=', $startDate)
                         ->where('entries.spent_at', '<=', $endDate);
                })
                ->join('activities', function($join) use($activityRelatedId) {
                    $join->on('activities.id', '=', 'entries.activity_id')
                         ->where('activities.related_id','=', $activityRelatedId);
                })
                ->join('ticket_type', function($join) use($withOutId1, $withOutId2) {
                    $join->on('tickets.ticket_type_id', '=', 'ticket_type.id')
                         ->where('ticket_type.related_id','!=', $withOutId1)
                         ->where('ticket_type.related_id','!=', $withOutId2);
                })
                ->where('tickets.deleted_at', null)
                ->where('entries.deleted_at', null);;
                if (is_array($projectIds)) {
                    $result = $query->whereIn('projects.id', $projectIds)->groupBy('projects.id')->get();
                } else {
                    $result = $query->where('projects.id', $projectIds)->first();
                }
                return $result;
    }

    /**
     * @todo User permission view list project
     *
     * @author thanhnb6719
     * @param int $userId
     * @param int $check
     * @param int $teamId
     * @return $projectJoin
     */
    private function getProjectMemberForProjectSearch($userId, $check, $teamId){
        $query = Project::join('departments','departments.id','=','projects.department_id');

        if ($check == 0) {
            $projectJoin = $query->select('projects.id',
                                          'projects.name',
                                          'projects.department_id',
                                          'projects.project_id',
                                          'projects.project_key',
                                          'projects.source_id',
                                          'projects.brse',
                                          'projects.plant_start_date',
                                          'projects.plant_end_date',
                                          'projects.status',
                                          'projects.actual_start_date',
                                          'projects.actual_end_date',
                                          'projects.type_id',
                                          'projects.language_id',
                                          'projects.active',
                                          'projects.sync_flag',
                                          'projects.crawler_flag',
                                          'projects.plant_total_effort',
                                          'projects.plant_total_effort',
                                          'projects.actual_effort',
                                          'projects.description',
                                          'projects.created_at',
                                          'projects.updated_at');
        }elseif ($check == 1) {
            $projectJoin = $query->whereIn('projects.department_id',$teamId)
                                ->select('projects.id',
                                        'projects.name',
                                        'projects.department_id',
                                        'projects.project_id',
                                        'projects.project_key',
                                        'projects.source_id',
                                        'projects.brse',
                                        'projects.plant_start_date',
                                        'projects.plant_end_date',
                                        'projects.status',
                                        'projects.actual_start_date',
                                        'projects.actual_end_date',
                                        'projects.type_id',
                                        'projects.language_id',
                                        'projects.active',
                                        'projects.sync_flag',
                                        'projects.crawler_flag',
                                        'projects.plant_total_effort',
                                        'projects.plant_total_effort',
                                        'projects.actual_effort',
                                        'projects.description',
                                        'projects.created_at',
                                        'projects.updated_at');
        } else {
            $projectJoin = $query->join('project_member','projects.id','=','project_member.project_id')
                                 ->join('users','users.id','=','project_member.user_id')
                                 ->join('roles','roles.id','=','project_member.role_id')
                                 ->where('users.id',$userId)
                                 ->select('projects.id',
                                         'projects.name',
                                         'projects.department_id',
                                         'projects.project_id',
                                         'projects.project_key',
                                         'projects.source_id',
                                         'projects.brse',
                                         'projects.plant_start_date',
                                         'projects.plant_end_date',
                                         'projects.status',
                                         'projects.actual_start_date',
                                         'projects.actual_end_date',
                                         'projects.type_id',
                                         'projects.language_id',
                                         'projects.active',
                                         'projects.sync_flag',
                                         'projects.crawler_flag',
                                         'projects.plant_total_effort',
                                         'projects.plant_total_effort',
                                         'projects.actual_effort',
                                         'projects.description',
                                         'projects.created_at',
                                         'projects.updated_at',
                                         'roles.permissions');
        }
        return $projectJoin;
    }

    public function getDatasByAttribute($params,$attribute,$iterite){
        $query = Project::select($params)
                 ->where($attribute,'=',$iterite);
        return $query;
    }

    /**
     * Get number of test case with activity
     *
     * @author thanhnb6719
     * @param int $projectIds
     * @param date $startDate
     * @param date $endDate
     * @param int $activityRelatedId
     * @return unknown
     */
    public function getTestcaseWithActivity($projectIds, $startDate, $endDate, $activityRelatedId)
    {
        $query =  Project::select('tickets.test_case')
                ->join('tickets', function($join) use($startDate, $endDate) {
                    $join->on( 'tickets.project_id', '=', 'projects.id')
                    ->where('tickets.start_date', '>=', $startDate)
                    ->where('tickets.start_date', '<=', $endDate);
                })
                ->join('entries', function($join) {
                    $join->on('projects.id', '=', 'entries.project_id')
                    ->on('entries.ticket_id','=','tickets.id');
                })
                ->join('activities', function($join) use($activityRelatedId) {
                    $join->on('activities.id', '=', 'entries.activity_id')
                    ->where('activities.related_id','=', $activityRelatedId);
                })
                ->where('projects.id', $projectIds)
                ->where('projects.active', 1)
                ->where('tickets.test_case', '>', 0)
                ->whereNull('tickets.deleted_at')
                ->groupBy('tickets.id')
                ->pluck('tickets.test_case');

                $count = 0;
                if (count($query) > 0) {
                    foreach ($query as $data) {
                        $count += $data;
                    }
                }

        return $count;
    }

    public function getTestcaseWithActivity1($projectIds, $startDate, $endDate, $activityRelatedId)
    {
       $query =  Project::select('projects.id as project_id',
                DB::raw('sum(tickets.test_case)  as test_case'))
        ->join('tickets', function($join) use($startDate, $endDate) {
            $join->on( 'tickets.project_id', '=', 'projects.id')
                 ->where('tickets.start_date', '>=', $startDate)
                 ->where('tickets.start_date', '<=', $endDate);
        })
        ->join('entries', function($join) {
            $join->on('projects.id', '=', 'entries.project_id')
                 ->on('entries.ticket_id','=','tickets.id');
        })
        ->join('activities', function($join) use($activityRelatedId) {
            $join->on('activities.id', '=', 'entries.activity_id')
                 ->where('activities.related_id','=', $activityRelatedId);
        })
        ->where('tickets.deleted_at', null);
        if (is_array($projectIds)) {
            $result = $query->whereIn('projects.id', $projectIds)->groupBy('projects.id')->get();
        } else {
            $result = $query->where('projects.id', $projectIds)->first();
        }
        return $result;
    }


    public function updateOldBrse($data, $project_id)
    {
        if(!empty($data))
        {
            $project_member = ProjectMember::where('user_id',$data)->where('project_id',$project_id)->first();
            if ($project_member != null)
            {
                $member          = ProjectMember::find($project_member->id);
                $member->role_id = 2;
                $member->save();
            }
        }
        return true;
    }

    public function saveUpdateProjectMember($data, $id)
    {
        $member  = ProjectMember::where('project_id',$id)->get();
        $user_id = [];
        foreach ($member as $t) {
            $user_id[] = $t['user_id'];
        }
        if(empty($user_id))
        {
            $project_member              = new ProjectMember();
            $project_member->project_id  = $id;
            $project_member->user_id     = $data;
            $project_member->role_id     = 7;
            $project_member->save();

        }else {
                if (!in_array($data, $user_id))
                {
                    $project_member              = new ProjectMember();
                    $project_member->project_id  = $id;
                    $project_member->user_id     = $data;
                    $project_member->role_id     = 7;
                    $project_member->save();
                }else {
                    $project_member_all          = ProjectMember::where('user_id',$data)->where('project_id',$id)->first();
                    $project_member_id           = $project_member_all->id;
                    $project_member              = ProjectMember::find($project_member_id);
                    $project_member->role_id     = 7;
                    $project_member->save();
                }
        }
        return true;

    }

    public function getDepartmentWhichManagerManage($managerId) {
        $teamId = [];
        $departments = Department::join('users','departments.manager_id','=','users.member_code')
                                ->select('departments.id','departments.parent_id')
                                ->where('users.id', $managerId)
                                ->get();
        foreach ($departments as $department) {
            if ($department->parent_id == 0) {
                $listDivisionId = Department::where('parent_id', $department->id)->pluck('id')->toArray();
                $listTeamId     = Department::whereIn('parent_id', $listDivisionId)->pluck('id')->toArray();
                $teamId         = array_merge($listTeamId, $teamId);
            } else {
                $getDepartment  = Department::where('id',$department->parent_id)->first();
                if ($getDepartment->parent_id == 0) {
                    $listTeamId   = Department::where('parent_id', $department->id)->pluck('id')->toArray();
                    $teamId       = array_merge($listTeamId, $teamId);
                } else {
                    $listTeamId   = [$department->id];
                    $teamId       = array_merge($listTeamId, $teamId);
                }
            }
        }
        $teamManageProject = array_unique($teamId);
        return $teamManageProject;
    }

    /**
     * @todo Get Project in search form
     *
     * @author thanhnb6719
     * @param int $projectIdSearch
     * @param int $getDepartment
     * @param int $getDivision
     * @param int $getTeam
     * @param array $projects
     * @return project
     * @see \App\Repositories\Project\ProjectRepositoryInterface::getProjectInSearch()
     */
    public function getProjectInSearch($projectIdSearch, $getDepartment, $getDivision, $getTeam, $getStatus, $projectMemberJoin)
    {
        if ($getStatus == 0) {
            if (($projectIdSearch == null) || ($projectIdSearch == -1)) {
                if (($getTeam != null) && ($getTeam != -1)) {
                    $result = Project::whereIn('id',$projectMemberJoin)
                                     ->where('active',1)
                                     ->where('department_id', $getTeam);
                } elseif (($getDivision != null) && ($getDivision != -1)) {
                    $listTeam = Department::where('parent_id', $getDivision)->pluck('id')->toArray();
                    $result = Project::whereIn('id',$projectMemberJoin)
                    ->where('active',1)
                    ->where(function ($query) use ($listTeam, $getDivision) {
                        $query->whereIn('department_id', $listTeam)
                              ->orWhere('department_id', $getDivision);
                    });
                } elseif (($getDepartment != null) && ($getDepartment != -1)) {
                    $listDivision = Department::where('parent_id', $getDepartment)->pluck('id')->toArray();
                    $listTeam     = Department::whereIn('parent_id', $listDivision)->pluck('id')->toArray();
                    $result = Project::whereIn('id', $projectMemberJoin)
                    ->where('active',1)
                    ->where(function ($query) use ($listTeam, $listDivision, $getDepartment) {
                        $query->whereIn('department_id', $listTeam)
                        ->orWhereIn('department_id', $listDivision)
                        ->orWhere('department_id', $getDepartment);
                    });
                } else {
                    $result = Project::whereIn('id',$projectMemberJoin)
                    ->where('active',1);
                }
            } else {
                $result = Project::where('id', $projectIdSearch)
                ->where('active',1)
                ->whereIn('id', $projectMemberJoin);
            }
            return $result->select('id','name');
        } else {
            if (($projectIdSearch == null) || ($projectIdSearch == -1)) {
                if (($getTeam != null) && ($getTeam != -1)) {
                    $result = Project::whereIn('id',$projectMemberJoin)
                    ->where('department_id', $getTeam)
                    ->where('active',1)
                    ->where('status', $getStatus);
                } elseif (($getDivision != null) && ($getDivision != -1)) {
                    $listTeam = Department::where('parent_id', $getDivision)->pluck('id')->toArray();
                    $result = Project::whereIn('id',$projectMemberJoin)
                    ->where('status', $getStatus)
                    ->where('active',1)
                    ->where(function ($query) use ($listTeam, $getDivision) {
                        $query->whereIn('department_id', $listTeam)
                        ->orWhere('department_id', $getDivision);
                    });
                } elseif (($getDepartment != null) && ($getDepartment != -1)) {
                    $listDivision = Department::where('parent_id', $getDepartment)->pluck('id')->toArray();
                    $listTeam     = Department::whereIn('parent_id', $listDivision)->pluck('id')->toArray();
                    $result = Project::whereIn('id', $projectMemberJoin)
                    ->where('status', $getStatus)
                    ->where('active',1)
                    ->where(function ($query) use ($listTeam, $listDivision, $getDepartment) {
                        $query->whereIn('department_id', $listTeam)
                        ->orWhereIn('department_id', $listDivision)
                        ->orWhere('department_id', $getDepartment);
                    });
                } else {
                    $result = Project::whereIn('id',$projectMemberJoin)
                    ->where('active',1)
                    ->where('status', $getStatus);
                }
            } else {
                $result = Project::where('id', $projectIdSearch)
                ->where('status', $getStatus)
                ->where('active',1)
                ->whereIn('id', $projectMemberJoin);
            }
            return $result->select('id','name');
        }
    }

    /**
     * @todo Get real time in select form
     *
     * @author thanhnb6719
     * @param int $time
     * @param string $defaultTime
     * @param date $requestStartDate
     * @param date $requestEndDate
     * @return array date
     * @see \App\Repositories\Project\ProjectRepositoryInterface::getTimeSearch()
     */
    public function getTimeSearch($time, $defaultTime, $requestStartDate, $requestEndDate){
        if ($time == 1) {
            switch ($defaultTime)
            {
                case 'this_month':
                    $startDate = date('Y-m-d 00:00:00', strtotime('first day of this month'));
                    $endDate   = date('Y-m-d 23:59:59', strtotime('last day of this month'));
                    break;
                case 'last_month':
                    $startDate = date('Y-m-d 00:00:00', strtotime('first day of last month'));
                    $endDate   = date('Y-m-d 23:59:59', strtotime('last day of last month'));
                    break;
                case 'today':
                    $startDate = date('Y-m-d 00:00:00');
                    $endDate   = date('Y-m-d 23:59:59');
                    break;
                case 'yesterday':
                    $startDate = date('Y-m-d 00:00:00', time() - 86400);
                    $endDate   = date('Y-m-d 23:59:59', time() - 86400);
                    break;
                case 'this_week':
                    $startDate = date('Y-m-d 00:00:00', strtotime('monday this week'));
                    $endDate   = date('Y-m-d 23:59:59', strtotime('sunday this week'));
                    break;
                case 'last_week':
                    $startDate = date('Y-m-d 00:00:00', strtotime('monday last week'));
                    $endDate   = date('Y-m-d 23:59:59', strtotime('sunday last week'));
                    break;
                case 'this_year':
                    $startDate = date('Y-01-01 00:00:00');
                    $endDate   = date('Y-12-31 23:59:59');
                    break;
                case 'last_three_month':
                    $startmonth = date('Y-m-d 00:00:00',strtotime('first day of this month'));
                    $startDate = date('Y-m-d 00:00:00', strtotime('-3 month',strtotime($startmonth)));
                    $endDate   = date('Y-m-d 23:59:59', strtotime('last day of last month'));
                    break;
                case 'last_six_month':
                    $startmonth = date('Y-m-d 00:00:00',strtotime('first day of this month'));
                    $startDate = date('Y-m-d 00:00:00', strtotime('-6 month',strtotime($startmonth)));
                    $endDate   = date('Y-m-d 23:59:59', strtotime('last day of last month'));
                    break;
                case 'last_year':
                    $year = date('Y') - 1;
                    $start = "January 1st, {$year}";
                    $end = "December 31st, {$year}";
                    $startDate = date('Y-m-d 00:00:00', strtotime($start));
                    $endDate   = date('Y-m-d 00:00:00', strtotime($end));
                    break;
            }
        } elseif ($time == 2) {
            if (($requestStartDate == "")&&($requestEndDate == "")) {
                $startDate   = date('1970-01-01 00:00:00');
                $endDate     = date('9999-01-01 23:59:59');
            } elseif (($requestStartDate == "")&&(!$requestEndDate == "")) {
                $startDate   = date('1970-01-01 00:00:00');
                $eDate       = str_replace('/', '-', $requestEndDate);
                $endDate     = date('Y-m-d 23:59:59', strtotime($eDate));
            } elseif ((!$requestStartDate == "")&&($requestEndDate == "")) {
                $sDate       = str_replace('/', '-', $requestStartDate);
                $startDate   = date('Y-m-d 00:00:00', strtotime($sDate));
                $endDate     = date('Y-m-d 23:59:59');
            } else {
                $sDate       = str_replace('/', '-', $requestStartDate);
                $startDate   = date('Y-m-d 00:00:00', strtotime($sDate));
                $eDate       = str_replace('/', '-', $requestEndDate);
                $endDate     = date('Y-m-d 23:59:59', strtotime($eDate));
            }
        } else {
            $startDate  = date('Y-m-d 00:00:00', strtotime('first day of this month'));
            $endDate    = date('Y-m-d 23:59:59', strtotime('today'));
        }
        return ['start' => $startDate, 'end' => $endDate];
    }

    public function saveDeparamentSearch($getDepartment,$getDivision,$getTeam,$divisions,$teams,$projects)
    {
            if($getDepartment != -1)
            {
                foreach ($divisions as $key=>$division)
                {
                    if($division['parent_id'] != $getDepartment)
                    {
                        unset($divisions[$key]);
                    }
                }
            }
            if($getDivision == -1)
            {
                $teamsResult = [];
                foreach ($teams as $key=>$team)
                {
                    foreach($divisions as $division)
                    {
                        if($team['parent_id'] == $division['id'])
                        {
                            array_push($teamsResult, $team);
                            break;
                        }
                    }
                }
            }else{
                $teamsResult = [];
                foreach ($teams as $key=>$team)
                {
                    if($team['parent_id'] != $getDivision)
                    {
                        unset($teams[$key]);
                    }
                }
                $teamsResult = $teams;
            }

            if($getTeam == -1)
            {
                $projectsResult = [];
                foreach ($projects as $key=>$project)
                {
                    foreach($teamsResult as $team)
                    {
                        if($project['department_id'] == $team['id'])
                        {
                            array_push($projectsResult, $project);
                            break;
                        }
                    }
                }
            }else{
                $projectsResult = [];
                foreach ($projects as $key=>$project)
                {
                    if($project['department_id'] != $getTeam)
                    {
                        unset($projects[$key]);
                    }
                }
                $projectsResult = $projects;
            }

        usort($projectsResult, function ($item1, $item2) {
            return $item1['name'] > $item2['name'];
        });
        usort($teamsResult, function ($item1, $item2) {
            return $item1['name'] > $item2['name'];
        });
        usort($divisions, function ($item1, $item2) {
            return $item1['name'] > $item2['name'];
        });

        return [
            'projects'    => $projectsResult,
            'teams'       => $teamsResult,
            'divisions'   => $divisions,
        ];
    }


    /**
     *
     * @author tampt6722
     *
     * @param string $startDate
     * @param string $endDate
     * @param integer $activityRelatedId
     * @return array
     */
    public function getTestcasesForPQ($startDate, $endDate, $activityRelatedId) {
        $query =  Project::select('projects.id as project_id', 'tickets.id as ticket_id',
                                    'tickets.test_case')
                ->join('tickets', function($join) use($startDate, $endDate) {
                    $join->on( 'tickets.project_id', '=', 'projects.id')
                    ->where('tickets.start_date', '>=', $startDate)
                    ->where('tickets.start_date', '<=', $endDate);
                })
                ->join('entries', function($join) {
                    $join->on('projects.id', '=', 'entries.project_id')
                    ->on('entries.ticket_id','=','tickets.id');
                })
                ->join('activities', function($join) use($activityRelatedId) {
                    $join->on('activities.id', '=', 'entries.activity_id')
                    ->where('activities.related_id','=', $activityRelatedId);
                })
                ->where('projects.active', 1)
                ->where('tickets.test_case', '>', 0)
                ->whereNull('tickets.deleted_at')
                ->groupBy('projects.id', 'tickets.id')
                ->get();

                return $query;
    }

    /**
     *
     * @author tampt6722
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getLocsForPQ ($startDate, $endDate){
        $query =  Project::select('projects.id as project_id',
                    DB::raw('sum(locs.loc)  as loc'))
        ->join('tickets', function($join) use($startDate, $endDate) {
            $join->on( 'projects.id', '=', 'tickets.project_id')
            ->where('tickets.start_date', '>=', $startDate)
            ->where('tickets.start_date', '<=', $endDate);
        })
        ->join('locs','tickets.id', '=', 'locs.ticket_id')
        ->where('projects.active', 1)
        ->whereNull('locs.deleted_at')
        ->whereNull('tickets.deleted_at')
        ->groupBy('projects.id')->get();

        return $query;
    }

    /**
     *
     * @author tampt6722
     *
     * @param integer $typeRelatedId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getBugsForPQ($typeRelatedId, $startDate, $endDate) {
        $query = Project::select('projects.id as project_id',
                'bugs_weight.related_id as bug_weight_id',
                DB::raw('count(tickets.id)  as countId'))
                ->join('tickets', function($join) use($startDate, $endDate) {
                    $join->on( 'tickets.project_id', '=', 'projects.id')
                    ->where('tickets.integrated_created_at', '>=', $startDate)
                    ->where('tickets.integrated_created_at', '<=', $endDate);
                })
                 ->join('status', function($join) {
                    $join->on('status.id', '=', 'tickets.status_id')
                            ->where('status.related_id','<>', 6);
                })
                ->join('ticket_type', function($join) use($typeRelatedId) {
                    $join->on('tickets.ticket_type_id', '=', 'ticket_type.id')
                    ->where('ticket_type.related_id','=', $typeRelatedId);
                })
                ->join('bugs_weight','tickets.bug_weight_id', '=', 'bugs_weight.id')
                ->where('projects.active', 1)
                ->whereNull('tickets.deleted_at')
                ->groupBy('projects.id', 'bugs_weight.id')
                ->get();


        return $query;
    }

    /**
     *
     * @author tampt6722
     *
     * @return array
     */
    public function getActiveProjects()
    {
        $query = Project::select('projects.id', 'projects.name',
                'projects.department_id', 'projects.status',
                'departments.name as department_name')
                ->join('departments', 'projects.department_id', '=', 'departments.id')
                ->where('projects.active', 1)
                ->get();
        return $query;
    }

    /**
     *
     * @author tampt6722
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function countTasksOfProject($startDate, $endDate)
    {
        $query =  Project::select('projects.id as project_id',
                    DB::raw('count(tickets.id)  as countId'))
        ->join('tickets', function($join) use($startDate, $endDate) {
            $join->on( 'projects.id', '=', 'tickets.project_id')
            ->where('tickets.start_date', '>=', $startDate)
            ->where('tickets.start_date', '<=', $endDate);
        })
        ->where('projects.active', 1)
        ->whereNull('tickets.deleted_at')
        ->groupBy('projects.id')->get();

        return $query;
    }

    /**
     *
     * @author nhatnh6565
     *
     * @param integer $project_id
     * @param integer $userId
     * @return array
     */
    public function getProjectRole($project_id, $userId)
    {
        $query = Project::select('projects.*', 'roles.permissions')
                ->join('project_member', 'projects.id', '=', 'project_member.project_id')
                ->join('roles', 'project_member.role_id', '=', 'roles.id')
                ->where('projects.id', $project_id)
                ->where('project_member.user_id', $userId)
                ->get();
        return $query;
    }
}