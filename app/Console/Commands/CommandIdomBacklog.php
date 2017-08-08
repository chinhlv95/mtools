<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use atomita\Backlog;
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
use App\Repositories\ProjectMember\ProjectMemberRepositoryInterface;
use App\Repositories\Status\StatusRepositoryInterface;
use App\Repositories\Crawler\CrawlerTypeRepository;
use App\Repositories\Priority\PriorityRepositoryInterface;
use App\Repositories\BugWeight\BugWeightRepositoryInterface;
use App\Repositories\BugType\BugTypeRepositoryInterface;
use App\Repositories\RootCause\RootCauseRepositoryInterface;
use App\Repositories\Activity\ActivityRepositoryInterface;
use atomita\BacklogException;
use App\Repositories\Loc\LocRepositoryInterface;


class CommandIdomBacklog extends Command {

    public $backlog = null;
    public $adjuster = null;
    public function __construct(CrawlerTypeRepositoryInterface $crawlerType,
            CrawlerUrlRepositoryInterface $crawlerUrl,
            ProjectRepositoryInterface $project,
            UserRepositoryInterface $user,
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
            ActivityRepositoryInterface $activity,
            LocRepositoryInterface $loc) {
        Cache::flush();
        $this->backlog = new Backlog(
                env('BACKLOG_SPACE_NAME', ''),
                env('BACKLOG_API_KEY', ''));
        $this->crawlerTypeRepository = $crawlerType;
        $this->crawlerUrlRepository = $crawlerUrl;
        $this->projectRepository = $project;
        $this->userRepository = $user;
        $this->ticketRepository = $ticket;
        $this->ticketTypeRepository = $ticketType;
        $this->entryRepository = $entry;
        $this->projectVersionRepository = $projectVersion;
        $this->projectMemberRepository = $projectMember;
        $this->statusRepository = $status;
        $this->priorityRepository = $priority;
        $this->bugTypeRepository = $bugType;
        $this->bugWeightRepository = $bugWeight;
        $this->rootCauseRepository = $rootCause;
        $this->activityRepository = $activity;
        $this->locRepository = $loc;
        $this->adjuster = new IntervalAdjuster();
        parent::__construct();
    }

    /**
     * Get version id
     * @author tampt6722
     *
     * @param array $milestone
     * @return integer
     */
    protected function getVersionId($milestone, $projectId) {
        $existedProjectVersion = $this->projectVersionRepository
        ->findByAttributes('integrated_version_id', $milestone['id'],
                'source_id', 1);
        if (count($existedProjectVersion) == 0) {
            $dataVersion['integrated_version_id'] = $milestone['id'];
            $dataVersion['name'] = $milestone['name'];
            $dataVersion['project_id'] = $projectId;
            $dataVersion['source_id'] = 1;
            $dataVersion['description'] =  $milestone['description'];
            $dataVersion['start_date'] =  date('Y-m-d H:i:s', strtotime($milestone['startDate']));
            $dataVersion['end_date'] =  date('Y-m-d H:i:s', strtotime($milestone['releaseDueDate']));
            $this->projectVersionRepository->save($dataVersion);
            $versionId = $this->projectVersionRepository
            ->findByAttributes('integrated_version_id', $milestone['id'],
                    'source_id', 1)->id;
        } else {
            $versionId = $existedProjectVersion->id;
        }
        return $versionId;
    }

