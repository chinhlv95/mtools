<?php
namespace App\Repositories\ProjectKpt;

interface ProjectKptRepositoryInterface
{
    public function getKptList($release_id = '', $category_id = '',
                               $type_id = '', $project_id);

    public function all();

    public function paginate($quantity);

    public function find($id);

    public function save($data);

    public function update($data, $projectId, $kptId);

    public function delete($id);
}