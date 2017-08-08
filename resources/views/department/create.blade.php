@extends('layouts.master')
@section('title','Create department')
@section('breadcrumbs','Create department')
@section('style')
   <link href="{{ asset('/css/chosen/chosen.min.css') }}" rel="stylesheet">
@stop
@section('content')
<div class="padding-md">
    <div class="main-header clearfix">
      <div class="page-title">
        <h3 class="no-margin">Create department</h3>
      </div>
    </div>
    <div class="panel panel-body panel-default">
      <div class="panel-body">
        {!! Form::open(array('method' => 'POST','class'=>'form-horizontal','url'=>Route('department.store') )) !!}
        <div class="form-group {{ ($errors->has('name')) ? 'has-error' : '' }}">
            <label for="title" class="col-sm-3 control-label">Name<span class="field-asterisk">*</span></label>
            <div class="col-sm-6">
                {!! Form::text('name',null,['class'=>'form-control', 'maxlength' => '255']) !!}
                <p class="help-block">{{ ($errors->has('name') ? $errors->first('name') : '') }}</p>
            </div>
        </div>
        <div class="form-group {{ ($errors->has('parent_id')) ? 'has-error' : '' }}">
            <label for="title" class="col-sm-3 control-label">Parent ID</label>
            <div class="col-sm-6">
                <select class="form-control" name="parent_id">
                    <option value="0"></option>
                    <?php parent($department);  ?>
                </select>
                <p class="help-block">{{ ($errors->has('parent_id') ? $errors->first('parent_id') : '') }}</p>
            </div>
        </div>
        <div class="form-group {{ ($errors->has('manager_id')) ? 'has-error' : '' }}">
            <label for="title" class="col-sm-3 control-label">Manager<span class="field-asterisk">*</span></label>
            <div class="col-sm-6">
                {!! Form::select('manager_id', array('' => '')+$manager_id, null, ['class'=>'form-control chzn-select']) !!}
                <p class="help-block">{{ ($errors->has('manager_id') ? $errors->first('manager_id') : '') }}</p>
            </div>
        </div>
        <div class="form-group {{ ($errors->has('description')) ? 'has-error' : '' }}">
            <label for="title" class="col-sm-3 control-label">Description</label>
            <div class="col-sm-6">
                {!! Form::textarea('description',null,['class'=>'form-control','rows'=>3]) !!}
                <p class="help-block">{{ ($errors->has('description') ? $errors->first('description') : '') }}</p>
            </div>
        </div>
        <div class="col-sm-5 col-sm-offset-5">
            <button id="createProject" type="submit" class="btn btn-success">Create</button>
            <a href="{{ URL::route('department.index') }}" class="btn btn-danger" role="button">Cancel</a>
        </div>
        {!! Form::close() !!}
      </div><!-- /panel-body -->
    </div><!-- /panel -->
</div>
@stop
@section('script')
<script src="{{ asset('/js/chosen.jquery.min.js') }}"></script>
<script type="text/javascript">
$(function(){
    $(".chzn-select").chosen();
});
</script>
@stop