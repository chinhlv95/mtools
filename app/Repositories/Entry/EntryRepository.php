<?php
namespace App\Repositories\Entry;

use App\Models\Entry;
use App\Repositories\Activity\ActivityRepositoryInterface;
use App\Repositories\Ticket\TicketRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use DB;
use App\Models\User;

class EntryRepository implements EntryRepositoryInterface
{
    public function __construct(ActivityRepositoryInterface $activity,
                                TicketRepositoryInterface $ticket,
                                UserRepositoryInterface $user)
    {
        $this->activity = $activity;
        $this->ticket   = $ticket;
        $this->user     = $user;
    }

    public function all(){
        return Entry::all();
    }

    public function paginate($quantity){
        return Entry::paginate($quantity);
    }

    public function find($id){
        return Entry::find($id);
    }

    /**
     * @todo Save data to entries table
     *
     * @author tampt6722
     * @param array $data
     * @see \App\Repositories\Entry\EntryRepositoryInterface::save()
     */
    public function save($data){
        $entry = new Entry();
        if (isset($data['project_id'])) {
            $entry->project_id = $data['project_id'];
        } else {
            $entry->project_id = 0;
        }
        if (isset($data['ticket_id'])) {
            $entry->ticket_id = $data['ticket_id'];
        } else {
            $entry->ticket_id = 0;
        }
        if (isset($data['integrated_entry_id'])) {
            $entry->integrated_entry_id = $data['integrated_entry_id'];
        } else {
            $entry->integrated_entry_id = 0;
        }
        if (isset($data['user_id'])) {
            $entry->user_id = $data['user_id'];
        } else {
            $entry->user_id = 0;
        }
        if (isset($data['actual_hour'])) {
            $entry->actual_hour = $data['actual_hour'];
        } else {
            $entry->actual_hour = 0;
        }
        if (isset($data['activity_id'])) {
            $entry->activity_id = $data['activity_id'];
        } else {
            $entry->activity_id = 0;
        }
        if (isset($data['spent_at'])) {
            $entry->spent_at = $data['spent_at'];
        } else {
            $entry->spent_at = "";
        }
        if (isset($data['integrated_created_at'])) {
            $entry->integrated_created_at = $data['integrated_created_at'];
        } else {
            $entry->integrated_created_at = '';
        }
        if (isset($data['integrated_updated_at'])) {
            $entry->integrated_updated_at = $data['integrated_updated_at'];
        } else {
            $entry->integrated_updated_at = '';
        }
        $entry->save();
        return true;
    }

    public function delete($id) {
        Entry::find($id)->delete();
        return true;
    }
    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Entry\EntryRepositoryInterface::update()
     */
    public function update($data, $id){
        $entry = Entry::find($id);
        if (isset($data['actual_hour'])) {
            $entry->actual_hour = $data['actual_hour'];
        }
        if (isset($data['activity_id'])) {
            $entry->activity_id = $data['activity_id'];
        }
        if (isset($data['spent_at'])) {
            $entry->spent_at = $data['spent_at'];
        }
        if (isset($data['integrated_updated_at'])) {
            $entry->integrated_updated_at = $data['integrated_updated_at'];
        }
        if (isset($data['integrated_entry_id'])) {
            $entry->integrated_entry_id = $data['integrated_entry_id'];
        }
        $entry->save();
        return true;
    }

    public function findByAttribute($att, $name){
        return Entry::where($att, $name)->first();
    }

    public function getDataByAttribute($att, $name){
        return Entry::where($att, $name)->get();
    }
    public function getDataByAttributes($att1, $name1, $att2, $name2){
        return Entry::where($att1, $name1)
                    ->where($att2,$name2)->get();
    }

    public function findByAttributes($att1, $name1, $att2, $name2){
        return Entry::where($att1, $name1)
                    ->where($att2,$name2)->first();
    }

    public function findByTriAttributes($att1, $name1, $att2, $name2, $att3, $name3){
        return Entry::where($att1, $name1)
                    ->where($att2,$name2)
                    ->where($att3,$name3)
                    ->first();
    }

