<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Redmine\Client;
use Illuminate\Support\Facades\Cache;
use App\Repositories\Crawler\CrawlerTypeRepositoryInterface;
use App\Repositories\Crawler\CrawlerUrlRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Response;
use App\Repositories\Ticket\TicketRepositoryInterface;
use App\Repositories\TicketType\TicketTypeRepositoryInterface;
use App\Repositories\Entry\EntryRepositoryInterface;
use App\Repositories\ProjectVersion\ProjectVersionRepositoryInterface;
use Cartalyst\Sentinel\Sentinel;
use App\Repositories\ProjectMember\ProjectMemberRepositoryInterface;
use App\Repositories\Activity\ActivityRepositoryInterface;
use App\Repositories\Status\StatusRepositoryInterface;
use App\Repositories\Priority\PriorityRepositoryInterface;
use App\Repositories\BugWeight\BugWeightRepositoryInterface;
use App\Repositories\BugType\BugTypeRepositoryInterface;
use App\Repositories\RootCause\RootCauseRepositoryInterface;
use Exception;
use DB;
use App\Repositories\Loc\LocRepositoryInterface;

class CommandRedmine02 extends Command {

    public $client = null;
    public $adjuster = null;

    public function __construct(
                                CrawlerTypeRepositoryInterface $crawlerType,
                                CrawlerUrlRepositoryInterface $crawlerUrl,
                                ProjectRepositoryInterface $project,
                                UserRepositoryInterface $user,
                                ActivityRepositoryInterface $activity,
                                TicketRepositoryInterface $ticket,
                                TicketTypeRepositoryInterface $ticketType,
                                EntryRepositoryInterface $entry,
                                ProjectVersionRepositoryInterface $projectVersion,
                                ProjectMemberRepositoryInterface $projectMember,
                                StatusRepositoryInterface $status,
                                PriorityRepositoryInterface $priority,
                                BugTypeRepositoryInterface $bugType,
                                BugWeightRepositoryInterface $bugWeight,
                                RootCauseRepositoryInterface $rootCause,
                                LocRepositoryInterface $loc) {
        Cache::flush();
        $this->client = new Client(
                env('REDMINE02_URL', ''),
                env('REDMINE02_API_KEY', ''));
        $this->crawlerTypeRepository = $crawlerType;
        $this->crawlerUrlRepository = $crawlerUrl;
        $this->projectRepository = $project;
        $this->userRepository = $user;
        $this->ticketRepository = $ticket;
        $this->ticketTypeRepository = $ticketType;
        $this->entryRepository = $entry;
        $this->projectVersionRepository = $projectVersion;
        $this->projectMemberRepository = $projectMember;
        $this->activityRepository = $activity;
        $this->statusRepository = $status;
        $this->priorityRepository = $priority;
        $this->bugTypeRepository = $bugType;
        $this->bugWeightRepository = $bugWeight;
        $this->rootCauseRepository = $rootCause;
        $this->locRepository = $loc;
        $this->adjuster = new IntervalAdjuster();
        parent::__construct();
    }

    /**
     * Get Versions
     * @author tampt3733
     *
     * @param integer $integratedProjectId
     * @return void
     */
    protected function getVersions($integratedProjectId, $projectId){
        $versions = $this->client->version->all($integratedProjectId);
        if (is_array($versions)) {
            foreach ($versions as $key =>$value) {
                if ($key === 'versions') {
                    foreach ($value as $version){
                        $dataVersion = [];
                        // Save data to project_versions table
                        $existedProjectVersion = $this->projectVersionRepository
                        ->findByAttributes('integrated_version_id', $version['id'],
                                'source_id', 3);
                        if (empty($existedProjectVersion )) {
                            try {
                                $dataVersion ['integrated_version_id'] = $version['id'];
                                if (!empty($version ['name'])) {
                                    $dataVersion ['name'] = $version ['name'];
                                }
                                $dataVersion ['project_id'] = $projectId;
                                $dataVersion ['source_id'] = 3;
                                if (!empty($version ['description'])) {
                                    $dataVersion ['description'] = $version ['description'];
                                }
                                if (!empty($version ['due_date'])) {
                                    $dataVersion ['end_date'] = date('Y-m-d H:i:s', strtotime($version ['due_date']));
                                }
                                $this->projectVersionRepository->save( $dataVersion );
                            } catch (Exception $e) {
                                print_r( $e->getMessage());
                            }
                        } else {
                            $versionId = $existedProjectVersion->id;
                            if (!empty($version ['description'])) {
                                $dataVersion ['description'] = $version ['description'];
                            }
                            if (!empty($version ['due_date'])) {
                                $dataVersion ['end_date'] = date('Y-m-d H:i:s', strtotime($version ['due_date']));
                            }
                            $this->projectVersionRepository->update($dataVersion, $versionId);
                        }
                    }
                    break;
                }
            }
        }
    }

