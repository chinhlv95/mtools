<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Exception;
use Illuminate\Http\Response;
use Carbon\Carbon;

/**
 * Get all tickets from Redmine 02
 *
 * Oct 3, 20133:18:03 PM
 * @author tampt6722
 *
 */
class Redmine02GetTicketInfo extends CommandRedmine02
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '02_ticket_info:get {crawler_type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Redmine 02 ticket get info';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        try {
            $crawlerType = $this->argument( 'crawler_type' );
            $checkCrawlerType = $this->crawlerTypeRepository
                ->findByAttribute('name', $crawlerType);
            if (count($checkCrawlerType) > 0) {
                $crawlerTypeId = $checkCrawlerType->id;
                $projects = $this->projectRepository->getProjectsByAttribute('source_id', 3, 1);
                if (count($projects) > 0) {
                    foreach($projects as $project ) {
                        try {
                            $projectId = $project->id;
                            $integratedProjectId = $project->project_id;
                            $this->line('Project '.$project->name );
                            $params = [
                                            'project_id' => $integratedProjectId,
                                            'limit' => 100,
                                            'offset' => 0,
                                            'created_on' => '>=2017-01-01',
                                            'sort' => 'id'
                            ];
                            $params1 = [
                                            'project_id' => $integratedProjectId,
                                            'limit' => 100,
                                            'offset' => 0,
                                            'sort' => 'id',
                                            'status_id' => 'closed'
                            ];
                            $this->getVersions($integratedProjectId, $projectId);
                            $this->getTickets($params, $crawlerType, $projectId);
                            $this->getTickets($params1, $crawlerType, $projectId);
                            $paramEntry =  [
                                            'project_id' => $integratedProjectId,
                                            'limit' => 100,
                                            'offset' => 0,
                                            'created_on' => '>=2017-01-01',
                                            'sort' => 'id'
                            ];
                            $this->getTimeEntries($projectId, $paramEntry, 3);

                        } catch (Exception $e) {
                            $crawlerErr = [];
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
                            print_r($e->getMessage());
                        }
                    }
                    $this->info ( 'Success!' );
                } else {
                    $this->error('Empty project!');
                }
            } else {
                $this->error("Wrong crawler type!");
            }
        } catch ( Exception $e ) {
            print_r($e->getMessage());
        }
    }
}