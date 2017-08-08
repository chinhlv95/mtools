@extends('layouts.master')
@section('title', 'User Management')

@section('breadcrumbs','User Management')
@section('style')
    <link href="{{ asset('css/custom/date-form.css') }}" rel="stylesheet">
@stop
@section('content')
<div class="padding-md">
    <div class="alert alert-danger hide" id="errorMessage">
        <button type="button" class="close closeMessage">
            <i class="fa fa-times"></i>
        </button>
        <span id="message"></span>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" id="form_heading">Admin Setting - User Management</div>
            <div class="panel-body" id="form_body">
                <form method="get" id="search_form" class="form-horizontal" enctype="multipart/form-data">
                    <div class="col-md-6">
                        <div class="panel-body">
                            <div class="form-group">
                                    <label class="col-lg-3 control-label">
                                        Status
                                    </label>
                                    <div class="col-lg-7">
                                        <select class="form-control" name="status">
                                            <option value=""> -- All --</option>
                                            @foreach($status_user as $key=>$value)
                                                <option value="{{$key}}" <?php if(Request::get('status') == $key) echo 'selected';?>>{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                            </div>
                            <div class="form-group">
                                    <label class="col-lg-3 control-label">
                                        Role
                                    </label>
                                    <div class="col-lg-7">
                                        <select class="form-control" name="role_id" id="role_id">
                                            <option value=""> -- All --</option>
                                            @foreach($roles as $role)
                                                <option <?php if(Request::get('role_id') == $role->id) echo 'selected';?> value="{{$role->id}}">{{$role->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel-body">
                            <div class="form-group">
                                    <label class="col-lg-3 control-label">
                                        ID/Name/Email
                                    </label>
                                    <div class="col-lg-7">
                                        <input class="form-control input-sm" value="{{Request::get('name','')}}" maxlength="255" name="name" type="text">
                                    </div>
                            </div>
                            <div class="form-group">
                                    <label class="col-lg-3 control-label">
                                        Sub email
                                    </label>
                                    <div class="col-lg-7">
                                        <select class="form-control" name="type" id="type">
                                            <option value=""> -- All --</option>
                                            @foreach($types as $type)
                                                <option <?php if(Request::get('type') == $type['id']) echo 'selected';?> value="{{$type['id']}}">{{$type['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="limit" value="{{Request::get('limit',10)}}">
                    <div class="col-sm-5 col-sm-offset-5">
                            <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>
            </div><!-- /panel-body -->
            <div class="panel-body">
              <div class="panel-body">
                <div class="col-md-6">
                        <span class="text-left"><strong>Total number of records: {{$count}}</strong></span>
                </div>
                <div class="col-md-6">
                    <form method="get" class="pull-right">
                        <label for="choose_item">Item display on page: &nbsp; &nbsp;</label>
                        <input type="hidden" name="role_id" value="{{Request::get('role_id','')}}">
                        <input type="hidden" name="status" value="{{Request::get('status','')}}">
                        <input type="hidden" name="name" value="{{Request::get('name','')}}">
                        <select id="choose_item" name="limit" class="form-control input-sm inline-block" size="1" onchange="this.form.submit()">
                            @foreach($paginate_number as $key=>$value)
                                <option value="{{$key}}" <?php if(Request::get('limit') == $value) echo 'selected';?>>{{$value}}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
                <div class="table-responsive" id="scroll-x">
                     <table class="table table-bordered table-hover table-striped tbl-project" id="responsiveTable">
                        <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="10%" class="text-center">Name</th>
                            <th width="10%" class="text-center">Member Code</th>
                            <th with="20%" class="text-center">Email</th>
                            <th with="10%" class="text-center">Username - Source</th>
                            <th width="15%" class="text-center">Sub User</th>
                            <th width="10%" class="text-center">Role</th>
                            <th width="10%" class="text-center">Last Login</th>
                            <th width="10%" class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody class="tbody">
                            @foreach($users as $user)
                                <tr>
                                    <td>{{++$stt}}</td>
                                    <td class="text-left">{{ $user->last_name." ".$user->first_name}}</td>
                                    <td class="text-left">{{$user->member_code}}</td>
                                    <td class="text-left">{{$user->email}}</td>
                                    <?php $source = $sources[$user->source_id];?>
                                    <td class="text-left">@if(!empty($source)){{$user->user_name}} - {{$source}}@endif</td>
                                    <td class="text-left">
                                        <ol>
                                            @foreach($user->subSources as $subSource)
                                                <li>{{$subSource}}</li>
                                            @endforeach
                                        </ol>
                                    </td>
                                    <td class="text-left">
                                        @foreach($user->roles as $role)
                                            {{$role->name}}
                                        @endforeach
                                    </td>
                                    <td>{{date("d-m-Y h:m",strtotime($user->last_login))}}</td>
                                    <td>
                                        @if(!($user->id == Sentinel::check()->id))
                                            @if(!Activation::completed($user))
                                                <a class="lock" href="{{Route('user-management.lockUser',['id'=>$user->id])}}"><i class="fa fa-lock" aria-hidden="true"></i>UnLock</a>|
                                            @else
                                                <a class="lock" href="{{Route('user-management.lockUser',['id'=>$user->id])}}"><i class="fa fa-unlock" aria-hidden="true"></i>Lock</a>|
                                            @endif
                                            <a class="lock" href="{{Route('user-management.editUser',['id'=>$user->id,
                                                'status' => Request::get('status'),
                                                'role_id' => Request::get('role_id'),
                                                'name' => Request::get('name'),
                                                'limit' => Request::get('limit'),
                                                'page' => Request::get('page', 1),
                                                'type' => Request::get('type')])}}">
                                                <i class="fa fa-pencil fa-lg" title="Edit User" aria-hidden="true"></i>Edit</a>
                                        @endif
                                      </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div><!-- /table-responsive -->
                 <div class="text-right">
                    {{
                        $users->appends(array(
                            'limit'   => Request::get('limit',10),
                            'status'  => Request::get('status',''),
                            'role_id' => Request::get('role_id',''),
                            'name'    => Request::get('name',''),
                            )
                        )->links()
                    }}
                </div>
            </div>
            <hr>
        </div><!-- /panel-default -->
    </div><!-- /padding-md -->
</div>
@stop
@section('modal')
<div id="editModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>Change role user</h4>
            </div>
            <div class="modal-body roles_body" style="max-height: calc(100vh - 120px);overflow-y: auto;">

            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
@stop