    /**
     * Get time entry
     * @author tampt6722
     *
     * @param integer $sourceId
     * @param array $paramEntry
     * @return void
     */
    protected function getTimeEntries($projectId, $paramEntry, $sourceId) {
        $countEntry = 0;
        $timeEntries = $this->client->time_entry->all($paramEntry);
        if (is_array($timeEntries)) {
            $getEntry = [];
            foreach ($timeEntries as $tKey => $tValue) {
                if ($tKey === 'time_entries') {
                    $countEntry = count($tValue);
                    foreach ($tValue as $timeEntry) {

                        if (!empty($timeEntry['issue']['id'])) {
                            $getEntry[] = $timeEntry['id'];
                            $integratedId = $timeEntry['issue']['id'];
                            $checkVars = [  'project_id' => $projectId,
                                            'integrated_ticket_id' => $integratedId,
                                            'source_id' => $sourceId
                            ];
                            $checkTicket = $this->ticketRepository->findByManyAttributes($checkVars);
                            if (count($checkTicket) > 0) {
                                $ticketId = $checkTicket->id;
                            } else {
                                $ticketId = $this->ticketRepository->save($checkVars);
                            }

                            $existedEntry = $this->entryRepository->findByAttribute('integrated_entry_id', $timeEntry['id']);
                            if (count($existedEntry) == 0) {
                                $this->saveDataEntries($timeEntry, $projectId, $ticketId, $sourceId);
                            } else {
                                $this->updateEntry($timeEntry, $sourceId, $existedEntry);
                            }
                        }
                    }
                    break;
                }
            }
        }

        if ($countEntry == $paramEntry['limit']) {
            $paramEntry['offset']+= ($paramEntry['limit']);
            $this->getTimeEntries($projectId, $paramEntry, $sourceId);
        }
    }

    /**
     *
     * @author TamPT_6722
     *
     * @param integer $projectId
     */
    public function checkDeletedEntries($projectId)
    {
        $entries = $this->entryRepository->getDataByAttribute('project_id', $projectId);
        if (count($entries) > 0) {
            foreach ($entries as $entry) {
                $this->adjuster->lap();
                $checkEntry = [];
                $checkEntry = $this->client->time_entry->show($entry->integrated_entry_id);
                if (empty($checkEntry)){
                    $this->entryRepository->delete($entry->id);
                }
                $this->adjuster->adjust(1);
            }
        }
    }

    /**
     * Save user info
     * @author tampt6722
     *
     * @param integer $assigneeId
     * @param integer $projectId
     * @param integer $flag
     * @return array
     */
    protected function saveUserInfo($assigneeId, $projectId = 0, $flag = 0)
    {
        $pUserId = 0;
        $username = '';
        $assigneeInfo = $this->client->user->show($assigneeId);
        if (is_array($assigneeInfo)) {
            foreach ($assigneeInfo as $assignee) {
                    $username = $assigneeId;
                    $email = '';
                    $firstName = '';
                    $lastName = '';
                    if (!empty($assignee['mail'])) {
                        $email = $assignee['mail'];
                    }
                    if (!empty($assignee['firstname'])) {
                        $firstName = $assignee['firstname'];
                    }
                    if (!empty($assignee['lastname'])) {
                        $lastName = $assignee['lastname'];
                    }
                    $pUserId = $this->userRepository->saveUserByUsername($username,
                            $email, $firstName, $lastName, 3);
            }
        }
        if ($flag != 0) {
            $this->projectMemberRepository->saveProjectMember($projectId, $pUserId, 2);
        }
        $data = ['user_name' => $username, 'user_id' => $pUserId];

        return $data;
    }

