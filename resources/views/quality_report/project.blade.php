@extends('layouts.master')
@section('title', 'Summary Report')

@section('breadcrumbs','Summary Report')
@section('style')
    <link href="{{ asset('css/custom/date-form.css') }}" rel="stylesheet">
    <link href="{{ asset('css/quality/quality_productivity.css') }}" rel="stylesheet">
     <link href="{{ asset('css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
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
                            <option value="-1"> -- All --</option>
                            @if(!empty($departments))
                                @foreach($departments as $item)
                                    <option value="{{$item['id']}}" <?php if(Request::get('department') == $item['id']) echo "selected"?>>{{$item['name']}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="division">Division</label>
                    <div class="col-md-6">
                        <select class="form-control" name="division" id="division_id">
                            <option value="-1"> -- All --</option>
                            @if(!empty($divisions))
                                @foreach($divisions as $item)
                                    <option value="{{$item['id']}}" <?php if(Request::get('division') == $item['id']) echo "selected"?>>{{$item['name']}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="team">Team</label>
                    <div class="col-md-6">
                        <select class="form-control" name="team" id="team_id">
                            <option value="-1"> -- All --</option>
                            @if(!empty($teams))
                                @foreach($teams as $item)
                                    <option value="{{$item['id']}}"<?php if(Request::get('team') == $item['id']) echo "selected"?>>{{$item['name']}}</option>
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
                            <option value="-1"> -- All --</option>
                            @if(!empty($projects))
                                @foreach($projects as $item)
                                    <option value="{{$item['id']}}" <?php if(Request::get('project') == $item['id']) echo "selected"?>>{{$item['name']}}</option>
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
    @if(Request::get('reportType') == null)
    @else
        <div class="panel-body">
            <div class="panel panel-default">
                <!-- table data -->
                <div class="panel-body">
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="totalSummary">
                            <div class="table-responsive" id="report-scroll-x">
                                <table class="table table-bordered table-hover table-striped projectReportTable" id="responsiveTable">
                                 <caption><h5>Report Quality And Productivity By Project</h5></caption>
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th colspan="6">Common Data</th>
                                            <th colspan="3">Productivity</th>
                                            <th colspan="5">Quality</th>
                                        </tr>
                                        <tr>
                                            <th class="info_data" >No</th>
                                            <th class="info_data" >Project Name</th>
                                            <th class="info_data" ><div data-toggle="tooltip" title="Số test case đã được chạy">Tested TC</div></th>
                                            <th class="info_data"><div data-toggle="tooltip" title="Số dòng code">LOC</div></th>
                                            <th class="info_data"><div data-toggle="tooltip" title="Số task công việc thực hiện">Task</div></th>
                                            <th class="info_data"><div data-toggle="tooltip" title="Tổng trọng số Bug">Bug (weighted)</div></th>
                                            <th class="info_data"><div data-toggle="tooltip" title="Tổng trọng số bug để lọt sang khách hàng sau khi bàn giao">Bug after release (weighted)</div></th>
                                            <th class="info_data"><div data-toggle="tooltip" title="Công số làm việc thực tế">Workload (mm)</div></th>
                                            <th class="productivity"><div data-toggle="tooltip" title="Trung bình số LOC viết được trong 1 tháng ( 1 KLOC=1000 LOC)">KLOC / mm</div></th>
                                            <th class="productivity"><div data-toggle="tooltip" title="Trung bình số test case đã được chạy trong 1 tháng">TC / mm</div></th>
                                            <th class="productivity"><div data-toggle="tooltip" title="Trung bình số task công việc thực hiện trong 1 tháng">Task / mm</div></th>
                                            <th class="quality"><div data-toggle="tooltip" title="Trung bình tổng trọng số lỗi trên 1000 LOC">Bug / KLOC</div></th>
                                            <th class="quality"><div data-toggle="tooltip" title="Trung bình tổng trọng số lỗi lọt sang khách hàng sau bàn giao trên 1000 LOC">Bug after release/KLOC</div></th>
                                            <th class="quality"><div data-toggle="tooltip" title="Trung bình tổng trọng số lỗi tìm được trên 1000 test case">Bug / 1000TC</div></th>
                                            <th class="quality"><div data-toggle="tooltip" title="Tỷ lệ lỗi tìm được trước khi bàn giao trên tổng lỗi của dự án ( gồm lỗi trước và sau khi bàn giao sản phẩm)">% Bug before release</div></th>
                                            <th class="quality"><div data-toggle="tooltip" title="Trung bình tổng trọng số lỗi trong 1 tháng">Bug / mm</div></th>
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
                        </div>
                    </div>
                </div>
<!-- table data -->
            </div>
<!-- data display area -->
        </div>

    </div>
     @endif
</div>
@stop
@section('script')
 <script type="text/javascript" src="{{ asset('/js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/dataTables.bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/common/ajax_company_struct.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/quality_report/tooltip_report.js')}}"></script>
    <script type="text/javascript" src="{{ asset('/js/quality_report/quality_report.js')}}"></script>
    <script type="text/javascript" src="{{ asset('/js/jquery.inputmask.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/jquery.validate.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/common/validate_date.js') }}"></script>
@stop