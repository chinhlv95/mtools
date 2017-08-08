@extends('layouts.master')
@section('title','Create project version')
@section('breadcrumbs','Create project version')
@section('content')
<div class="padding-md">
    <div class="main-header clearfix">
      <div class="page-title">
        <h3 class="no-margin">Create project version</h3>
      </div>
    </div>
    <div class="panel panel-body panel-default">
      <div class="panel-body">
        {!! Form::open(array('method' => 'POST','class'=>'form-horizontal','url'=>Route('version.store', $project_id) )) !!}
         <div class="form-group">
                <label for="title" class="col-sm-3 control-label">Project name</label>
                <div class="col-sm-6">
                    {!! Form::text('title',$project->name,['class'=>'form-control','readonly' =>'readonly']) !!}
                </div>
            </div>
            <div class="form-group">
                <label for="title" class="col-sm-3 control-label">Source</label>
                <div class="col-sm-6">
                    {!! Form::text('source',$source[$project->source_id],['class'=>'form-control','readonly' =>'readonly']) !!}
                </div>
            </div>
            <div class="form-group {{ ($errors->has('name')) ? 'has-error' : '' }}">
                <label for="title" class="col-sm-3 control-label">Name<span class="field-asterisk">*</span></label>
                <div class="col-sm-6">
                    {!! Form::text('name',null,['class'=>'form-control', 'maxlength' => '255']) !!}
                    <p class="help-block">{{ ($errors->has('name') ? $errors->first('name') : '') }}</p>
                </div>
            </div>
            <div class="form-group {{ ($errors->has('status')) ? 'has-error' : '' }}">
                <label for="title" class="col-sm-3 control-label">Status<span class="field-asterisk">*</span></label>
                <div class="col-sm-6">
                    {!! Form::select('status', array('' => '')+$status, null, ['class'=>'form-control']) !!}
                    <p class="help-block">{{ ($errors->has('status') ? $errors->first('status') : '') }}</p>
                </div>
            </div>
            <div class="form-group {{ ($errors->has('description')) ? 'has-error' : '' }}">
                <label for="title" class="col-sm-3 control-label">Description</label>
                <div class="col-sm-6">
                    {!! Form::textarea('description',null,['class'=>'form-control','rows'=>3]) !!}
                    <p class="help-block">{{ ($errors->has('description') ? $errors->first('description') : '') }}</p>
                </div>
            </div>
            <div class="form-group {{ ($errors->has('start_date')) ? 'has-error' : '' }}">
                <label for="title" class="col-sm-3 control-label"> Plan date<span class="field-asterisk">*</span></label>
                <div class="col-sm-6">
                <div class='input-group'>
                    {!! Form::text('start_date','',['class'=>'form-control', 'id' => 'start_date', 'onpaste'=>'return false']) !!}
                    <span class="input-group-addon open-startdate">
                        <span class="glyphicon glyphicon-calendar open-startdate"></span>
                    </span>
                </div>
                    <p class="help-block">{{ ($errors->has('start_date') ? $errors->first('start_date') : '') }}</p>
                </div>
            </div>
            <div class="form-group {{ ($errors->has('end_date')) ? 'has-error' : '' }}">
                <label for="title" class="col-sm-3 control-label">Actual date<span class="field-asterisk">*</span></label>
                <div class='col-sm-6'>
                    <div class='input-group'>
                        {!! Form::text('end_date','',['class'=>'form-control ','id' => 'end_date', 'onpaste'=>'return false']) !!}
                        <span class="input-group-addon open-enddate">
                            <span class="glyphicon glyphicon-calendar open-enddate"></span>
                        </span>
                    </div>
                 <p class="help-block">{{ ($errors->has('end_date') ? $errors->first('end_date') : '') }}</p>
              </div>
            </div>
            <div class="col-sm-5 col-sm-offset-5">
                <button id="createProject" type="submit" class="btn btn-success">Create</button>
                <a href="{{ URL::route('version.index',$project_id) }}" class="btn btn-danger" role="button">Cancel</a>
            </div>
        {!! Form::close() !!}
      </div><!-- /panel-body -->
    </div><!-- /panel -->
</div>
@stop
@section('script')
<script type="text/javascript" src="{{ asset('/js/project_version/version.js') }}"></script>
@stop