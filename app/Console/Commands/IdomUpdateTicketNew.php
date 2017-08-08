<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use atomita\Backlog;
use atomita\BacklogException;
use Carbon\Carbon;

/**
 * Update ticket yesterday from Idom Backlog
 *
 * @author tampt6722
 *
 */
class IdomUpdateTicketNew extends CommandIdomBacklog
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idom_update_new_ticket:get {crawler_type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Add ticket created  yesterday";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $crawlerType = $this->argument('crawler_type');
            $yesterday = Carbon::now()->subDay()->setTimezone('UTC')->toDateString();
            $checkCrawlerType = $this->crawlerTypeRepository
            ->findByAttribute('name', $crawlerType);
            if (count($checkCrawlerType) > 0) {
                $crawlerTypeId = $checkCrawlerType->id;
                $projects = $this->projectRepository->getProjectsByAttribute(
                                                           'source_id', 1, 2);
                if (count($projects) > 0) {
                    foreach ($projects as $project) {
                        $crawlerErr = [];
                        try {
                            $projectId = $project->id;
                            $projectName = $project->name;
                            $integratedProId = $project->project_id;
                            $params =  [
                                            'projectId[]' => $integratedProId,
                                            'createdSince'=>$yesterday,
                                            'count' => 100,
                                            'offset' => 0
                            ];
                            $this->getTickets($params, $projectId, $crawlerType);
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
                    $this->info('Success!');
                } else {
                    $this->error("Empty project");
                }
            } else {
                $this->error("Wrong crawler type!");
            }
        } catch (BacklogException $e) {
            $this->error('Error!');
            print_r( $e->getMessage());
        }
    }
}
