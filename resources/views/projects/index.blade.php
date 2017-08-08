@extends('layouts.master')
@section('title','Project list')
@section('style')
    <link href="{{ asset('/css/custom/date-form.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/project/project.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/custom/css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/custom/cost.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/custom/kpi.projects.css') }}" rel="stylesheet">
@stop
@section('breadcrumbs','Project list')
@section('content')
<div class="padding-md">
    <div class="panel panel-default">
        <div class="panel-heading" id="form_heading">Project List</div>
        <div class="panel-body padding-md">
        <form method="GET" action="{{Route('projects.index')}}" accept-charset="UTF-8" class="form-horizontal" id="form-search" role="search">
            <div class="col-md-6">
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Project Name</label>
                        <div class="col-lg-7">
                            <input class="form-control input-sm" maxlength="255" name="name" type="text" value="{{Request::get('name','')}}">
                        </div><!-- /.col -->
                    </div><!-- /form-group -->
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Project Type</label>
                        <div class="col-lg-7">
                        <select class="form-control" name="type_id">
                            <option value="">-- All --</option>
                            @foreach($type as $key=>$value)
                                <option value="{{$key}}" {{ Request::get('type_id','') == $key ? 'selected' : '' }}>{{$value}}</option>
                            @endforeach
                        </select>
                        </div><!-- /.col -->
                    </div><!-- /form-group -->
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Status</label>
                        <div class="col-lg-7">
                        <select class="form-control" name="status">
                            @if(Request::get('status','') == '')
                                @foreach($status_id as $key=>$value)
                                    <option value="{{$key}}" {{ $key == 2 ? 'selected' : '' }}>{{$value}}</option>
                                @endforeach
                            @else
                                @foreach($status_id as $key=>$value)
                                    <option value="{{$key}}" {{ Request::get('status','') == $key ? 'selected' : '' }}>{{$value}}</option>
                                @endforeach
                            @endif
                        </select>
                        </div><!-- /.col -->
                    </div><!-- /form-group -->
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Project Language</label>
                        <div class="col-lg-7">
                        <select class="form-control" name="language_id">
                            <option value="">-- All --</option>
                            @foreach($language as $key=>$value)
                                <option value="{{$key}}" {{ Request::get('language_id','') == $key ? 'selected' : '' }}>{{$value}}</option>
                            @endforeach
                        </select>
                        </div><!-- /.col -->
                    </div><!-- /form-group -->
                </div>
            </div><!-- /.col -->
            <div class="col-md-6">
                <div class="panel-body">
                    <div class="form-group">
                        <label for="inputEmail1" class="col-lg-3 control-label">Department</label>
                        <div class="col-lg-7">
                            <select class="form-control" name="department" id="department_id">
                                <option value="-1"> -- All --</option>
                                @if(!empty($departments))
                                    @foreach($departments as $item)
                                        <option value="{{$item['id']}}" <?php if(Request::get('department') == $item['id']) echo "selected"?>>{{$item['name']}}</option>
                                    @endforeach
                                @endif
                            </select>
                            </select>
                        </div><!-- /.col -->
                    </div><!-- /form-group -->
                    <div class="form-group">
                        <label for="inputPassword1" class="col-lg-3 control-label">Division</label>
                        <div class="col-lg-7">
                            <select class="form-control" name="division" id="division_id">
                                <option value="-1"> -- All --</option>
                                @if(!empty($divisions))
                                    @foreach($divisions as $item)
                                        <option value="{{$item['id']}}" <?php if(Request::get('division') == $item['id']) echo "selected"?>>{{$item['name']}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div><!-- /.col -->
                    </div><!-- /form-group -->
                    <div class="form-group">
                        <label for="inputPassword1" class="col-lg-3 control-label">Team</label>
                        <div class="col-lg-7">
                            <select class="form-control" name="team" id="team_id">
                                <option value="-1"> -- All --</option>
                                @if(!empty($teams))
                                    @foreach($teams as $item)
                                        <option value="{{$item['id']}}"<?php if(Request::get('team') == $item['id']) echo "selected"?>>{{$item['name']}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div><!-- /.col -->
                    </div><!-- /form-group -->
                    <div class="form-group">
                        <label for="inputPassword1" class="col-lg-3 control-label">BSE</label>
                        <div class="col-lg-7">
                            <select class="form-control" name="bse">
                                @if(!empty($brse))
                                    <option value="" selected="selected">-- All --</option>
                                    @foreach($brse as $key=>$value)
                                        <option value="{{$key}}"<?php if(Request::get('bse') == $key) echo "selected"?>>{{$value}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div><!-- /.col -->
                    </div><!-- /form-group -->
                </div>
            </div><!-- /.col -->
            <div class="col-sm-5 col-sm-offset-5">
                <button type="submit" class="btn btn-primary">Search</button>
                @if ($user->hasAccess('user.create_project') || in_array($user->id, $managerIds))
                <a href="{{ URL::route('projects.create')}}" class="btn btn-success" role="button">Create</a>
                @else
                <a href="{{ URL::route('projects.create')}}" class="btn btn-success" disabled role="button">Create</a>
                @endif
            </div>
            </div>
        </form>
        </div>
            <div class="panel-body">
              <div class="panel-body">
                <div class="col-md-6">
                    @if(count($project) == 0)
                        <span class="text-left"><strong>Total number of records: 0</strong></span>
                    @else
                        <span class="text-left"><strong>Total number of records: {{ $project->total() }}</strong></span>
                    @endif
                </div>
                <div class="col-md-6">
                    <form method="get" class="pull-right">
                        <label for="choose_item">Item display on page: &nbsp; &nbsp;</label>
                        <input type="hidden" name="name"        value="{{Request::get('name','')}}">
                        <input type="hidden" name="type_id"     value="{{Request::get('type_id','')}}">
                        <input type="hidden" name="status"      value="{{Request::get('status','')}}">
                        <input type="hidden" name="language_id" value="{{Request::get('language_id','')}}">
                        <input type="hidden" name="department"  value="{{Request::get('department','')}}">
                        <input type="hidden" name="division"    value="{{Request::get('division','')}}">
                        <input type="hidden" name="team"        value="{{Request::get('team','')}}">
                        <input type="hidden" name="bse"         value="{{Request::get('bse','')}}">
                        <select id="choose_item" name="limit" class="form-control input-md inline-block" size="1" onchange="this.form.submit()">
                            @if(!empty($paginate))
                                @foreach($paginate as $key => $values)
                                    @if(Request::get('limit', 10) == $values)
                                        <option value="{{$key}}" selected>{{$values}}</option>
                                    @else
                                        <option value="{{$key}}">{{$values}}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </form>
                </div>
            </div>
                <div class="table-responsive" id="scroll-x">
                     <table class="table table-bordered table-hover table-striped tbl-project" id="responsiveTable">
                        <thead>
                        <tr>
                            <th width="5%"  class="text-center">No</th>
                            <th width="10%" class="text-center">Project name</th>
                            <th width="10%" class="text-center">Department</th>
                            <th width="7%" class="text-center">Division</th>
                            <th width="7%"  class="text-center">Team</th>
                            <th width="7%" class="text-center">Bse</th>
                            <th width="7%" class="text-center">Status</th>
                            <th width="5%" class="text-center">Plan Start</th>
                            <th width="5%"  class="text-center">Plan End</th>
                            <th width="8%" class="text-center">Project Type</th>
                            <th width="5%"  class="text-center">Project Language</th>
                            <th width="6%"  class="text-center">Active Status</th>
                            <th width="10%"  class="text-center">Sync</th>
                            <th width="8%"  class="text-center" >Action</th>
                        </tr>
                        </thead>
                        <tbody class="tbody">
                        @forelse ($project as $data)
                            <tr>
                               <td>{{ ++$stt }}</td>
                               <td class="text-left">
                               @if ($role_id == 1)
                                   <a  href="{{ URL::route('project.show',$data->id) }}" data-toggle="tooltip" title="{{ $data->name }}">
                                    {{ str_limit($data->name, 20) }}
                                   </a>
                               @else
                                  @if ($user->hasAccess('user.view_project_info'))
                                   <a  href="{{ URL::route('project.show',$data->id) }}" data-toggle="tooltip" title="{{ $data->name }}">
                                    {{ str_limit($data->name, 20) }}
                                   </a>
                                  @else
                                   <a data-toggle="tooltip" title="{{ $data->name }}">
                                    {{ str_limit($data->name, 20) }}
                                   </a>
                                  @endif
                               @endif
                               </td>
                               <td class="text-left">
                                @if(!empty($department))
                                  @foreach($department as $de)
                                    @foreach($division as $di)
                                        @foreach($team as $t)
                                            @if($data->department_id == $t['id'] && $t['parent_id'] == $di['id'] && $di['parent_id'] == $de['id'])
                                            <a data-toggle="tooltip" title="{{ $de['name'] }}">
                                            <?php $rest = substr($de['name'], -3,2); ?>
                                              @if($de['name'] != "Training Department" )
                                                {{ $rest }}
                                              @else
                                                {{ $de['name'] }}
                                              @endif
                                             </a>
                                            @endif
                                        @endforeach
                                    @endforeach
                                  @endforeach
                                @endif
                               </td>
                                <td class="text-left">
                              @if(!empty($division))
                                  @foreach($division as $di)
                                    @foreach($team as $t)
                                     @if($data->department_id == $t['id'] && $t['parent_id'] == $di['id'])
                                        <a data-toggle="tooltip" title="{{ $di['name'] }}">
                                            {{ \Illuminate\Support\Str::words($di['name'], $limit = 1) }}
                                         </a>
                                        @endif
                                    @endforeach
                                  @endforeach
                                @endif
                               </td>
                               <td class="text-left">
                               @if(!empty($team))
                                   @foreach($team as $t)
                                     @if($data->department_id == $t['id'])
                                        @if($t->status  == 0 )
                                       <a data-toggle="tooltip" title="Project hasn't been belong to any team. You must update Team for this project!" style="color:red">
                                          {{ \Illuminate\Support\Str::words($t['name'], $limit = 1) }}
                                        </a>
                                        @else
                                        <a data-toggle="tooltip" title="{{ $t['name'] }}">
                                          {{ \Illuminate\Support\Str::words($t['name'], $limit = 1) }}
                                        </a>
                                        @endif
                                      @endif
                                  @endforeach
                                @endif
                               </td>
                               <td class="text-left">
                               @if (!empty($brse[$data->brse]) )
                               <a data-toggle="tooltip" title="{{ $brse[$data->brse] }}">
                                    {{ \Illuminate\Support\Str::words($brse[$data->brse], $limit = 1) }}
                                </a>
                                @endif
                               </td>

                               <td class="text-left">
                               @if ($data->status !=0)
                                  {{ $status_id[$data->status] }}
                               @endif
                               </td>
                               <td class="text-left">
                                   @if( empty($data['plant_start_date']) || $data->plant_start_date == '0000-00-00')
                                   @else
                                   {{ date('d/m/Y',strtotime(str_replace('/', '-', $data['plant_start_date']))) }}
                                   @endif
                               </td>
                                <td class="text-left">
                                   @if(empty($data['plant_end_date']) || $data->plant_end_date == '0000-00-00')
                                   @else
                                   {{ date('d/m/Y',strtotime(str_replace('/', '-', $data['plant_end_date']))) }}
                                   @endif
                               </td>
                               <td>
                                   {{ \Illuminate\Support\Str::words($type[$data->type_id], $limit = 1) }}
                               </td>
                               <td>
                                   @if ($data->language_id !=0)
                                   {{ $language[$data->language_id]}}
                                   @endif
                               </td>
                               <td style="min-width: 60px">
                                    @if ($data->active == 1)
                                        <div id="status_active">Active</div>
                                    @else
                                        <div id="status_active">Inactive</div>
                                    @endif
                               </td>
                               <td style="min-width: 90px">
                               @if ($role_id == 1 || $role_id == 13 || in_array($userId, $managerIds))
                                    @if ($data->active == 0)
                                        <button class="btn btn-default" id="sync_status" disabled>Disable</button>
                                      @elseif (empty($data->project_id) && empty($data->project_key))
                                        <a data-target="#myModal" data-toggle="modal" data_id="{{ $data->id }}" href="javascript:void(0)" id = "sync_status" class="add_id_key btn btn-primary" role="button">Start</a>
                                      @elseif ($data->sync_flag == 0)
                                          <button class="btn btn-primary" id ="sync_status" project_id="{{$data->id}}" >Start</button>
                                      @elseif ($data->sync_flag == 1)
                                          <button class="btn btn-danger" id ="sync_status" project_id="{{$data->id}}">Stop</button>
                                      @endif
                                  @else
                                      @if ($data->active == 0)
                                      <button class="btn btn-default" id="sync_status" disabled>Disable</button>
                                      @elseif (empty($data->project_id) && empty($data->project_key))
                                           @if(count($data->permissions) > 0)
                                               @if(array_key_exists("user.change_status_sync",json_decode($data->permissions)))
                                                <a data-target="#myModal" data-toggle="modal" data_id="{{ $data->id }}" href="javascript:void(0)" class="add_id_key btn btn-primary" role="button">Start</a>
                                               @else
                                                <a data-target="#myModal" data-toggle="modal" data_id="{{ $data->id }}" id ="sync_status" class="add_id_key btn btn-primary" disabled role="button">Start</a>
                                               @endif
                                           @else
                                                <a data-target="#myModal" data-toggle="modal" data_id="{{ $data->id }}" id ="sync_status" class="add_id_key btn btn-primary" disabled role="button">Start</a>
                                           @endif
                                      @elseif ($data->sync_flag == 0)
                                            @if(count($data->permissions) > 0)
                                                @if(array_key_exists("user.change_status_sync",json_decode($data->permissions)))
                                                    <button class="btn btn-primary" id ="sync_status" project_id="{{$data->id}}" >Start</button>
                                                @else
                                                    <button class="btn btn-primary" id ="sync_status" project_id="{{$data->id}}" disabled>Start</button>
                                                @endif
                                            @else
                                                <button class="btn btn-primary" id ="sync_status" project_id="{{$data->id}}" disabled>Start</button>
                                            @endif
                                      @elseif ($data->sync_flag == 0)
                                            @if(count($data->permissions) > 0)
                                                @if(array_key_exists("user.change_status_sync",json_decode($data->permissions)))
                                                    <button class="btn btn-danger" id ="sync_status" project_id="{{$data->id}}">Stop</button>
                                                @else
                                                    <button class="btn btn-danger" id ="sync_status" project_id="{{$data->id}}" disabled>Stop</button>
                                                @endif
                                            @else
                                                <button class="btn btn-danger" id ="sync_status" project_id="{{$data->id}}" disabled>Stop</button>
                                            @endif
                                      @endif
                                  @endif
                               </td>
                                <td style="min-width: 90px">
                                @if ($role_id == 1 || $role_id == 13 || in_array($userId, $managerIds))
                                    <a href="{{ URL::route('project.edit' , $data->id) }}" data-toggle="tooltip" title="Update project info"> <i class="fa fa-edit fa-lg"></i></a>|
                                    <a href="{{ URL::route('projects.members.assign.index' , $data->id) }}" data-toggle="tooltip" title="Assign member"><i class="fa fa-user-plus" aria-hidden="true"></i></a>|
                                    @if ($data->active == 0)
                                    <a id ="active" project_id="{{$data->id}}" data-toggle="tooltip" title="Active project"><i class="fa fa-check-circle fa-lg"></i></a>
                                    @else
                                    <a id ="active" project_id="{{$data->id}}" data-toggle="tooltip" title="Inactive project"><i class="fa fa-times-circle fa-lg"></i></a>
                                    @endif
                                @else
                                      @if(count($data->permissions) > 0)
                                          @if(array_key_exists("user.update_project_info",json_decode($data->permissions)))
                                            <a href="{{ URL::route('project.edit' , $data->id) }}" data-toggle="tooltip" title="Update project info"> <i class="fa fa-edit fa-lg"></i></a>|
                                          @else
                                            <a href="{{ URL::route('project.edit' , $data->id) }}" class="disabled"><i class="fa fa-edit fa-lg disabled" id = "disabled"></i></a>|
                                          @endif
                                          @if(array_key_exists("user.view_member",json_decode($data->permissions)))
                                            <a href="{{ URL::route('projects.members.assign.index' , $data->id) }}" data-toggle="tooltip" title="Assign member"><i class="fa fa-user-plus" aria-hidden="true"></i></a>|
                                          @else
                                            <a href="{{ URL::route('projects.members.assign.index' , $data->id) }}" class="disabled"><i class="fa fa-user-plus" aria-hidden="true"></i></a>|
                                          @endif
                                          @if(array_key_exists("user.active_inactive_project",json_decode($data->permissions)))
                                            @if ($data->active == 0)
                                            <a id ="active" project_id="{{$data->id}}" data-toggle="tooltip" title="Active project"><i class="fa fa-check-circle fa-lg"></i></a>
                                            @else
                                            <a id ="active" project_id="{{$data->id}}" data-toggle="tooltip" title="Inactive project"><i class="fa fa-times-circle fa-lg"></i></a>
                                            @endif
                                          @else
                                            @if ($data->active == 0)
                                            <a id ="active" project_id="{{$data->id}}" class="disabled"><i class="fa fa-check-circle fa-lg"></i></a>
                                            @else
                                            <a id ="active" project_id="{{$data->id}}" class="disabled"><i class="fa fa-times-circle fa-lg"></i></a>
                                            @endif
                                          @endif
                                      @else
                                           <a class="disabled"><i style="color: #b1bcce" class="fa fa-edit fa-lg"></i></a>|
                                           <a class="disabled"><i style="color: #b1bcce" class="fa fa-user-plus"></i></a>|
                                           <a class="disabled"><i style="color: #b1bcce" class="fa fa-times-circle fa-lg"></i></a>
                                      @endif
                                  @endif
                                </td>
                             </tr>
                        @empty
                        @endforelse
                        </tbody>
                    </table>
                </div><!-- /table-responsive -->
                 <div class="page-right padding-md">
                {{
                    $project->appends(array(
                        'limit'       => Request::get('limit', 10),
                        'name'        => Request::get('name',''),
                        'type_id'     => Request::get('type_id',''),
                        'status'      => Request::get('status',''),
                        'language_id' => Request::get('language_id',''),
                        'department'  => Request::get('department',''),
                        'division'    => Request::get('division',''),
                        'team'        => Request::get('team',''),
                        'bse'         => Request::get('bse',''),
                        )
                    )->links()
                }}
                 </div><!-- page-right -->
            </div>
        </div><!-- /panel-body -->
    </div><!-- /panel -->
@stop
@section('modal')
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Sync</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{Route('project.sync')}}" accept-charset="UTF-8" class="form-horizontal form-border no-margin" id="login-form">
            {{ csrf_field() }}
            <input id="id_project" name="project" type="hidden">
            <div class="form-group {{ $errors->has('project_id') ? ' has-error' : null }}">
            @if (Session::has('message'))
               <div class="alert alert-info">{{ Session::get('message') }}</div>
            @endif
              <div class="col-sm-3">
                <div class="radio" style="float: right;">
                      <label>
                        <input type="radio" name="stack-radio" id="radio_id" value="1" {{ $errors->has('project_id') ? 'checked' : 'checked'}}>
                        Project ID
                        </label>
                    </div>
              </div>
              <div class="col-sm-8">
                   <input class="form-control input-sm" id="project_id" {{ $errors->has('project_key') ? 'disabled' : '' }} name="project_id" type="text" value="">
                  <p class="help-block">{{ ($errors->has('project_id') ? $errors->first('project_id') : '') }}</p>
              </div><!-- /.col -->
            </div>
            <div class="form-group {{ $errors->has('project_key') ? ' has-error' : null }}">
              <div class="col-sm-3">
                <div class="radio" style="float: right;">
                   <label>
                   <input type="radio" name="stack-radio" id="radio_key" value="2" {{ $errors->has('project_key') ? 'checked' : ''}}>
                   Project Key
                   </label>
                </div>
              </div>
              <div class="col-sm-8">
                 <input class="form-control input-sm" id="project_key" {{ $errors->has('project_key') ? '' : 'disabled'}} name="project_key" type="text">
                  <p class="help-block">{{ ($errors->has('project_key') ? $errors->first('project_key') : '') }}</p>
              </div><!-- /.col -->
            </div>
              <div class="form-group {{ $errors->has('source_id') ? ' has-error' : null }}">
                  <label for="title" class="col-sm-3 control-label">Source</label>
                  <div class="col-sm-8">
                    <select class="form-control" id="source_id" name="source_id">
                        <option value="" selected="selected"></option>
                        @foreach($source_id as $key=>$value)
                            <option value="{{$key}}">{{$value}}</option>
                        @endforeach
                    </select>
                      <p class="help-block">{{ ($errors->has('source_id') ? $errors->first('source_id') : '') }}</p>
                    </div>
                </div>
                 <div class="alert alert-danger hide">
                        </div>
                        <div class="alert alert-success hide">
                        </div>
             <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                {{Form::submit('Update',array('class'=>'btn btn-success','id'=>'submit'))}}
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
@stop
@section('script')
<script src="{{ asset('/js/project/sync.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(".add_id_key").click(function(e){
            $('#id_project').val($(this).attr('data_id'));
        });
        <?php if(count($errors) > 0){?>
        $('#myModal').modal({ 'show' : true });
        <?php }?>
    });
</script>
<script src="{{ asset('/js/project/crawler.js') }}"></script>
<script src="{{ asset('/js/common/ajax_company_struct.js') }}"></script>
<script src="{{ asset('/js/project/index.js') }}"></script>

@stop