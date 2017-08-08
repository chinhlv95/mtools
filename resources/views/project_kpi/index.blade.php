@extends('layouts.master')
@section('title','KPI management')
@section('breadcrumbs','KPI management')
@section('style')
    <link href="{{ asset('/css/custom/kpi.projects.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/custom/cost.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/custom/date-form.css') }}" rel="stylesheet">
@stop
@section('content')
<div class="padding-md">
    <div class="main-header clearfix">
        <div class="page-title">
            <h3 class="no-margin">KPI management</h3>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body" id="form_body">
            <form method="get" action="{{ URL::route('kpi.index', $project_id) }}" id="search_form" class="form-horizontal" enctype="multipart/form-data">
                <div class="info-left col-md-6">
                    <div class="form-group">
                        <label class="col-md-4 control-label">
                            <span class="col-md-4"><input type="radio" id="default_time" name="check_time" value="1" <?php if(Request::get('check_time') == 1) echo "checked='checked'";?>></span>
                            <span class="col-md-8">Choose from list</span>
                        </label>
                        <div class="col-md-6">
                            <select class="form-control" name="date" id="select_defalt_time">
                                @foreach($select_date as $key => $value)
                                    <option value="{{$key}}" <?php if(Request::get('date')==$key) echo "selected";?>>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">
                            <span class="col-md-4"><input type="radio" id="choose_time" class="col-md-4" name="check_time" value="2" <?php if(Request::get('check_time') == 2) echo "checked='checked'";?>></span>
                            <span class="col-md-8">Choose start, end</span>
                        </label>
                        <div class="col-md-6">
                            <div class="input-group col-md-12" id="dateForm">
                                <input class="form-control" id="start_date" data-inputmask="'alias': 'date'" value="{{Request::get('start_date') ? Request::get('start_date','') : $firstDateDefault}}" name="start_date"  type="text" <?php if(Request::get('check_time','1') == 1) echo "disabled='disabled'";?> onpaste="return false;">
                                <span class="input-group-addon open-startdate">
                                    <i class="fa fa-calendar open-startdate"></i>
                                </span>
                            </div>
                            <label class="error" for="start_date"></label>
                            @if ($errors->has('start_date'))
                                <span class="error-message help-block">
                                    <strong>{{ $errors->first('start_date') }}</strong>
                                </span>
                            @endif
                            <div class="col-md-8 col-md-offset-3">
                            </div>
                            <div class="input-group col-md-12" id="dateForm">
                                <input class="form-control" id="end_date" data-inputmask="'alias': 'date'" value="{{Request::get('end_date') ? Request::get('end_date','') : $endDateDefault}}" name="end_date" type="text" <?php if(Request::get('check_time','1') == 1) echo "disabled='disabled'";?> onpaste="return false;">
                                <span class="input-group-addon open-enddate"><i class="fa fa-calendar open-enddate"></i></span>
                            </div>
                            <label id="end_date-error" class="error" for="end_date"></label>
                            <div class="col-md-8 col-md-offset-3"></div>
                            @if ($errors->has('end_date'))
                                <span class="error-message help-block">
                                    <strong>{{ $errors->first('end_date') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="info-right col-md-6">
                    <div class="form-group">
                        <label class="col-md-4 control-label text-left" for="reportType">Report Type</label>
                        <div class="col-md-6">
                            <select class="form-control" name="reportType">
                                @foreach($reportType as $key=>$value)
                                    <option value="{{$key}}" <?php if(Request::get('reportType') == $key) echo "selected";?>>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <button type="button" class="btn btn-danger" id="configreset">Reset</button>
                    <a class="btn btn-success" href="{{ URL::route('kpi.create',$project_id) }}">Create</a>
<!--                     <button type="button" class="btn btn-warning" id="syncOldData" data-toggle="modal" data-target="#syncOldData1">Sync Data</button> -->
                </div>
            </form>
        </div>
        <div class="panel-body">
            @if(Request::get('reportType') == null)
            @elseif((count($week_project_kpi) > 0) || (count($month_project_kpi) > 0) || (count($base_project_kpi) > 0))
                <div class="table-responsive" id="scroll-x">
                    <table class="table table-bordered table-hover table-striped" id="">
                        <thead>
                            @if(Request::get('reportType') == 1)
                                <tr>
                                    <th rowspan="2" class="width250"></th>
                                    <th rowspan="2" class="width160">Unit</th>
                                    @foreach ($week_project_kpi as $data)
                                        <th colspan="2" class="width160">
                                            <div class="" data-toggle="tooltip" title="{{ $data['name'] }}">
                                                {{ str_limit($data['name'], 16) }}
                                            </div>
                                        </th>
                                        <div class="cost_graph_efficiency" hidden="hidden">{{ $data['name'] }}</div>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach ($week_project_kpi as $data)
                                        <th class="width80">Target</th>
                                        <th class="width80">Actual</th>
                                    @endforeach
                                </tr>
                            @elseif(Request::get('reportType') == 2)
                                <tr>
                                    <th rowspan="2" class="width250"></th>
                                    <th rowspan="2" class="width160">Unit</th>
                                    @foreach ($month_project_kpi as $data)
                                       <th colspan="2" class="width160">
                                           <div class="" data-toggle="tooltip" title="{{ $data['name'] }}">
                                                {{ str_limit($data['name'], 16) }}
                                           </div>
                                       </th>
                                       <div class="cost_graph_efficiency" hidden="hidden">{{ $data['name'] }}</div>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach ($month_project_kpi as $data)
                                        <th class="width80">Target</th>
                                        <th class="width80">Actual</th>
                                    @endforeach
                                </tr>
                            @elseif(Request::get('reportType') == 3)
                                <tr>
                                    <th rowspan="2" class="width250"></th>
                                    <th rowspan="2" class="width160">Unit</th>
                                    @foreach ($base_project_kpi as $data)
                                       <th colspan="2" class="width80">
                                           <div class="" data-toggle="tooltip" title="{{ $data['name'] }}">
                                                {{ str_limit($data['name'], 16) }}<br>
                                                <span>( {{ date('d/m/y', strtotime($data['start_date'])) }} - {{ date('d/m/y', strtotime($data['end_date'])) }} )</span>
                                           </div>
                                       </th>
                                       <div class="cost_graph_efficiency" hidden="hidden">{{ $data['name'] }}</div>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach ($base_project_kpi as $data)
                                        <th class="width80">Target</th>
                                        <th class="width80">Actual</th>
                                    @endforeach
                                </tr>
                            @endif
                        </thead>
                       <tbody>
                           <tr>
                               <td class="titleKpi">Cost</td>
                               <td class="titleKpi"></td>
                                @if((Request::get('reportType') == 1) && (count($week_project_kpi) > 0))
                                    <td class="titleKpi" colspan="{{ count($week_project_kpi)*2 }}"></td>
                                @elseif((Request::get('reportType') == 2) && (count($month_project_kpi) > 0))
                                    <td class="titleKpi" colspan="{{ count($month_project_kpi)*2 }}"></td>
                                @elseif((Request::get('reportType') == 3) && (count($base_project_kpi) > 0))
                                    <td class="titleKpi" colspan="{{ count($base_project_kpi)*2 }}"></td>
                                @endif
                           </tr>
                            <tr class="name_cost_efficiency">
                                <td class="name">Cost efficiency</td>
                                <td>
                                    <div>%</div>
                                </td>
                                @if((Request::get('reportType') == 1) && (count($week_project_kpi) > 0))
                                    @foreach ($week_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_cost_efficiency) > 0)
                                                {{ $data->plan_cost_efficiency }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_cost_efficiency) > 0)
                                                {{ $data->actual_cost_efficiency }}
                                            @endif
                                        </td>
                                        <td class="description" hidden="hidden">
                                            @if (count($data->description) > 0)
                                                {{ $data->description }}
                                            @endif
                                        </td>
                                    @endforeach
                                @elseif((Request::get('reportType') == 2) && (count($month_project_kpi) > 0))
                                    @foreach ($month_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_cost_efficiency) > 0)
                                                {{ $data->plan_cost_efficiency }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_cost_efficiency) > 0)
                                                {{ $data->actual_cost_efficiency }}
                                            @endif
                                        </td>
                                        <td class="description" hidden="hidden">
                                            @if (count($data->description) > 0)
                                                {{ $data->description }}
                                            @endif
                                        </td>
                                    @endforeach
                                @elseif((Request::get('reportType') == 3) && (count($base_project_kpi) > 0))
                                    @foreach ($base_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_cost_efficiency) > 0)
                                                {{ $data->plan_cost_efficiency }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_cost_efficiency) > 0)
                                                {{ $data->actual_cost_efficiency }}
                                            @endif
                                        </td>
                                        <td class="description" hidden="hidden">
                                            @if (count($data->description) > 0)
                                                {{ $data->description }}
                                            @endif
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                            <tr class="name_fix_cost">
                                <td class="name">Fixing cost</td>
                                <td>
                                   <div>%</div>
                                </td>
                                @if((Request::get('reportType') == 1) && (count($week_project_kpi) > 0))
                                    @foreach ($week_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_fix_code) > 0)
                                                {{ $data->plan_fix_code }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_fix_code) > 0)
                                                {{ $data->actual_fix_code }}
                                            @endif
                                        </td>
                                    @endforeach
                                @elseif((Request::get('reportType') == 2) && (count($month_project_kpi) > 0))
                                    @foreach ($month_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_fix_code) > 0)
                                                {{ $data->plan_fix_code }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_fix_code) > 0)
                                                {{ $data->actual_fix_code }}
                                            @endif
                                        </td>
                                    @endforeach
                                @elseif((Request::get('reportType') == 3) && (count($base_project_kpi) > 0))
                                    @foreach ($base_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_fix_code) > 0)
                                                {{ $data->plan_fix_code }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_fix_code) > 0)
                                                {{ $data->actual_fix_code }}
                                            @endif
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                            <tr>
                                <td class="titleKpi">Quality</td>
                                <td class="titleKpi"></td>
                                @if((Request::get('reportType') == 1) && (count($week_project_kpi) > 0))
                                    <td class="titleKpi" colspan="{{ count($week_project_kpi)*2 }}"></td>
                                @elseif((Request::get('reportType') == 2) && (count($month_project_kpi) > 0))
                                    <td class="titleKpi" colspan="{{ count($month_project_kpi)*2 }}"></td>
                                @elseif((Request::get('reportType') == 3) && (count($base_project_kpi) > 0))
                                    <td class="titleKpi" colspan="{{ count($base_project_kpi)*2 }}"></td>
                                @endif
                            </tr>
                            <tr class="name_leakage">
                                <td class="name">Leakage</td>
                                <td>
                                    <div>Wdef/mm</div>
                                </td>
                                @if((Request::get('reportType') == 1) && (count($week_project_kpi) > 0))
                                    @foreach ($week_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_leakage) > 0)
                                                {{ $data->plan_leakage }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_leakage) > 0)
                                                {{ $data->actual_leakage }}
                                            @endif
                                        </td>
                                    @endforeach
                                @elseif((Request::get('reportType') == 2) && (count($month_project_kpi) > 0))
                                    @foreach ($month_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_leakage) > 0)
                                                {{ $data->plan_leakage }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_leakage) > 0)
                                                {{ $data->actual_leakage }}
                                            @endif
                                        </td>
                                    @endforeach
                                @elseif((Request::get('reportType') == 3) && (count($base_project_kpi) > 0))
                                    @foreach ($base_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_leakage) > 0)
                                                {{ $data->plan_leakage }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_leakage) > 0)
                                                {{ $data->actual_leakage }}
                                            @endif
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                            <tr class="name_UAT_bug_number">
                                <td class="name">Bug after release (number)</td>
                                <td>
                                    <div>Number</div>
                                </td>
                                @if((Request::get('reportType') == 1) && (count($week_project_kpi) > 0))
                                    @foreach ($week_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_bug_after_release_number) > 0)
                                                {{ $data->plan_bug_after_release_number }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_bug_after_release_number) > 0)
                                                {{ $data->actual_bug_after_release_number }}
                                            @endif
                                        </td>
                                    @endforeach
                               @elseif((Request::get('reportType') == 2) && (count($month_project_kpi) > 0))
                                    @foreach ($month_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_bug_after_release_number) > 0)
                                                {{ $data->plan_bug_after_release_number }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_bug_after_release_number) > 0)
                                                {{ $data->actual_bug_after_release_number }}
                                            @endif
                                        </td>
                                    @endforeach
                                @elseif((Request::get('reportType') == 3) && (count($base_project_kpi) > 0))
                                    @foreach ($base_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_bug_after_release_number) > 0)
                                                {{ $data->plan_bug_after_release_number }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_bug_after_release_number) > 0)
                                                {{ $data->actual_bug_after_release_number }}
                                            @endif
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                            <tr class="name_UAT_bug_weight">
                                <td class="name">Bug after release (weight)</td>
                                <td>
                                    <div>Weight</div>
                                </td>
                                @if((Request::get('reportType') == 1) && (count($week_project_kpi) > 0))
                                    @foreach ($week_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_bug_after_release_weight) > 0)
                                                {{ $data->plan_bug_after_release_weight }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_bug_after_release_weight) > 0)
                                                {{ $data->actual_bug_after_release_weight }}
                                            @endif
                                        </td>
                                    @endforeach
                               @elseif((Request::get('reportType') == 2) && (count($month_project_kpi) > 0))
                                    @foreach ($month_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_bug_after_release_weight) > 0)
                                                {{ $data->plan_bug_after_release_weight }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_bug_after_release_weight) > 0)
                                                {{ $data->actual_bug_after_release_weight }}
                                            @endif
                                        </td>
                                    @endforeach
                                @elseif((Request::get('reportType') == 3) && (count($base_project_kpi) > 0))
                                    @foreach ($base_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_bug_after_release_weight) > 0)
                                                {{ $data->plan_bug_after_release_weight }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_bug_after_release_weight) > 0)
                                                {{ $data->actual_bug_after_release_weight }}
                                            @endif
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                            <tr class="name_customer_survey">
                                <td class="name">Customer survey</td>
                                <td>
                                    <div>Point</div>
                                </td>
                               @if((Request::get('reportType') == 1) && (count($week_project_kpi) > 0))
                                    @foreach ($week_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_customer_survey) > 0)
                                                {{ $data->plan_customer_survey }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_customer_survey) > 0)
                                                {{ $data->actual_customer_survey }}
                                            @endif
                                        </td>
                                    @endforeach
                               @elseif((Request::get('reportType') == 2) && (count($month_project_kpi) > 0))
                                    @foreach ($month_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_customer_survey) > 0)
                                                {{ $data->plan_customer_survey }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_customer_survey) > 0)
                                                {{ $data->actual_customer_survey }}
                                            @endif
                                        </td>
                                    @endforeach
                                @elseif((Request::get('reportType') == 3) && (count($base_project_kpi) > 0))
                                    @foreach ($base_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_customer_survey) > 0)
                                                {{ $data->plan_customer_survey }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_customer_survey) > 0)
                                                {{ $data->actual_customer_survey }}
                                            @endif
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                            <tr class="name_defect_remove_efficiency">
                                <td class="name">Defect remove efficiency</td>
                                <td>
                                    <div>%</div>
                                </td>
                               @if((Request::get('reportType') == 1) && (count($week_project_kpi) > 0))
                                    @foreach ($week_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_defect_remove_efficiency) > 0)
                                                {{ $data->plan_defect_remove_efficiency }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_defect_remove_efficiency) > 0)
                                                {{ $data->actual_defect_remove_efficiency }}
                                            @endif
                                        </td>
                                    @endforeach
                               @elseif((Request::get('reportType') == 2) && (count($month_project_kpi) > 0))
                                    @foreach ($month_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_defect_remove_efficiency) > 0)
                                                {{ $data->plan_defect_remove_efficiency }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_defect_remove_efficiency) > 0)
                                                {{ $data->actual_defect_remove_efficiency }}
                                            @endif
                                        </td>
                                    @endforeach
                                @elseif((Request::get('reportType') == 3) && (count($base_project_kpi) > 0))
                                    @foreach ($base_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_defect_remove_efficiency) > 0)
                                                {{ $data->plan_defect_remove_efficiency }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_defect_remove_efficiency) > 0)
                                                {{ $data->actual_defect_remove_efficiency }}
                                            @endif
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                            <tr class="name_defect_rate">
                                <td class="name">Defect rate</td>
                                <td>
                                    <div>Wdef/mm</div>
                                </td>
                                @if((Request::get('reportType') == 1) && (count($week_project_kpi) > 0))
                                    @foreach ($week_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_defect_rate) > 0)
                                                {{ $data->plan_defect_rate }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_defect_rate) > 0)
                                                {{ $data->actual_defect_rate }}
                                            @endif
                                        </td>
                                    @endforeach
                                @elseif((Request::get('reportType') == 2) && (count($month_project_kpi) > 0))
                                    @foreach ($month_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_defect_rate) > 0)
                                                {{ $data->plan_defect_rate }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_defect_rate) > 0)
                                                {{ $data->actual_defect_rate }}
                                            @endif
                                        </td>
                                    @endforeach
                                @elseif((Request::get('reportType') == 3) && (count($base_project_kpi) > 0))
                                    @foreach ($base_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_defect_rate) > 0)
                                                {{ $data->plan_defect_rate }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_defect_rate) > 0)
                                                {{ $data->actual_defect_rate }}
                                            @endif
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                            <tr>
                                <td class="titleKpi">Productivity</td>
                                <td class="titleKpi"></td>
                                @if((Request::get('reportType') == 1) && (count($week_project_kpi) > 0))
                                    <td class="titleKpi" colspan="{{ count($week_project_kpi)*2 }}"></td>
                                @elseif((Request::get('reportType') == 2) && (count($month_project_kpi) > 0))
                                    <td class="titleKpi" colspan="{{ count($month_project_kpi)*2 }}"></td>
                                @elseif((Request::get('reportType') == 3) && (count($base_project_kpi) > 0))
                                    <td class="titleKpi" colspan="{{ count($base_project_kpi)*2 }}"></td>
                                @endif
                            </tr>
                            <tr class="name_code_productivity">
                                <td class="name">Code productivity</td>
                                <td>
                                    <div>LOC/mm</div>
                                </td>
                                @if((Request::get('reportType') == 1) && (count($week_project_kpi) > 0))
                                    @foreach ($week_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_code_productivity) > 0)
                                                {{ $data->plan_code_productivity }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_code_productivity) > 0)
                                                {{ $data->actual_code_productivity }}
                                            @endif
                                        </td>
                                    @endforeach
                                @elseif((Request::get('reportType') == 2) && (count($month_project_kpi) > 0))
                                    @foreach ($month_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_code_productivity) > 0)
                                                {{ $data->plan_code_productivity }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_code_productivity) > 0)
                                                {{ $data->actual_code_productivity }}
                                            @endif
                                        </td>
                                    @endforeach
                                @elseif((Request::get('reportType') == 3) && (count($base_project_kpi) > 0))
                                    @foreach ($base_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_code_productivity) > 0)
                                                {{ $data->plan_code_productivity }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_code_productivity) > 0)
                                                {{ $data->actual_code_productivity }}
                                            @endif
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                            <tr class="name_create_test_productivity">
                                <td class="name">Create testcase productivity</td>
                                <td>
                                    <div>TC/mm</div>
                                </td>
                                @if((Request::get('reportType') == 1) && (count($week_project_kpi) > 0))
                                    @foreach ($week_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_test_case_productivity) > 0)
                                                {{ $data->plan_test_case_productivity }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_test_case_productivity) > 0)
                                                {{ $data->actual_test_case_productivity }}
                                             @endif
                                        </td>
                                    @endforeach
                                @elseif((Request::get('reportType') == 2) && (count($month_project_kpi) > 0))
                                    @foreach ($month_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_test_case_productivity) > 0)
                                                {{ $data->plan_test_case_productivity }}
                                            @endif
                                        </td>
                                        <td class="data">
                                             @if (count($data->actual_test_case_productivity) > 0)
                                                {{ $data->actual_test_case_productivity }}
                                             @endif
                                        </td>
                                    @endforeach
                                @elseif((Request::get('reportType') == 3) && (count($base_project_kpi) > 0))
                                    @foreach ($base_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_test_case_productivity) > 0)
                                                {{ $data->plan_test_case_productivity }}
                                            @endif
                                        </td>
                                        <td class="data">
                                             @if (count($data->actual_test_case_productivity) > 0)
                                                {{ $data->actual_test_case_productivity }}
                                             @endif
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                            <tr class="name_tested_productivity">
                                <td class="name">Tested productivity</td>
                                <td>
                                    <div>Tested/mm</div>
                                </td>
                                @if((Request::get('reportType') == 1) && (count($week_project_kpi) > 0))
                                    @foreach ($week_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_tested_productivity) > 0)
                                                {{ $data->plan_tested_productivity }}
                                            @endif
                                        </td>
                                        <td class="data">
                                            @if (count($data->actual_tested_productivity) > 0)
                                                {{ $data->actual_tested_productivity }}
                                             @endif
                                        </td>
                                    @endforeach
                                @elseif((Request::get('reportType') == 2) && (count($month_project_kpi) > 0))
                                    @foreach ($month_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_tested_productivity) > 0)
                                                {{ $data->plan_tested_productivity }}
                                            @endif
                                        </td>
                                        <td class="data">
                                             @if (count($data->actual_tested_productivity) > 0)
                                                {{ $data->actual_tested_productivity }}
                                             @endif
                                        </td>
                                    @endforeach
                                @elseif((Request::get('reportType') == 3) && (count($base_project_kpi) > 0))
                                    @foreach ($base_project_kpi as $data)
                                        <td class="data-plan">
                                            @if (count($data->plan_tested_productivity) > 0)
                                                {{ $data->plan_tested_productivity }}
                                            @endif
                                        </td>
                                        <td class="data">
                                             @if (count($data->actual_tested_productivity) > 0)
                                                {{ $data->actual_tested_productivity }}
                                             @endif
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                            <tr>
                                @if((Request::get('reportType') == 1) && (count($week_project_kpi) > 0))
                                @elseif((Request::get('reportType') == 2) && (count($month_project_kpi) > 0))
                                @elseif((Request::get('reportType') == 3) && (count($base_project_kpi) > 0))
                                    <td class="row-action-kpi-2">Action</td>
                                    <td class="row-action-kpi-2"></td>
                                    @foreach ($base_project_kpi as $data)
                                        <td colspan="2" class="row-action-kpi-2" style="text-align: center;">
                                            <a href="{{ URL::route('kpi.edit',[$project_id, $data->id]) }}"><i class="fa fa-edit fa-lg"></i></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                            <a href="javascript:void(0);" name="{{ $data->id }}" dataId="{{$data->id}}" class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div>
                <br><hr><br>
                <div class="col-lg-12">
                    <div class="col-md-6">
                        <div id="graph_wrap">
                            <div id="cost_efficiency"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div id="graph_wrap">
                            <div id="fix_cost"></div>
                        </div>
                    </div>
                </div>
                <br><hr><br>
                <div class="col-lg-12">
                    <div class="col-md-6">
                        <div id="graph_wrap">
                            <div id="leakage"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div id="graph_wrap">
                            <div id="customer_survey"></div>
                        </div>
                    </div>
                </div>
                <br><hr><br>
                <div class="col-lg-12">
                    <div class="col-md-6">
                        <div id="graph_wrap">
                            <div id="UAT_bug_number"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div id="graph_wrap">
                            <div id="UAT_bug_weight"></div>
                        </div>
                    </div>
                </div>
                <br><hr><br>
                <div class="col-lg-12">
                    <div class="col-md-6">
                        <div id="graph_wrap">
                            <div id="defect_remove_efficiency"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div id="graph_wrap">
                            <div id="defect_rate"></div>
                        </div>
                    </div>
                </div>
                <br><hr><br>
                <div class="col-lg-12">
                    <div class="col-md-6">
                        <div id="graph_wrap">
                            <div id="testcase_productivity"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div id="graph_wrap">
                            <div id="tested_productivity"></div>
                        </div>
                    </div>
                </div>
                <br><hr><br>
                <div class="col-lg-12">
                    <div class="col-md-6">
                        <div id="graph_wrap">
                            <div id="code_productivity"></div>
                        </div>
                    </div>
                </div>
            @else
                <div class="panel-body" id="form_body">
                    <center>There are no results that match your search</center>
                </div>
            @endif
        </div>
    </div>