    /**
     * Get tickets
     * @author tampt6722
     *
     * @param array $params
     * @param integer $projectId
     * @param string $crawlerType
     * @return void
     */
    protected function getTickets($params = [], $projectId, $crawlerType){
        $this->line('Get tickets limit: ' . $params['count'] . ', offset: ' . $params['offset']);
        $tickets = $this->backlog->issues->get($params);
        $ticketCount = count($tickets);
        if ($ticketCount > 0) {
           foreach ($tickets as $ticket){
                try {
                    $dataTicket = [];
                    $dataEntry = [];
                    $activityId = 0;
                    $loc = 0;
                    $pUserId = 0;
                    $integratedCreatedAt = date('Y-m-d H:i:s', strtotime($ticket ['created']));
                    $integratedUpdatedAt = date('Y-m-d H:i:s', strtotime($ticket ['updated']));
                    $crawlerTypeId = $this->crawlerTypeRepository
                        ->findByAttribute('name', $crawlerType)->id;
                    $integratedTicketId = $ticket['id'];
                    $checkVars = [              'project_id' => $projectId,
                                                'integrated_ticket_id' => $integratedTicketId,
                                                'source_id' => 1
                                ];
                    $existedTicket = $this->ticketRepository
                            ->findByManyAttributes($checkVars);
                    if (count($existedTicket) == 0) {
                        $dataTicket['integrated_ticket_id'] = $integratedTicketId;
                        $dataTicket ['integrated_created_at'] = $integratedCreatedAt;
                        $dataTicket ['integrated_updated_at'] = $integratedUpdatedAt;
                        if (!empty($ticket['parentIssueId'])) {
                            $dataTicket['integrated_parent_id'] = $ticket['parentIssueId'];
                        }
                        $dataTicket['source_id'] = 1;
                        if (!empty($ticket ['status'])) {
                            $dataTicket['status_id'] = $this->statusRepository
                            ->getStatusId($ticket ['status']['id'],
                                    $ticket ['status']['name'], 1);
                        }
                        if (!empty($ticket['priority'])) {
                            $dataTicket ['priority_id'] = $this->priorityRepository
                            ->getPriorityId($ticket['priority'], 1);
                        }
                        if (!empty($ticket['summary'])) {
                            $dataTicket['title'] = $ticket['summary'];
                        }
                        if (!empty($ticket['issueType'])) {
                            $dataTicket['ticket_type_id']  = $this->ticketTypeRepository->getTicketTypeId(
                                    $ticket['issueType']['id'], $ticket['issueType']['name'], 1);
                        }

                        if (!empty($ticket['category'])) {
                            $dataTicket['category'] = serialize($ticket['category']);
                        }
                        if (!empty($ticket['milestone'])) {
                            $milestone = array_shift($ticket['milestone']); // get the first milestone
                            $dataTicket['version_id'] = $this->getVersionId($milestone, $projectId);

                        }
                        if (!empty($ticket['estimatedHours'])) {
                            $dataTicket['estimate_time'] = $ticket['estimatedHours'];
                        }

                        if (!empty($ticket['startDate'])) {
                            $dataTicket['start_date'] = date('Y-m-d H:i:s', strtotime($ticket['startDate']));
                        } else {
                            $dataTicket ['start_date'] = $integratedCreatedAt;
                        }

                        if (!empty($ticket['dueDate'])) {
                            $dataTicket['due_date'] = date('Y-m-d H:i:s', strtotime($ticket['dueDate']));
                        }

                        if (!empty($ticket['createdUser']['name'])){
                            $createdName = $ticket['createdUser']['name'];
                            $dataTicket['created_by_user'] = $createdName;
                            $this->userRepository->saveUserByUsername(
                                    $createdName,
                                    $ticket['createdUser']['mailAddress'],
                                    $createdName,'', 1);
                        }
                        if (!empty($ticket['assignee']['name'])){
                            $assigneeName = $ticket['assignee']['name'];
                            $dataTicket['assign_to_user'] = $assigneeName;
                            $pUserId = $this->userRepository->saveUserByUsername(
                                    $assigneeName,
                                    $ticket['assignee']['mailAddress'],
                                    $assigneeName, '', 1);
                            $this->projectMemberRepository->saveProjectMember($projectId, $pUserId, 2);
                        }

                        if (!empty($ticket['customFields'])) {
                            $cusFields = $this->getCustomFields($ticket['customFields']);
                            if (!empty($cusFields['bug_type_id'])) {
                                $dataTicket['bug_type_id'] = $cusFields['bug_type_id'];
                            }
                            if (!empty($cusFields['bug_weight_id'])) {
                                $dataTicket['bug_weight_id'] = $cusFields['bug_weight_id'];
                            }
                            if (!empty($cusFields['activity_id'])) {
                                $activityId = $cusFields['activity_id'];
                            }
                            if (!empty($cusFields['loc'])) {
                                $loc = $cusFields['loc'];
                            }
                            if (!empty($cusFields['test_case'])) {
                                $dataTicket['test_case'] = $cusFields['test_case'];
                            }
                        }

                        $dataTicket['project_id'] = $projectId;
                        $ticketId = $this->ticketRepository->save($dataTicket);

                        if (isset($ticket['updatedUser']['mailAddress'])) {
                            $saveUpdatedUser =  $this->userRepository->saveUserByUsername(
                                    $ticket['updatedUser']['name'],
                                    $ticket['updatedUser']['mailAddress'],
                                    $ticket['updatedUser']['name'],'', 1);
                        }

                        $this->locRepository->saveloc($projectId, $ticketId,
                            $pUserId, $loc, $integratedCreatedAt, $integratedUpdatedAt);
                        // Get time entry
                        $dataEntry['integrated_ticket_id'] = $integratedTicketId;
                        $dataEntry['user_id'] = $pUserId;
                        $dataEntry['project_id'] = $projectId;
                        $dataEntry['ticket_id'] = $ticketId;
                        $dataEntry['activity_id'] = $activityId;
                        $paramEntry = [
                                        'count' => 100,
                                        'offset' => 0
                        ];
                        $this->getTimeEntries($dataEntry, $paramEntry);
                        // Save crawler url
                        $this->crawlerUrlRepository->saveToCrawlerUrls($crawlerTypeId,
                                $ticketId, $ticket, 'Ticket Backlog', 'issues');
                    }
                } catch (BacklogException $e) {
                    $crawlerErr = [];
                    $crawlerUrl = $this->crawlerUrlRepository
                    ->findCrawUrlByAttributes('crawler_type_id', $crawlerTypeId,
                            'target_id', $projectId, 'url', 'projects');
                    if (!empty($crawlerUrl)) {
                        $errorCount = $crawlerUrl->errors_count;
                        $crawlerErr['status_code'] = $e->getResponse();
                        $crawlerErr['errors_count'] = ++$errorCount;
                        $crawlerErr['errors_message'] = $e->getMessage();
                        $this->crawlerUrlRepository->updateWithError($crawlerErr, $crawlerUrl->id);
                    }
                    print_r ( $e->getMessage () );
                }
            }
            if ($ticketCount == $params['count']) {
                $params['offset']+= ($params['count']);
                $this->getTickets($params, $projectId, $crawlerType);
            }
        }
    }