    /**
     * Save entries data
     * @author tampt6722
     *
     * @param array $timeEntry
     * @param integer $projectId
     * @param integer $ticketId
     * @param integer $sourceId
     */
    protected function saveDataEntries($timeEntry, $projectId, $ticketId, $sourceId)
    {
        $dataEntry = [];
        $integratedCreatedAt = date('Y-m-d H:i:s', strtotime($timeEntry['created_on']));
        if (!empty($timeEntry['user']['id'])) {
            $user = $this->saveUserInfo($timeEntry['user']['id'], $projectId, 1);
            $dataEntry['user_id'] = $user['user_id'];
        }
        if (!empty($timeEntry['activity']['name'])){
            $dataEntry['activity_id'] = $this->activityRepository->getActivityId($timeEntry['activity'], $sourceId);
        }
        $dataEntry['project_id'] = $projectId;
        $dataEntry['ticket_id'] = $ticketId;
        $dataEntry['integrated_entry_id'] = $timeEntry['id'];
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
     *
     * @author tampt6722
     *
     * @param integer $ticketId
     * @param array $paramEntry
     * @return void
     */
    protected function updateEntry($timeEntry, $sourceId, $existedEntry) {
        $updatedAt = date('Y-m-d H:i:s', strtotime($timeEntry['updated_on']));
        if ($updatedAt != $existedEntry->integrated_updated_at) {
            $dataEntry['integrated_entry_id'] = $timeEntry['id'];
            if (!empty($timeEntry['user']['id'])) {
                $user = $this->saveUserInfo($timeEntry['user']['id'], $projectId, 1);
                $dataEntry['user_id'] = $user['user_id'];
            }
            if (!empty($timeEntry['activity']['name'])) {
                $dataEntry['activity_id'] = $this->activityRepository->getActivityId($timeEntry['activity'], $sourceId);
            }
            if (!empty($timeEntry['hours'])) {
                $dataEntry['actual_hour'] = $timeEntry['hours'];
            }
            if (!empty($timeEntry['spent_on'])) {
                $dataEntry['spent_at'] = $timeEntry['spent_on'];
            }
            $dataEntry['integrated_updated_at'] = $updatedAt;
            $this->entryRepository->update($dataEntry, $existedEntry->id);
        }
    }


    /**
     * Get tickets
     * @author tampt3722
     *
     * @param array $params
     * @param integer $projectId
     * @param integer $integratedProjectId
     * @param integer $crawlerType
     * @return void
     */
    protected function getTickets($params = [], $crawlerType, $projectId) {
        $ticketCount = 0;
        $this->line('Get tickets limit: ' . $params['limit'] . ', offset: ' . $params['offset']);
        $tickets = $this->client->issue->all ($params);
        if (is_array($tickets)) {
            foreach ( $tickets as $key => $value ) {
                if ($key === 'issues') {
                    $ticketCount = count($value);
                    if ($ticketCount > 0) {
                        foreach ( $value as $ticket ) {
                            try {
                                $this->adjuster->lap();
                                $loc = 0;
                                $paramEntry = [];
                                $dataTicket = [];
                                $pUserId = 0;
                                $crawlerErr = [];
                                $integratedCreatedAt = date('Y-m-d H:i:s', strtotime($ticket ['created_on']));
                                $integratedUpdatedAt = date('Y-m-d H:i:s', strtotime($ticket ['updated_on']));
                                $integratedTicketId = $ticket['id'];
                                $crawlerTypeId = $this->crawlerTypeRepository
                                    ->findByAttribute('name', $crawlerType)->id;
                                $checkVars = [  'project_id' => $projectId,
                                                'integrated_ticket_id' => $integratedTicketId,
                                                'source_id' => 3
                                ];
                                $existedTicket = $this->ticketRepository
                                        ->findByManyAttributes($checkVars);
                                if (count($existedTicket) == 0) {
                                    $integratedProjectId = $ticket['project']['id'];
                                    $dataTicket ['integrated_ticket_id'] = $integratedTicketId;
                                    $dataTicket ['source_id'] = 3;
                                    $dataTicket ['integrated_created_at'] = $integratedCreatedAt;
                                    $dataTicket ['integrated_updated_at'] = $integratedUpdatedAt;
                                    if (!empty($ticket ['status'])) {
                                        $integratedStatusId = $ticket ['status']['id'];
                                        $statusName = $ticket ['status']['name'];
                                        $dataTicket ['status_id'] =  $this->statusRepository
                                            ->getStatusId($integratedStatusId, $statusName, 3);
                                    }
                                    if (!empty($ticket['subject'])) {
                                        $dataTicket['title'] = $ticket['subject'];
                                    }
                                    if (!empty($ticket['priority'])) {
                                        $dataTicket ['priority_id'] = $this->priorityRepository
                                        ->getPriorityId($ticket['priority'], 3);
                                    }
                                    if (!empty( $ticket ['parent'])) {
                                        $dataTicket ['integrated_parent_id'] = $ticket ['parent']['id'];
                                    }

                                    if (!empty( $ticket ['estimated_hours'])) {
                                        $dataTicket ['estimate_time'] = $ticket ['estimated_hours'];
                                    }

                                    if (!empty($ticket ['start_date'])) {
                                        $dataTicket ['start_date'] = date('Y-m-d H:i:s', strtotime($ticket ['start_date']));
                                    } else {
                                        $dataTicket ['start_date'] = $integratedCreatedAt;
                                    }

                                    if (!empty($ticket ['due_date'])) {
                                        $dataTicket ['due_date'] = date('Y-m-d H:i:s', strtotime($ticket ['due_date']));
                                    }

                                    if (!empty($ticket ['done_ratio'])) {
                                        $dataTicket ['progress'] = $ticket ['done_ratio'];
                                    }

                                    if (!empty($ticket ['category'])) {
                                        $dataTicket ['category'] = serialize($ticket ['category']);
                                    }
                                    if (!empty($ticket['fixed_version'])) {
                                        $dataTicket['version_id'] = $this->projectVersionRepository->getVersionId(
                                                $ticket['fixed_version']['id'],
                                                $ticket['fixed_version']['name'],
                                                $projectId, 3);
                                    }
                                    if (!empty($ticket['assigned_to']['id'])) {
                                        $pUser = $this->saveUserInfo($ticket['assigned_to']['id'], $projectId, 1);
                                        $pUserId = $pUser['user_id'];
                                        $dataTicket['assign_to_user'] = $pUser['user_name'];
                                    }

                                    if (!empty($ticket['author']['id'])) {
                                        $author = $this->saveUserInfo($ticket['author']['id']);
                                        $dataTicket['created_by_user'] = $author['user_name'];
                                    }

                                    if (!empty($ticket ['tracker'])) {
                                        $dataTicket ['ticket_type_id'] = $this->ticketTypeRepository
                                                ->getTicketTypeId( $ticket ['tracker'] ['id'],
                                                        $ticket ['tracker'] ['name'], 3);
                                    }
                                    $dataTicket ['project_id'] = $projectId;
                                    $ticketId = $this->ticketRepository->save($dataTicket);
                                    $this->locRepository->saveloc($projectId, $ticketId,
                                            $pUserId, $loc, $integratedCreatedAt, $integratedUpdatedAt);
                                    $this->crawlerUrlRepository->saveToCrawlerUrls($crawlerTypeId,
                                            $ticketId, $ticket, 'Ticket Redmine 02', 'issue');
                                }
                                $this->adjuster->adjust(1);
                            } catch (Exception $e) {
                                $crawlerUrl = $this->crawlerUrlRepository
                                    ->findCrawUrlByAttributes('crawler_type_id', $crawlerTypeId,
                                        'target_id', $projectId, 'url', 'project');
                                if (!empty($crawlerUrl)) {
                                    $errorCount = $crawlerUrl->errors_count;
                                    $crawlerErr['status_code'] = app('Illuminate\Http\Response')->status();
                                    $crawlerErr['errors_count'] = ++$errorCount;
                                    $crawlerErr['errors_message'] = $e->getMessage();
                                    $this->crawlerUrlRepository->updateWithError($crawlerErr, $crawlerUrl->id);
                                }
                                print_r ( $e->getMessage () );
                            }
                        }
                    }
                }
            }
        }
        if ($ticketCount == $params['limit']) {
            $params['offset']+= ($params['limit']);
            $this->getTickets($params, $crawlerType, $projectId);
        }
    }

    /**
     * Update ticket daily
     * @author tampt6722
     *
     */
    protected function updateTicketsDaily() {
        try {
            $crawlerType = $this->argument('crawler_type');
            $today = Carbon::now()->toDateTimeString();
            $crawlerTypeObj = $this->crawlerTypeRepository->findByAttribute('name', $crawlerType);
            if (!empty($crawlerTypeObj)) {
                $crawlerTypeId = $crawlerTypeObj->id;
                $tickets = $this->crawlerUrlRepository->getTicketNeedUpdate($today,'issue', $crawlerTypeId);
                if (count($tickets) > 0) {
                    foreach ($tickets as $tk) {
                        try {

                            $this->adjuster->lap();
                            $crawlerUrlId = $tk->crawler_urls_id;
                            $dataTicket = [];
                            $ticketId = $tk->id;
                            $projectId = $tk->project_id;
                            $integratedProjectId = $tk->integrated_project_id;
                            $updatedAt = $tk->integrated_updated_at;
                            $ticketUpdate = $this->client->issue
                            ->show($tk->integrated_ticket_id);
                            if (!empty($ticketUpdate)){
                                foreach ($ticketUpdate as $ticket) {
                                    $integratedCreatedAt = date('Y-m-d H:i:s', strtotime($ticket ['created_on']));
                                    $newUpdated = date('Y-m-d H:i:s', strtotime($ticket['updated_on']));
                                    if ($newUpdated > $updatedAt) {
                                        $dataTicket ['integrated_created_at'] = $integratedCreatedAt;
                                        $dataTicket['integrated_updated_at'] = $newUpdated;
                                        if (!empty($ticket ['status'])) {
                                            $dataTicket['status_id'] = $this->statusRepository
                                            ->getStatusId($ticket ['status']['id'],
                                                    $ticket ['status']['name'], 3);
                                        }
                                        if (!empty($ticket['subject'])) {
                                            $dataTicket['title'] = $ticket['subject'];
                                        }
                                        if (!empty($ticket['priority'])) {
                                            $dataTicket ['priority_id'] = $this->priorityRepository
                                            ->getPriorityId($ticket['priority'], 3);
                                        }

                                        if (!empty( $ticket ['parent'])) {
                                            $dataTicket ['integrated_parent_id'] = $ticket ['parent']['id'];
                                        }
                                        if (!empty( $ticket ['estimated_hours'])) {
                                            $dataTicket ['estimate_time'] = $ticket ['estimated_hours'];
                                        }

                                        if (!empty($ticket ['start_date'])) {
                                            $dataTicket ['start_date'] = date('Y-m-d H:i:s', strtotime($ticket ['start_date']));
                                        } else {
                                            $dataTicket ['start_date'] = date('Y-m-d H:i:s', strtotime($ticket ['created_on']));
                                        }

                                        if (!empty($ticket ['due_date'])) {
                                            $dataTicket ['due_date'] = date('Y-m-d H:i:s', strtotime($ticket ['due_date']));
                                        }

                                        if (!empty($ticket ['done_ratio'])) {
                                            $dataTicket ['progress'] = $ticket ['done_ratio'];
                                        }

                                        if (!empty($ticket ['category'])) {
                                            $dataTicket ['category'] = serialize($ticket ['category']);
                                        }

                                        if (!empty($ticket['fixed_version'])) {
                                            $dataTicket['version_id'] = $this->projectVersionRepository
                                            ->getVersionId($ticket['fixed_version']['id'],
                                                    $ticket['fixed_version']['name'],
                                                    $projectId, 3);
                                        }

                                        if (!empty($ticket['assigned_to']['id'])) {
                                            $pUser = $this->saveUserInfo($ticket['assigned_to']['id'], $projectId, 1);
                                            $pUserId = $pUser['user_id'];
                                            $dataTicket['assign_to_user'] = $pUser['user_name'];
                                        }

                                        if (!empty($ticket['author']['id'])) {
                                            $author = $this->saveUserInfo($ticket['author']['id']);
                                            $dataTicket['created_by_user'] = $author['user_name'];
                                        }

                                        if (!empty($ticket ['tracker'])) {
                                            $dataTicket ['ticket_type_id'] = $this->ticketTypeRepository->getTicketTypeId(
                                                    $ticket ['tracker'] ['id'],
                                                    $ticket ['tracker'] ['name'], 3);
                                        }
                                        $t = $this->ticketRepository->update($dataTicket, $ticketId);
                                        $this->crawlerUrlRepository->updateCrawlerUrl($crawlerUrlId, $ticket, $today);
                                        $this->line('Updated '. $ticket['subject']);
                                    }
                                }
                            } else {
                                $this->ticketRepository->delete($ticketId);
                                $checkLoc = $this->locRepository->findByAttribute('ticket_id', $ticketId);
                                if (count($checkLoc) > 0) {
                                    $this->locRepository->delete($checkLoc->id);
                                }
                                $checkEntries = $this->entryRepository->getDataByAttribute('ticket_id', $ticketId);
                                if (count($checkEntries) > 0) {
                                    foreach ($checkEntries as $e) {
                                        $this->entryRepository->delete($e->id);
                                    }
                                }
                                $this->line('Deleted ticket: '. $ticketId);
                            }
                            $this->adjuster->adjust(1);
                        } catch (Exception $e) {
                            $errorCount = $tk->errors_count;
                            $crawlerErr['status_code'] = app('Illuminate\Http\Response')->status();
                            $crawlerErr['errors_count'] = ++$errorCount;
                            $crawlerErr['errors_message'] = $e->getMessage();
                            $this->crawlerUrlRepository->updateWithError($crawlerErr, $crawlerUrlId);
                            print_r($e->getMessage());
                        }
                    }
                }
            } else {
                $this->error("Wrong crawler type! Please, enter again!");
            }
        } catch ( Exception $e ) {
            print_r($e->getMessage());
        }

    }
}