    /**
     * @todo Get entry in ticket
     *
     * @author thanhnb6719
     * @param array $listProjectId
     * @param date $startDate
     * @param date $endDate
     * @param int $getReportType
     * @param int $getStatus
     * @see \App\Repositories\Entry\EntryRepositoryInterface::getEntryInTicket()
     */
    public function getEntryInTicket($listProjectId, $startDate, $endDate, $getReportType, $getStatus)
    {
        if (count($listProjectId) > 0) {
            switch ($getReportType){
                case "summary_report":
                    $entry = DB::table('entries')
                        ->where('entries.spent_at', '>=', $startDate)
                        ->where('entries.spent_at', '<=', $endDate)
                        ->join('projects', function($join) use($listProjectId){
                                $join->on('entries.project_id', '=', 'projects.id')
                                     ->whereIn('projects.id', $listProjectId->toArray());
                          })
                        ->join('users', 'users.id', '=', 'entries.user_id')
                        ->select('projects.id as all_project_id',
                                 'users.email as email',
                                 'users.related_id as user_id',
                                  DB::raw('SUM(entries.actual_hour) as actual_hour'))
                        ->where('projects.deleted_at', null)
                        ->groupBy('all_project_id','email')
                        ->get();
                break;
                case "position_report":
                    $entry = DB::table('entries')
                        ->where('entries.spent_at', '>=', $startDate)
                        ->where('entries.spent_at', '<=', $endDate)
                        ->join('projects', function($join) use($listProjectId){
                                $join->on('entries.project_id', '=', 'projects.id')
                                     ->whereIn('projects.id', $listProjectId->toArray());
                          })
                        ->join('project_member', 'projects.id', '=','project_member.project_id')
                        ->leftJoin('roles', 'roles.id', '=','project_member.role_id')
                        ->join('users', function($join){
                            $join->on('users.related_id', '=', 'project_member.user_id')
                                 ->on('users.id', '=', 'entries.user_id');
                        })
                        ->select('projects.id as all_project_id',
                                'roles.name as user_position',
                                DB::raw('SUM(entries.actual_hour) as actual_hour'))
                        ->where('projects.deleted_at', null)
                        ->groupBy('all_project_id','user_position')
                        ->get();
                break;
                case "entries_detail_report":
                    if (ceil(abs(strtotime($endDate) - strtotime($startDate)) / 86400) > 31){
                        $eachMonth = DB::table('entries')
                            ->where('entries.spent_at', '>=', $startDate)
                            ->where('entries.spent_at', '<=', $endDate)
                            ->join('projects', function($join) use($listProjectId){
                                    $join->on('entries.project_id', '=', 'projects.id')
                                         ->whereIn('projects.id', $listProjectId->toArray());
                              })
                            ->join('users', 'users.id', '=', 'entries.user_id')
                            ->select('projects.id as all_project_id',
                                    'users.email as user_email',
                                    DB::raw('SUM(entries.actual_hour) as actual_hour'),
                                    DB::raw('month(entries.spent_at) as spent_month'),
                                    DB::raw('year(entries.spent_at) as spent_year'))
                            ->where('projects.deleted_at', null)
                            ->groupBy('user_email', 'all_project_id', 'spent_month', 'spent_year')
                            ->get();
                        $total     = DB::table('entries')
                            ->where('entries.spent_at', '>=', $startDate)
                            ->where('entries.spent_at', '<=', $endDate)
                            ->join('projects', function($join) use($listProjectId){
                                    $join->on('entries.project_id', '=', 'projects.id')
                                         ->whereIn('projects.id', $listProjectId->toArray());
                              })
                            ->join('users', 'users.id', '=', 'entries.user_id')
                            ->select('projects.id as all_project_id',
                                    'users.email as user_email',
                                    DB::raw('SUM(entries.actual_hour) as actual_hour'))
                            ->where('projects.deleted_at', null)
                            ->groupBy('user_email','all_project_id')
                            ->get();
                        $entry = array('eachMonth' => $eachMonth, 'total' => $total);
                    } elseif ((ceil(abs(strtotime($endDate) - strtotime($startDate)) / 86400) > 7) && ceil(abs(strtotime($endDate) - strtotime($startDate)) / 86400) <= 31){
                        $eachWeek = DB::table('entries')
                            ->where('entries.spent_at', '>=', $startDate)
                            ->where('entries.spent_at', '<=', $endDate)
                            ->join('projects', function($join) use($listProjectId){
                                    $join->on('entries.project_id', '=', 'projects.id')
                                         ->whereIn('projects.id', $listProjectId->toArray());
                              })
                            ->join('users', 'users.id', '=', 'entries.user_id')
                            ->select('projects.id as all_project_id',
                                    'users.email as user_email',
                                    DB::raw('SUM(entries.actual_hour) as actual_hour'),
                                    DB::raw('week(entries.spent_at) as spent_week'),
                                    DB::raw('year(entries.spent_at) as spent_year'))
                            ->where('projects.deleted_at', null)
                            ->groupBy('user_email', 'all_project_id', 'spent_week', 'spent_year')
                            ->get();
                        $total     = DB::table('entries')
                            ->where('entries.spent_at', '>=', $startDate)
                            ->where('entries.spent_at', '<=', $endDate)
                            ->join('projects', function($join) use($listProjectId){
                                    $join->on('entries.project_id', '=', 'projects.id')
                                         ->whereIn('projects.id', $listProjectId->toArray());
                              })
                            ->join('users', 'users.id', '=', 'entries.user_id')
                            ->select('projects.id as all_project_id',
                                    'users.email as user_email',
                                    DB::raw('SUM(entries.actual_hour) as actual_hour'))
                            ->where('projects.deleted_at', null)
                            ->groupBy('user_email','all_project_id')
                            ->get();
                        $entry = array('eachWeek' => $eachWeek, 'total' => $total);
                    } else {
                        $entry = DB::table('entries')
                            ->where('entries.spent_at', '>=', $startDate)
                            ->where('entries.spent_at', '<=', $endDate)
                            ->join('projects', function($join) use($listProjectId){
                                    $join->on('entries.project_id', '=', 'projects.id')
                                         ->whereIn('projects.id', $listProjectId);
                              })
                            ->join('users', 'users.id', '=', 'entries.user_id')
                            ->select('projects.id as all_project_id',
                                    'users.email as email',
                                    'entries.spent_at as personal_spent_at',
                                    'entries.actual_hour as personal_actual_hour')
                            ->where('projects.deleted_at', null)
                            ->get();
                    }
                break;
                case "graph_report":
                    if ($getStatus == 0) {
                        $entry = DB::table('entries')
                                    ->where('entries.spent_at', '>=', $startDate)
                                    ->where('entries.spent_at', '<=', $endDate)
                                    ->join('projects', function($join) use($listProjectId){
                                            $join->on('entries.project_id', '=', 'projects.id')
                                                 ->whereIn('projects.id', $listProjectId->toArray());
                                      })
                                    ->leftJoin('users', 'users.id', '=', 'entries.user_id')
                                    ->select('projects.id as all_project_id',
                                            'projects.name as project_name',
                                            DB::raw('SUM(entries.actual_hour) as actual_hour'))
                                    ->where('projects.deleted_at', null)
                                    ->groupBy('all_project_id')
                                    ->get();
                    } else {
                        $entry = DB::table('entries')
                                    ->where('entries.spent_at', '>=', $startDate)
                                    ->where('entries.spent_at', '<=', $endDate)
                                    ->join('projects', function($join) use($listProjectId, $getStatus){
                                            $join->on('entries.project_id', '=', 'projects.id')
                                                 ->where('projects.status', '=', $getStatus)
                                                 ->whereIn('projects.id', $listProjectId->toArray());
                                      })
                                    ->leftJoin('users', 'users.id', '=', 'entries.user_id')
                                    ->select('projects.id as all_project_id',
                                            'projects.name as project_name',
                                            DB::raw('SUM(entries.actual_hour) as actual_hour'))
                                    ->where('projects.deleted_at', null)
                                    ->groupBy('all_project_id')
                                    ->get();
                    }
                break;
                default:
                    $entry = DB::table('entries')
                                ->where('entries.spent_at', '>=', $startDate)
                                ->where('entries.spent_at', '<=', $endDate)
                                ->join('projects', function($join) use($listProjectId){
                                        $join->on('entries.project_id', '=', 'projects.id')
                                             ->whereIn('projects.id', $listProjectId->toArray());
                                  })
                                ->join('users', 'users.id', '=', 'entries.user_id')
                                ->select('projects.id as all_project_id',
                                        'users.email as email',
                                        DB::raw('SUM(entries.actual_hour) as actual_hour'))
                                ->where('projects.deleted_at', null)
                                ->groupBy('all_project_id','email')
                                ->get();
                break;
            }
        } else {
            $entry = [];
        }
        return $entry;
    }