    /**
     * Get custom fields of Backlog
     * @author tampt6722
     *
     * @param array $ticketCusFields
     * @return number[]|string[]
     */
    protected function getCustomFields($ticketCusFields){
        $dataTicket = [];
        $locAdd = 0;
        $locModify = 0;
        $locDelete = 0;
        foreach ($ticketCusFields as $customFields) {
            if ($customFields['name'] == 'Action') {
                if (!empty($customFields['value'])) {
                    $dataTicket['activity_id'] = $this->activityRepository
                                    ->getActivityId($customFields['value'], 1);
                }
            }
            if ($customFields['name'] == 'Bug Type') {
                if (!empty($customFields['value'])) {
                    $dataTicket['bug_type_id'] =  $this->bugTypeRepository
                            ->getBugTypeId($customFields['value']['name'], 1);
                }
            }
            if ($customFields['name'] == 'Bug Weight') {
                if (!empty($customFields['value'])) {
                    $dataTicket['bug_weight_id'] =  $this->bugWeightRepository
                        ->getBugWeightId($customFields['value']['name'], 1);
                }
            }
            if ($customFields['name'] == 'Related TestCase') {
                if (!empty($customFields['value'])) {
                    $dataTicket['test_case'] =  $customFields['value'];
                }
            }

            if ($customFields['name'] == "Line of code to Add") {
                if (!empty($customFields['value'])) {
                    $locAdd =  $customFields['value'];
                }
            }
            if ($customFields['name'] == 'Line of code to Modify') {
                if (!empty($customFields['value'])) {
                    $locModify =  $customFields['value'];
                }
            }
            if ($customFields['name'] == 'Line of code to Delete') {
                if (!empty($customFields['value'])) {
                    $locDelete =  $customFields['value'];
                }
            }
            $dataTicket['loc'] = $locAdd + $locDelete + $locModify;
        }
        return $dataTicket;
    }


