<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Get all projects from Redmine 02
 *
 * Oct 3, 20133:18:03 PM
 * @author tampt3722
 *
 */
class Redmine02GetProjectInfo extends CommandRedmine02
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '02_project_info:get {crawler_type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Redmine 02 project get info';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $crawlerType = $this->argument('crawler_type');
        try {
            $params = [
                    'limit' => 100,
                    'offset' => 0
            ];
           $this->getProjects($params);
        } catch (Exception $e) {
            print_r( $e->getMessage());
        }
    }

    /**
     *
     * @author tampt3722
     *
     * @param array $params
     */
    private function getProjects($params) {
        $this->line('Get project limit: ' . $params['limit'] . ', offset: ' . $params['offset']);
        $projects = $this->client->project->all($params);
        foreach ($projects as $key => $value) {
            if ($key === 'projects') {
                $projectCount = count($value);
                if ($projectCount > 0) {
                    foreach ($value as $project){
                        $checkProject = $this->projectRepository
                            ->findByAttributes('project_id', $project['id'], 'source_id', 3);
                        if (count($checkProject) == 0) {
                            try {
                                $data['project_id'] = $project['id'];
                                $data['project_key'] = $project['identifier'];
                                $data['name'] = $project['name'];
                                $data['description'] = $project['description'];
                                $data['source_id'] = 3;
                                $p  = $this->projectRepository->saveProjectFromCrawler($data);
                            } catch (Exception $e) {
                                print_r($e->getMessage());
                            }
                            $this->info('Get project ' . $project['name'] . ' success!');
                        }
                    }
                    if ($projectCount == $params['limit']) {
                        $params['offset']+= ($params['limit']);
                        $this->getProjects($params);
                    }
                } else {
                    $this->error("empty project");
                }
            }
        }
    }
}