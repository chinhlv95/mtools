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
    <div class="panel-heading" id="form_heading">Report Productivity And Quality By Member</div>
    <!-- common area -->
    <div class="panel-body">
        <!-- search area -->
        <form action="{{ URL::route('quality-report.member.show') }}" id="search_form" method="get" class="form-horizontal">
            <div class="info-left col-md-6">
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
                <div class="form-group">
                    <label class="col-md-4 control-label" for="position">Position</label>
                    <div class="col-md-6">
                            <label class="col-md-3 radio-inline"> <input name="position" type="radio" value="Dev" checked="checked">Dev</label>
                            <label class="col-md-3 radio-inline"><input name="position" type="radio" value="QA" <?php if(Request::get('position') == 'QA') echo "checked='checked'";?>>QA</label>
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
    <!-- data display area -->
    <div class="panel-body">
    @if(Request::get('year') == null)
    @else
        <div class="panel-body">
            <div class="panel panel-default">
                <!-- table data -->
                <div class="panel-body">
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="totalSummary">
                            <div class="table-responsive" id="report-scroll-x">
                                @if($flag == 1)
                                <table class="table table-bordered table-hover table-striped developerData" id="devResponsiveTable">
                                    <caption><h5>DEVELOPERS</h5></caption>
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th colspan="6">Common Data</th>
                                            <th colspan="3">Productivity</th>
                                            <th colspan="2">Quality</th>
                                        </tr>
                                        <tr>
                                            <th class="info_data">No</th>
                                            <th class="info_data">Username</th>
                                            <th class="info_data" >Fullname</th>
                                            <th class="info_data"><div data-toggle="tooltip" title="Tổng số dòng code">LOC</div></th>
                                            <th class="info_data"><div data-toggle="tooltip" title="Công số làm việc thực tế">Workload (mm)</div></th>
                                            <th class="info_data"><div data-toggle="tooltip" title="Tổng trọng số lỗi được yêu cầu sửa">Assigned Bug (weighted)</div></th>
                                            <th class="info_data"><div data-toggle="tooltip" title="Tổng trọng số lỗi gây ra trong quá trình xây dựng sản phẩm ">Made Bug (weighted)</div></th>
                                            <th class="info_data"><div data-toggle="tooltip" title="Số task công việc thực hiện">Task</div></th>
                                            <th class="productivity"><div data-toggle="tooltip" title="Trung bình số LOC trong 1 tháng (1 KLOC= 1000 LOC)">KLOC / mm</div></th>
                                            <th class="productivity"><div data-toggle="tooltip" title="Trung bình tổng trọng số lỗi được yêu cầu sửa trong tháng">Assigned Bug / mm</div></th>
                                            <th class="productivity"><div data-toggle="tooltip" title="Trung bình số task công việc thực hiện trong 1 tháng">Task / mm</div></th>
                                            <th class="quality"><div data-toggle="tooltip" title="Trung bình tổng trọng số lỗi gây ra trên 1000 LOC">Made Bug / KLOC</div></th>
                                            <th class="quality"><div data-toggle="tooltip" title="Trung bình tổng trọng số lỗi gây ra trong 1 tháng">Made Bug / mm</div></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($devDatas))
                                    <?php $number1 = 0; ?>
                                        @foreach($devDatas as $data)
                                        <?php $workload =  Helpers::writeNumber($data['workload'], $mm);?>
                                        <tr class="record">
                                            <td >{{ ++$number1 }}</td>
                                            <td class="text-left">{{ $data['user_name'] }}</td>
                                            <td class="text-left"><div data-toggle="tooltip" title="{{$data['email']}}">{{$data['name']}}</div></td>
                                            <td>{{ $data['kloc'] }}</td>
                                            <td>{{ $workload }}</td>
                                            <td>{{ $data['bug_weighted'] }}</td>
                                            <td>{{ $data['madebug_weighted'] }}</td>
                                            <td>{{ $data['task'] }}</td>
                                            <td>{{ Helpers::writeNumber($data['kloc'], $data['workload']/$mm)}}</td>
                                            <td>{{ Helpers::writeNumber($data['bug_weighted'], $data['workload']/$mm)}}</td>
                                            <td>{{ Helpers::writeNumber($data['task'], $data['workload']/$mm)}}</td>
                                            <td>{{ Helpers::writeNumber($data['madebug_weighted'], $data['kloc'])}}</td>
                                            <td>{{ Helpers::writeNumber($data['madebug_weighted'], $data['workload']/$mm) }}</td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr><td class="text-left" colspan="10">Empty User!</td></tr>
                                    @endif
                                    </tbody>
                                </table>
                                @endif
                                @if($flag == 2)
                                 <table class="table table-bordered table-hover table-striped qaTable" id="qaResponsiveTable">
                                    <caption><h5>QUALITY ASSURANCE</h5></caption>
                                        <thead>
                                            <tr>
                                                 <th colspan="9">Common Data</th>
                                                <th colspan="3">Productivity</th>
                                                <th colspan="2">Quality</th>
                                            </tr>
                                            <tr>
                                                <th class="info_data">No</th>
                                                <th class="info_data" >Username</th>
                                                <th class="info_data" >Fullname</th>
                                                <th class="info_data" ><div data-toggle="tooltip" title="Số task công việc thực hiện">Task</div></th>
                                                <th class="info_data"><div data-toggle="tooltip" title="Số test case viết được">Test Case Creation</div></th>
                                                <th class="info_data"><div data-toggle="tooltip" title="Thời gian thực tế tạo test case ( đơn vị tháng)">Workload for create (mm)</div></th>
                                                <th class="info_data"><div data-toggle="tooltip" title="Số test case chạy được">Test Case Execution</div></th>
                                                <th class="info_data"><div data-toggle="tooltip" title="Thời gian thực tế chạy test ( đơn vị tháng)">Workload For Test (mm)</div></th>
                                                <th class="info_data"><div data-toggle="tooltip" title="Tổng trọng số lỗi tìm được">Bug (weighted)</div></th>
                                                <th class="productivity"><div data-toggle="tooltip" title="Trung bình số test case viết được trong tháng">Test Case Creation/ mm</div></th>
                                                <th class="productivity"><div data-toggle="tooltip" title="Trung bình số test case chạy được trong tháng">Test Case Execution/ mm</div></th>
                                                <th class="productivity"><div data-toggle="tooltip" title="Trung bình số task công việc thực hiện trong 1 tháng">Task / mm</div></th>
                                                <th class="quality"><div data-toggle="tooltip" title="Trung bình tổng trọng số lỗi tìm được trên  1000 test case đã chạy ">Bug / 1000TC</div></th>
                                                <th class="quality"><div data-toggle="tooltip" title="Trung bình tổng trọng số lỗi tìm được trong 1 tháng">Bug / mm</div></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                         @if(!empty($qaDatas))
                                         <?php $number2 = 0;?>
                                         @foreach($qaDatas as $data)

                                            <tr class="record">
                                                <td>{{ ++$number2 }}</td>
                                                <td class="text-left">{{ $data['user_name'] }}</td>
                                                <td class="text-left"><div data-toggle="tooltip" title="{{$data['email']}}">{{$data['name']}}</div></td>
                                                <td>{{ $data['task'] }}</td>
                                                <td>{{ $data['testcase_create'] }}</td>
                                                <td>{{ $data['createTc_workload'] }}</td>
                                                <td>{{ $data['testcase_test'] }}</td>
                                                <td>{{ $data['test_workload'] }}</td>
                                                <td>{{ $data['foundbug_weighted'] }}</td>
                                                <td>{{ Helpers::writeNumber($data['testcase_create'], $data['createTc_workload']/$mm) }}</td>
                                                <td>{{ Helpers::writeNumber($data['testcase_test'], $data['test_workload']/$mm) }}</td>
                                                <td>{{ Helpers::writeNumber($data['task'], $data['workload']/$mm) }}</td>
                                                <td>{{ Helpers::writeNumber($data['foundbug_weighted'], ($data['testcase_test']/1000))}}</td>
                                                <td>{{ Helpers::writeNumber($data['foundbug_weighted'], $data['workload']/$mm) }}</td>
                                            </tr>
                                        @endforeach
                                        @else
                                            <tr><td class="text-left" colspan="10">Empty User!</td></tr>
                                        @endif
                                        </tbody>
                                    </table>
                                    @endif
                                    @if($flag == 0)
                                    <p>You do not have a position to view!</p>
                                    @endif
                                 </div>
                            </div>
                        </div>
                    </div>
                </div>
<!-- table data -->
            </div>
<!-- data display area -->
        </div>
        @endif
    </div>
</div>
@stop
@section('script')
    <script type="text/javascript" src="{{ asset('/js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/dataTables.bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('/js/quality_report/quality_report.js')}}"></script>
    <script type="text/javascript" src="{{ asset('/js/common/ajax_company_struct.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/quality_report/tooltip_report.js')}}"></script>
    <script type="text/javascript" src="{{ asset('/js/jquery.inputmask.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/jquery.validate.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/common/validate_date.js') }}"></script>
@stop