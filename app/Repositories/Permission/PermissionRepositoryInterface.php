<?php
namespace App\Repositories\Permission;

interface PermissionRepositoryInterface
{
    public function getRoleOfUser();

    public function checkPermissionForProject($projectId, $actionCanDo);
}