    /**
     * @todo Get work time of personal
     *
     * @author thanhnb6719
     * @param date $startDate
     * @param date $endDate
     * @see \App\Repositories\Entry\EntryRepositoryInterface::getEntryOfPersonal()
     */
    public function getEntryOfPersonal($startDate, $endDate)
    {
        $personalEntry = DB::table('entries')
            ->join('users', 'users.id', '=', 'entries.user_id')
            ->select('users.email', 'entries.*', 'users.id')
            ->whereBetween('spent_at', [$startDate, $endDate])
            ->where('entries.deleted_at', null)
            ->get();
        return $personalEntry;
    }

    /**
     * @todo Check entry when import (Exist or not)
     *
     * @author thanhnb6719
     * @param date $spentAt
     * @param int $ticketId
     * @param int $projectId
     * @param int $userId
     * @see \App\Repositories\Entry\EntryRepositoryInterface::checkEntryBeforeSaveImport()
     */
    public function getEntryBeforeSaveImport($spentAt, $ticketId, $projectId, $userId){
        $checkEntry = DB::table('entries')
                        ->where('entries.spent_at', $spentAt)
                        ->where('entries.project_id', $projectId)
                        ->where('entries.user_id', $userId)
                        ->where('entries.ticket_id', $ticketId);
        return $checkEntry;
    }

