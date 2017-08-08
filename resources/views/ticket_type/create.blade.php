@extends('layouts.master')
@section('title','Create project version')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@section('breadcrumbs','Create ticket type')
@section('content')
<div class="padding-md">
    <div class="main-header clearfix">
      <div class="page-title">
        <h3 class="no-margin">Create ticket type</h3>
      </div>
    </div>
    <div class="panel panel-body panel-default">
      <div class="panel-body">
        {!! Form::open(array('method' => 'POST','class'=>'form-horizontal','url'=>Route('ticket_type.store') )) !!}
            <div class="form-group {{ ($errors->has('name')) ? 'has-error' : '' }}">
                <label for="title" class="col-sm-3 control-label">Name<span class="field-asterisk">*</span></label>
                <div class="col-sm-6">
                    {!! Form::text('name',null,['class'=>'form-control', 'maxlength' => '255']) !!}
                    <p class="help-block">{{ ($errors->has('name') ? $errors->first('name') : '') }}</p>
                </div>
            </div>
            <div class="form-group {{ ($errors->has('source_id')) ? 'has-error' : '' }}">
                <label for="title" class="col-sm-3 control-label">Source<span class="field-asterisk">*</span></label>
                <div class="col-sm-6">
                    {!! Form::select('source_id', array('' => '')+$source_id, null, ['class'=>'form-control']) !!}
                    <p class="help-block">{{ ($errors->has('source_id') ? $errors->first('source_id') : '') }}</p>
                </div>
            </div>
            <div class="form-group {{ ($errors->has('related_id')) ? 'has-error' : '' }}">
                <label for="title" class="col-sm-3 control-label">Related ID<span class="field-asterisk">*</span></label>
                <div class="col-sm-6">
                    {!! Form::select('related_id', array('' => ''), null, ['class'=>'form-control']) !!}
                    <p class="help-block">{{ ($errors->has('related_id') ? $errors->first('related_id') : '') }}</p>
                </div>
            </div>
            <input hidden="hidden" name = "integrated_ticket_type_id">
            <div class="col-sm-5 col-sm-offset-5">
                <button type="submit" class="btn btn-success">Create</button>
                <a href="#" class="btn btn-danger" role="button">Cancel</a>
            </div>
        {!! Form::close() !!}
      </div><!-- /panel-body -->
    </div><!-- /panel -->
</div>
@stop