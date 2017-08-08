<?php
namespace App\Repositories\Activity;

/**
 *
 * @author tampt6722
 *
 */
interface ActivityRepositoryInterface
{
    public function all();

    public function paginate($quantity);

    public function find($id);

    public function delete($id);

    public function save($data);

    public function update($data, $id);

    public function findByAttribute($att, $name);

    public function findByAttributes($att1, $name1, $att2, $name2);

    public function getActivityId($activity, $sourceId);
}