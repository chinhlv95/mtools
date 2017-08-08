<?php
namespace App\Repositories\Import;

interface ImportRepositoryInterface
{   
    public function all();

    public function paginate($quantity);

    public function find($id);
    
    public function findByAttribute($att, $name);

    public function saveFileName($name, $userId, $projectId, $fileType, $parentId, $team, $excelType);
    
    public function getFileManagement();
    
    public function import($file, $excelType, $confirmUpdatedBefore, $checkBox, $team = null, $project = null);
}
