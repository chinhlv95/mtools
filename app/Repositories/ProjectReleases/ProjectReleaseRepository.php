<?php
namespace App\Repositories\ProjectReleases;

use App\Models\ProjectRelease;

class ProjectReleaseRepository implements ProjectReleaseRepositoryInterface
{
    public function all()
    {
        return ProjectRelease::all();
    }

    public function paginate($quantity)
    {
    }

    public function find($id)
    {
    }

    public function save($data)
    {
    }

    public function delete($id)
    {
    }

    public function update($data, $id)
    {
    }
}