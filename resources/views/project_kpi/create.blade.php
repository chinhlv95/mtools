@extends('layouts.master')
@section('title','Create project KPI')
@section('breadcrumbs','Create project KPI')
@section('style')
<link href="{{ asset('/css/project-kpi/project_kpi.css') }}" rel="stylesheet">
@stop()
@section('content')
<div class="padding-md">
    <div class="main-header clearfix">
      <div class="page-title">
        <h3 class="no-margin">Create project KPI</h3>
      </div>
    </div>
    <div class="panel panel-body panel-default">
      <div class="panel-body">
        {!! Form::open(array('method' => 'POST','class'=>'form-horizontal','url'=>Route('kpi.store', $project_id) )) !!}
        <div class="form-group {{ ($errors->has('name')) ? 'has-error' : '' }}">
            <label for="title" class="col-sm-3 control-label">Name<span class="field-asterisk">*</span></label>
            <div class="col-sm-6">
                {!! Form::text('name',null,['class'=>'form-control', 'maxlength' => '255']) !!}
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
        <div class="form-group">
            <label for="title" class="col-sm-3 control-label">Start date</label>
            <div class="col-sm-6">
               <div class='input-group'>
                <input disabled="disabled" type="text" name="start_date" value="{{ $start_date }}" class="form-control" id="start_date" >
                <input hidden="hidden" type="text" name="kpi_start_date" value="{{ $start_date }}">
                <span class="input-group-addon open-startdate">
                    <span class="glyphicon glyphicon-calendar open-startdate"></span>
                </span>
               </div>
            </div>
        </div>
        <div class="form-group {{ ($errors->has('kpi_end_date')) ? 'has-error' : '' }}">
            <label for="title" class="col-sm-3 control-label">End date<span class="field-asterisk">*</span></label>
            <div class="col-sm-6">
                <div class='input-group'>
                    <input type="text" name="kpi_end_date" value="{{ $end_date }}" class="form-control" id="kpi_end_date" onpaste="return false;">
                    <span class="input-group-addon open-enddate">
                        <span class="glyphicon glyphicon-calendar open-enddate"></span>
                    </span>
                </div>
                <p class="help-block">{{ ($errors->has('kpi_end_date') ? $errors->first('kpi_end_date') : '') }}</p>
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
                {!! Form::text('plan_cost_efficiency',null,['class'=>'form-control number_only']) !!}
            </div>
            <div class="col-sm-2">
                {!! Form::text('actual_cost_efficiency',$metric['costEfficiency'],['class'=>'form-control','id'=>'actual_cost_efficiency', 'readonly'=> 'readonly']) !!}
            </div>
        </div>
        <div class="form-group">
            <label for="title" class="col-sm-3 control-label">Fixing cost</label>
            <div class="col-sm-2">
                <div class="unit">%</div>
            </div>
            <div class="col-sm-2">
                {!! Form::text('plan_fix_code',null,['class'=>'form-control number_only']) !!}
            </div>
            <div class="col-sm-2">
                {!! Form::text('actual_fix_code',$metric['fixingBugCost'],['class'=>'form-control','id'=>'actual_fix_code', 'readonly'=> 'readonly']) !!}
            </div>
        </div>
        <div class="form-group">
          <label for="title" class="col-sm-3 control-label">Leakage</label>
            <div class="col-sm-2">
                <div class="unit">WDef/mm</div>
            </div>
            <div class="col-sm-2">
                {!! Form::text('plan_leakage',null,['class'=>'form-control number_only']) !!}
            </div>
            <div class="col-sm-2">
                {!! Form::text('actual_leakage',$metric['leakage'],['class'=>'form-control','id'=>'actual_leakage', 'readonly'=> 'readonly']) !!}
            </div>
        </div>
        <div class="form-group">
            <label for="title" class="col-sm-3 control-label">Bug after release (number)</label>
            <div class="col-sm-2">
                <div class="unit">Number</div>
            </div>
            <div class="col-sm-2">
                {!! Form::text('plan_bug_after_release_number',null,['class'=>'form-control number_only']) !!}
            </div>
            <div class="col-sm-2">
                {!! Form::text('actual_bug_after_release_number',$metric['numberUATBug'],['class'=>'form-control','id'=>'actual_bug_after_release_number', 'readonly'=> 'readonly']) !!}
            </div>
        </div>
        <div class="form-group">
            <label for="title" class="col-sm-3 control-label">Bug after release (weight)</label>
            <div class="col-sm-2">
                <div class="unit">Weight</div>
            </div>
            <div class="col-sm-2">
                {!! Form::text('plan_bug_after_release_weight',null,['class'=>'form-control number_only']) !!}
            </div>
            <div class="col-sm-2">
                {!! Form::text('actual_bug_after_release_weight',$metric['weightUATBug'],['class'=>'form-control','id'=>'actual_bug_after_release_weight', 'readonly'=> 'readonly']) !!}
            </div>
        </div>
        <div class="form-group">
            <label for="title" class="col-sm-3 control-label">Customer survey</label>
            <div class="col-sm-2">
                <div class="unit">Point</div>
            </div>
            <div class="col-sm-2">
                {!! Form::text('plan_customer_survey',null,['class'=>'form-control number_only']) !!}
            </div>
        </div>
        <div class="form-group">
            <label for="title" class="col-sm-3 control-label">Defect remove efficiency</label>
            <div class="col-sm-2">
                <div class="unit">%</div>
            </div>
            <div class="col-sm-2">
                {!! Form::text('plan_defect_remove_efficiency',null,['class'=>'form-control number_only']) !!}
            </div>
            <div class="col-sm-2">
                {!! Form::text('actual_defect_remove_efficiency',$metric['defectRemove'],['class'=>'form-control','id'=>'actual_defect_remove_efficiency', 'readonly'=> 'readonly']) !!}
            </div>
        </div>
        <div class="form-group">
            <label for="title" class="col-sm-3 control-label">Defect rate</label>
            <div class="col-sm-2">
                <div class="unit">WDef/mm</div>
            </div>
            <div class="col-sm-2">
                {!! Form::text('plan_defect_rate',null,['class'=>'form-control number_only']) !!}
            </div>
            <div class="col-sm-2">
                {!! Form::text('actual_defect_rate',$metric['defectRate'],['class'=>'form-control','id'=>'actual_defect_rate', 'readonly'=> 'readonly']) !!}
            </div>
        </div>
        <div class="form-group">
            <label for="title" class="col-sm-3 control-label">Code productivity</label>
            <div class="col-sm-2">
                <div class="unit">LOC/ mm</div>
            </div>
            <div class="col-sm-2">
                {!! Form::text('plan_code_productivity',null,['class'=>'form-control number_only']) !!}
            </div>
            <div class="col-sm-2">
                {!! Form::text('actual_code_productivity',$metric['codeProductivity'],['class'=>'form-control','id'=>'actual_code_productivity', 'readonly'=> 'readonly']) !!}
            </div>
        </div>
        <div class="form-group">
            <label for="title" class="col-sm-3 control-label">Create testcase productivity</label>
            <div class="col-sm-2">
                <div class="unit">TC/mm</div>
            </div>
            <div class="col-sm-2">
                {!! Form::text('plan_test_case_productivity',null,['class'=>'form-control number_only']) !!}
            </div>
            <div class="col-sm-2">
                {!! Form::text('actual_test_case_productivity',$metric['testCaseProductivity'],['class'=>'form-control','id'=>'actual_test_case_productivity', 'readonly'=> 'readonly']) !!}
            </div>
        </div>
        <div class="form-group">
            <label for="title" class="col-sm-3 control-label">Tested productivity</label>
            <div class="col-sm-2">
                <div class="unit">Tested/mm</div>
            </div>
            <div class="col-sm-2">
                {!! Form::text('plan_tested_productivity',null,['class'=>'form-control number_only']) !!}
            </div>
            <div class="col-sm-2">
                {!! Form::text('actual_tested_productivity',$metric['testedProductivity'],['class'=>'form-control','id'=>'actual_tested_productivity', 'readonly'=> 'readonly']) !!}
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
            <button type="submit" class="btn btn-success">Create</button>
            <a href="{{ URL::route('kpi.index',$project_id) }}" class="btn btn-danger" role="button">Cancel</a>
        </div>
        {!! Form::close() !!}
      </div><!-- /panel-body -->
    </div><!-- /panel -->
</div>
@stop
@section('script')
  <script type="text/javascript" src="{{ asset('/js/summary_metric/select_date.js') }}"></script>
  <script>
  $(".number_only").keypress(function (e) {
      if (String.fromCharCode(e.keyCode).match(/[^0-9]/g)) return false;
  });
  $("#start_date").keypress(function(event) {event.preventDefault();});
  $("#end_date").keypress(function(event) {event.preventDefault();});
  </script>
@stop()