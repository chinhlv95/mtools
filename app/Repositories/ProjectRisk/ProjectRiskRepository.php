<?php
namespace App\Repositories\ProjectRisk;
use App\Models\ProjectRisk;

class ProjectRiskRepository implements ProjectRiskRepositoryInterface
{
    /**
     * @todo get builder join table project_risk and categories
     * @author thangdv8182
     * @param string $q search risk_title
     * @return
     */
    public function getBuilder($strategy= '', $status ='',
                               $category_id = '', $id = 1){
        $query = ProjectRisk::select(
            'cat.value',
            'project_risk.*'
            )
            ->join('categories as cat','project_risk.category_id','=','cat.id')
            ->where('project_risk.project_id','=',$id);

            if(!empty($strategy)){
               $query->where('project_risk.strategy','=',$strategy);
            }
            if(!empty($status)){
                $query->where('project_risk.status','=',$status);
            }
            if(!empty($category_id)){
                $query->where('project_risk.category_id','=',$category_id);
            }
            $query->orderBy('project_risk.id','DESC');

            return $query;
    }

    public function all($id = 1){
        return $this->getBuilder($q = '',$id)->get();
    }

    /**
     * @todo paginate list risk
     *
     * @author thangdv8182
     * @param int $quantity, string $q, int id
     * {@inheritDoc}
     * @see \App\Repositories\ProjectRisk\ProjectRiskRepositoryInterface::paginate()
     */
    public function paginate($strategy= '', $status ='',
                             $category_id = '',$quantity = 10,
                             $id = 1){
        return $this->getBuilder($strategy,$status,$category_id,$id)->paginate($quantity);
    }

    public function find($id){
        return ProjectRisk::find($id);
    }

    /**
     * @todo Save new risk
     *
     * @author thangdv8182
     * @param array $data
     * @see \App\Repositories\ProjectRisk\ProjectRiskRepositoryInterface::save()
     */
    public function save($data){
        $risk = new ProjectRisk();
        $risk->status = $data['status'];
        $risk->impact = $data['impact'];
        $risk->propability = $data['propability'];
        $risk->strategy = $data['strategy'];
        $risk->mitigration_plan = $data['mitigration_plan'];
        $risk->risk_title = $data['risk_title'];
        $risk->guideline_link = $data['guideline_link'];
        $risk->task_id = $data['task_id'];
        $risk->category_id = $data['category_id'];
        $risk->project_id = $data['project_id'];
        $risk->save();

        return $risk->id;
    }

    /**
     * @todo delete risk
     *
     * @author thangdv8182
     * {@inheritDoc}
     * @see \App\Repositories\ProjectRisk\ProjectRiskRepositoryInterface::delete()
     */
    public function delete($id){
        $risk = ProjectRisk::find($id);
        $risk->delete();
    }

    /**
     * @todo update risk
     *
     * @author thangdv8182
     * {@inheritDoc}
     * @see \App\Repositories\ProjectRisk\ProjectRiskRepositoryInterface::update()
     */
    public function update($data, $id){
        $risk = ProjectRisk::find($id);
        $risk->status = $data['status'];
        $risk->impact = $data['impact'];
        $risk->propability = $data['propability'];
        $risk->strategy = $data['strategy'];
        $risk->mitigration_plan = $data['mitigration_plan'];
        $risk->risk_title = $data['risk_title'];
        $risk->guideline_link = $data['guideline_link'];
        $risk->task_id = $data['task_id'];
        $risk->category_id = $data['category_id'];
        $risk->save();

        return $risk;
    }

    /**
     * @todo count risk
     *
     * @author thangdv8182
     * {@inheritDoc}
     * @see \App\Repositories\ProjectRisk\ProjectRiskRepositoryInterface::count()
     */
    public function count($strategy= '', $status ='',
                          $category_id = '',$id = 1)
    {
        return $this->getBuilder($strategy,$status,$category_id,$id)->count();
    }
}