    /**
     * Update tickets
     * @author tampt6722
     *
     * @param string $crawlerType
     * @return void
     */
    protected function updateTickets() {
        $today = Carbon::now()->toDateTimeString();
        $crawlerType = $this->argument('crawler_type');
        $crawlerTypeObj = $this->crawlerTypeRepository->findByAttribute('name', $crawlerType);
        if (!empty($crawlerTypeObj)) {
            $crawlerTypeId = $crawlerTypeObj->id;
            $tickets = $this->crawlerUrlRepository->getTicketNeedUpdate($today, 'issues', $crawlerTypeId);
            if (count($tickets) > 0) {
                foreach ($tickets as $ticket) {
                    $crawlerUrlId = $ticket->crawler_urls_id;
                    $dataTicket = [];
                    $crawlerErr = [];
                    $dataEntry = [];
                    try {
                       $this->adjuster->lap();
                        $activityId = 0;
                        $loc = 0;
                        $pUserId = 0;
                        $ticketId = $ticket->id;
                        $projectId = $ticket->project_id;
                        $updatedAt = $ticket->integrated_updated_at;
                        $integratedTicketId = $ticket->integrated_ticket_id;
                        $ticketUpdate = $this->backlog->issues
                                    ->param($integratedTicketId)->get();
                        if (!empty($ticketUpdate)) {
                            $integratedCreatedAt = date('Y-m-d H:i:s', strtotime($ticketUpdate ['created']));
                            $newUpdated = date('Y-m-d H:i:s', strtotime($ticketUpdate['updated']));
                            if ($newUpdated > $updatedAt) {
                                $dataTicket ['integrated_created_at'] = $integratedCreatedAt;
                                $dataTicket['integrated_updated_at'] = $newUpdated;
                                if (!empty($ticket['summary'])) {
                                    $dataTicket['title'] = $ticket['summary'];
                                }
                                // Update ticket type
                                if (!empty($ticketUpdate['issueType'])) {
                                    $ticketType = $ticketUpdate['issueType'];
                                    $dataTicket['ticket_type_id']  = $this->ticketTypeRepository->getTicketTypeId(
                                            $ticketType['id'], $ticketType['name'], 1);
                                }
                                // Update status
                                if (!empty($ticketUpdate ['status'])) {
                                    $dataTicket['status_id'] = $this->statusRepository
                                    ->getStatusId($ticketUpdate ['status']['id'],
                                            $ticketUpdate ['status']['name'], 1);
                                }
                                // Update version
                                if (!empty($ticketUpdate['milestone'])) {
                                    $milestone = array_shift($ticketUpdate['milestone']);
                                    $dataTicket['version_id'] = $this->updateVersionId($milestone, $projectId);
                                }

                                if (!empty($ticketUpdate['estimatedHours'])) {
                                    $dataTicket['category'] = serialize($ticketUpdate['category']);
                                }
                                if (!empty($ticketUpdate['category'])) {
                                    $dataTicket['estimate_time'] = $ticketUpdate['estimatedHours'];
                                }
                                if (!empty($ticketUpdate['startDate'])) {
                                    $dataTicket['start_date'] = date('Y-m-d H:i:s', strtotime($ticketUpdate['startDate']));
                                }
                                if (!empty($ticketUpdate['dueDate'])) {
                                    $dataTicket['due_date'] = date('Y-m-d H:i:s', strtotime($ticketUpdate['dueDate']));
                                }
                                if (!empty($ticketUpdate['createdUser']['name'])){
                                    $createdName = $ticketUpdate['createdUser']['name'];
                                    $dataTicket['created_by_user'] = $createdName;
                                    $saveCreateduser = $this->userRepository->saveUserByUsername(
                                            $createdName,
                                            $ticketUpdate['createdUser']['mailAddress'],
                                            $createdName, '', 1);
                                }
                                if (!empty($ticketUpdate['assignee']['name'])){
                                    $oldUser = $ticket->assign_to_user;
                                    $username = $ticketUpdate['assignee']['name'];
                                    if ($oldUser != $username) {
                                        $dataTicket['assign_to_user'] = $username;
                                        $pUserId = $this->userRepository->saveUserByUsername(
                                                $username,
                                                $ticketUpdate['assignee']['mailAddress'],
                                                $username, '', 1);
                                        $this->projectMemberRepository->saveProjectMember($projectId, $pUserId, 2);
                                    }
                                }
                                if (!empty($ticketUpdate['updatedUser']['name'])) {
                                    $saveUpdatedUser =  $this->userRepository->saveUserByUsername(
                                            $ticketUpdate['updatedUser']['name'],
                                            $ticketUpdate['updatedUser']['mailAddress'],
                                            $ticketUpdate['updatedUser']['name'], '', 1);
                                }

                                if (!empty($ticketUpdate['customFields'])) {
                                    $cusFields = $this->getCustomFields($ticket['customFields']);
                                    if (!empty($cusFields['bug_type_id'])) {
                                        $dataTicket['bug_type_id'] = $cusFields['bug_type_id'];
                                    }

                                    if (!empty($cusFields['bug_weight_id'])) {
                                        $dataTicket['bug_weight_id'] = $cusFields['bug_weight_id'];
                                    }

                                    if (!empty($cusFields['loc'])) {
                                        $loc = $cusFields['loc'];
                                    }
                                    if (!empty($cusFields['activity_id'])) {
                                        $activityId = $cusFields['activity_id'];
                                    }

                                    if (!empty($cusFields['test_case'])) {
                                        $dataTicket['test_case'] = $cusFields['test_case'];
                                    }
                                }

                                $this->ticketRepository->update($dataTicket, $ticketId);
                                $this->locRepository->updateLoc($projectId, $ticketId, $pUserId,
                                        $loc, $integratedCreatedAt, $newUpdated);
                                $dataEntry['integrated_ticket_id'] = $integratedTicketId;
                                $dataEntry['user_id'] = $pUserId;
                                $dataEntry['project_id'] = $projectId;
                                $dataEntry['ticket_id'] = $ticketId;
                                $dataEntry['activity_id'] = $activityId;
                                $paramEntry = [
                                                'count' => 100,
                                                'offset' => 0
                                ];
                                $this->updateTimeEntries($dataEntry, $paramEntry);
                                $this->crawlerUrlReporitory->updateCrawlerUrl($crawlerUrlId, $ticketUpdate, $today);
                                $this->line('Updated '. $ticketUpdate['issueKey']);
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
                    } catch (BacklogException $e) {
                        $errorCount = $ticket->errors_count;
                        $crawlerErr['status_code'] = $e->getResponse();
                        $crawlerErr['errors_count'] = ++$errorCount;
                        $crawlerErr['errors_message'] = $e->getMessage();
                        $this->crawlerUrlRepository->updateWithError($crawlerErr, $crawlerUrlId);
                        print_r($e->getMessage());
                    }
                }
            } else {
                $this->error("Empty Ticket");
            }
        } else {
            $this->error("Wrong crawler type! Please, enter again!");
        }
    }

    /**
     * Get version id
     * @author tampt6722
     *
     * @param array $milestone
     * @return integer
     */
    protected function updateVersionId($milestone, $projectId) {
        $existedProjectVersion = $this->projectVersionRepository
        ->findByAttributes('integrated_version_id', $milestone['id'],
                'source_id', 1);
        if (count($existedProjectVersion) == 0) {
            $dataVersion['integrated_version_id'] = $milestone['id'];
            $dataVersion['name'] = $milestone['name'];
            $dataVersion['project_id'] = $projectId;
            $dataVersion['source_id'] = 1;
            $dataVersion['description'] =  $milestone['description'];
            $dataVersion['start_date'] =  date('Y-m-d H:i:s', strtotime($milestone['startDate']));
            $dataVersion['end_date'] =  date('Y-m-d H:i:s', strtotime($milestone['releaseDueDate']));
            $this->projectVersionRepository->save($dataVersion);
            $versionId = $this->projectVersionRepository
            ->findByAttributes('integrated_version_id', $milestone['id'],
                    'source_id', 1)->id;
            return $versionId;
        } else {
            $dataVersion['name'] = $milestone['name'];
            $dataVersion['start_date'] =  date('Y-m-d H:i:s', strtotime($milestone['startDate']));
            $dataVersion['description'] =  $milestone['description'];
            $dataVersion['end_date'] =  date('Y-m-d H:i:s', strtotime($milestone['releaseDueDate']));
            $this->projectVersionRepository->update($dataVersion, $existedProjectVersion->id);
            return $existedProjectVersion->id;
        }
    }

    /**
     * Update time entries
     * @author tampt6722
     *
     * @param array $data
     * @param array $params
     * @return void
     */
    protected function updateTimeEntries($data, $params) {
        $comments = $this->backlog->issues
            ->param($data['integrated_ticket_id'])->comments->get();
        $countComment = count($comments);
        if($countComment > 0) {
            $getEntry = [];
            foreach($comments as $comment) {
                $dataEntry = [];
                $logs = $comment['changeLog'];
                if (!empty($logs)) {
                    $createdAt = date('Y-m-d H:i:s', strtotime($comment['created']));
                    $updatedAt = date('Y-m-d H:i:s', strtotime($comment['updated']));
                    foreach ($logs as $log) {
                        if ($log['field'] == 'actualHours') {
                            $getEntry[] = $createdAt;
                            $entry = $this->entryRepository->findByAttributes('ticket_id', $ticketId,
                                            'integrated_created_id', $createdAt);
                            if (count($entry) > 0) {
                                $actualHour = $log['newValue'] - $log['originalValue'];
                                $entryId = $entry->id;
                                $dataEntry['user_id'] = $data['user_id'];
                                $dataEntry['activity_id'] = $data['activity_id'];
                                $dataEntry['actual_hour'] = $actualHour;
                                $dataEntry['spent_at'] = $updatedAt;
                                $dataEntry['integrated_updated_at'] = $updatedAt;
                                $this->entryRepository->update($dataEntry, $entryId);
                            } else {
                                $actualHour = $log['newValue'] - $log['originalValue'];
                                $dataEntry['project_id'] = $data['project_id'];
                                $dataEntry['ticket_id'] = $data['ticket_id'];
                                $dataEntry['user_id'] = $data['user_id'];
                                $dataEntry['actual_hour'] = $actualHour;
                                $dataEntry['spent_at'] = $createdAt;
                                $dataEntry['activity_id'] = $data['activity_id'];
                                $dataEntry['integrated_created_at'] = $createdAt;
                                $dataEntry['integrated_updated_at'] = $updatedAt;
                                $this->entryRepository->save($dataEntry);
                            }
                            break;
                        }
                    }
                } else {
                    continue;
                }
            }
            $entries = $this->entryRepository->getDataByAttribute('ticket_id', $data['ticket_id']);
            if (count($entries) > 0) {
                foreach ($entries as $entry) {
                    if (!in_array($entry->integrated_created_at, $getEntry)) {
                        $this->entryRepository->delete($entry->id);
                    }
                }
            }

        }
        if ($countComment == $params['count']) {
            $params['offset']+= ($params['count']);
            $this->updateTimeEntries($data);
        }
    }

    /**
     * Get actual hour from backlog
     * @author tampt6722
     *
     * @param array $data
     * @param array $params
     */
    protected function getTimeEntries($data, $params)
    {
        $comments = $this->backlog->issues
                        ->param($data['integrated_ticket_id'])->comments->get();
        $countComment = count($comments);
        if($countComment > 0) {
            foreach($comments as $comment) {
                $dataEntry = [];
                $logs = $comment['changeLog'];
                if (!empty($logs)) {
                    $createdAt = date('Y-m-d H:i:s', strtotime($comment['created']));
                    $updatedAt = date('Y-m-d H:i:s', strtotime($comment['updated']));
                    foreach ($logs as $log) {
                        if ($log['field'] == 'actualHours') {
                            $actualHour = $log['newValue'] - $log['originalValue'];
                            $dataEntry['project_id'] = $data['project_id'];
                            $dataEntry['ticket_id'] = $data['ticket_id'];
                            $dataEntry['user_id'] = $data['user_id'];
                            $dataEntry['actual_hour'] = $actualHour;
                            $dataEntry['spent_at'] = $createdAt;
                            $dataEntry['activity_id'] = $data['activity_id'];
                            $dataEntry['integrated_created_at'] = $createdAt;
                            $dataEntry['integrated_updated_at'] = $updatedAt;
                            $this->entryRepository->save($dataEntry);
                            break;
                        }
                    }
                } else {
                    continue;
                }
            }
        }
        if ($countComment == $params['count']) {
            $params['offset']+= ($params['count']);
            $this->getTimeEntries($data);
        }
    }
}