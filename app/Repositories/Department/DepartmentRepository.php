<?php
namespace App\Repositories\Department;

use App\Models\Department;
use App\Repositories\Api\ApiRepositoryInterface;
/**
 *
 * @author chaunm8181
 *
 */

class DepartmentRepository implements DepartmentRepositoryInterface
{

    public function __construct(ApiRepositoryInterface $api)
    {
        $this->api = $api;
    }

    public function apiDepartment()
    {
        try{
            $url = env('PORTAL_URL')."department.php";
            $timeout = 30;
            $json_data = $this->api->getApi($url, $timeout);
            $result    = $this->filterDepartment($json_data);
            return $result;
        } catch (Exception $e) {
            $this->error('Error!');
            print_r( $e->getResponse());
        }
    }

    public function saveDepartment($data)
    {
        $department               = new Department();
        $department->id           = $data['id'];
        $department->parent_id    = $data['parent_department_id'];
        $department->name         = $data['value'];
        $department->manager_id   = $data['manager'];
        $department->status       = $data['status'];
        $department->save();
        return true;
    }

    public function save($data)
    {
        $department = Department::all();
        $data['status']  = '1';
        $department = Department::create($data);
        return true;
    }

    public function update($data, $id){
        $department = Department::find($id);
        $department->name = $data['name'];
        $department->manager_id = $data['manager_id'];
        $department->description = $data['description'];
        $department->save();
        return true;
    }

    public function filterDepartment($data)
    {
        $removeKeys = [3,4,28];
        if($data != null){
            foreach ($data as $key => $department) {
                foreach ($removeKeys as $k => $value){
                    if($department['id'] == $value ||
                            $department['parent_department_id'] == $value){
                                $key == $k;
                                unset($data[$key]);
                    }
                }
            }
            return $data;
        }
    }

    public function getDepDevTeam($apiData)
    {
        $departments     = [];
        $departmentId    = [];
        $divisionAndTeam = [];
        $divisions       = [];
        $teams           = [];
        if($apiData != null){
            foreach ($apiData as $key => $data) {
                if ($data['parent_id'] == 0) {
                    $departments[] = $data;
                    $departmentId[] = $data['id'];
                }
                else {
                    $divisionAndTeam[] = $data;
                }
            }

            foreach ($divisionAndTeam as $key => $divisionData) {
                if (in_array($divisionData['parent_id'], $departmentId)) {
                    $divisions[] = $divisionData;
                }
                else {
                    $teams[] = $divisionData;
                }
            }
        }else{
            $departments = array();
            $teams = array();
            $divisions = array();
        }

        return [
            'departments' => $departments,
            'teams'       => $teams,
            'divisions'   => $divisions
        ];
    }

    public function findByAttribute($att, $name){
        return Department::where($att, $name)->first();
    }

    public function saveUpdate($data, $id){
        $department             = Department::find($id);
        $department->parent_id  = $data['parent_department_id'];
        $department->name       = $data['value'];
        $department->manager_id = $data['manager'];
        $department->status     = $data['status'];
        $department->save();
    }
}
