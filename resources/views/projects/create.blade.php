@extends('layouts.master')
@section('title','Structure management')
@section('breadcrumbs','Create project')
@section('content')
<div class="padding-md">
    <div class="main-header clearfix">
      <div class="page-title">
        <h3 class="no-margin">Create project</h3>
      </div>
    </div>
    <div class="panel panel-body panel-default">
      <div class="panel-body">
            {!! Form::open(array('method' => 'POST','class'=>'form-horizontal' , 'route' => 'projects.store', 'id'=>'form-search-users', 'role'=>'search')) !!}
           <div class="form-group row">
            <div class="col-6 {{ $errors->has('department') ? ' has-error' : '' }}">
                <label for="title" class="col-sm-2 control-label">Department:<span class="field-asterisk">*</span></label>
                <div class="col-sm-3 {{ $errors->has('department') ? ' has-error' : '' }}">
                  <select class="form-control" name="department" id="department_id" role="department">
                    <option value="-1">-- All --</option>
                    @foreach($departments as $department)
                        @if(old('department') == $department['id'])
                            <option value="{{$department['id']}}" selected='selected'>{{ \Illuminate\Support\Str::words($department['name'], $limit = 3) }}</option>
                        @else
                            <option value="{{$department['id']}}" >{{ \Illuminate\Support\Str::words($department['name'], $limit = 3) }}</option>
                        @endif
                    @endforeach
                  </select>
                    <p class="help-block">{{ ($errors->has('department') ? $errors->first('department') : '') }}</p>
                </div>
            </div>
            <div class="col-6 {{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="title" class="col-sm-3 control-label">Project name:<span class="field-asterisk">*</span></label>
                <div class="col-sm-3">
                  {!! Form::text('name',null,['class'=>'form-control input-sm', 'maxlength' => '255']) !!}
                    <p class="help-block">{{ ($errors->has('name') ? $errors->first('name') : '') }}</p>
                </div>
            </div>
           </div>
           <div class="form-group row">
            <div class="col-6 {{ $errors->has('division') ? ' has-error' : '' }}">
                <label for="title" class="col-sm-2 control-label">Division:<span class="field-asterisk">*</span></label>
                    <div class="col-sm-3 {{ $errors->has('division') ? ' has-error' : '' }}">
                    <select class="form-control" name="division" id="division_id" role="division">
                       <option value="-1" selected='selected'>-- All --</option>
                            @if(!empty(old('division')))
                              @foreach($divisions as $division)
                               @if($division['parent_id'] == old('department'))
                                    @if(old('division') == $division['id'])
                                        <option value="{{$division['id']}}" selected='selected'>{{$division['name']}}</option>
                                    @else
                                        <option value="{{$division['id']}}" >{{$division['name']}}</option>
                                    @endif
                                @endif
                              @endforeach
                            @endif
                    </select>
                        <p class="help-block">{{ ($errors->has('division') ? $errors->first('division') : '') }}</p>
                    </div>
            </div>
            <div class="col-6 {{ $errors->has('type_id') ? ' has-error' : '' }}">
               <label for="title" class="col-sm-3 control-label">Project type:<span class="field-asterisk">*</span></label>
                <div class="col-sm-3">
                  {!! Form::select('type_id', array()+$type_id, null, ['class'=>'form-control']) !!}
                    <p class="help-block">{{ ($errors->has('type_id') ? $errors->first('type_id') : '') }}</p>
                </div>
            </div>
           </div>
           <div class="form-group row">
            <div class="col-6 {{ $errors->has('department_id') ? ' has-error' : '' }}">
                <label for="title" class="col-sm-2 control-label">Team:<span class="field-asterisk">*</span></label>
                <div class="col-sm-3 {{ $errors->has('team_id') ? ' has-error' : '' }}">
                  <select class="form-control" name="department_id" id="team_id" role="team">
                    <option value="-1" selected='selected'>-- All --</option>
                    @if(!empty(old('department_id')))
                      @foreach($teams as $team)
                      @if($team['parent_id'] == old('division'))
                        @if(old('department_id') == $team['id'])
                            <option value="{{$team['id']}}" selected='selected'>{{$team['name']}}</option>
                        @else
                            <option value="{{$team['id']}}" >{{$team['name']}}</option>
                        @endif
                      @endif
                    @endforeach
                   @endif
                </select>
                    <p class="help-block">{{ ($errors->has('department_id') ? $errors->first('department_id') : '') }}</p>
                </div>
            </div>
            <div class="col-6 {{ $errors->has('status') ? ' has-error' : '' }}">
                <label for="title" class="col-sm-3 control-label">Status:<span class="field-asterisk">*</span></label>
                <div class="col-sm-3">
                  {!! Form::select('status', array()+$status, null, ['class'=>'form-control']) !!}
                    <p class="help-block">{{ ($errors->has('status') ? $errors->first('status') : '') }}</p>
                </div>
            </div>
           </div>
           <div class="form-group row">
            <div class="col-6 {{ $errors->has('brse') ? ' has-error' : '' }}">
                <label for="title" class="col-sm-2 control-label">BSE:<span class="field-asterisk">*</span></label>
                <div class="col-sm-3">
                  {!! Form::select('brse', array('' => '-- All --')+$brse, null, ['class'=>'form-control']) !!}
                    <p class="help-block">{{ ($errors->has('brse') ? $errors->first('brse') : '') }}</p>
                </div>
            </div>
             <div class="col-6 {{ $errors->has('language_id') ? ' has-error' : '' }}">
                <label for="title" class="col-sm-3 control-label">Project language:</label>
                <div class="col-sm-3">
                  {!! Form::select('language_id', array()+$language_id,3, ['class'=>'form-control']) !!}
                    <p class="help-block">{{ ($errors->has('language_id') ? $errors->first('language_id') : '') }}</p>
                </div>
            </div>
           </div>
           <div class="form-group row">
            <div class="col-6 {{ $errors->has('plant_start_date') ? ' has-error' : '' }}">
                <label for="title" class="col-sm-2 control-label">Plan start date:<span class="field-asterisk">*</span></label>
                <div class="col-sm-3">
                  <div class='input-group'>
                    {!! Form::text('plant_start_date','',['class'=>'form-control','id' => 'start_date', 'onpaste'=>'return false']) !!}
                    <span class="input-group-addon open-startdate">
                        <span class="glyphicon glyphicon-calendar open-startdate"></span>
                    </span>
                   </div>
                    <p class="help-block">{{ ($errors->has('plant_start_date') ? $errors->first('plant_start_date') : '') }}</p>
                </div>
            </div>
            <div class="col-6 {{ $errors->has('plant_end_date') ? ' has-error' : '' }}">
                <label for="title" class="col-sm-3 control-label">Plan end date :<span class="field-asterisk">*</span></label>
                <div class="col-sm-3">
                  <div class='input-group'>
                    {!! Form::text('plant_end_date','',['class'=>'form-control','id' => 'end_date', 'onpaste'=>'return false']) !!}
                    <span class="input-group-addon open-enddate">
                        <span class="glyphicon glyphicon-calendar open-enddate"></span>
                    </span>
                   </div>
                    <p class="help-block">{{ ($errors->has('plant_end_date') ? $errors->first('plant_end_date') : '') }}</p>
                </div>
            </div>
           </div>
            <div class="col-sm-5 col-sm-offset-5">
            <button id="createProject" type="submit" class="btn btn-success">Create</button>
            <a href="{{ URL::route('projects.index') }}" class="btn btn-primary" role="button">Cancel</a>
        </div>
        {!! Form::close() !!}
        </div><!-- /panel-body -->
    </div><!-- /panel -->
</div>
@stop
@section('script')
  <script type="text/javascript" src="{{ asset('/js/project/project.js') }}"></script>
  <script type="text/javascript" src="{{ asset('/js/project_version/version.js') }}"></script>
  <script type="text/javascript" src="{{ asset('/js/project/select_department.js') }}"></script>
@stop