</div>
@stop
@section('modal')
<div id="deleteModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>Delete project kpi</h4>
            </div>
            <div class="modal-body">
                <p>Do you want delete project kpi?</p>
            </div>
            <div class="modal-footer">
                <form method="post" action="{{ Route('kpi.delete', $project_id) }}">
                {{csrf_field()}}
                <input type="hidden" value="0" id="data-id" name="id" />
                <input type="hidden" value="{{Request::get('keyword', '')}}" name="keyword" />
                <button class="btn btn-sm btn-success" name="deletey" type="submit">Delete</button>
                <button class="btn btn-sm btn-danger" data-dismiss="modal" type="button">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="syncOldData1" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4><center><strong>Synchronize old data</strong></center></h4>
            </div>
            <div class="modal-body">
                <p>Notice: Synchronize will update new data for this project, and maybe change figures.</p>
                <p>Do you want synchronize old kpi data for this project?</p>
            </div>
            <div class="modal-footer">
            <div>
                <form method="post" action="{{ URL::route('kpi.sync', $project_id) }}" class="form-horizontal">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="submit" class="btn">Sync Data</button>
                </form>
            </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('script')
    <script type="text/javascript" src="{{ asset('/js/project_version/version.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/select_date/select.date.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/common/reset_form.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/common/highcharts.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/common/project_kpi.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/jquery.inputmask.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/jquery.validate.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/common/validate_date.js') }}"></script>
@stop
