<?php

namespace App\Repositories\ProjectKpt;

use App\Models\Category;
use App\Models\ProjectKpt;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class ProjectKptRepository implements ProjectKptRepositoryInterface
{
    /**
     * @todo Get query for kpt function.
     *
     * @author thanhnb6719
     * @param date $start_date, $end_date;
     * @param int $category_id, $release_id, $type_id, $project_id
     * @see \App\Repositories\ProjectKpt\ProjectKptRepositoryInterface::getKptList()
     */
    public function getKptList($release_id = '', $category_id = '', $type_id = '',$project_id)
    {
        $query = ProjectKpt::select(
                'project_kpt.id',
                'project_kpt.project_id',
                'project_kpt.user_id',
                'project_kpt.type_id',
                'project_kpt.category_id',
                'project_kpt.status',
                'project_kpt.content',
                'project_kpt.action',
                'categories.value',
                'projects.project_id as p_id',
                'project_versions.name')
                ->join('categories','project_kpt.category_id','=','categories.id')
                ->join('projects', 'project_kpt.project_id', '=', 'projects.id')
                ->join('project_versions', 'project_kpt.release_id', '=', 'project_versions.id')
                ->where('project_kpt.project_id', '=', $project_id);

        if(!empty($type_id)){
            $query->where('project_kpt.type_id', '=', $type_id);
        }
        if(!empty($release_id)){
            $query->where('project_kpt.release_id', '=', $release_id);
        }
        if(!empty($category_id)){
            $query->where('project_kpt.category_id', '=', $category_id);
        }
        $query->orderBy('project_kpt.id','DESC');

        return $query;
    }

    /**
     * @todo Get all kpt from project_kpt table
     *
     * @author thanhnb6719
     * @see \App\Repositories\ProjectKpt\ProjectKptRepositoryInterface::all()
     */
    public function all(){
        return ProjectKpt::all();
    }

    /**
     * @todo Get data from project_kpt table and paginate
     *
     * @author thanhnb6719
     * @param int $quantity
     * @see \App\Repositories\ProjectKpt\ProjectKptRepositoryInterface::paginate()
     */
    public function paginate($quantity){
        return ProjectKpt::paginate($quantity);
    }

    /**
     * @todo Find project kpt with id
     *
     * @author thanhnb6719
     * @see \App\Repositories\ProjectKpt\ProjectKptRepositoryInterface::find()
     */
    public function find($id){
        return ProjectKpt::find($id);
    }

    /**
     * @todo Save project Pki
     *
     * @author thanhnb6719
     * @param array $data
     * @see \App\Repositories\ProjectKpt\ProjectKptRepositoryInterface::save()
     */
    public function save($data){
        $kpt = new ProjectKpt();
        $kpt->user_id     = Sentinel::getUser()->id;
        $kpt->project_id  = $data['project_id'];
        $kpt->release_id  = $data['version'];
        $kpt->category_id = $data['category'];
        $kpt->type_id     = $data['type'];
        $kpt->status      = $data['status'];
        $kpt->content     = $data['description'];
        $kpt->action      = $data['action'];
        $kpt->save();
        return $kpt->id;
    }

    /**
     * @todo Update project Pki
     *
     * @author thanhnb6719
     * @param array $data
     * @see \App\Repositories\ProjectKpt\ProjectKptRepositoryInterface::update()
     */
    public function update($data, $projectId, $kptId){
        $kpt = ProjectKpt::find($kptId);
        $kpt->release_id  = $data['release'];
        $kpt->category_id = $data['category'];
        $kpt->type_id     = $data['type'];
        $kpt->status      = $data['status'];
        $kpt->content     = $data['description'];
        $kpt->action      = $data['action'];
        $kpt->save();
        return $kpt;
    }

    /**
     * @todo Delete project kpi
     *
     * @author thanhnb6719
     * @param int $id
     * @see \App\Repositories\ProjectKpt\ProjectKptRepositoryInterface::delete()
     */
    public function delete($id){
        $kpt = ProjectKpt::find($id);
        $kpt->delete();
    }

}
