<?php

namespace App\Http\Controllers;

use Request;
use Config;
use View;
use App\Repositories\ProjectRisk\ProjectRiskRepositoryInterface;
use App\Repositories\Categories\CategoriesRepositoryInterface;
use App\Http\Requests\ProjectRiskRequest;
use App\Http\Requests\ProjectRiskEditRequest;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\Permission\PermissionRepositoryInterface;
use Illuminate\Support\Facades\Redirect;

class RiskController extends Controller
{
    /**
     * @author thangdv8182
     * @param ProjectRiskRepositoryInterface $risks
     * @param CategoriesRepositoryInterface $categories
     */
    public function __construct(ProjectRepositoryInterface $project,
                                ProjectRiskRepositoryInterface $risks,
                                CategoriesRepositoryInterface $categories,
                                PermissionRepositoryInterface $permission)
    {
        $this->project      = $project;
        $this->risks        = $risks;
        $this->categories   = $categories;
        $this->permission   = $permission;
        $strategies         = Config::get('constant.risk_strategy');
        $status             = Config::get('constant.status');
        $impacts            = Config::get('constant.risk_impact');
        View::share('strategies', $strategies);
        View::share('status', $status);
        View::share('impacts', $impacts);
    }

    public function index($id)
    {
        $checkPermission = $this->permission->checkPermissionForProject($id, "user.view_list_risk");
        if ($checkPermission == true) {
            $paginate    = Config::get('constant.paginate_number');
            $limit       = Request::get('limit',Config::get('constant.RECORD_PER_PAGE'));
            $category_id = Request::get('category_id','');
            $strategy    = Request::get('strategy','');
            $status      = Request::get('status','');
            $stt         = ( Request::get('page','1') - 1 ) * $limit;
            $count       = $this->risks->count($strategy,$status,$category_id,$id);
            $categories  = $this->categories->all();
            return view('risk.index',[
                'risks'     =>$this->risks->paginate($strategy,$status,$category_id,$limit,$id),
                'count'     =>$count,
                'project_id'=>$id,
                'categories'=>$categories,
                'stt'       => $stt,
                'paginate'  => $paginate,
            ]);
        } else {
            $errorsMessage = 'Sorry. You can not access this page because lack permission!';
            return Redirect::back()->with('errorsMessage', $errorsMessage);
        }
    }

    public function getCreate($id)
    {
        $checkPermission = $this->permission->checkPermissionForProject($id, "user.create_risk");
        if($checkPermission == true){
            $categories = $this->categories->all();
            return view('risk.create',[
                'categories'=>$categories,
                'project_id'=>$id,
            ]);
        } else {
            $errorsMessage = 'Sorry. You can not access this page because lack permission!';
            return Redirect::back()->with('errorsMessage', $errorsMessage);
        }
    }

    public function postCreate(ProjectRiskRequest $request)
    {
        $risk = $this->risks->save($request->all());
        if($risk != 0)
        {
            return redirect(Route('projects.risk.index',[
                'id'=>$request->get('project_id')
            ]));
        }else {
            return redirect(Route('risk.getCreate',[
                'id'=>$request->get('project_id')
            ]));
        }
    }

    public function getEdit($project_id, $riskId)
    {
        $checkPermission = $this->permission->checkPermissionForProject($project_id, "user.update_risk");
        if($checkPermission == true){
            $categories = $this->categories->all();
            $risk       = $this->risks->find($riskId);
            return view('risk.edit',[
                'categories'=>$categories,
                'risk'=>$risk,
                'project_id'=>$project_id,
            ]);
        } else {
            $errorsMessage = 'Sorry. You can not access this page because lack permission!';
            return Redirect::back()->with('errorsMessage', $errorsMessage);
        }
    }

    public function postEdit(ProjectRiskEditRequest $request,$riskId)
    {
        $risk = $this->risks->update($request->all(), $riskId);
        return redirect(Route('projects.risk.index',[
            'id'=>$risk->project_id,
            'page'=>$request->get('page',1)
        ]));
    }

    public function postDelete(\Illuminate\Http\Request $request, $id)
    {
        $checkPermission = $this->permission->checkPermissionForProject($id, "user.delete_risk");
        if($checkPermission == true){
            $this->risks->delete($request->get('id'));
            return redirect()->back();
        } else {
            $errorsMessage = 'Sorry. You can not access this page because lack permission!';
            return Redirect::back()->with('errorsMessage', $errorsMessage);
        }
    }
}
