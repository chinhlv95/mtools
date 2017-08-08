@extends('layouts.master')
@section('title', 'Summary Report')

@section('breadcrumbs','Summary Report')
@section('style')
    <link href="{{ asset('css/custom/date-form.css') }}" rel="stylesheet">
    <link href="{{ asset('css/quality/quality_productivity.css') }}" rel="stylesheet">
@stop
@section('content')
<div class="panel panel-default">
    <div class="panel-heading" id="form_heading">Report Productivity and Quality by Project</div>
    <!-- common area -->
    <div class="panel-body">
        <!-- search area -->
        <form action="{{ URL::route('quality-report.project.show') }}" id="search_form" method="get" class="form-horizontal">
            <div class="info-left col-md-6">
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
                <div class="form-group">
                    <label class="col-md-4 control-label" for="year">Select Year:</label>
                    <div class="col-md-6">
                        <select class="form-control" name="year" id="year">
                            <option value="-1"> -- Select --</option>
                            @if(!empty($years))
                                @foreach($years as $key => $value)
                                    <option value="{{$key}}" <?php if(Request::get('year') == $key) echo "selected"?>>{{$value}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="month">Select Month:</label>
                    <div class="col-md-6">
                        <select class="form-control" name="month[]" id="monthReport" multiple>
                            @if(!empty($months))
                                @foreach($months as $key => $value)
                                    <option value="{{$key}}"
                                     <?php
                                     $getMonths = Request::get('month');
                                     if (!empty($getMonths)) {
                                         foreach ($getMonths as $item) {
                                             if($item == $key) echo "selected";
                                         }
                                     }
                                    ?>>{{$value}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>
            <div class="info-right col-md-6">
                <div class="form-group">
                    <label class="col-md-4 control-label" for="department">Department</label>
                    <div class="col-md-6">
                        <select class="form-control" name="department" id="department_id">
                            <option value=""> -- All --</option>
                            @if(!empty($departments))
                                @foreach($departments as $department)
                                    @if($department['id'] == Request::get('department',0))
                                        <option value="{{$department['id']}}" selected='selected'>{{$department['name']}}</option>
                                    @else
                                        <option value="{{$department['id']}}" >{{$department['name']}}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="division">Division</label>
                    <div class="col-md-6">
                        <select class="form-control" name="division" id="division_id">
                            <option value=""> -- All --</option>
                            @if(!empty($divisions))
                                @foreach($divisions as $division)
                                    @if($division['id'] == Request::get('division',0))
                                        <option value="{{ $division['id']}}" selected='selected'>{{$division['name']}}</option>
                                    @else
                                        <option value="{{$division['id']}}" >{{$division['name']}}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="team">Team</label>
                    <div class="col-md-6">
                        <select class="form-control" name="team" id="team_id">
                            <option value=""> -- All --</option>
                            @if(!empty($teams))
                                @foreach($teams as $team)
                                    @if($team['id'] == Request::get('team',0))
                                        <option value="{{$team['id']}}" selected='selected'>{{$team['name']}}</option>
                                    @else
                                        <option value="{{$team['id']}}" >{{$team['name']}}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                 <div class="form-group">
                    <label class="col-md-4 control-label text-left" for="status">Project Status</label>
                    <div class="col-md-6">
                        <select class="form-control" name="status">
                            @foreach($status as $key => $value)
                                <option value="{{$key}}" <?php if(Request::get('status') == $key) echo "selected";?>>{{$value}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="project">Project</label>
                    <div class="col-md-6">
                        <select class="form-control" name="project" id="project_id">
                            <option value=""> -- All --</option>
                            @if(!empty($projects))
                                @foreach($projects as $project)
                                    @if (Request::get('project', '') == $project->id)
                                         <option value="{{ $project->id }}" selected>{{ $project->name }}</option>
                                    @else
                                         <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-12 text-center">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>
        <!-- //search area -->
    </div>
    <!-- common area -->
    <hr>
    <!-- data display area -->

        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                            <div class="table-responsive" id="scroll-x">
                                <div class="hiddenTableReport">
                                    <table class="table table-bordered table-hover table-striped projectReportTable" id="responsiveTable">
                                        <thead>
                                            <tr>
                                                <th colspan="10">Common Data</th>
                                                <th colspan="2">Productivity</th>
                                                <th colspan="5">Quality</th>
                                            </tr>
                                            <tr>
                                                <th class="info_data">No</th>
                                                <th class="info_data" >Project Name</th>
                                                <th class="info_data">UAT execution</th>
                                                <th class="info_data">Detail design?</th>
                                                <th class="info_data">UT or TF?</th>
                                                <th class="info_data">Tested TC</th>
                                                <th class="info_data">LOC</th>
                                                <th class="info_data">Bug (weighted)</th>
                                                <th class="info_data">Bug after release (weighted)</th>
                                                <th class="info_data">Workload (mm)</th>
                                                <th class="productivity">KLOC / mm</th>
                                                <th class="productivity">TC / mm</th>
                                                <th class="quality">Bug / KLOC</th>
                                                <th class="quality">Bug after release / KLOC</th>
                                                <th class="quality">Bug / 1000TC</th>
                                                <th class="quality">% Bug before release</th>
                                                <th class="quality">Bug / mm</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                    @if(!empty($dataProjects))
                                    <?php $number = 0; ?>
                                        @foreach($dataProjects as $data)
                                        <?php
                                            $kloc = Helpers::writeNumber($data['loc'], 1000);
                                            $kTestcase = Helpers::writeNumber($data['tested_tc'], 1000);
                                            $workload = Helpers::writeNumber($data['actual_hour'], $mm);
                                            $bugBeforeRelease = Helpers::writeNumberInPercent($data['weighted_bug'], ($data['weighted_bug']+$data['weighted_uat_bug']));
                                        ?>
                                        <tr class="record">
                                            <td >{{ ++$number }}</td>
                                            <td class="text-left">{{ $data['project_name'] }}</td>
                                            <td>{{$data['tested_tc']}}</td>
                                            <td>{{$kloc}}</td>
                                            <td>{{$data['task']}}</td>
                                            <td>{{ $data['weighted_bug'] }}</td>
                                            <td>{{ $data['weighted_uat_bug'] }}</td>
                                            <td>{{ $workload }}</td>
                                            <td>{{ Helpers::writeNumber($kloc, $workload)}}</td>
                                            <td>{{ Helpers::writeNumber($data['tested_tc'], $workload)}}</td>
                                            <td>{{ Helpers::writeNumber($data['task'], $workload)}}</td>
                                            <td>{{ Helpers::writeNumber($data['weighted_bug'], $kloc)}}</td>
                                            <td>{{ Helpers::writeNumber($data['weighted_uat_bug'], $kloc) }}</td>
                                            <td>{{ Helpers::writeNumber($data['weighted_bug'], $kTestcase) }}</td>
                                            <td>{{ $bugBeforeRelease }}</td>
                                            <td>{{ Helpers::writeNumber($data['weighted_bug'], $workload) }}</td>
                                        </tr>
                                        @endforeach
                                    @endif

                                    </tbody>

                                    </table>
                                </div>
                            <div id="graph">
                                <div id="graph_wrap">
                                    <div class="row">
                                        <div class="col-sm-9" id="productivity_kloc"></div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-9" id="productivity_tc_mt"></div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-9" id="quality_bug_kloc"></div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-9" id="quality_uat"></div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-9" id="quality_bug_tc"></div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-9" id="quality_bug_before_release"></div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-9" id="quality_bug_mm"></div>
                                    </div>
                                </div>
                            </div>
                </div>
<!-- table data -->
            </div>
<!-- data display area -->
        </div>
    </div>
</div>
@stop
@section('script')
    <script type="text/javascript" src="{{ asset('/js/common/highcharts.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/common/quality_chart.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/jquery.inputmask.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/jquery.validate.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/common/validate_date.js') }}"></script>
@stop