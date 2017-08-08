<?php
namespace App\Repositories\BugWeight;

interface BugWeightRepositoryInterface
{
    public function all();

    public function paginate($quantity);

    public function find($id);

    public function delete($id);

    public function save($data);

    public function update($data, $id);

    public function findByAttribute($att1, $name1);

    public function findByAttributes($att1, $name1, $att2, $name2);

    public function getBugWeightWithKey($key);

    public function getBugWeightIdDefault();

    public function getBugWeightId($bugName, $sourceId);
}
