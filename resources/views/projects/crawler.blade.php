@extends('layouts.master')
@section('title','Crawler Data')
@section('breadcrumbs','Crawler Data')
@section('content')
<div class="padding-md">
    <div class="main-header clearfix">
      <div class="page-title">
        <h3 class="no-margin">Crawler Data</h3>
      </div>
    </div>
    <div class="panel panel-body panel-default">
      <div class="panel-body">
        {!! Form::model($project, ['method' => 'POST','url'=>Route('project.crawlerupdate', $project['id']), 'class' => 'form-horizontal']) !!}
        <div class="form-group row">
           <div class="col-10">
              <div class="col-sm-4">
                    <div class="radio" style="float: right;">
                      <label>
                        <input type="radio" name="stack-radio" id="radio_id" value="1" checked="checked">
                        Project ID
                        </label>
                    </div>
                </div>
              <div class="col-sm-4">
                  {!! Form::text('project_id',$project->project_id,['class'=>'form-control input-sm', 'id' => 'project_id']) !!}
                </div>
           </div>
        </div>
        <div class="form-group row">
           <div class="col-10">
              <div class="col-sm-4">
                <div class="radio" style="float: right;">
                  <label>
                    <input type="radio" name="stack-radio" id="radio_key" value="2">
                    Project Key
                    </label>
                </div>
                </div>
              <div class="col-sm-4">
                  {!! Form::text('project_key',$project->project_key,['class'=>'form-control input-sm', 'id'=>'project_key', 'disabled']) !!}
                </div>
           </div>
        </div>
        <div class="form-group row">
           <div class="col-10">
              <label for="title" class="col-sm-4 control-label">Source</label>
              <div class="col-sm-4">
                  {!! Form::select('source_id', array('' => '')+$source_id, null, ['class'=>'form-control']) !!}
                </div>
           </div>
        </div>
        <div class="col-sm-5 col-sm-offset-5">
            <button type="submit" class="btn btn-success">Sync</button>
            <a href="{{ URL::route('projects.index') }}" class="btn btn-danger" role="button">cancel</a>
        </div>
        {!! Form::close() !!}
        </div><!-- /panel-body -->
    </div><!-- /panel -->
</div>
@stop
@section('script')
  <script src="{{ asset('/js/project/crawler.js') }}"></script>
@stop