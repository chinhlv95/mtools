@extends('layouts.master')
@section('title','Edit User')
@section('breadcrumbs','User Management / Edit User')
@section('content')
<div class="padding-md">
    <div class="main-header clearfix">
        <div class="page-title">
            <h3 class="no-margin">
                Edit User - {{ $mainUsers->last_name.' '.$mainUsers->first_name }} {{ $mainUsers->member_code}}
            </h3>
        </div>
    </div>
    <div class="panel panel-body panel-default">
        <div class="panel-body">
             <form method="POST" action="{{Route('user-management.editUser',['id'=>$user->id])}}" accept-charset="UTF-8" class="form-horizontal" id="frmEditUser">
             {{ csrf_field() }}
                <input type="hidden" name="name" value="{{Request::get('name')}}"/>
                <input type="hidden" name="limit" value="{{Request::get('limit', 10)}}"/>
                <input type="hidden" name="page" value="{{Request::get('page', 1)}}"/>
                <input type="hidden" name="status" value="{{Request::get('status')}}"/>
                <input type="hidden" name="role_id" value="{{Request::get('role_id')}}"/>
                <input type="hidden" name="type" value="{{Request::get('type')}}"/>
                 <div class="col-md-6">
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-lg-3 control-label">First name:<span class="field-asterisk">*</span></label>
                            <div class="col-lg-7">
                                <input type="text" name="first_name" value="{{old('first_name') ? old('first_name') : $mainUsers->first_name}}" class="form-control input-sm"/>
                            </div><!-- /.col -->
                        </div><!-- /form-group -->
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Last name:</label>
                            <div class="col-lg-7">
                                <input type="text" name="last_name" value="{{old('last_name') ? old('last_name') : $mainUsers->last_name}}" class="form-control input-sm"/>
                            </div><!-- /.col -->
                        </div><!-- /form-group -->
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Email:</label>
                            <div class="col-lg-7">
                                <input type="email" name="main_email" class="form-control" id="main_email" value="{{$mainUsers->email}}" readonly="readonly">
                            </div><!-- /.col -->
                        </div><!-- /form-group -->
                        <?php $source = $listSources[$mainUsers->source_id];?>
                        @if(!empty($source))
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Source:</label>
                                <div class="col-lg-7">
                                    <input type="email" name="user_source" class="form-control" id="user_source" value="{{$source}}" readonly="readonly">
                                </div><!-- /.col -->
                            </div><!-- /form-group -->
                        @endif
                        @if(!empty(old('sub_user')))
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Sub user:</label>
                                <select multiple="multiple" name="sub_user[]" id="sub_user" class="col-lg-7">
                                    @foreach($userNotSub as $user)
                                        <option value="{{$user['id']}}" @if(in_array($user['id'], old('sub_user'))) selected @endif>
                                            {{$user['first_name']}} - {{$user['user_name']}} - {{$user['email']}} - {{$user['source']}}
                                        </option>
                                    @endforeach
                                </select>
                            </div><!-- /form-group -->
                        @else
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Sub user:</label>
                                <select multiple="multiple" name="sub_user[]" id="sub_user" class="col-lg-7">
                                    @foreach($userNotSub as $user)
                                        <option value="{{$user['id']}}" @if(in_array($user['id'], $subUsers)) selected @endif>
                                            {{$user['first_name']}} - {{$user['user_name']}} - {{$user['email']}} - {{$user['source']}}
                                        </option>
                                    @endforeach
                                </select>
                            </div><!-- /form-group -->
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-sm-5">
                                <label class="col-lg-7 control-label">Administrator:</label>
                                <div class="col-lg-3">
                                    <input type="checkbox" name="administrator" value="1" class="form-control input-sm"
                                        @if($role == 1) checked @endif @if(old('administrator') == 1) checked @endif/>
                                </div><!-- /.col -->
                            </div>
                        </div><!-- /form-group -->
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Password:</label>
                            <div class="col-lg-7">
                                <input type="password" name="password" value="" class="form-control input-sm"/>
                            </div><!-- /.col -->
                        </div><!-- /form-group -->
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Confirmation:</label>
                            <div class="col-lg-7">
                                <input type="password" name="password_confirmation" value="" class="form-control input-sm"/>
                            </div><!-- /.col -->
                        </div><!-- /form-group -->
                    </div>
                </div>
               <div class="col-sm-5 col-sm-offset-5">
                    <button id="editUser" type="submit" class="btn btn-success">Update</button>
                    <a href="{{Route('user-management.index',[
                                    'status' => Request::get('status'),
                                    'role_id' => Request::get('role_id'),
                                    'name' => Request::get('name'),
                                    'limit' => Request::get('limit'),
                                    'page' => Request::get('page', 1),
                                    'type' => Request::get('type')])}}" class="btn btn-primary" role="button">Cancel</a>
                </div>
             </form>
        <div class="form-group row">
    </div>
</div>
@stop
@section('script')
    <script type="text/javascript" src="{{asset('/js/user_management/editUser.js')}}"></script>
    <script type="text/javascript" src="{{asset('/js/select2/select2.min.js')}}"></script>
@stop