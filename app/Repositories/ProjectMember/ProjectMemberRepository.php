<?php
namespace App\Repositories\ProjectMember;

use App\Models\ProjectMember;
use DB;
use App\Models\User;
use App\Models\Project;
use App\Models\Department;

class ProjectMemberRepository implements ProjectMemberRepositoryInterface
{
    /**
     * Get data from 3 tables: project_member, users and projects and search with key
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::getMembersAssigned()
     */
    public function getMembersAssigned($project_id){
         $query = ProjectMember::select(
                'project_member.user_id',
                'project_member.project_id',
                'project_member.role_id',
                'project_member.status',
                'u2.first_name',
                'u2.last_name',
                'u2.email',
                'projects.type_id',
                'projects.status as project_status',
                'project_member.id',
                'roles.name as role')
                ->join('users as u1', 'project_member.user_id', '=', 'u1.id')
                ->join('users as u2', 'u1.related_id', '=', 'u2.id')
                ->leftjoin('roles', 'project_member.role_id', '=', 'roles.id')
                ->join('projects', function($join) use($project_id){
                    $join->on('project_member.project_id', '=', 'projects.id')
                    ->where('project_member.project_id','=', $project_id);
                })
                ->where('projects.deleted_at', null)
                ->orderBy('project_member.status','DESC')
                ->orderBy('project_member.id','DESC')
                ->groupBy('u2.related_id','project_member.role_id')
                ;

        return $query;
    }

    /**
     * @todo check assigned member
     * @author sonNA
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::find()
     */
    public function checkAssignedMember($project_id){
        $query = ProjectMember::select(
                'project_member.user_id',
                'users.related_id as uid')
                ->leftJoin('users', 'users.id', '=', 'project_member.user_id')
                ->leftjoin('roles', 'project_member.role_id', '=', 'roles.id')
                ->join('projects', function($join) use($project_id){
                    $join->on('project_member.project_id', '=', 'projects.id')
                    ->where('project_member.project_id','=', $project_id);
                })
                ->where('projects.deleted_at', null);
        return $query;
    }

    /**
     *
     * @author sonNA
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::find()
     */
    public function find($id){
        return ProjectMember::with('roles')->find($id);
    }
    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::save()
     */
    public function save($data){
        $projectMember = new ProjectMember();
        $projectMember->user_id = $data['user_id'];
        $projectMember->project_id = $data['project_id'];
        if (isset($data['assign'])) {
            $projectMember->assign = $data['assign'];
        }
        if (isset($data['role_id'])) {
            $projectMember->role_id = $data['role_id'];
        }
        $projectMember->save($data);
        return true;
    }

    public function delete($id){
        ProjectMember::find($id)->delete();
    }

    public function restoreOrRemove($id, $status){
        $projectMember         = ProjectMember::find($id);
        $projectMember->status = $status;
        $projectMember->save();
    }

    public function update($data, $id){
        $projectMember = ProjectMember::find($id);
        $projectMember->user_id = $data['user_id'];
        $projectMember->project_id = $data['project_id'];
        $projectMember->assign = $data['effort'];
        $projectMember->start_date = date('Y-m-d',strtotime(str_replace('/', '-', $data['startDate'])));
        $projectMember->end_date = date('Y-m-d',strtotime(str_replace('/', '-', $data['endDate'])));
        $projectMember->position = $data['position'];
        $projectMember->save();
    }

    public function updatePmFromCrawler($data, $id){
        $projectMember = ProjectMember::find($id);
        $projectMember->user_id = $data['user_id'];
        $projectMember->save();
    }

