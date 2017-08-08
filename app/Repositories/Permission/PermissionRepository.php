<?php
namespace App\Repositories\Permission;

use App\Repositories\Permission\PermissionRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class PermissionRepository implements PermissionRepositoryInterface
{
    public function __construct(ProjectRepositoryInterface $project)
    {
        $this->project    = $project;
    }

    /**
     * @todo Get role
     *
     * @author thanhnb6719
     * @return int $role_id
     */
    public function getRoleOfUser() {
        $role       = Sentinel::getUser()->roles()->get();
        $role_id    = $role[0]['id'];
        return $role_id;
    }

    /**
     * @todo Check permission for access a page or function
     *
     * @author thanhnb6719
     * @param int $projectId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function checkPermissionForProject($projectId, $actionCanDo) {
        $role_id    = $this->getRoleOfUser();
//         dd($role_id);
        if ($role_id != 1 && $role_id != 13) {
            $groupCheck        = $this->project->getGroupProjectMemberJoin($actionCanDo);
//             dd($groupCheck);
            $projectMemberJoin = $groupCheck['projectJoin'];
            if(!in_array($projectId, $projectMemberJoin))
            {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }
}