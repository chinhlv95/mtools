<?php
namespace App\Repositories\Department;

/**
 *
 * @author chaunm8181
 *
 */
interface DepartmentRepositoryInterface
{
    public function apiDepartment();

    public function save($data);

    public function update($data, $id);

    public function saveDepartment($data);

    public function filterDepartment($data);

    public function getDepDevTeam($apiData);

}