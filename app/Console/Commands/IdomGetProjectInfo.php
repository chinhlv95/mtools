<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use atomita\Backlog;
use atomita\BacklogException;

/**
 * Get project info from Backlog
 *
 * Sep 26, 201610:24:34 AM
 * @author @author tampt6722
 *
 */
class IdomGetProjectInfo extends CommandIdomBacklog
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idom_project_info:get {crawler_type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Project get info';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $crawlerType = $this->argument('crawler_type');
        try {
            $projects = $this->backlog->projects->get();
            if (count($projects) > 0) {
                foreach ($projects as $project) {
                    $checkProject = $this->projectRepository->findByAttributes('project_id',
                            $project['id'],
                            'source_id', 1);
                    if (count($checkProject) == 0) {
                        $data['project_id'] = $project['id'];
                        $data['project_key'] = $project['projectKey'];
                        $data['name'] = $project['name'];
                        $data['source_id'] = 1;
                        $p = $this->projectRepository->saveProjectFromCrawler($data);
                        $this->info('Get project ' . $project['name'] . ' success!');
                    }
                }
                $this->line('Get ' . count($projects));
            } else {
                $this->error("Empty project!");
            }
        } catch (BacklogException $e) {
            $this->error('Error!');
            print_r( $e->getResponse());
        }
    }


}