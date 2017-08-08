<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateNewKptRequest;
use App\Http\Requests\EditKptRequest;
use App\Repositories\Categories\CategoriesRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\ProjectKpt\ProjectKptRepositoryInterface;
use App\Repositories\ProjectVersion\ProjectVersionRepositoryInterface;
use Illuminate\Http\Request as DelRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use App\Repositories\Permission\PermissionRepositoryInterface;
use Illuminate\Support\Facades\Redirect;

class KptController extends Controller
{
    public function __construct(ProjectKptRepositoryInterface $kpt,
                                CategoriesRepositoryInterface $categories,
                                ProjectRepositoryInterface $project,
                                ProjectVersionRepositoryInterface $version,
                                PermissionRepositoryInterface $permission
            )
    {
        $this->project    = $project;
        $this->kpt        = $kpt;
        $this->categories = $categories;
        $this->version    = $version;
        $this->permission = $permission;
    }

    /**
     * @todo Display a listing of Kpt.
     *
     * @author thanhnb6719
     * @param $request
     * @param int $projectId
     * @return resources\views\project_kpt\index
     */
    public function index(Request $request, $projectId)
    {
        $checkPermission = $this->permission->checkPermissionForProject($projectId, "user.view_kpt");
        if($checkPermission == true){
            // Get data in constant
            $paginate        = Config::get('constant.paginate_number');
            $types           = Config::get('constant.kpt_type');
            $status          = Config::get('constant.status');

            // Request data for search
            $limit           = Request::get('limit', Config::get('constant.RECORD_PER_PAGE'));
            $version_id      = Request::get('version','');
            $category_id     = Request::get('category','');
            $type_id         = Request::get('type','');
            $number          = (Request::get('page','1') - 1)* $limit;

            // Get data for search box
            $category        = $this->categories->all()->sortBy('value');
            $version         = $this->version->getVersionByAttribute('project_id', $projectId);
            $list_kpt        = $this->kpt->getKptList($version_id, $category_id, $type_id, $projectId) ->paginate($limit);
            return view('project_kpt.index', ['kpts'       => $list_kpt,
                                              'project_id' => $projectId,
                                              'category'   => $category,
                                              'version'    => $version,
                                              'types'      => $types,
                                              'status'     => $status,
                                              'paginate'   => $paginate,
                                              'number'     => $number]);
        } else {
            $errorsMessage = 'Sorry. You can not access this page because lack permission!';
            return Redirect::back()->with('errorsMessage', $errorsMessage);
        }
    }

    /**
     * @todo Show the form create new Kpt.
     *
     * @author thanhnb6719
     * @param $projectId
     * @return resources\views\project_kpt\create
     */
    public function create($projectId)
    {
        $checkPermission = $this->permission->checkPermissionForProject($projectId, "user.create_kpt");
        if($checkPermission == true){
            $category   = $this->categories->all();
            $version    = $this->version->getVersionByAttribute('project_id', $projectId);
            $status     = Config::get('constant.status_project');
            $types      = Config::get('constant.kpt_type');
            return view('project_kpt.create', ['project_id' => $projectId,
                                               'category'   => $category,
                                               'version'    => $version,
                                               'types'      => $types,
                                               'status'     => $status]);
        } else {
            $errorsMessage = 'Sorry. You can not access this page because lack permission!';
            return Redirect::back()->with('errorsMessage', $errorsMessage);
        }
    }

    /**
     * @todo Store a newly created resource in storage.
     *
     * @author thanhnb6719
     * @param  \Illuminate\Http\Request\CreateNewKptRequest  $request
     * @return resources\views\project_kpt\create with message
     */
    public function store(CreateNewKptRequest $request, $projectId)
    {
        $data = $request->all();
        $kpt  = $this->kpt->save($data);
        if(isset($data['save'])){
            if($kpt != 0)
            {
                return redirect()->route('projects.kpt.list', $projectId)->with('success', 'Add new KPT successfully!');
            }else{
                return redirect()->back()->with('error', 'Have error when add new KPT');
            }
        }elseif(isset($data['save_and_continue'])){
            if($kpt != 0)
            {
                return redirect()->back()->with('success', 'Add new KPT successfully!');
            }else{
                return redirect()->back()->with('error', 'Add new KPT successfully!');
            }
        }
    }

    /**
     * @todo Show the form for editing the specified resource.
     *
     * @author thanhnb6719
     * @param  int  $projectId, $kptId
     * @return resources\views\project_kpt\edit
     */
    public function edit($projectId, $kptId)
    {
        $checkPermission = $this->permission->checkPermissionForProject($projectId, "user.update_kpt");
        if($checkPermission == true){
            $kpt         = $this->kpt->find($kptId);
            $categories  = $this->categories->all();
            $releases    = $this->version->getVersionByAttribute('project_id', $projectId);
            $types       = Config::get('constant.kpt_type');
            $status      = Config::get('constant.status');
            return view('project_kpt.edit',
                    [
                     'project_id' => $projectId,
                     'kpt'        => $kpt,
                     'categories' => $categories,
                     'releases'   => $releases,
                     'types'      => $types,
                     'status'     => $status
                    ]);
        } else {
            $errorsMessage = 'Sorry. You can not access this page because lack permission!';
            return Redirect::back()->with('errorsMessage', $errorsMessage);
        }
    }

    /**
     * @todo Update the specified resource in storage.
     *
     * @author thanhnb6719
     * @param  \Illuminate\Http\EditKptRequest  $request
     * @param  int  $projectId, $kptId
     * @return redirect resources\views\project_kpt\index
     */
    public function update(EditKptRequest $request, $projectId, $kptId)
    {
        $kpt = $this->kpt->update($request->all(), $projectId, $kptId);
        return redirect(Route('projects.kpt.list', $projectId));
    }

    /**
     * @todo Remove the specified resource from storage.
     *
     * @author thanhnb6719
     * @param  \Illuminate\Http\DelRequest  $request
     * @return redirect resources\views\project_kpt\index
     */
    public function destroy(DelRequest $request, $projectId)
    {
        $checkPermission = $this->permission->checkPermissionForProject($projectId, "user.delete_kpt");
        if($checkPermission == true){
            $this->kpt->delete($request->get('id'));
            return redirect()->back()->with('success', 'Delete KPT successfully!');
        } else {
            $errorsMessage = 'Sorry. You can not access this page because lack permission!';
            return Redirect::back()->with('errorsMessage', $errorsMessage);
        }
    }
}