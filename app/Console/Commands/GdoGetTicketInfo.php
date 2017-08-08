<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Exception;
use Carbon\Carbon;

/**
 * Get tickets from  Gdo Redmine
 *
 * Sep 29, 201610:36:31 AM
 *
 * @author tampt6722
 *
 */
class GdoGetTicketInfo extends CommandGDORedmine {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gdo_ticket_info:get {crawler_type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'GDO ticket get info';

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
                $projects = $this->projectRepository->getProjectsByAttribute('source_id', 2, 1);
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
                                            'sort' => 'id',
                                            'created_on' => '>=2017-01-01',
                            ];
                            $params1 = [
                                            'project_id' => $integratedProjectId,
                                            'limit' => 100,
                                            'offset' => 0,
                                            'sort' => 'id',
                                            'status_id' => 'closed',
                                            'created_on' => '>=2017-01-01',
                            ];
                            $paramEntry =  [
                                            'project_id' => $integratedProjectId,
                                            'limit' => 100,
                                            'offset' => 0,
                                            'sort' => 'id'
                            ];
                            $this->getVersions($integratedProjectId, $projectId);
                            $this->getTickets($params, $crawlerType, $projectId);
                            $this->getTickets($params1, $crawlerType, $projectId);
                            $this->getTimeEntries($projectId, $paramEntry, 2);

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
                            print_r ( $e->getMessage () );
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
            print_r ( $e->getMessage () );
        }
    }
}
