<?php
namespace App\Repositories\ContentManagement;

interface ContentManagementRepositoryInterface{

    public function findBySourceAndType($type,$source,$attributes);
    public function save($type,$name, $source_id);
    public function update($type,$attributes,$id);
    public function checkExits($type,$name);
    public function all($type);
}