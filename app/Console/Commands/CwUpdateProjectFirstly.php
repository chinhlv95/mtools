<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Response;

/**
 * Update CoWell projects firstly
 *
 * @author tampt6722
 *
 */
class CwUpdateProjectFirstly extends CommandCwRedmine
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cw_update_project_firstly:get {crawler_type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CoWell Project updates info';

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
                $projects = $this->projectRepository->getProjectsToUpdateFirstly(
                                                            'source_id', 4);
                if (count($projects) > 0) {
                    foreach ($projects as $project) {
                        $this->adjuster->lap();
                        $data = [];
                        $projectName = $project->name;
                        $projectId = $project->id;
                        if (!empty($project->project_id)) {
                            try {
                                $tStart = Carbon::now()->toDateTimeString();
                                $this->line('Update firstly Time Start: '. $tStart);
                                $projectUpdate = $this->client->project->show($project->project_id);
                                if (!empty($projectUpdate)) {
                                    foreach ($projectUpdate as $pro) {
                                        $data['flag'] = 1;
                                        $data['project_key'] = $pro['identifier'];
                                        $this->projectRepository->updateProjectFromCrawler($data, $projectId);
                                        $this->saveProjectToCrawlerUrls($crawlerType, $projectId, $pro, $projectName);
                                        $tEnd = Carbon::now()->toDateTimeString();
                                        $this->line('Update firstly Time End: '. $tEnd);
                                        $this->info('update project ' . $projectName . ' success!');
                                    }
                                } else {
                                    $dataUpdate['active'] = 0;
                                    $dataUpdate['sync_flag'] = 0;
                                    $this->projectRepository->updateProjectFromCrawler($dataUpdate, $projectId);
                                    $this->info('Project ' . $projectName . ' has been inactive!');
                                }
                            } catch (Exception $e) {
                                $data['sync_flag'] = 2;
                                $this->projectRepository->updateProjectFromCrawler($data, $projectId);
                                print_r($e->getMessage());
                            }

                        } elseif (!empty($project->project_key)){
                            try {
                                $tStart = Carbon::now()->toDateTimeString();
                                $this->line('Update firstly Time Start: '. $tStart);
                                $projectUpdate = $this->client->project->show($project->project_key);
                                if (!empty($projectUpdate)) {
                                    foreach ($projectUpdate as $pro){
                                        $data['flag'] = 1;
                                        $data['project_id'] = $pro['id'];
                                        $this->projectRepository->updateProjectFromCrawler($data, $projectId);
                                        $this->saveProjectToCrawlerUrls($crawlerType, $projectId, $pro, $projectName);
                                        $tEnd = Carbon::now()->toDateTimeString();
                                        $this->line('Update firstly Time End: '. $tEnd);
                                        $this->info('update project ' . $pro['name'] . ' success!');
                                    }
                                } else {
                                    $dataUpdate['active'] = 0;
                                    $dataUpdate['sync_flag'] = 0;
                                    $this->projectRepository->updateProjectFromCrawler($dataUpdate, $projectId);
                                    $this->info('Project ' . $projectName . ' has been inactive!');
                                }
                            } catch (Exception $e) {
                                $data['sync_flag'] = 2;
                                $this->projectRepository->updateProjectFromCrawler($data, $projectId);
                                print_r($e->getMessage());
                            }
                        }
                        $this->adjuster->adjust(1);
                    }
                } else {
                    $this->error("Empty project!");
                }
            } else {
                $this->error("Wrong crawler type!");
            }
        } catch (Exception $e) {
            $this->error('Error!');
            print_r( $e->getMessage());

        }
    }

    /**
     * Save crawler url data
     * @author tampt6722
     *
     * @param string $crawlerType
     * @param string $projectName
     * @param integer $projectId
     * @param array $projectUpdate
     * @return void
     */
    private function saveProjectToCrawlerUrls($crawlerType, $projectId, $projectUpdate, $projectName){
        $crawlerTypeId = $this->crawlerTypeRepository
            ->findByAttribute('name', $crawlerType)->id;
        $checkCrawlerUrl = $this->crawlerUrlRepository
            ->findCrawUrlByAttributes('crawler_type_id', $crawlerTypeId,
                'target_id', $projectId, 'url', 'project');
        if (count($checkCrawlerUrl) == 0) {
            $crawlerUrl['crawler_type_id'] = $crawlerTypeId;
            $crawlerUrl['target_id'] = $projectId;
            $crawlerUrl['content_type'] = "Project D5";
            $crawlerUrl['last_modified'] = "";
            $crawlerUrl['etag'] = "";
            $crawlerUrl['title'] = $projectName;
            $crawlerUrl['url']  = 'project';
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