    /**
     * email suggest autocomplete
     * @author SonNA
     * @param $query
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::emailAutocomplete()
     */
    public function emailAutocomplete($query){
        $result = User::select('email','id','first_name','last_name')
                        ->where('email', 'LIKE', "%$query%")->get();
        return $result;
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::findByAttribute()
     */
    public function findByAttribute($att, $name){
        return ProjectMember::where($att, $name)->first();
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\User\UserRepositoryInterface::findByAttributes()
     */
    public function findByAttributes($att1, $name1,$att2, $name2){
        return ProjectMember::where($att1, $name1)
        ->where($att2, $name2)
        ->first();
    }

    /**
     * Get main user in project
     *
     * @author thanhnb6719
     * @param array $listProjectId
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::getUserInProject()
     */
    public function getUserInProject($listProjectId)
    {
        $projectMember = DB::table('project_member')
            ->join('users as u1', 'project_member.user_id', '=', 'u1.id')
            ->join('users as u2', 'u1.related_id', '=', 'u2.id')
            ->leftjoin('roles', 'project_member.role_id', '=', 'roles.id')
            ->join('projects', 'projects.id', '=', 'project_member.project_id')
            ->whereIn('projects.id', $listProjectId)
            ->where('projects.deleted_at', null)
            ->distinct()
            ->select(
                 'u2.id as user_id',
                 'u2.email',
                 'roles.name as user_position',
                 'u2.first_name',
                 'u2.last_name',
                 'projects.id as project_id',
                 'projects.name as project_name',
                 'u2.related_id as related_id',
                 'u2.user_name'
            );
        return $projectMember;
    }

    /**
     * Get email of member in project
     *
     * @author thanhnb6719
     * @param int $projectId
     * @param int $getDepartment
     * @param int $getDivision
     * @param int $getTeam
     * @param array $projectMemberJoin
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::getMemberOrder()
     */
    public function getMemberOrder($projectId, $getDepartment, $getDivision, $getTeam, $projectMemberJoin)
    {
        $query = DB::table('project_member')
            ->join('users as u1', 'project_member.user_id', '=', 'u1.id')
            ->join('users as u2', 'u1.related_id', '=', 'u2.id')
            ->leftjoin('roles', 'project_member.role_id', '=', 'roles.id')
            ->join('projects', 'projects.id', '=', 'project_member.project_id')
            ->where('projects.deleted_at', null)
            ->select(
                    'u2.email',
                    'roles.name as position',
                    'u2.id', 'u2.related_id',
                    'u2.user_name','u2.last_name',
                    'u2.first_name'
                    )
            ->where('projects.deleted_at', null)
            ->distinct();
        $result = $this->checkWhetherProjectIsNull($query, $projectId,
                $getDepartment, $getDivision, $getTeam, $projectMemberJoin);
        $result = $query->groupBy('u2.id','roles.name');
        return $result;
    }

     /**
      * get sub member in project
      *
      * @author thuynv6723
      * @param unknown $projectId
      * @param unknown $getDepartment
      * @param unknown $getDivision
      * @param unknown $getTeam
      * @param unknown $projectMemberJoin
      * @return unknown
      */
    public function getSubMemberOrder($userId, $projectId, $getDepartment, $getDivision, $getTeam, $projectMemberJoin)
    {
        $query = DB::table('project_member')
        ->join('projects', 'projects.id', '=', 'project_member.project_id')
        ->join('users', 'users.id', '=', 'project_member.user_id')
        ->join('roles', 'project_member.role_id', '=', 'roles.id')
        ->select('users.email', 'roles.name as position', 'users.id', 'users.related_id', 'users.user_name')
        ->where('projects.deleted_at', null)
        ->whereColumn('users.id', '<>', 'users.related_id')
        ->where('users.related_id', $userId)
        ->where('users.id', '<>', $userId);
        $result = $this->checkWhetherProjectIsNull($query, $projectId,
                $getDepartment, $getDivision, $getTeam, $projectMemberJoin);

        return $result;
    }

    /**
     * Get data of email in project
     *
     * @author thanhnb6719
     * @param int $projectId
     * @param int $getDepartment
     * @param int $getDivision
     * @param int $getTeam
     * @param array $projectMemberJoin
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::getMemberInPersonalCost()
     */
    public function getMemberInPersonalCost($projectId, $getDepartment, $getDivision, $getTeam, $projectMemberJoin)
    {
        $query = DB::table('project_member')
            ->join('projects', 'projects.id', '=', 'project_member.project_id')
            ->join('users', 'users.related_id', '=', 'project_member.user_id')
            ->select(
                'users.*',
                'project_member.assign as assign',
                'projects.department_id as department_id',
                'projects.name as project_name',
                'projects.id as project_id'
            )
            ->where('projects.deleted_at', null);

        return $query;
    }

   /**
    * Get data with conditions such as projectId, teamId, divisionId, departmentId
    *
    * @author tampt6722
    * {@inheritDoc}
    * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::checkWhetherProjectIsNull()
    */
    public function checkWhetherProjectIsNull($query, $projectId, $getDepartment,
                        $getDivision, $getTeam, $projectMemberJoin)
    {
        if (($projectId == null) || ($projectId == -1)) {
            if (($getTeam != null) && ($getTeam != -1)) {
                $result = $query->whereIn('projects.id', $projectMemberJoin)
                    ->where('projects.department_id', $getTeam);
            } elseif (($getDivision != null) && ($getDivision != -1)) {
                $listTeam = Department::where('parent_id', $getDivision)->pluck('id')->toArray();
                $result = $query->whereIn('projects.id', $projectMemberJoin)
                    ->where(function ($query) use ($listTeam, $getDivision) {
                          $query->whereIn('projects.department_id', $listTeam)
                              ->orWhere('projects.department_id', $getDivision);
                 });
            } elseif (($getDepartment != null) && ($getDepartment != -1)) {
                $listDivision = Department::where('parent_id', $getDepartment)->pluck('id')->toArray();
                $listTeam     = Department::whereIn('parent_id', $listDivision)->pluck('id')->toArray();
                $result = $query->whereIn('projects.id', $projectMemberJoin)
                    ->where(function ($query) use ($listTeam, $listDivision, $getDepartment) {
                          $query->whereIn('projects.department_id', $listTeam)
                              ->orWhereIn('projects.department_id', $listDivision)
                              ->orWhere('projects.department_id', $getDepartment);
                });
            } else {
                $result = $query->whereIn('projects.id', $projectMemberJoin);
            }
        } else {
            $result = $query->where('projects.id', $projectId)
                ->whereIn('projects.id', $projectMemberJoin);
        }

        return $result;
    }

    /**
     * Get members assigned group by related id
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::getMembersWithPositions()
     */
   public function getMembersWithPositions($positions)
   {
       $query = ProjectMember::select('users.id')
                ->join('users', 'users.id', '=', 'project_member.user_id')
                ->join('roles', 'roles.id', '=', 'project_member.role_id')
                ->where('roles.name' , $positions[0])
                ->orWhere('roles.name' , $positions[1])
                ->groupBy('users.related_id')
                ->pluck('id')
                ->toArray();
        return $query;
    }

    /**
     * Get members assigned group by id (get information of main members )
     * @author tampt6722
     *
     * @param array $ids
     * @param string $position1
     * @param string $position2
     * @return Collection
     */
    public function getMainMembers($ids, $positions)
    {
        $query = ProjectMember::select(
                'project_member.project_id',
                'projects.department_id',
                'departments.name as department_name',
                'u2.id as user_id',
                'u2.related_id',
                'u2.first_name',
                'u2.last_name',
                'u2.email',
                'u2.user_name')
                ->join('projects', 'projects.id', '=', 'project_member.project_id')
                ->join('users as u1', 'u1.id', '=', 'project_member.user_id')
                ->join('roles', 'roles.id', '=', 'project_member.role_id')
                ->join('departments', 'projects.department_id', '=', 'departments.id')
                ->join('users as u2', 'u2.id', '=', 'u1.related_id')
                ->whereNull('departments.deleted_at')
                ->whereNull('projects.deleted_at')
                ->whereIn('u1.id', $ids)
                ->where(function($q) use ($positions) {
                    $q->where('roles.name' , $positions[0])
                    ->orWhere('roles.name' , $positions[1]);
                })
                ->groupBy('u2.id', 'project_member.project_id')
                ->orderBy('project_member.project_id')
                ->get();
        return $query;
    }

    /**
     * Get data project member for API
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::getApiProjectMember()
     */
    public function getApiProjectMember()
    {
        $query = ProjectMember::select('users.email', 'roles.name as role_name', 'project_member.*')
            ->join('users', 'project_member.user_id', '=','users.id' )
            ->join('roles', 'roles.id', '=', 'project_member.role_id')
            ->get();
        return $query;
    }

    /**
     * Get all members who are assigned in projects group by member and position for API
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::getAllMembersInProjects()
     */
    public function getAllMembersInProjects()
    {
        $query = ProjectMember::select('project_member.user_id',
                'roles.name as role_name',
                'users.email',
                'users.related_id')
                ->join('projects', 'projects.id', '=', 'project_member.project_id')
                ->join('users', 'users.id', '=', 'project_member.user_id')
                ->join('roles', 'roles.id', '=', 'project_member.role_id')
                ->groupBy('users.related_id', 'roles.name')
                ->where('projects.deleted_at', null)
                ->get();
        return $query;
    }

    /**
     * Get all members who are assigned in projects group by project and member for API
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::getAllMembersInAProject()
     */
    public function getAllMembersInAProject()
    {
        $query = ProjectMember::select('projects.id as project_id',
                'projects.name as project_name',
                'project_member.user_id',
                'roles.name as role_name',
                'users.email',
                'users.related_id')
                ->join('projects', 'projects.id', '=', 'project_member.project_id')
                ->join('users', 'users.id', '=', 'project_member.user_id')
                ->leftJoin('roles', 'roles.id', '=', 'project_member.role_id')
                ->groupBy('projects.id', 'users.related_id', 'roles.name')
                ->where('projects.deleted_at', null)
                ->get();

        return $query;
    }

    /**
     * Get line of code of a member
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::getLocOfDevs()
     */
    public function getLocOfDevs($startDate, $endDate)
    {
        $query = ProjectMember::select('projects.id as project_id',
                               'projects.department_id',
                               'users.related_id',
                                DB::raw('sum(locs.loc)  as loc'))
                ->join('projects', 'projects.id', '=', 'project_member.project_id')
                ->join('departments', 'projects.department_id', '=', 'departments.id')
                ->join('users', 'users.id', '=', 'project_member.user_id')
                ->join('roles', 'roles.id', '=', 'project_member.role_id')
                ->join('tickets', function($join) use($startDate, $endDate) {
                    $join->on( 'tickets.project_id', '=', 'projects.id')
                    ->on('users.user_name', '=', 'tickets.assign_to_user')
                    ->where('tickets.start_date', '>=', $startDate)
                    ->where('tickets.start_date', '<=', $endDate);
                })
                ->join('locs','tickets.id', '=', 'locs.ticket_id')
                ->where('projects.deleted_at', null)
                ->where('tickets.deleted_at', null)
                ->where('locs.deleted_at', null)
                ->where('roles.name', 'Dev')
                ->orWhere('roles.name', 'DevL')
                ->groupBy('users.related_id', 'projects.id')
                ->get();

        return $query;

    }

    /**
     * Get total actual hours of a member
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::getWorkLoadOfAMember()
     */
    public function getWorkLoadOfAMember($startDate, $endDate, $positions, $activityRelatedId = 0)
    {
        $query = ProjectMember::select('project_member.project_id',
                'users.related_id',
                DB::raw('sum(entries.actual_hour) as actual_hour'))
                ->join('projects', 'projects.id', '=', 'project_member.project_id')
                ->join('users', 'users.id', '=', 'project_member.user_id')
                ->join('roles', 'roles.id', '=', 'project_member.role_id')
                ->join('entries',  function($join) use($startDate, $endDate) {
                    $join->on('entries.project_id', '=', 'projects.id')
                    ->on('entries.user_id','=','users.id')
                    ->where('entries.spent_at', '>=', $startDate)
                    ->where('entries.spent_at', '<=', $endDate);
                })
                ->whereNull('projects.deleted_at')
                ->whereNull('entries.deleted_at')
                ->where(function($q) use($positions) {
                    $q->where('roles.name','=', $positions[0])
                    ->orWhere('roles.name', '=',$positions[1]);
                })->groupBy('project_member.project_id', 'users.related_id' );
                if ($activityRelatedId != 0) {
                    $query = $query->join('activities', function($join) use($activityRelatedId) {
                        $join->on('activities.id', '=', 'entries.activity_id')
                                ->where('activities.related_id','=', $activityRelatedId);
                    });
                };

        return $query->get();
    }

    /**
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::countTicketsWithTicketType()
     */
    public function countTicketsWithBugWeightOfMember($typeRelatedId,
            $startDate, $endDate, $prositions, $userFlag = 0)
    {
        $query = ProjectMember::select('project_member.project_id',
                'roles.name as role_name',
                'users.related_id',
                'bugs_weight.related_id as weight_related_id',
                DB::raw('count(tickets.id)  as countId'))
        ->join('projects', 'projects.id', '=', 'project_member.project_id')
        ->join ('users', 'users.id', '=', 'project_member.user_id')
        ->join('roles', 'roles.id', '=', 'project_member.role_id');

        if ($userFlag == 0) {
            $query1 = $query->join('tickets', function($join) use($startDate, $endDate) {
                $join->on( 'tickets.project_id', '=', 'project_member.project_id')
                 ->on('users.user_name', '=', 'tickets.assign_to_user')
                ->where('tickets.integrated_created_at', '>=', $startDate)
                ->where('tickets.integrated_created_at', '<=', $endDate);
            });
        } elseif ($userFlag == 1) {
            $query1 = $query->join('tickets', function($join) use($startDate, $endDate) {
                $join->on( 'tickets.project_id', '=', 'project_member.project_id')
                ->on('users.user_name', '=', 'tickets.made_by_user')
                ->where('tickets.integrated_created_at', '>=', $startDate)
                ->where('tickets.integrated_created_at', '<=', $endDate);
            });
        } else {
            $query1 = $query->join('tickets', function($join) use($startDate, $endDate) {
                $join->on( 'tickets.project_id', '=', 'project_member.project_id')
                ->on('users.user_name', '=', 'tickets.created_by_user')
                ->where('tickets.integrated_created_at', '>=', $startDate)
                ->where('tickets.integrated_created_at', '<=', $endDate);
            });
        }


        $result = $query1->join('status', function($join) {
            $join->on('status.id', '=', 'tickets.status_id')
            ->where('status.related_id','<>', 6); // 6: rejected
        })
        ->join('ticket_type', function($join) use($typeRelatedId) {
            $join->on('tickets.ticket_type_id', '=', 'ticket_type.id')
            ->where('ticket_type.related_id','=', $typeRelatedId);
        })
        ->join('bugs_weight', 'tickets.bug_weight_id', '=', 'bugs_weight.id')
        ->where('projects.deleted_at', null)
        ->where('tickets.deleted_at', null)
        ->where('roles.name', $prositions[0])
        ->orWhere('roles.name', $prositions[1])
        ->groupBy('projects.id', 'users.related_id', 'tickets.bug_weight_id')
        ->get();

        return $result;

    }


    /**
     * Get total number of test case with an activity of a member report
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::getTestCaseOfAMember()
     */
    public function getTestCaseOfAMember($startDate, $endDate, $activityRelatedId)
    {

        $query = ProjectMember::select('project_member.project_id',
                                        'tickets.id',
                                        'users.related_id',
                                        'tickets.test_case')
                ->join ('projects', 'projects.id', '=', 'project_member.project_id')
                ->join ('users', 'users.id', '=', 'project_member.user_id')
                ->join('roles', 'roles.id', '=', 'project_member.role_id')
                ->join('tickets', function($join) use($startDate, $endDate) {
                    $join->on( 'tickets.project_id', '=', 'project_member.project_id')
                    ->on('users.user_name', '=', 'tickets.assign_to_user')
                    ->where('tickets.start_date', '>=', $startDate)
                    ->where('tickets.start_date', '<=', $endDate);
                })
                ->join('entries',  'entries.ticket_id', '=', 'tickets.id')
                ->join('activities', function($join) use($activityRelatedId) {
                    $join->on('activities.id', '=', 'entries.activity_id')
                    ->where('activities.related_id','=', $activityRelatedId);
                })
                ->where('tickets.test_case', '>', 0)
                ->whereNull('entries.deleted_at')
                ->whereNull('tickets.deleted_at')
                ->where('roles.name', 'QA')
                ->orWhere('roles.name', 'QAL')
                ->distinct('tickets.id')
                ->groupBy('project_member.project_id', 'users.related_id', 'tickets.id')
                ->get();


        return $query;
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::getTestCaseOfAMemberOnProjectForApi()
     */
    public function getTestCaseOfAMemberOnProjectForApi($startDate, $endDate, $activityRelatedId)
    {
       $query = ProjectMember::select('project_member.user_id', 'roles.name as role_name',
               'users.related_id',
                DB::raw('sum(tickets.test_case) as test_case'))
                ->join ('users', 'users.id', '=', 'project_member.user_id')
                ->leftJoin('roles', 'roles.id', '=', 'project_member.role_id')
                ->join('tickets', function($join) use($startDate, $endDate) {
                    $join->on( 'tickets.project_id', '=', 'project_member.project_id')
                    ->on('users.user_name', '=', 'tickets.assign_to_user')
                    ->where('tickets.start_date', '>=', $startDate)
                    ->where('tickets.start_date', '<=', $endDate);
                })
                ->join('entries',  'entries.ticket_id', '=', 'tickets.id')
                ->join('activities', function($join) use($activityRelatedId) {
                    $join->on('activities.id', '=', 'entries.activity_id')
                    ->where('activities.related_id','=', $activityRelatedId);
                })
                ->where('entries.deleted_at', null)
                ->where('tickets.deleted_at', null)
                ->groupBy('project_member.project_id', 'users.related_id', 'roles.name');

        return $query->get();
    }


    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::countTasksOfMembers()
     */
    public function countTasksOfMembers($startDate, $endDate, $potision)
    {
        $query = ProjectMember::select(
                'projects.id as project_id',
                'projects.department_id',
                'users.related_id',
                DB::raw('count(tickets.id)  as countId'))
        ->join('projects', 'projects.id', '=', 'project_member.project_id')
        ->join ('users', 'users.id', '=', 'project_member.user_id')
        ->join('roles', 'roles.id', '=', 'project_member.role_id')
        ->join('tickets', function($join) use($startDate, $endDate) {
            $join->on( 'tickets.project_id', '=', 'projects.id')
            ->on('users.user_name', '=', 'tickets.assign_to_user')
            ->where('tickets.start_date', '>=', $startDate)
            ->where('tickets.start_date', '<=', $endDate);
        })
        ->whereNull('projects.deleted_at')
        ->whereNull('tickets.deleted_at')
        ->where('roles.name', $potision[0])
        ->orWhere('roles.name', $potision[1])
        ->groupBy('users.related_id', 'projects.id')
        ->get();
        return $query;
    }


    /**
     * Count tickets which have ticket type is bug or uat bug with weight bug
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::countTicketsWithBugWeightOfMemberForApi()
     */
    public function countTicketsWithBugWeightOfMemberForApi($typeRelatedId,
            $weightRelatedId, $startDate, $endDate, $userFlag = 0)
    {
        $query = ProjectMember::select('project_member.user_id', 'roles.name as role_name',
                'users.related_id',
                DB::raw('count(tickets.id)  as countId'))
                ->join ('users', 'users.id', '=', 'project_member.user_id')
                ->leftJoin('roles', 'roles.id', '=', 'project_member.role_id');
                if ($userFlag == 0) {
                    $query1 = $query->join('tickets', function($join) use($startDate, $endDate) {
                        $join->on( 'tickets.project_id', '=', 'project_member.project_id')
                        ->on('users.user_name', '=', 'tickets.assign_to_user')
                        ->where('tickets.integrated_created_at', '>=', $startDate)
                        ->where('tickets.integrated_created_at', '<=', $endDate);
                    });
                } elseif ($userFlag == 1) {
                    $query1 = $query->join('tickets', function($join) use($startDate, $endDate) {
                    $join->on( 'tickets.project_id', '=', 'project_member.project_id')
                    ->on('users.user_name', '=', 'tickets.made_by_user')
                    ->where('tickets.integrated_created_at', '>=', $startDate)
                    ->where('tickets.integrated_created_at', '<=', $endDate);
                    });
                } else {
                    $query1 = $query->join('tickets', function($join) use($startDate, $endDate) {
                        $join->on( 'tickets.project_id', '=', 'project_member.project_id')
                        ->on('users.user_name', '=', 'tickets.created_by_user')
                        ->where('tickets.integrated_created_at', '>=', $startDate)
                        ->where('tickets.integrated_created_at', '<=', $endDate);
                    });
                }
       $result = $query1->join('ticket_type', function($join) use($typeRelatedId) {
                                $join->on('tickets.ticket_type_id', '=', 'ticket_type.id')
                                ->where('ticket_type.related_id','=', $typeRelatedId);
                        })
                        ->join('bugs_weight', function($join) use($weightRelatedId) {
                            $join->on('tickets.bug_weight_id', '=', 'bugs_weight.id')
                            ->where('bugs_weight.related_id','=', $weightRelatedId);
                        })
                        ->groupBy('users.related_id', 'roles.name')
                        ->get();
        return $result;


    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::countTicketsWithTicketTypeOfMemberForApi()
     */
    public function countTicketsWithTicketTypeOfMemberForApi($startDate, $endDate)
    {
       $query = ProjectMember::select('project_member.user_id', 'roles.name as role_name',
               'users.related_id',
                DB::raw('count(tickets.id)  as countId'))
            ->join ('users', 'users.id', '=', 'project_member.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'project_member.role_id')
            ->join('tickets', function($join) use($startDate, $endDate) {
                $join->on( 'tickets.project_id', '=', 'project_member.project_id')
                ->on('users.user_name', '=', 'tickets.assign_to_user')
                ->where('tickets.start_date', '>=', $startDate)
                ->where('tickets.start_date', '<=', $endDate);
            })
            ->join('ticket_type','tickets.ticket_type_id', '=', 'ticket_type.id')
            ->where('tickets.deleted_at', null)
            ->groupBy('users.related_id', 'roles.name')
            ->get();
        return $query;
    }
    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::getLocOfAMemberForApi()
     */
    public function getLocOfAMemberForApi($startDate, $endDate)
    {
        $query = ProjectMember::select('project_member.user_id',
                'roles.name as role_name', 'users.related_id',
                DB::raw('sum(locs.loc)  as loc'))
                ->join('users', 'users.id', '=', 'project_member.user_id')
                ->leftJoin('roles', 'roles.id', '=', 'project_member.role_id')
                ->join('tickets', function($join) use($startDate, $endDate) {
                    $join->on( 'tickets.project_id', '=', 'project_member.project_id')
                    ->on('users.user_name', '=', 'tickets.assign_to_user')
                    ->where('tickets.start_date', '>=', $startDate)
                    ->where('tickets.start_date', '<=', $endDate);
                })
                ->join('locs','locs.ticket_id', '=', 'tickets.id')
                ->where('tickets.deleted_at', null)
                ->where('locs.deleted_at', null)
                ->groupBy('users.related_id', 'roles.name');

        return $query;
    }

    /**
     * Get total entries of a member for api
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::getWorkLoadOfAMemberForApi()
     */
    public function getWorkLoadOfAMemberForApi($startDate, $endDate, $activityRelatedId = 0)
    {
        $query = ProjectMember::select('project_member.user_id', 'roles.name as role_name',
                DB::raw('sum(entries.actual_hour) as actual_hour'), 'users.related_id')
                ->join('projects', 'projects.id', '=', 'project_member.project_id')
               ->join ('users', 'users.id', '=', 'project_member.user_id')
               ->leftJoin('roles', 'roles.id', '=', 'project_member.role_id')
                ->join('entries',  function($join) use($startDate, $endDate) {
                    $join->on('entries.project_id', '=', 'projects.id')
                            ->on('entries.user_id', '=', 'users.id')
                    ->where('entries.spent_at', '>=', $startDate)
                    ->where('entries.spent_at', '<=', $endDate);
                });

               if ($activityRelatedId != 0) {
                   $queryAll = $query->join('activities', function($join) use($activityRelatedId) {
                       $join->on('activities.id', '=', 'entries.activity_id')
                       ->where('activities.related_id','=', $activityRelatedId);
                   })->groupBy('project_member.user_id', 'roles.name');

               } else {
                   $queryAll = $query->groupBy('users.related_id', 'roles.name');
               }

        return $query->get();
    }

    /**
     * Get total number of test case with an activity of a member for api
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::getTestCaseOfAMemberForApi()
     */
    public function getTestCaseOfAMemberForApi($startDate, $endDate, $activityRelatedId)
    {
        $query = ProjectMember::select('project_member.user_id', 'roles.name as role_name',
                DB::raw('sum(tickets.test_case) as test_case'), 'users.related_id')
                ->join ('users', 'users.id', '=', 'project_member.user_id')
                ->leftJoin('roles', 'roles.id', '=', 'project_member.role_id')
                ->join('tickets', function($join) use($startDate, $endDate) {
                    $join->on( 'tickets.project_id', '=', 'project_member.project_id')
                    ->on('users.user_name', '=', 'tickets.assign_to_user')
                    ->where('tickets.start_date', '>=', $startDate)
                    ->where('tickets.start_date', '<=', $endDate);
                })
                ->join('entries',  'entries.ticket_id', '=', 'tickets.id')
                ->join('activities', function($join) use($activityRelatedId) {
                    $join->on('activities.id', '=', 'entries.activity_id')
                    ->where('activities.related_id','=', $activityRelatedId);
                })
                ->where('entries.deleted_at', null)
                ->where('tickets.deleted_at', null)
                ->groupBy('users.related_id', 'roles.name');

        return $query->get();
    }

    /**
     * Count tickets which have ticket type is bug or uat bug with weight bug group by project and member
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::countWeightBugsOfMemberOnProjectsForApi()
     */
    public function countWeightBugsOfMemberOnProjectsForApi($typeRelatedId,
            $weightRelatedId, $startDate, $endDate, $userFlag)
    {
         $query = ProjectMember::select('project_member.project_id',
                 'project_member.user_id', 'roles.name as role_name', 'users.related_id',
                DB::raw('count(tickets.id)  as countId'))
                ->join ('users', 'users.id', '=', 'project_member.user_id')
                ->leftJoin('roles', 'roles.id', '=', 'project_member.role_id');
                if ($userFlag == 0) {
                    $query1 = $query->join('tickets', function($join) use($startDate, $endDate) {
                    $join->on( 'tickets.project_id', '=', 'project_member.project_id')
                    ->on('users.user_name', '=', 'tickets.assign_to_user')
                    ->where('tickets.start_date', '>=', $startDate)
                    ->where('tickets.start_date', '<=', $endDate);
                });
                } elseif ($userFlag == 1) {
                    $query1 = $query->join('tickets', function($join) use($startDate, $endDate) {
                        $join->on( 'tickets.project_id', '=', 'project_member.project_id')
                        ->on('users.user_name', '=', 'tickets.made_by_user')
                        ->where('tickets.start_date', '>=', $startDate)
                        ->where('tickets.start_date', '<=', $endDate);
                    });
                } else {
                    $query1 = $query->join('tickets', function($join) use($startDate, $endDate) {
                        $join->on( 'tickets.project_id', '=', 'project_member.project_id')
                        ->on('users.user_name', '=', 'tickets.created_by_user')
                        ->where('tickets.start_date', '>=', $startDate)
                        ->where('tickets.start_date', '<=', $endDate);
                    });
                }

                $result = $query1->join('ticket_type', function($join) use($typeRelatedId) {
                    $join->on('tickets.ticket_type_id', '=', 'ticket_type.id')
                    ->where('ticket_type.related_id','=', $typeRelatedId);
                })
                ->join('bugs_weight', function($join) use($weightRelatedId) {
                    $join->on('tickets.bug_weight_id', '=', 'bugs_weight.id')
                    ->where('bugs_weight.related_id','=', $weightRelatedId);
                })
                ->where('tickets.deleted_at', null)
                ->groupBy('project_member.project_id',
                        'users.related_id', 'roles.name')
                ->get();
        return $result;

    }

    /**
     * Count tickets with a ticket type group by project and member
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::countTicketsWithTicketTypeOfMemberOnProjectsForApi()
     */
    public function countTicketsWithTicketTypeOfMemberOnProjectsForApi($typeRelatedId, $startDate, $endDate)
    {
         $query = ProjectMember::select('project_member.project_id',
                 'project_member.user_id', 'roles.name as role_name', 'users.related_id',
                DB::raw('count(tickets.id)  as countId'))
        ->join ('users', 'users.id', '=', 'project_member.user_id')
        ->leftJoin('roles', 'roles.id', '=', 'project_member.role_id')
        ->join('tickets', function($join) use($startDate, $endDate) {
            $join->on( 'tickets.project_id', '=', 'project_member.project_id')
            ->on('users.user_name', '=', 'tickets.assign_to_user')
            ->where('tickets.start_date', '>=', $startDate)
            ->where('tickets.start_date', '<=', $endDate);
        })
        ->join('ticket_type', function($join) use($typeRelatedId) {
            $join->on('tickets.ticket_type_id', '=', 'ticket_type.id')
            ->where('ticket_type.related_id','=', $typeRelatedId);
        })
        ->where('tickets.deleted_at', null)
        ->groupBy('project_member.project_id', 'users.related_id', 'roles.name')
        ->get();
        return $query;
    }

    /**
     * Count line of code of a member group by project and member
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::getLocOfAMemberOnProjectsForApi()
     */
    public function getLocOfAMemberOnProjectsForApi($startDate, $endDate)
    {
        $query = ProjectMember::select('project_member.project_id',
                                    'project_member.user_id',
                                    'roles.name as role_name','users.related_id',
                                    DB::raw('sum(locs.loc)  as loc'))
                        ->join('users', 'users.id', '=', 'project_member.user_id')
                        ->leftJoin('roles', 'roles.id', '=', 'project_member.role_id')
                        ->join('tickets', function($join) use($startDate, $endDate) {
                            $join->on( 'tickets.project_id', '=', 'project_member.project_id')
                            ->on('users.user_name', '=', 'tickets.assign_to_user')
                            ->where('tickets.start_date', '>=', $startDate)
                            ->where('tickets.start_date', '<=', $endDate);
                        })
                        ->join('locs', 'tickets.id', '=', 'locs.ticket_id')
                        ->where('tickets.deleted_at', null)
                        ->where('locs.deleted_at', null)
                        ->groupBy('project_member.project_id',
                                'users.related_id', 'roles.name')
                                ->orderBy('loc', 'DESC')
                                ->get();
        return $query;
    }

    /**
     * Sum actual hours of a member group by project and member
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::getWorkLoadOfAMemberOnProjectsForApi()
     */
    public function getWorkLoadOfAMemberOnProjectsForApi($startDate, $endDate, $activityRelatedId = 0)
    {
        $query = ProjectMember::select('project_member.project_id',
                'project_member.user_id', 'roles.name as role_name','users.related_id',
                DB::raw('sum(entries.actual_hour) as actual_hour'))
                ->join ('users', 'users.id', '=', 'project_member.user_id')
                ->leftJoin('roles', 'roles.id', '=', 'project_member.role_id')
                ->leftJoin('entries',  function($join) use($startDate, $endDate) {
                    $join->on('entries.user_id', '=', 'users.id')
                    ->where('entries.spent_at', '>=', $startDate)
                    ->where('entries.spent_at', '<=', $endDate);
                })
                ->where('entries.deleted_at', null);
                if ($activityRelatedId != 0) {
                    $result = $query->join('activities', function($join) use($activityRelatedId) {
                        $join->on('activities.id', '=', 'entries.activity_id')
                        ->where('activities.related_id','=', $activityRelatedId);
                    })->groupBy('project_member.project_id',
                            'project_member.user_id', 'roles.name')->get();
                } else {
                    $result = $query->groupBy('project_member.project_id',
                            'users.related_id', 'roles.name')->get();
                }

        return $result;
    }


    public function getLocOfAMemberInProjects($startDate, $endDate)
    {
        $query = ProjectMember::select('projects.id as project_id','project_member.user_id',
                'projects.department_id', 'users.related_id',
                'roles.name as role_name', DB::raw('sum(locs.loc)  as loc'))
                ->join('projects', function($join) {
                    $join->on('projects.id', '=', 'project_member.project_id')
                        ->where('projects.active','=', 1);
                })
                ->join ('users', 'users.id', '=', 'project_member.user_id')
                ->leftJoin('roles', 'roles.id', '=', 'project_member.role_id')
                ->leftJoin('tickets', function($join) use($startDate, $endDate) {
                    $join->on( 'tickets.project_id', '=', 'projects.id')
                    ->on('users.user_name', '=', 'tickets.assign_to_user')
                    ->where('tickets.start_date', '>=', $startDate)
                    ->where('tickets.start_date', '<=', $endDate);
                })
                ->join('locs','locs.ticket_id', '=', 'tickets.id')
                ->where('tickets.deleted_at', null)
                ->where('locs.deleted_at', null)
                ->groupBy('projects.id','users.related_id', 'roles.name')->get();

        return $query;

    }

    /**
     * Get total actual hours of a member
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::getWorkLoadOfAMemberInProjects()
     */
    public function getWorkLoadOfAMemberInProjects($startDate, $endDate, $activityRelatedId = 0)
    {
        $query = ProjectMember::select('projects.id as project_id', 'project_member.user_id',
                'roles.name as role_name', 'projects.department_id', 'users.related_id',
                DB::raw('sum(entries.actual_hour) as actual_hour'))
                ->join('projects', function($join) {
                    $join->on('projects.id', '=', 'project_member.project_id')
                        ->where('projects.active','=', 1);
                })
                ->join ('users', 'users.id', '=', 'project_member.user_id')
                ->leftJoin('roles', 'roles.id', '=', 'project_member.role_id')
                ->leftJoin('tickets', function($join) {
                    $join->on( 'tickets.project_id', '=', 'projects.id')
                    ->on('users.user_name', '=', 'tickets.assign_to_user');
                })
                ->leftJoin('entries',  function($join) use($startDate, $endDate) {
                    $join->on('entries.ticket_id', '=', 'tickets.id')
                    ->where('entries.spent_at', '>=', $startDate)
                    ->where('entries.spent_at', '<=', $endDate);
                })
                ->where('projects.deleted_at', null)
                ->where('tickets.deleted_at', null);
        if ($activityRelatedId != 0) {
            $queryAll = $query->leftJoin('activities', function($join) use($activityRelatedId) {
                $join->on('activities.id', '=', 'entries.activity_id')
                ->where('activities.related_id','=', $activityRelatedId);
            })->groupBy('projects.id', 'project_member.user_id', 'roles.name')->get();

        } else {
            $queryAll = $query->groupBy('projects.id','users.related_id', 'roles.name')->get();
        }

        return $queryAll;
    }


    /**
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::countTicketsWithTicketType()
     */
    public function countTicketsWithBugWeightOfMemberInProjects($typeRelatedId,
            $weightRelatedId, $startDate, $endDate)
    {
        $query = ProjectMember::select('projects.id as project_id', 'project_member.user_id',
                'roles.name as role_name', 'projects.department_id',
                'users.related_id',
                DB::raw('count(tickets.id)  as countId'))
                ->join('projects', function($join) {
                    $join->on('projects.id', '=', 'project_member.project_id')
                        ->where('projects.active','=', 1);
                })
                ->join ('users', 'users.id', '=', 'project_member.user_id')
                ->leftJoin('roles', 'roles.id', '=', 'project_member.role_id')
                ->leftJoin('tickets', function($join) use($startDate, $endDate) {
                    $join->on( 'tickets.project_id', '=', 'projects.id')
                    ->on('users.user_name', '=', 'tickets.assign_to_user')
                    ->where('tickets.start_date', '>=', $startDate)
                    ->where('tickets.start_date', '<=', $endDate);
                })
                ->leftJoin('ticket_type', function($join) use($typeRelatedId) {
                    $join->on('tickets.ticket_type_id', '=', 'ticket_type.id')
                    ->where('ticket_type.related_id','=', $typeRelatedId);
                })
                ->leftJoin('bugs_weight', function($join) use($weightRelatedId) {
                    $join->on('tickets.bug_weight_id', '=', 'bugs_weight.id')
                    ->where('bugs_weight.related_id','=', $weightRelatedId);
                })
                ->groupBy('projects.id', 'users.related_id', 'roles.name')
                ->where('projects.deleted_at', null)
                ->where('tickets.deleted_at', null)
                ->get();

        return $query;

    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::countTicketsWithTicketTypeOfMemberInProjects()
     */
    public function countTicketsWithTicketTypeOfMemberInProjects($typeRelatedId,
                                                            $startDate, $endDate)
    {
        $query = ProjectMember::select('projects.id as project_id',
                'project_member.user_id', 'roles.name as role_name', 'projects.department_id',
                'users.related_id', DB::raw('count(tickets.id)  as countId'))
                ->join('projects', function($join) {
                    $join->on('projects.id', '=', 'project_member.project_id')
                        ->where('projects.active','=', 1);
                })
                ->join ('users', 'users.id', '=', 'project_member.user_id')
                ->leftJoin('roles', 'roles.id', '=', 'project_member.role_id')
                ->leftJoin('tickets', function($join) use($startDate, $endDate) {
                    $join->on( 'tickets.project_id', '=', 'projects.id')
                    ->on('users.user_name', '=', 'tickets.assign_to_user')
                    ->where('tickets.start_date', '>=', $startDate)
                    ->where('tickets.start_date', '<=', $endDate);
                })
                ->leftJoin('ticket_type', function($join) use($typeRelatedId) {
                    $join->on('tickets.ticket_type_id', '=', 'ticket_type.id')
                    ->where('ticket_type.related_id','=', $typeRelatedId);
                })
                ->where('tickets.deleted_at', null)
                ->groupBy('projects.id', 'users.related_id', 'roles.name')
                ->get();

        return $query;
    }

    public function saveProjectMember($projectId, $userId, $roleId)
    {
        if ($userId > 0) {
            $existedProjectMember = $this->findByAttributes('project_id', $projectId, 'user_id', $userId);
            if (empty($existedProjectMember)) {
                $dataPM['user_id'] = $userId;
                $dataPM['project_id'] = $projectId;
                $dataPM['assign'] = 0;
                $dataPM['role_id'] = $roleId;
                $this->save($dataPM);
            }
        }
    }

    public function getPermissionOfAMemberInProject($userId, $projectId){
        $query = ProjectMember::join ('roles', 'roles.id', '=', 'project_member.role_id')
                                ->where('project_member.user_id', $userId)
                                ->where('project_member.project_id', $projectId)
                                ->select('roles.permissions')
                                ->first();
        return $query;
    }
}