<?php
namespace App\Repositories\RootCause;

use App\Models\RootCause;

class RootCauseRepository implements RootCauseRepositoryInterface{

    public function all(){
        return RootCause::all();
    }

    public function paginate($quantity){
        return RootCause::paginate($quantity);
    }

    public function find($id){
        return RootCause::find($id);
    }

    public function delete($id){
        RootCause::delete($id);
    }

    public function save($data){
        $rootCause = new RootCause();
        if (isset($data['integrated_root_id'])) {
            $rootCause->integrated_root_id = $data['integrated_root_id'];
        } else {
            $rootCause->integrated_root_id = 0;
        }
        $rootCause->name = $data['name'];
        $rootCause->source_id = $data['source_id'];
        $rootCause->save();
        return $rootCause->id;
    }

    public function update($data, $id){
        $rootCause = new RootCause();
        //to do
        $rootCause->save();
        return $rootCause->id;
    }

    public function findByAttribute($att1, $name1){
        return RootCause::where($att1, $name1)->first();
    }

    public function findByAttributes($att1, $name1, $att2, $name2){
        return RootCause::where($att1, $name1)
        ->where($att2, $name2)->first();
    }

    /**
     * Get the id of default root cause
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\RootCause\RootCauseRepositoryInterface::getRootCauseIdDefault()
     */
    public function getRootCauseIdDefault() {
        $rootCause = $this->findByAttributes('source_id', 0, 'key', 3);
        return $rootCause->id;
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\RootCause\RootCauseRepositoryInterface::getRootCauseId()
     */
    public function getRootCauseId($name, $sourceId) {
        $existedRootCause = $this->findByAttributes(
                'source_id', $sourceId, 'name', $name);
        if (empty($existedRootCause)) {
            $data['source_id'] = $sourceId;
            $data['name'] = $name;
            $rootId = $this->save($data);
        } else {
            $rootId = $existedRootCause->id;
        }
        return $rootId;
    }



}