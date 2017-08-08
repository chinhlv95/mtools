<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Get projects from GDO Redmine
 *
 * Sep 29, 201610:36:31 AM
 * @author tampt6722
 *
 */
class GdoGetProjectInfo extends CommandGDORedmine
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gdo_project_info:get {crawler_type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'GDO project get info';

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
     * @author tampt6722
     *
     * @param unknown $params
     */
    private function getProjects($params) {
        $this->line('Get project limit: ' . $params['limit'] . ', offset: ' . $params['offset']);
        $projects = $this->client->project->all($params);
        if (is_array($projects)) {
            foreach ($projects as $key => $value) {
                if ($key === 'projects') {
                    $projectCount = count($value);
                    if ($projectCount > 0){
                        foreach ($value as $project){
                            $checkProject = $this->projectRepository
                            ->findByAttributes('project_id', $project['id'], 'source_id', 2);
                            if (count($checkProject) == 0) {
                                try {
                                    $data['project_id'] = $project['id'];
                                    $data['project_key'] = $project['identifier'];
                                    $data['name'] = $project['name'];
                                    $data['description'] = $project['description'];
                                    $data['source_id'] = 2;
                                    $p  = $this->projectRepository->saveProjectFromCrawler($data);
                                } catch (Exception $e) {
                                    print_r( $e->getMessage());
                                }
                                $this->info('Get project ' . $project['name'] . ' success!');
                            }
                        }
                        if ($projectCount == $params['limit']) {
                            $params['offset']+= ($params['limit']);
                            $this->getProjects($params);
                        }
                    } else {
                        $this->error("Empty project!");
                    }
                }
            }
        } else {
            $this->error("Can not get projects!");
        }
    }
}
