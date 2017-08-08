<?php
namespace App\Repositories\BugType;
interface BugTypeRepositoryInterface
{
    public function all();

    public function paginate($quantity);

    public function find($id);

    public function delete($id);

    public function save($data);

    public function update($data, $id);

    public function findByAttribute($att1, $name1);

    public function findByAttributes($att1, $name1, $att2, $name2);

    public function getBugTypeIdDefault();

    public function getBugTypeId($bugTypeName, $sourceId);
}
