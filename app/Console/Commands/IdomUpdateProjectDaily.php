<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use atomita\Backlog;
use atomita\BacklogException;
use Carbon\Carbon;


/**
 * Update Backlog projects daily
 *
 * @author tampt6722
 *
 */
class IdomUpdateProjectDaily extends CommandIdomBacklog
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idom_update_project_daily:get {crawler_type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Project updates info';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $today = Carbon::now()->toDateTimeString();
        $crawlerType = $this->argument('crawler_type');
        $crawlerTypeObj = $this->crawlerTypeRepository->findByAttribute('name', $crawlerType);
        if (!empty($crawlerTypeObj)) {
            $crawlerTypeId = $crawlerTypeObj->id;
            $projects = $this->crawlerUrlRepository->getProjectNeedUpdate($today,'projects', $crawlerTypeId);
            if (count($projects) > 0) {
                foreach ($projects as $project) {
                    $this->adjuster->lap();
                    $data = [];
                    $crawlerUrl = [];
                    $crawlerErr = [];
                    try {
                        $projectUpdate = $this->backlog->projects
                            ->param($project->project_id)->get();
                        $data['project_key'] = $projectUpdate['projectKey'];
                        $data['flag'] = 2;
                        $p = $this->projectRepository->updateProjectFromCrawler($data, $project->projectId);
                        if ($p){
                            $crawlerUrlId = $project->crawler_url_id;
                            $crawlerUrl['content'] = serialize($projectUpdate);
                            $crawlerUrl['result'] = true;
                            $crawlerUrl['last_crawled_date'] = $today;
                            $crawlerUrl['next_crawled_date'] = Carbon::now()->addDay()->toDateTimeString();
                            $this->crawlerUrlRepository->update($crawlerUrl, $crawlerUrlId);
                        }
                        $this->info('Update project: ' . $project->name . ' success!');

                    } catch (BacklogException $e) {
                        $crawlerUrlId = $project->crawler_url_id;
                        $errorCount = $project->errors_count;
                        $crawlerErr['status_code'] = $e->getResponse();
                        $crawlerErr['errors_count'] = ++$errorCount;
                        $crawlerErr['errors_message'] = $e->getMessage();
                        $this->crawlerUrlRepository->updateWithError($crawlerErr, $crawlerUrlId);
                        print_r($e->getMessage());
                    }
                    $this->adjuster->adjust(1);
                }
            } else {
                $this->error("Empty Projects");
            }
        } else {
            $this->error('Wrong crawler type!');
        }
    }
}
