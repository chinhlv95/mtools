<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProjectVersionRequest;
use App\Models\Project;
use App\Models\ProjectVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

class VersionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $project_id)
    {
        $status = config::get('constant.status');
        $limit = $request->get('limit',Config::get('constant.RECORD_PER_PAGE'));
        $paginate = Config::get('constant.paginate_number');
        $stt = ( $request->get('page','1') - 1 ) * $limit;
        $keyword = $request->get('keyword','');

        if ($keyword) {
            $query = ProjectVersion::where('project_id','=',$project_id)
                                ->where('name','LIKE',"%$keyword%")
                                ->orderBy('created_at','DESC');
            $version = $query->paginate($limit);
        }else {
            $query = ProjectVersion::Where('project_id','=',$project_id)
                            ->orderBy('created_at','DESC');
            $version = $query->paginate($limit);
        }
        $count = $version->count();

        return view('version.index', compact('version','stt','project_id','status','paginate','count'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($project_id)
    {
        $status = config::get('constant.status');
        $project = Project::find($project_id);
        $source = config::get('constant.stream_types');
        return view('version.create', compact('project','project_id','source','status'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProjectVersionRequest $request, $project_id)
    {
        $project = Project::find($project_id);
        $version = $request->all();
        $version['start_date'] = \Helpers::formatDateYmd($version['start_date']);
        $version['end_date'] = \Helpers::formatDateYmd($version['end_date']);
        $version['project_id'] = $project->id;
        $version['source_id'] = $project->source_id;
        ProjectVersion::create($version);
        return redirect(Route('version.index',$project_id))
            ->withSuccess(Lang::get('message.create_projectversion_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($project_id,$id )
    {
        $status = config::get('constant.status');
        $project = Project::find($project_id);
        $source = config::get('constant.stream_types');
        $version = ProjectVersion::find($id);
        return view('version.edit', compact('version','project_id',
                                    'source','id','project','status'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateProjectVersionRequest $request, $project_id, $id)
    {
        $project = Project::find($project_id);
        $version = ProjectVersion::find($id);
        $data = $request->all();
        $data['start_date'] = \Helpers::formatDateYmd($data['start_date']);
        $data['end_date'] = \Helpers::formatDateYmd($data['end_date']);
        $data['project_id'] = $project->id;
        $data['source_id'] = $project->source_id;
        $version->update($data);
        return redirect(Route('version.index',$project_id))
            ->withSuccess(Lang::get('message.update_projectversion_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$project_id)
    {
        $id = $request->id;
        $version = ProjectVersion::find($id);
        $current_page = $request->page;
        $keyword = $request->keyword;
        $count = $request->count;
        $version->delete();
        if($count == 1){
            return redirect(Route('version.index',['project_id' => $project_id,'keyword' => $keyword,'page' => $current_page - 1 ]))
            ->withSuccess(Lang::get('message.delete_projectversion_success'));
        }
        return redirect(Route('version.index',['project_id' => $project_id,'keyword' => $keyword,'page' => $current_page ]))
        ->withSuccess(Lang::get('message.delete_projectversion_success'));
    }
}
