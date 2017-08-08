<?php
namespace App\Repositories\BugWeight;

use App\Models\BugWeight;

class BugWeightRepository implements BugWeightRepositoryInterface{

    public function all(){
        return BugWeight::all();
    }

    public function paginate($quantity){
        return BugWeight::paginate($quantity);
    }

    public function find($id){
        return BugWeight::find($id);
    }

    public function delete($id){
        BugWeight::delete($id);
    }

    public function save($data){
        $bugWeight = new BugWeight();
        if (isset($data['integrated_bug_weight_id'])) {
            $bugWeight->integrated_bug_weight_id = $data['integrated_bug_weight_id'];
        } else {
            $bugWeight->integrated_bug_weight_id = 0;
        }
        if (isset($data['name'])) {
            $bugWeight->name = $data['name'];
        } else {
            $bugWeight->name = '';
        }
        if (isset($data['source_id'])) {
            $bugWeight->source_id = $data['source_id'];
        } else {
            $bugWeight->source_id = 0;
        }
        $bugWeight->save();
        return $bugWeight->id;
    }

    public function update($data, $id){
        $bugWeight = new BugWeight();
        //to do
        $bugWeight->save();
        return $bugWeight->id;
    }

    public function findByAttribute($att1, $name1){
        return BugWeight::where($att1, $name1)->first();
    }

    public function findByAttributes($att1, $name1, $att2, $name2){
        return BugWeight::where($att1, $name1)
        ->where($att2,$name2)->first();
    }

    public function getBugWeightWithKey($key)
    {
        $id = BugWeight::where('key', $key)->first()->id;
        $query = BugWeight::where('related_id', $id)->get();

        return $query;
    }

    /**
     * Get the id of default bug weight
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\BugWeight\BugWeightRepositoryInterface::getBugWeightIdDefault()
     */
    public function getBugWeightIdDefault() {
        $bugWeight = $this->findByAttributes('source_id', 0, 'key', 2);
        return $bugWeight->id;
    }

    /**
     * @author tampt6722
     *
     * @param array $bugName
     * @return integer
     */
    public function getBugWeightId($bugName, $sourceId) {
        $existedBugWeight = $this->findByAttributes(
                'source_id', $sourceId, 'name', $bugName);
        if (empty($existedBugWeight)) {
            $data['name'] = $bugName;
            $data['source_id'] = $sourceId;
            $bugId = $this->save($data);
        } else {
            $bugId = $existedBugWeight->id;
        }

        return $bugId;
    }

}