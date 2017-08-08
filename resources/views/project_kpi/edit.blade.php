@extends('layouts.master')
@section('title','Update project KPI')
@section('breadcrumbs','Update project KPI')
@section('style')
<link href="{{ asset('/css/project-kpi/project_kpi.css') }}" rel="stylesheet">
@stop()
@section('content')
<?php
    $start_date = date('d/m/Y', strtotime($kpi->start_date));
    $end_date   = date('d/m/Y', strtotime($kpi->end_date));
?>
<div class="padding-md">
    <div class="main-header clearfix">
      <div class="page-title">
        <h3 class="no-margin">Create project KPI</h3>
      </div>
    </div>
    <div class="panel panel-body panel-default">
      <div class="panel-body">
      {!! Form::model($kpi, ['method' => 'POST',  'class' => 'form-horizontal']) !!}
        <div class="form-group {{ ($errors->has('name')) ? 'has-error' : '' }}">
            <label for="title" class="col-sm-3 control-label">Name<span class="field-asterisk">*</span></label>
            <div class="col-sm-6">
                {!! Form::text('name',$kpi->name,['class'=>'form-control', 'maxlength' => '255']) !!}
                <p class="help-block">{{ ($errors->has('name') ? $errors->first('name') : '') }}</p>
            </div>
        </div>
        <div class="form-group">
            <label for="title" class="col-sm-3 control-label">Project name</label>
            <div class="col-sm-6">
                <input type="text" name= "project_id" value="{{ $project->name }}" class="form-control" readonly = "readonly">
                <p class="help-block">{{ ($errors->has('project_id') ? $errors->first('project_id') : '') }}</p>
                <input type="hidden" value="{{ $project->id}}" id="project_id">
            </div>
        </div>
        <div class="form-group {{ ($errors->has('start_date')) ? 'has-error' : '' }}">
            <label for="title" class="col-sm-3 control-label">Start date<span class="field-asterisk">*</span></label>
            <div class="col-sm-6">
                <input type="text" name="start_date" value="{{ $start_date }}" class="form-control start_date123" onpaste="return false;" id="start_date" >
                <p class="help-block">{{ ($errors->has('start_date') ? $errors->first('start_date') : '') }}</p>
            </div>
        </div>
        <div class="form-group {{ ($errors->has('end_date')) ? 'has-error' : '' }}">
            <label for="title" class="col-sm-3 control-label">End date<span class="field-asterisk">*</span></label>
            <div class="col-sm-6">
                <input type="text" name="end_date" value="{{ $end_date }}" class="form-control end_date123" id="end_date" onpaste="return false;">
                <p class="help-block">{{ ($errors->has('end_date') ? $errors->first('end_date') : '') }}</p>
            </div>
        </div>
         <div class="form-group">
            <label for="title" class="col-sm-3 control-label">Metrics</label>
            <label for="title" class="col-sm-2 unit">Unit</label>
            <label for="title" class="col-sm-2 unit">Target</label>
            <label for="title" class="col-sm-2 unit">Current actual </label>
        </div>
        <div class="form-group">
            <label for="title" class="col-sm-3 control-label">Cost efficiency</label>
            <div class="col-sm-2">
                <div class="unit">%</div>
            </div>
            <div class="col-sm-2">
                <input type="text" value ="{{ $kpi->plan_cost_efficiency }}" class="form-control number_only" id="plan_cost_efficiency" name="plan_cost_efficiency">
            </div>
            <div class="col-sm-2">
                <input type="text" value ="{{ $kpi->actual_cost_efficiency }}" class="form-control" readonly="readonly" id="actual_cost_efficiency" name="actual_cost_efficiency">
            </div>
        </div>
        <div class="form-group">
            <label for="title" class="col-sm-3 control-label">Fixing cost</label>
            <div class="col-sm-2">
                <div class="unit">%</div>
            </div>
            <div class="col-sm-2">
                <input type="text" value ="{{ $kpi->plan_fix_code }}" class="form-control number_only" id="plan_fix_code" name="plan_fix_code">
            </div>
            <div class="col-sm-2">
                <input type="text" value ="{{ $kpi->actual_fix_code }}" class="form-control" readonly="readonly" id="actual_fix_code" name="actual_fix_code">
            </div>
        </div>
        <div class="form-group">
          <label for="title" class="col-sm-3 control-label">Leakage</label>
            <div class="col-sm-2">
                <div class="unit">WDef/mm</div>
            </div>
            <div class="col-sm-2">
                <input type="text" value ="{{ $kpi->plan_leakage }}" class="form-control number_only" id="plan_leakage" name="plan_leakage">
            </div>
            <div class="col-sm-2">
                <input type="text" value ="{{ $kpi->actual_leakage }}" class="form-control" readonly="readonly" id="actual_leakage" name="actual_leakage">
            </div>
        </div>
        <div class="form-group">
            <label for="title" class="col-sm-3 control-label">Bug after release (number)</label>
            <div class="col-sm-2">
                <div class="unit">Number</div>
            </div>
            <div class="col-sm-2">
                <input type="text" value ="{{ $kpi->plan_bug_after_release_number }}" class="form-control number_only" name="plan_bug_after_release_number" id="plan_bug_after_release_number">
            </div>
            <div class="col-sm-2">
                <input type="text" value ="{{ $kpi->actual_bug_after_release_number }}" class="form-control" readonly="readonly" id="actual_bug_after_release_number" name="actual_bug_after_release_number">
            </div>
        </div>
        <div class="form-group">
            <label for="title" class="col-sm-3 control-label">Bug after release (weight)</label>
            <div class="col-sm-2">
                <div class="unit">Weight</div>
            </div>
            <div class="col-sm-2">
                <input type="text" value ="{{ $kpi->plan_bug_after_release_weight }}" class="form-control number_only" name="plan_bug_after_release_weight" id="plan_bug_after_release_weight">
            </div>
            <div class="col-sm-2">
                <input type="text" value ="{{ $kpi->actual_bug_after_release_weight }}" class="form-control" readonly="readonly" id="actual_bug_after_release_weight" name="actual_bug_after_release_weight">
            </div>
        </div>
        <div class="form-group">
            <label for="title" class="col-sm-3 control-label">Customer survey</label>
            <div class="col-sm-2">
                <div class="unit">Point</div>
            </div>
            <div class="col-sm-2">
                <input type="text" value ="{{ $kpi->plan_customer_survey }}" class="form-control number_only" name="plan_customer_survey" id="plan_customer_survey">
            </div>
        </div>
        <div class="form-group">
            <label for="title" class="col-sm-3 control-label">Defect remove efficiency</label>
            <div class="col-sm-2">
                <div class="unit">%</div>
            </div>
            <div class="col-sm-2">
                <input type="text" value ="{{ $kpi->plan_defect_remove_efficiency }}" class="form-control number_only" name="plan_defect_remove_efficiency" id="plan_defect_remove_efficiency">
            </div>
            <div class="col-sm-2">
                <input type="text" value ="{{ $kpi->actual_defect_remove_efficiency }}" class="form-control" readonly="readonly" id="actual_defect_remove_efficiency" name="actual_defect_remove_efficiency">
            </div>
        </div>
        <div class="form-group">
            <label for="title" class="col-sm-3 control-label">Defect rate</label>
            <div class="col-sm-2">
                <div class="unit">WDef/mm</div>
            </div>
            <div class="col-sm-2">
                <input type="text" value ="{{ $kpi->plan_defect_rate }}" class="form-control number_only" name="plan_defect_rate" id="plan_defect_rate">
            </div>
            <div class="col-sm-2">
                <input type="text" value ="{{ $kpi->actual_defect_rate }}" class="form-control" readonly="readonly" id="actual_defect_rate" name="actual_defect_rate">
            </div>
        </div>
        <div class="form-group">
            <label for="title" class="col-sm-3 control-label">Code productivity</label>
            <div class="col-sm-2">
                <div class="unit">LOC/ mm</div>
            </div>
            <div class="col-sm-2">
                <input type="text" value ="{{ $kpi->plan_code_productivity }}" class="form-control number_only" name="plan_code_productivity" id="plan_code_productivity">
            </div>
            <div class="col-sm-2">
                <input type="text" value ="{{ $kpi->actual_code_productivity }}" class="form-control" readonly="readonly" id="actual_code_productivity" name="actual_code_productivity">
            </div>
        </div>
        <div class="form-group">
            <label for="title" class="col-sm-3 control-label">Create testcase productivity</label>
            <div class="col-sm-2">
                <div class="unit">TC/mm</div>
            </div>
            <div class="col-sm-2">
                <input type="text" value ="{{ $kpi->plan_test_case_productivity }}" class="form-control number_only" name="plan_test_case_productivity" id="plan_test_case_productivity">
            </div>
            <div class="col-sm-2">
                <input type="text" value ="{{ $kpi->actual_test_case_productivity }}" class="form-control" readonly="readonly" id="actual_test_case_productivity" name="actual_test_case_productivity">
            </div>
        </div>
        <div class="form-group">
            <label for="title" class="col-sm-3 control-label">Tested productivity</label>
            <div class="col-sm-2">
                <div class="unit">Tested/mm</div>
            </div>
            <div class="col-sm-2">
                <input type="text" value ="{{ $kpi->plan_tested_productivity }}" class="form-control number_only" name="plan_tested_productivity" id="plan_tested_productivity">
            </div>
            <div class="col-sm-2">
                <input type="text" value ="{{ $kpi->actual_tested_productivity }}" class="form-control" readonly="readonly" id="actual_tested_productivity" name="actual_tested_productivity">
            </div>
        </div>
        <div class="form-group {{ ($errors->has('description')) ? 'has-error' : '' }}">
            <label for="title" class="col-sm-3 control-label">Analysis</label>
            <div class="col-sm-6">
                {!! Form::textarea('description',null,['class'=>'form-control']) !!}
                <p class="help-block">{{ ($errors->has('description') ? $errors->first('description') : '') }}</p>
            </div>
        </div>
        <div class="col-sm-5 col-sm-offset-5">
            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ URL::route('kpi.index',$project_id) }}" class="btn btn-danger" role="button">Cancel</a>
        </div>
        {!! Form::close() !!}
      </div><!-- /panel-body -->
    </div><!-- /panel -->
</div>
@stop
@section('script')
  <script type="text/javascript" src="{{ asset('/js/summary_metric/edit_select_date.js') }}"></script>
  <script>
  $(".number_only").keypress(function (e) {
      if (String.fromCharCode(e.keyCode).match(/[^0-9]/g)) return false;
  });
  </script>
@stop()