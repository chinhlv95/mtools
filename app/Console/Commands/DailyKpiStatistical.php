<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\ProjectKpi;
use App\Repositories\ProjectKpi\ProjectKpiRepositoryInterface;
use Exception;
use Illuminate\Console\Command;

class DailyKpiStatistical extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily_kpi_statistic:save_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save kpi statistic follow month and week everyday';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ProjectKpiRepositoryInterface $projectKpi)
    {
        $this->projectKpi = $projectKpi;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->info ( 'Delete old data!' );
            $deleteKpi = ProjectKpi::whereIn('baseline_flag', [0, 1])
                                    ->delete();
            $this->info ( 'Delete completed! Begin import data!' );
            $listProject       = Project::where('active', 1)->get();
            $numberProject     = count($listProject);
            foreach($listProject as $project){
                $projectIds   = $project['id'];
                $startProject  = Project::where('id', $projectIds)->first()->actual_start_date;
                if ($startProject != null) {
                    $save = $this->projectKpi->saveSyncKpi($projectIds, $startProject);
                }elseif($startProject == null){
                    $startProject = Project::where('id', $projectIds)->first()->plant_start_date;
                    $save = $this->projectKpi->saveSyncKpi($projectIds, $startProject);
                }
                $numberProject = $numberProject - 1;
                $this->info ( 'Save project '. $project['name'] .' succesfully!' );
                $this->info ( 'Project left: '. $numberProject);
            }
        } catch(Exception $e) {
            return "Error: ".$e->getMessage();
        }
    }
}
