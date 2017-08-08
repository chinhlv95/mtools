<?php
namespace App\Repositories\Priority;

use App\Models\Priority;

class PriorityRepository implements PriorityRepositoryInterface{

    public function all(){
        return Priority::all();
    }

    public function paginate($quantity){
        return Priority::paginate($quantity);
    }

    public function find($id){
        return Priority::find($id);
    }

    public function delete($id){
        Priority::delete($id);
    }

    public function save($data){
        $priority = new Priority();
        if (isset($data['integrated_priority_id'])) {
            $priority->integrated_priority_id = $data['integrated_priority_id'];
        } else {
            $priority->integrated_priority_id = 0;
        }
        if (isset($data['name'])) {
            $priority->name = $data['name'];
        } else {
            $priority->name = '';
        }
        if (isset($data['related_id'])) {
            $priority->related_id = $data['related_id'];
        } else {
            $priority->related_id = 0;
        }
        if (isset($data['source_id'])) {
            $priority->source_id = $data['source_id'];
        } else {
            $priority->source_id = 0;
        }
        $priority->save();
        return $priority->id;
    }

    public function update($data, $id){
        $priority = new Priority();
        //to do
        $priority->save();
        return $priority->id;
    }

    public function findByAttribute($att1, $name1){
        return Priority::where($att1, $name1)->first();
    }

    public function findByAttributes($att1, $name1, $att2, $name2){
        return Priority::where($att1, $name1)
                        ->where($att2,$name2)
                        ->first();
    }

   /**
    * Get the id of the default priority
    * @author tampt6722
    * {@inheritDoc}
    * @see \App\Repositories\Priority\PriorityRepositoryInterface::getPriorityIdDefault()
    */
    public function getPriorityIdDefault() {
        $priority = $this->findByAttributes('source_id', 0, 'key', 2);
        return $priority->id;
    }

    /**
     *
     * @author tampt6722
     *
     * @param array $priority
     * @return integer
     */
    public function getPriorityId($priority, $sourceId){
        $existedPriority = $this->findByAttributes(
                'integrated_priority_id', $priority['id'], 'source_id', $sourceId);
        if (count($existedPriority) > 0) {
            $priorityId = $existedPriority->id;
        } else {
            $data['integrated_priority_id'] = $priority['id'];
            $data['name'] = $priority['name'];
            $data['source_id'] = $sourceId;
            $priorityId = $this->save($data);
        }

        return $priorityId;
    }

}