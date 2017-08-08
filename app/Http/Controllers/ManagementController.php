<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateContentRequest;
use App\Repositories\ContentManagement\ContentManagementRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class ManagementController extends Controller
{
    public function __construct(ContentManagementRepositoryInterface $contentManage ){
        $this->content = $contentManage;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $setting_type = Config::get('constant.setting_type');
        $source = Config::get('constant.stream_types');
        if(!empty(Session::get('type'))){
            $result = $this->content->findBySourceAndType(Session::get('type'), ['source_id' => Session::get('source')]
                    , ['id','name','key','related_id','source_id']);
            $map_name = $this->content->all(Session::get('type'))->toArray();
            return view('content_setting.index',[
                'setting_type' => $setting_type,
                'source' => $source,
                'result' => $result->toArray(),
                'selected_source' => Session::get('source'),
                'type_id' => Session::get('type'),
                'map_name' => $map_name
            ]);
        } else {
            $input_source = !empty(Session::get('source_id')) ? Session::get('source_id') : 0;
            $input_type = !empty(Session::get('type_id')) ? Session::get('type_id') : 0;
            return view('content_setting.index',[
                        'setting_type' => $setting_type,
                        'source' => $source,
                        'selected_source' => $input_source,
                        'type_id' => $input_type
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $setting_type = Config::get('constant.setting_type');
        $source = Config::get('constant.stream_types');
        $input_source = $request->source_id;
        $input_type = $request->type_id;
        if(!empty($input_source)) {
            Session::flash('source_id', $input_source);
        }
        if(!empty($input_type)) {
            Session::flash('type_id', $input_type);
        }
        return view('content_setting.create',[
            'setting_type' => $setting_type,
            'source' => $source,
            'selected_source' => $input_source,
            'type_id' => $input_type
            
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateContentRequest $request)
    {
        $input_type = !empty(Session::get('type_id')) ? Session::get('type_id') : $request->type;
        $source_id = !empty(Session::get('source_id')) ? Session::get('source_id') : 0;
        $input_name  = \Helpers::mst_trim($request->releated_name);
        $hasExits    = $this->content->findBySourceAndType($input_type,
               ['source_id' => $source_id,
                'name' => $input_name],['name']);
        if(count($hasExits->toArray()) > 0){
           $uniqueMsg  = 'This role already exists.';
           return redirect()->back()->with('uniqueMsg', $uniqueMsg);
        }else{
            
           $query      = $this->content->save($input_type, $input_name, $source_id);
           $parameters = [
                'type' => $input_type,
                'source' => $source_id,
                'success' => 'Add new releated name success'
           ];
           return redirect()->route('content-management.index')->with($parameters);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
 public function show(Request $request)
    {
        $input_type = $request->type;
        $input_source = $request->source;
        $setting_type = Config::get('constant.setting_type');
        $source = Config::get('constant.stream_types');
        $result = $this->content->findBySourceAndType($input_type, ['source_id' => $input_source]
                                                    , ['id','name','key','related_id',
                                                    'source_id']);
        if($result == "")
        {
            return redirect(Route('content-management.index'))->with('errorsMessage', 'Link not found');
        }
        $map_name = $this->content->all($input_type)->toArray();
//         if(in_array($input_source, $source));
//         {
//             $selected_source = $source[$input_source];
//         }
        return view('content_setting.index',[
                    'setting_type' => $setting_type,
                    'source' => $source,
                    'result' => $result->toArray(),
                    'selected_source' => $input_source,
                    'type_id' => $input_type,
                    'map_name' => $map_name
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $source_id = $request->source_id;
        $id        = $request->id;
        $type_id   = $request->type_id;
        if($source_id != 0){
            $setting_type = Config::get('constant.setting_type');
            $data         = $this->content->findBySourceAndType($type_id, ['id' => $id],['name','related_id']);
            $related_name = $this->content->findBySourceAndType($type_id, ['source_id' => 0] , ['name','key','related_id']);
            return view('content_setting.edit_two',[
                          'data'            => $data->toArray(),
                          'setting_type'    => $setting_type,
                          'type_id'         => $type_id,
                          'id'              => $id,
                          'source_id'       => $source_id,
                          'related_name'    => $related_name
                        ]);
        } else {
            $setting_type = Config::get('constant.setting_type');
            $data         = $this->content->findBySourceAndType($type_id, ['id' => $id], ['name']);
            return view('content_setting.edit_one',[
                          'data'            => $data->toArray(),
                          'setting_type'    => $setting_type,
                          'type_id'         => $type_id,
                          'id'              => $id,
                           'source_id'      => $source_id
                        ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateContentRequest $request)
    {
        $id             = $request->id;
        $type_id        = $request->type_id;
        $source_id      = $request->source_id;
        $related_id     = $request->related_id;
        if($source_id == 0) {
            $name = \Helpers::mst_trim($request->releated_name);
            $this->content->update($type_id, ['name' => $name], $id);
        }else{
            $this->content->update($type_id, ['related_id' => $related_id], $id);
        }
        $parameters = ['type'       => $type_id,
                       'success'    => 'Update name success',
                       'source'  => $source_id
        ];
        return redirect()->route('content-management.index')->with($parameters);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
