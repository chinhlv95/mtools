<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use atomita\Backlog;
use atomita\BacklogException;
use Carbon\Carbon;
use Illuminate\Http\Response;

/**
 * update Backlog projects firstly
 *
 * @author tampt6722
 *
 */
class IdomUpdateProjectFirstly extends CommandIdomBacklog
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idom_update_project_firstly:get {crawler_type}';

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
        $crawlerType = $this->argument('crawler_type');
        try {
            $projects = $this->projectRepository->getProjectsToUpdateFirstly('source_id', 1);
            if (count($projects)>0) {
                foreach ($projects as $project) {
                    $this->adjuster->lap();
                    $data = [];
                    $projectId = $project->id;
                    $projectName = $project->name;
                    if (!empty($project->project_id)) {
                        try {
                            $projectUpdate = $this->backlog->projects
                                ->param($project->project_id)->get();
                            $data['flag'] = 1;
                            $data['project_key'] = $projectUpdate['projectKey'];
                            $p = $this->projectRepository->updateProjectFromCrawler($data, $projectId);
                            if ($p){
                                $this->saveProjectToCrawlerUrls($crawlerType, $projectId, $projectUpdate, $projectName);
                            }
                            $this->info('update project ' . $projectName . ' success!');
                        } catch (BacklogException $e) {
                            $data['sync_flag'] = 2;
                            $this->projectRepository->updateProjectFromCrawler($data, $projectId);
                            print_r($e->getMessage());
                        }
                    } elseif (!empty($project->project_key)){
                        try {
                            $projectUpdate = $this->backlog->projects
                                ->param($project->project_key)->get();
                            $data['project_id'] = $projectUpdate['id'];
                            $data['flag'] = 1;
                            $p = $this->projectRepository->updateProjectFromCrawler($data, $projectId);
                            if ($p){
                                $this->saveProjectToCrawlerUrls($crawlerType, $projectId, $projectUpdate, $projectName);
                            }
                            $this->info('update project ' . $projectName . ' success!');
                        } catch (BacklogException $e) {
                            $data['sync_flag'] = 2;
                            $this->projectRepository->updateProjectFromCrawler($data, $projectId);
                            print_r($e->getMessage());
                        }
                    }
                    $this->adjuster->adjust(1);
                }
            } else {
                $this->error("Empty Projects");
            }
        } catch (BacklogException $e) {
            $this->error('Error!');
            print_r( $e->getResponse());
        }
    }

    /**
     * Save crawler url data
     * @author tampt6722
     *
     * @param string $crawlerType
     * @param string $projectName
     * @param integer $ticketId
     * @param array $ticket
     * @return void
     */
    private function saveProjectToCrawlerUrls($crawlerType,$projectId,$projectUpdate, $projectName){
        $crawlerTypeId = $this->crawlerTypeRepository
            ->findByAttribute('name', $crawlerType)->id;
        $checkCrawlerUrl = $this->crawlerUrlRepository
            ->findCrawUrlByAttributes('crawler_type_id', $crawlerTypeId,
                'target_id', $projectId, 'url', 'projects');
        if (count($checkCrawlerUrl) == 0) {
            $crawlerUrl['crawler_type_id'] = $crawlerTypeId;
            $crawlerUrl['target_id'] = $projectId;
            $crawlerUrl['content_type'] = "Project Backlog";
            $crawlerUrl['last_modified'] = "";
            $crawlerUrl['etag'] = "";
            $crawlerUrl['title'] = $projectName;
            $crawlerUrl['url']  = 'projects';
            $crawlerUrl['content'] = serialize($projectUpdate);
            $crawlerUrl['result'] = true;
            $crawlerUrl['time_out'] = 0;
            $crawlerUrl['last_crawled_date'] = Carbon::now()->toDateTimeString();
            $crawlerUrl['next_crawled_date'] = Carbon::now()->addDay()->toDateTimeString();
            $crawlerUrl['status_code'] = app('Illuminate\Http\Response')->status();
            $crawlerUrl['errors_count'] = 0;
            $crawlerUrl['errors_message'] = "Not error";
            $crawlerUrl['stop_flg'] = 0;
            $crawlerUrl['hidden_flg'] = 0;
            $this->crawlerUrlRepository->save($crawlerUrl);
        }
    }
}
