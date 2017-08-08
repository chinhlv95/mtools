<?php
namespace App\Repositories\ProjectRisk;
interface ProjectRiskRepositoryInterface
{
    public function all();

    public function paginate($quantity);

    public function find($id);

    public function save($data);

    public function delete($id);

    public function update($data, $id);

    public function count($q = '');

}