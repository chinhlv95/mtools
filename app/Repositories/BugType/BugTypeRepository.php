<?php
namespace App\Repositories\BugType;

use App\Models\BugType;

class BugTypeRepository implements BugTypeRepositoryInterface{

    public function all(){
        return BugType::all();
    }

    public function paginate($quantity){
        return BugType::paginate($quantity);
    }

    public function find($id){
        return BugType::find($id);
    }

    public function delete($id){
        BugType::delete($id);
    }

    public function save($data){
        $bugType = new BugType();
        if (isset($data['integrated_bug_type_id'])) {
            $bugType->integrated_bug_type_id = $data['integrated_bug_type_id'];
        } else {
            $bugType->integrated_bug_type_id = 0;
        }
        if (isset($data['name'])) {
            $bugType->name = $data['name'];
        } else {
            $bugType->name = '';
        }
        if (isset($data['source_id'])) {
            $bugType->source_id = $data['source_id'];
        } else {
            $bugType->source_id = 0;
        }
        $bugType->save();
        return $bugType->id;
    }

    public function update($data, $id){
        $bugType = new BugType();
        //to do
        $bugType->save();
        return $bugType->id;
    }

    public function findByAttribute($att1, $name1){
        return BugType::where($att1, $name1)->first();
    }

    public function findByAttributes($att1, $name1, $att2, $name2){
        return BugType::where($att1, $name1)
        ->where($att2,$name2)->first();
    }


    /**
     * Get the id of default bug type
     * @author tampt672
     * {@inheritDoc}
     * @see \App\Repositories\BugType\BugTypeRepositoryInterface::getBugTypeIdDefault()
     */
    public function getBugTypeIdDefault() {
        $bugType = $this->findByAttributes('source_id', 0, 'key', 1);
        return $bugType->id;
    }


    /**
     * @author tampt6722
     *
     * @param string $bugTypeName
     * @return integer
     */
    public function getBugTypeId($bugTypeName, $sourceId) {
        $existedBugType = $this->findByAttributes(
                'source_id', $sourceId, 'name', $bugTypeName);
        if (empty($existedBugType)) {
            $data['name'] = $bugTypeName;
            $data['source_id'] = $sourceId;
            $bugId = $this->save($data);
        } else {
            $bugId = $existedBugType->id;
        }

        return $bugId;
    }
}