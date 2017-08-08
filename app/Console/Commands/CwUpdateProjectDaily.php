<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Exception;

/**
 * Update information of Co-Well projects daily
 *
 * @author tampt6722
 *
 */
class CwUpdateProjectDaily extends CommandCwRedmine
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cw_update_project_daily:get {crawler_type}';

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
        $today = Carbon::now()->toDateTimeString();
        $crawlerType = $this->argument('crawler_type');
        $crawlerTypeObj = $this->crawlerTypeRepository->findByAttribute('name', $crawlerType);
        if (!empty($crawlerTypeObj)) {
            $crawlerTypeId = $crawlerTypeObj->id;
            $projects = $this->crawlerUrlRepository->getProjectNeedUpdate($today, 'project', $crawlerTypeId);
            if (count($projects) > 0){
                foreach ($projects as $project) {
                    $this->adjuster->lap();
                    try {
                        $crawlerErr = [];
                        $data = [];
                        $dataUpdate = [];
                        $crawlerUrl = [];
                        $crawlerUrlId = $project->crawler_url_id;
                        $errorCount = $project->errors_count;
                        $projectId = $project->projectId;
                        $projectName = $project->name;
                        $projectUpdate = $this->client->project->show($project->project_id);
                        if (!empty($projectUpdate)) {
                            foreach ($projectUpdate as $pro) {
                                $data['project_key'] = $pro['identifier'];
                                $data['flag'] = 2;
                                $p = $this->projectRepository->updateProjectFromCrawler($data, $project->projectId);
                                if ($p){
                                    $crawlerUrl['content'] = serialize($pro);
                                    $crawlerUrl['result'] = true;
                                    $crawlerUrl['last_crawled_date'] = $today;
                                    $crawlerUrl['next_crawled_date'] = Carbon::now()->addDay()->toDateTimeString();
                                    $this->crawlerUrlRepository->update($crawlerUrl, $crawlerUrlId);
                                }
                                $this->info('Updated project: ' . $project->name . ' success!');
                            }
                        } else {
                            $dataUpdate['active'] = 0;
                            $dataUpdate['sync_flag'] = 0;
                            $this->projectRepository->updateProjectFromCrawler($dataUpdate, $projectId);
                            $this->info('Project ' . $projectName . ' has been inactive!');
                        }

                    } catch (Exception $e) {
                        $crawlerErr['status_code'] = app('Illuminate\Http\Response')->status();
                        $crawlerErr['errors_count'] = ++$errorCount;
                        $crawlerErr['errors_message'] = $e->getMessage();
                        $this->crawlerUrlRepository->updateWithError($crawlerErr, $crawlerUrlId);
                        print_r( $e->getMessage());
                    }
                    $this->adjuster->adjust(1);
                }
            }
            $this->line('Count:  ' . count($projects));
        } else {
            $this->error("Wrong crawler type! Please, enter again!");
        }
    }
}
