<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\Department\DepartmentRepository;
use App\Models\Department;

class GetDepartment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portal_departments:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ProjectRepositoryInterface $project,
                                DepartmentRepository $department)
    {
        $this->project = $project;
        $this->department = $department;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $departments = $this->department->apiDepartment();
        foreach ($departments as $department){
            $id = $department['id'];
            if(!$this->department->findByAttribute('id',$id))
            {
                $data = $this->department->saveDepartment($department);
            }else {
                $saveUpdate = $this->department->saveUpdate($department, $id);
            }
        }
        $data_all        = Department::all();
        $data_filter     = $this->department->filterDepartment($data_all);
        $data_department = $this->department->getDepDevTeam($data_filter);
        $this->line(print_r($data_department));
        $divisions       = [];
        $divisions       = $data_department['divisions'];
        $teams           = $data_department['teams'];
        $t_id = [];
        foreach ($teams as $t) {
            $t_id[] = $t['parent_id'];
        }
        foreach ($divisions as $d) {
             if (!in_array($d->id, $t_id)){
                 $create_team = new Department();
                 $create_team->parent_id  = $d->id;
                 $create_team->name       = $d->name;
                 $create_team->manager_id = $d->manager_id;
                 $create_team->status     = $d->status;
                 $create_team->save();
             }
         }
    }

}