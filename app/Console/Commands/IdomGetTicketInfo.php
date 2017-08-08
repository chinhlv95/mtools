<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use atomita\Backlog;
use atomita\BacklogException;
use Carbon\Carbon;
/**
 * Get ticket info from Backlog
 *
 * Sep 26, 201610:24:08 AM
 * @author tampt6722
 *
 */
class IdomGetTicketInfo extends CommandIdomBacklog
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idom_ticket_info:get {crawler_type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ticket get info';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $crawlerType = $this->argument('crawler_type');
            $checkCrawlerType = $this->crawlerTypeRepository
                ->findByAttribute('name', $crawlerType);
            if (count($checkCrawlerType) > 0) {
                $crawlerTypeId = $checkCrawlerType->id;
                $projects = $this->projectRepository->getProjectsByAttribute(
                                                         'source_id', 1, 1);

                if (count($projects)>0) {
                    foreach ($projects as $project){
                        try {
                            $projectIdIntegrated = $project->project_id;
                            $projectId = $project->id;
                            $params = [
                                            "projectId[]" => $projectIdIntegrated,
                                            'count' => 100,
                                            'offset' => 0
                            ];
                            $start = Carbon::now()->toDateTimeString();
                            $this->line('Tickets Time Start: '. $start);
                            $this->getTickets($params, $projectId, $crawlerType);
                            $end = Carbon::now()->toDateTimeString();
                            $this->line('Tickets Time End: '. $end);
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
                    $this->error("Empty Project");
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
