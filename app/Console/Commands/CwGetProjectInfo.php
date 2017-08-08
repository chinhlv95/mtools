<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Get all projects from Cowell Redmine
 *
 * Oct 3, 20163:18:06 PM
 * @author tampt6722
 *
 */
class CwGetProjectInfo extends CommandCwRedmine
{
    /** 
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cw_project_info:get {crawler_type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cowell project get info';

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
        //printf info get project limit command line
        $this->line('Get project limit: ' . $params['limit'] . ', offset: ' . $params['offset']);
        //get all projects
        $projects = $this->client->project->all($params);
        foreach ($projects as $key => $value) {
            if ($key === 'projects') { // check if it is projects, excutes script code 
                $projectCount = count($value);
                if ($projectCount > 0){
                    foreach ($value as $project){
                        $checkProject = $this->projectRepository
                        ->findByAttributes('project_id', $project['id'], 'source_id',4); 
                        //save info project to table projects
                        if (count($checkProject) == 0) {
                            try {
                                $data['project_id'] = $project['id'];
                                $data['project_key'] = $project['identifier'];
                                $data['name'] = $project['name'];
                                $data['description'] = $project['description'];
                                $data['source_id'] = 4;
                                $p  = $this->projectRepository->saveProjectFromCrawler($data);
                            } catch (Exception $e) {
                                print_r($e->getMessage());
                            }
                            $this->info('Get project ' . $project['name'] . ' success!');
                        }
                    }
                    //if project count equal limit of project , offset=limit+1
                    if ($projectCount == $params['limit']) {
                        $params['offset']+= ($params['limit']);
                        $this->getProjects($params);
                    }
                }
                break;
            }
        }
    }
}