    /**
     * @todo Update actual time when isset entry time in database
     *
     * @author thanhnb6719
     * @param array $data
     * @param int $id
     * @see \App\Repositories\Entry\EntryRepositoryInterface::updateEntryWhenImportFile()
     */
    public function updateEntryWhenImportFile($data, $id){
        $entry = Entry::find($id);
        $entry->actual_hour = $data['actual_hour'];
        $entry->save();
        return true;
    }

    /**
     * @todo Save entries data
     *
     * @author tampt6722
     * @param array $timeEntry
     * @param integer $projectId
     * @param integer $ticketId
     * @param integer $sourceId
     */
    public function saveDataEntries($timeEntry, $projectId, $ticketId, $sourceId)
    {
        $dataEntry = [];
        $integratedCreatedAt = date('Y-m-d H:i:s', strtotime($timeEntry['created_on']));
        if (!empty($timeEntry['user']['id'])) {
            $userInfo = $this->client->user->show($timeEntry['user']['id']);
            if (is_array($userInfo)) {
                foreach ($userInfo as $user) {
                    if (!empty($user['login'])) {
                        $userId = $this->userRepository->saveUserByUsername(
                                $user['login'], $user['mail'],
                                $user['firstname'], $user['lastname'], $sourceId);
                        $dataEntry['user_id'] = $userId;
                        break;
                    }
                }
            }
        }
        if (!empty($timeEntry['activity']['name'])){
            $dataEntry['activity_id'] = $this->activityRepository->getActivityId($timeEntry['activity'], $sourceId);
        }
        $dataEntry['project_id'] = $projectId;
        $dataEntry['ticket_id'] = $ticketId;
        $dataEntry['integrated_created_at'] = $integratedCreatedAt;
        $dataEntry['integrated_updated_at'] = date('Y-m-d H:i:s', strtotime($timeEntry['updated_on']));
        if (!empty($timeEntry['hours'])) {
            $dataEntry['actual_hour'] = $timeEntry['hours'];
        }
        if (!empty($timeEntry['spent_on'])) {
            $dataEntry['spent_at'] = date('Y-m-d H:i:s', strtotime($timeEntry['spent_on']));
        }
        $this->entryRepository->save($dataEntry);
    }

    /**
     * @todo Get entries of eac
     * {@inheritDoc}
     * @see \App\Repositories\Entry\EntryRepositoryInterface::getEntryOfPersonalWithTickets()
     */
    public function getEntryOfPersonalWithTickets($startDate, $endDate, $listUserId){
        if (count($listUserId) > 0) {
            $userId = "(";
            foreach ($listUserId as $key => $value) {
                $userId .= $value.", ";
            }
            $searchUserId = substr($userId,0,-2).")";
            $query = "SELECT
                        users.user_name AS user_name,
                        temporaryEntry.user_id AS user_id,
                        temporaryEntry.project_id AS project_id,
                        temporaryEntry.project_name AS project_name,
                        temporaryEntry.ticket_name AS ticket_name,
                        temporaryEntry.spent_at AS spent_at,
                        temporaryEntry.actual_hour AS actual_hour
                    FROM
                    (
                        SELECT
                            projects.id AS project_id,
                            projects. NAME AS project_name,
                            users.related_id AS user_id,
                            tickets.title AS ticket_name,
                            entries.spent_at,
                            SUM(entries.actual_hour) AS actual_hour
                        FROM
                            entries
                        INNER JOIN projects ON projects.id = entries.project_id
                        INNER JOIN users ON users.id = entries.user_id
                        INNER JOIN tickets ON tickets.id = entries.ticket_id
                        WHERE
                            users.related_id IN {$searchUserId}
                        AND entries.spent_at >= '{$startDate}'
                        AND entries.spent_at <= '{$endDate}'
                        AND projects.deleted_at IS NULL
                        GROUP BY
                            entries.user_id,
                            entries.spent_at,
                            entries.ticket_id
                        ) AS temporaryEntry
                    INNER JOIN users ON users.id = temporaryEntry.user_id";
            $result = DB::select($query);
        } else {
            $result = [];
        }
        return $result;
    }

    /**
     *
     * @author tampt6722
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getActualHourForPQ($startDate, $endDate)
    {
        $query = Entry::select('project_id',
                DB::raw('sum(actual_hour) as actual_hour'))
                ->where('entries.spent_at', '>=', $startDate)
                ->where('entries.spent_at', '<=', $endDate)
                ->whereNull('deleted_at')
                ->groupBy('project_id')->get();

        return $query;
    }
}