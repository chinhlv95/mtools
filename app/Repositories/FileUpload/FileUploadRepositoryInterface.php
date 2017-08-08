<?php
namespace App\Repositories\FileUpload;

interface FileUploadRepositoryInterface
{
    public function findFileUploadByProjectId($project_id);

    public function save($name,$size,$extentions,$project_id);

    public function find($id);

    public function delete($id);
}