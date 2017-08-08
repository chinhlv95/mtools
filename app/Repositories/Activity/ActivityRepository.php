<?php
namespace App\Repositories\Activity;

use App\Models\Activity;

/**
 *
 * @author tampt6722
 *
 */
class ActivityRepository implements ActivityRepositoryInterface
{

    public function all(){
        return Activity::all();
    }

    public function paginate($quantity){
        return Activity::paginate($quantity);
    }

    public function find($id){
        return Activity::find($id);
    }

    public function save($data){
        $activity = new Activity();
        $activity->name = $data['name'];
        if (isset($data ['source_id'])) {
            $activity->source_id = $data ['source_id'];
        } else {
            $activity->source_id = 0;
        }
        if (isset($data['integrated_activity_id'])) {
            $activity->integrated_activity_id = $data['integrated_activity_id'];
        } else {
            $activity->integrated_activity_id = 0;
        }
        $activity->save();
        return $activity->id;
    }

    public function delete($id){
        Activity::find($id)->delete();
    }

    public function update($data, $id){
        $activity = Activity::find($id);
        $activity->save();
        return true;
    }

    public function findByAttribute($att, $name){
        return Activity::where($att, $name)->first();
    }

    public function findByAttributes($att1, $name1, $att2, $name2){
        return Activity::where($att1, $name1)
                        ->where($att2,$name2)->first();
    }
    /**
     * Get activities and save into database
     *
     * @author tampt6722
     * @param $activity
     * @return integer
     */
    public function getActivityId($activity, $sourceId) {
        $existedActivity = $this->findByAttributes(
                'source_id', $sourceId, 'integrated_activity_id', $activity['id']);
        if (count($existedActivity) == 0) {
            $dataActivity['integrated_activity_id'] = $activity['id'];
            $dataActivity['name'] = $activity['name'];
            $dataActivity['source_id'] = $sourceId;
            $activityId = $this->save($dataActivity);
        } else {
            $activityId = $existedActivity->id;
        }
        return $activityId;
    }
}