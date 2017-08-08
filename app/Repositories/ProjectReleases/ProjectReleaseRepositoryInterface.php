<?php
namespace App\Repositories\ProjectReleases;

interface ProjectReleaseRepositoryInterface
{
    public function all();

    public function paginate($quantity);

    public function find($id);

    public function save($data);

    public function delete($id);

    public function update($data, $id);
}