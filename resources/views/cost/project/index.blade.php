<?php
    use function GuzzleHttp\json_decode;
    $listYear = Helpers::getListYear();
    $listMonth = Helpers::getListMonth();
?>
@extends('layouts.master')
@section('title','Project cost')
@section('breadcrumbs','Project cost')
@section('style')
    <link href="{{ asset('css/custom/date-form.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom/cost.css') }}" rel="stylesheet">
    <style>
        .dowload_file_teamlate{
            color:#8A2BE2;
        }
        @media screen and (min-width: 480px) {
            .import-modal {
                overflow-y: auto;
            }
        }
    </style>
@stop
@section('content')
    <div class="ajax-loader" style="display:none">
        <img src="{{ asset('/img/ajax_loader.gif') }}" class="img-responsive" />
    </div>
    @if (Request::get('check_time') == 2)
        <?php
            $sdate = Request::get('start_date');
            $edate = Request::get('end_date');
        ?>
    @endif
    <div class="padding-md">
        <div class="alert alert-danger hide" id="errorMessage">
            <button type="button" class="close closeMessage">
                <i class="fa fa-times"></i>
            </button>
            <span id="message">
            </span>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading" id="form_heading">Project Cost</div>
            <div class="panel-body" id="form_body">
                <form method="get" action="{{ URL::route('project.cost.index') }}" id="search_form" class="form-horizontal" enctype="multipart/form-data">
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
                        <div class="form-group type_time_my" hidden="hidden">
                            <label class="col-md-4 control-label">
                                <span class="col-sm-12">
                                    <input type="radio" hidden="hidden" id="month_time" name="check_time" value="3" <?php if(Request::get('check_time') == 3) echo "checked='checked'";?>>
                                    Choose time
                                </span>
                            </label>
                            <div class="col-md-6">
                                <div class="col-md-6 row">
                                    <select class="form-control" name="month" id="month">
                                        @foreach($listMonth as $key=>$value)

                                            <option value="{{$key}}" <?php if(Request::get('month') == null && date('M') == $value){echo "selected";}elseif(Request::get('month') == $key){echo "selected";}?>>{{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 row div-right">
                                    <select class="form-control" name="year" id="year">
                                        @foreach($listYear as $key=>$value)
                                            <option value="{{$key}}" <?php if(Request::get('year') == null && date('Y') == $value){echo "selected";}elseif(Request::get('year') == $key){echo "selected";}?>>{{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group type_time_pd">
                            <label class="col-md-4 control-label">
                                <span class="col-sm-12">
                                    <input type="radio" id="default_time" name="check_time" value="1" <?php if(Request::get('check_time') == 1) echo "checked='checked'";?>>
                                    Choose from list
                                </span>
                            </label>
                            <div class="col-md-6">
                                <select class="form-control" name="date" id="select_defalt_time">
                                    @foreach($select_date as $key => $value)
                                        <option value="{{$key}}" <?php if(Request::get('date')==$key) echo "selected";?>>{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group type_time_pd">
                            <label class="col-md-4 control-label text-left">
                                <span class="col-sm-12">
                                    <input type="radio" id="choose_time" class="col-md-4" name="check_time" value="2" <?php if(Request::get('check_time') == 2) echo "checked='checked'";?>>
                                    Choose start, end
                                </span>
                            </label>
                            <div class="col-md-6">
                                <div class="input-group col-md-12" id="dateForm">
                                    <input class="form-control" id="start_date" data-inputmask="'alias': 'date'" value="{{Request::get('start_date') ? Request::get('start_date','') : $firstDateDefault}}" name="start_date"  type="text" <?php if(Request::get('check_time','1') == 1) echo "disabled='disabled'";?> onpaste="return false;">
                                    <span class="input-group-addon open-startdate">
                                        <i class="fa fa-calendar open-startdate"></i>
                                    </span>
                                </div>
                                <div style="display: inline-block;">
                                    <label id="start_date-error" class="error" for="start_date"></label>
                                </div>
                                @if ($errors->has('start_date'))
                                    <span class="error-message help-block">
                                        <strong>{{ $errors->first('start_date') }}</strong>
                                    </span>
                                @endif
                                <div class="col-md-8 col-md-offset-3">
                                </div>
                                <div class="input-group col-md-12" id="dateForm">
                                    <div>
                                        <input class="form-control" id="end_date" data-inputmask="'alias': 'date'" value="{{Request::get('end_date') ? Request::get('end_date','') : $endDateDefault}}" name="end_date" type="text" <?php if(Request::get('check_time','1') == 1) echo "disabled='disabled'";?> onpaste="return false;">
                                    </div>
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
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn btn-primary" id="search_button_1">Search</button>
                        <button type="button" class="btn btn-danger" id="configreset">Reset</button>
                         @if($isAdmin == 1 || (isset($projects[0]->permissions) && array_key_exists("user.import_cost", json_decode($projects[0]->permissions))))
                            <button type="button" class="btn btn-info" id="buttonShowModal" data-toggle="modal" data-target="#listImport">Import</button>
                        @endif
                        @if($isAdmin == 1 || (isset($projects[0]->permissions) && array_key_exists("user.export_cost", json_decode($projects[0]->permissions))))
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#export">Export</button>
                        @endif
                    </div>
                </form>
            </div><!-- /panel-body -->
            <hr/>
            <div class="panel-body" id="searchResult">
                @if(Request::get('reportType') == null)
                @else
                <div class="panel-body">
                    <div class="col-md-6">
                        @if(Request::get('reportType') == 'graph_report')
                        @elseif(Request::get('reportType') == 'personal_report')
                            @if(count($allMember) == 0)
                                <span class="wordbold text-left"><strong>Total number of records: No Result</strong></span>
                            @else
                                <span class="wordbold text-left"><strong>Total number of records: {{ $allMember->total() }}</strong></span>
                            @endif
                        @elseif(Request::get('reportType') == 'personal_detail_report')
                            @if(count($listUser) == 0)
                                <span class="wordbold text-left"><strong>Total number of records: No Result</strong></span>
                            @else
                                <span class="wordbold text-left"><strong>Total number of records: {{ count($listUser) }}</strong></span>
                            @endif
                        @else
                            @if(count($listProjects) == 0)
                                <span class="wordbold text-left"><strong>Total number of records: No Result</strong></span>
                            @else
                                <span class="wordbold text-left"><strong>Total number of records: {{ $listProjects->total() }}</strong></span>
                            @endif
                        @endif
                    </div>
                    <div class="col-md-6">
                        @if(Request::get('reportType') == 'graph_report' || Request::get('reportType') == null)
                        @elseif(Request::get('reportType') == 'personal_detail_report')
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <select class="form-control inline-block" size="1" id="select-detail-user-list"  onchange="location = this.value;">
                                    @foreach($listUser as $key => $values)
                                        <option value="#{{$values}}">{{$values}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <form method="get" class="pull-right">
                                <input type="hidden" name="check_time" value="{{Request::get('check_time', 1)}}">
                                <input type="hidden" name="date"       value="{{Request::get('date','this_month')}}">
                                <input type="hidden" name="start_date" value="{{Request::get('start_date','')}}">
                                <input type="hidden" name="end_date"   value="{{Request::get('end_date','')}}">
                                <input type="hidden" name="reportType" value="{{Request::get('reportType','')}}">
                                <input type="hidden" name="month"      value="{{Request::get('month','')}}">
                                <input type="hidden" name="year"       value="{{Request::get('year','')}}">
                                <input type="hidden" name="project"    value="{{Request::get('project','')}}">
                                <input type="hidden" name="department" value="{{Request::get('department','')}}">
                                <input type="hidden" name="division"   value="{{Request::get('division','')}}">
                                <input type="hidden" name="team"       value="{{Request::get('team','')}}">
                                <input type="hidden" name="status"     value="{{Request::get('status','')}}">
                                <label for="choose_item">Item display on page: &nbsp; &nbsp;</label>
                                <select id="choose_item" name="limit" class="form-control input-md inline-block" size="1" onchange="this.form.submit()">
                                    @if(!empty($paginate))
                                        @foreach($paginate as $key => $values)
                                            @if(Request::get('limit', 5) == $values)
                                                <option value="{{$key}}" selected>{{$values}}</option>
                                            @else
                                                <option value="{{$key}}">{{$values}}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="panel-body">
                    @if(Request::get('reportType') == null)

                    @elseif(Request::get('reportType') == 'summary_report')
                        <table class="table table-bordered table-hover table-striped">
                            <caption><h4 class="wordbold">Summary Report ({{date("d/m/Y",strtotime($start_date))}} - {{date("d/m/Y",strtotime($end_date))}})</h4></caption>
                            <thead>
                              <tr>
                                <th class="text-center fivePercent">No</th>
                                <th class="text-center">Project</th>
                                <th class="text-center tenPercent">Total effort (Hour)</th>
                                <th class="text-center twentyPercent">Full name</th>
                                <th class="text-center tenPercent">Role</th>
                                <th class="text-center tenPercent">Location</th>
                                <th class="text-center tenPercent">Work load (Hour)</th>
                              </tr>
                            </thead>
                            <tbody>
                                @if(isset($listProjects))
                                    @foreach($listProjects as $eachProject)
                                        <?php
                                            $flag = 0;
                                            $count = Helpers::matchProjectWithMember($projectMembers, $eachProject->id);
                                            $total = Helpers::entryOfEachProject($entry, $eachProject->id);
                                        ?>
                                        @if($count['flagCount'] == 1)
                                            @foreach($projectMembers as $member)
                                                @if($eachProject->id == $member->project_id)
                                                    <tr>
                                                        @if($flag == 0)
                                                            <td rowspan="{{ $count['count'] }}" class="text-center td-gray-color">{{ ++$number }}</td>
                                                            <td rowspan="{{ $count['count'] }}" class="pj_name td-gray-color">{{ $eachProject->name }}</td>
                                                            @if(isset($total))
                                                                <td class="total_effort text-center td-gray-color" rowspan ="{{ $count['count'] }}">{{ $total }}</td>
                                                            @else
                                                                <td class="total_effort text-center td-gray-color" rowspan ="{{ $count['count'] }}">0</td>
                                                            @endif
                                                            <?php $flag = 1; ?>
                                                        @endif
                                                        <td>{{ $member->last_name.' '.$member->first_name }}</td>
                                                        <td>{{ strtoupper($member->user_position) }}</td>
                                                        <td>{{ strtoupper(substr($member->email,-2)) }}</td>
                                                        <td class="text-center">
                                                            {{ $member->personalTime }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @elseif($count['flagCount'] == 2)
                                            <tr>
                                                <td class="text-center td-gray-color">{{ ++$number }}</td>
                                                <td class="pj_name td-gray-color">
                                                    <a href="" >{{ $eachProject->name }}</a>
                                                </td>
                                                <td class="total_effort text-center td-gray-color">0</td>
                                                <td>n/a</td>
                                                <td>n/a</td>
                                                <td>n/a</td>
                                                <td>n/a</td>
                                                <td class="text-center">n/a</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    @elseif(Request::get('reportType') == 'position_report')
                        <table class="table table-bordered table-hover table-striped" id="">
                            <caption><h4 class="wordbold">Position Report ({{date("d/m/Y",strtotime($start_date))}} - {{date("d/m/Y",strtotime($end_date))}})</h4></caption>
                            <thead>
                              <tr>
                                <th class="text-center fivePercent">No</th>
                                <th class="text-center">Project</th>
                                <th class="text-center tenPercent">Total effort (Hour)</th>
                                <th class="text-center eightPercent">BSE</th>
                                <th class="text-center eightPercent">BSE/JP</th>
                                <th class="text-center eightPercent">DEVL</th>
                                <th class="text-center eightPercent">DEV</th>
                                <th class="text-center eightPercent">QAL</th>
                                <th class="text-center eightPercent">QA</th>
                                <th class="text-center eightPercent">Comtor</th>
                                <th class="text-center eightPercent">JP support</th>
                                <th class="text-center eightPercent">Others</th>
                              </tr>
                            </thead>
                            <tbody>
                                @if(isset($listProjects))
                                    @foreach($listProjects as $eachProject)
                                        <?php
                                            $positionWork = Helpers::getActualTimeOfEachPosition($entry, $eachProject->id);
                                            $total        = Helpers::entryOfEachProject($entry, $eachProject->id)
                                        ?>
                                        <tr>
                                            <td class="fivePercent">{{ ++$number }}</td>
                                            <td class="t-left"><a href="">{{ $eachProject->name }}</a></td>
                                            @if(isset($total))
                                                <td class="text-center tenPercent">{{ $total }}</td>
                                            @else
                                                <td class="text-center tenPercent">0</td>
                                            @endif
                                            <td class="text-center eightPercent">{{ $positionWork['bse'] }}</td>
                                            <td class="text-center eightPercent">{{ $positionWork['bsejp'] }}</td>
                                            <td class="text-center eightPercent">{{ $positionWork['devl'] }}</td>
                                            <td class="text-center eightPercent">{{ $positionWork['dev'] }}</td>
                                            <td class="text-center eightPercent">{{ $positionWork['qal'] }}</td>
                                            <td class="text-center eightPercent">{{ $positionWork['qa'] }}</td>
                                            <td class="text-center eightPercent">{{ $positionWork['comtor'] }}</td>
                                            <td class="text-center eightPercent">{{ $positionWork['jpsupport'] }}</td>
                                            <td class="text-center eightPercent">{{ $positionWork['other'] }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    @elseif(Request::get('reportType') == 'entries_detail_report')
                        <div class="table-responsive" id="scroll-x">
                            <table class="table table-bordered table-hover table-striped" id="">
                                <thead>
                                  <tr>
                                    <th class="text-center noTable">No</th>
                                    <th class="text-center projectTable">Project</th>
                                    <th class="text-center memberIdTable">Member ID</th>
                                    <th class="text-center fullNameTable">Full name</th>
                                    <th class="text-center totalHour">Total hour</th>
                                    @if(ceil(abs(strtotime($end_date) - strtotime($start_date)) / 86400) > 31)
                                        <?php $period = Helpers::findMonthInPeriodOfTime($start_date, $end_date); ?>
                                        @foreach ($period as $month)
                                            <th class="text-center">{{ $month->format("m/Y") }}</th>
                                        @endforeach
                                    @elseif((ceil(abs(strtotime($end_date) - strtotime($start_date)) / 86400) > 7) && ceil(abs(strtotime($end_date) - strtotime($start_date)) / 86400) <= 31)
                                        <?php $period = Helpers::findWeekInPeriodOfTime($start_date, $end_date); ?>
                                        @foreach ($period as $week)
                                            <th class="text-center">W{{ $week->format("W/Y") }}</th>
                                        @endforeach
                                    @else
                                        @for ($i = strtotime($start_date); $i <= strtotime($end_date); $i = strtotime("+1 day", $i))
                                            <?php $weekend =  date("w", $i); ?>
                                            @if($weekend == 6 || $weekend == 0)
                                                <th class="text-center weekend">{{ date("d/m", $i) }}</th>
                                            @else
                                                <th class="text-center">{{ date("d/m", $i) }}</th>
                                            @endif
                                        @endfor
                                    @endif
                                  </tr>
                                </thead>
                                <tbody>
                                @if(ceil(abs(strtotime($end_date) - strtotime($start_date)) / 86400) > 31)
                                    <?php $period = Helpers::findMonthInPeriodOfTime($start_date, $end_date); ?>
                                    @if(isset($listProjects))
                                        @foreach($listProjects as $eachProject)
                                            <?php
                                                $flag = 0;
                                                $count = Helpers::matchProjectWithMember($projectMembers, $eachProject->id);
                                            ?>
                                            @if($count['flagCount'] == 1)
                                                @foreach($projectMembers as $member)
                                                    @if($eachProject->id == $member->project_id)
                                                        <tr>
                                                            @if($flag == 0)
                                                                <td rowspan="{{ $count['count'] }}" class="text-center td-gray-color">{{ ++$number }}</td>
                                                                <td rowspan="{{ $count['count'] }}" class="pj_name td-gray-color">{{ $eachProject->name }}</td>
                                                                <?php $flag = 1; ?>
                                                            @endif
                                                            <?php $explodeEmail = explode('@',$member->email); ?>
                                                            <td>{{ $explodeEmail[0] }}</td>
                                                            <td>{{ $member->last_name.' '.$member->first_name }}</td>
                                                            <td class="text-center">{{ $member->totalTime }}</td>
                                                            @foreach ($member->entryHour as $entryHour)
                                                                <td class="text-center">
                                                                @if($entryHour != 0)
                                                                    {{ $entryHour }}
                                                                @endif
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @elseif($count['flagCount'] == 2)
                                                <tr>
                                                    <td class="text-center td-gray-color">{{ ++$number }}</td>
                                                    <td class="pj_name td-gray-color">
                                                        <a href="" >{{ $eachProject->name }}</a>
                                                    </td>
                                                    <td class="t-left">n/a</td>
                                                    <td class="t-left">n/a</td>
                                                    <td class="text-center">0</td>
                                                    @foreach ($period as $month)
                                                        <td></td>
                                                    @endforeach
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                @elseif((ceil(abs(strtotime($end_date) - strtotime($start_date)) / 86400) > 7) && ceil(abs(strtotime($end_date) - strtotime($start_date)) / 86400) <= 31)
                                    <?php $period = Helpers::findWeekInPeriodOfTime($start_date, $end_date); ?>
                                    @if(isset($listProjects))
                                        @foreach($listProjects as $eachProject)
                                            <?php
                                                $flag = 0;
                                                $count = Helpers::matchProjectWithMember($projectMembers, $eachProject->id);
                                            ?>
                                            @if($count['flagCount'] == 1)
                                                @foreach($projectMembers as $member)
                                                    @if($eachProject->id == $member->project_id)
                                                        <tr>
                                                            @if($flag == 0)
                                                                <td rowspan="{{ $count['count'] }}" class="text-center td-gray-color">{{ ++$number }}</td>
                                                                <td rowspan="{{ $count['count'] }}" class="pj_name td-gray-color">{{ $eachProject->name }}</td>
                                                                <?php $flag = 1; ?>
                                                            @endif
                                                            <?php $explodeEmail = explode('@',$member->email); ?>
                                                            <td>{{ $explodeEmail[0] }}</td>
                                                            <td>{{ $member->last_name.' '.$member->first_name }}</td>
                                                            <td class="text-center">{{ $member->totalTime }}</td>
                                                            @foreach ($member->entryHour as $entryHour)
                                                                <td class="text-center">
                                                                @if($entryHour != 0)
                                                                    {{ $entryHour }}
                                                                @endif
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @elseif($count['flagCount'] == 2)
                                                <tr>
                                                    <td class="text-center td-gray-color">{{ ++$number }}</td>
                                                    <td class="pj_name td-gray-color">
                                                        <a href="" >{{ $eachProject->name }}</a>
                                                    </td>
                                                    <td class="t-left">n/a</td>
                                                    <td class="t-left">n/a</td>
                                                    <td class="text-center">0</td>
                                                    @foreach ($period as $month)
                                                        <td></td>
                                                    @endforeach
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                @else
                                    <?php $allTotal = 0; ?>
                                    @if(isset($listProjects))
                                        @foreach($listProjects as $getProject)
                                            @if(!empty($projectMembers))
                                                @foreach($projectMembers as $projectMems)
                                                    <?php $flag = 0;?>
                                                    @if($projectMems['project_id'] == $getProject->id)
                                                        @if($projectMems['member'] != null)
                                                            @foreach($projectMems['member'] as $member)
                                                                <tr>
                                                                    @if($flag == 0)
                                                                        <td rowspan ="{{ count($projectMems['member']) }}" class="text-center td-gray-color">{{ ++$number }}</td>
                                                                        <td class="t-left td-gray-color" rowspan ="{{ count($projectMems['member']) }}"><a href="">{{ $getProject->name }}</a></td>
                                                                    @endif
                                                                    <?php $explodeEmail = explode('@',$member->email); ?>
                                                                    <td class="t-left"> {{ $explodeEmail[0] }} </td>
                                                                    <td class="t-left">{{ $member->last_name.' '.$member->first_name }}</td>
                                                                    <td class="text-center">{{ $member->total }}</td>
                                                                    @foreach($member->totalEachCell as $totalEachCell)
                                                                        <td class="text-center">
                                                                            @if($totalEachCell == 0)
                                                                                {{ null }}
                                                                            @elseif($totalEachCell > 8)
                                                                                <div class="over8hour">{{ $totalEachCell }}</div>
                                                                            @else
                                                                                {{ $totalEachCell }}
                                                                            @endif
                                                                        </td>
                                                                    @endforeach
                                                                    @if($flag == 0)
                                                                        <?php $flag = 1; ?>
                                                                    @endif
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td class="text-center td-gray-color">{{ ++$number }}</td>
                                                                <td class="td-gray-color t-left"><a href="">{{ $getProject->name }}</a></td>
                                                                <td class="t-left">n/a</td>
                                                                <td class="t-left">n/a</td>
                                                                <td class="text-center">0</td>
                                                                @for ($i = strtotime($start_date); $i <= strtotime($end_date); $i = strtotime("+1 day", $i))
                                                                    <td></td>
                                                                @endfor
                                                            </tr>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                @endif
                                </tbody>
                            </table>
                        </div>
                        <br>
                    @elseif(Request::get('reportType') == 'graph_report')
                        @foreach($entry as $e)
                            <div class="pj_name hidden">{{ $e->project_name }}</div>
                            <div class="total_effort hidden">{{ $e->actual_hour }}</div>
                        @endforeach
                        <div id="graph_wrap">
                            <div id="cost_comparision_hour"></div>
                        </div>
                    @elseif(Request::get('reportType') == 'personal_report')
                        <div class="table-responsive" id="scroll-x">
                            <table class="table table-bordered table-hover table-striped" id="responsiveTable">
                                <caption><h4 class="wordbold">Report by personal ({{date("d/m/Y",strtotime($start_date))}} - {{date("d/m/Y",strtotime($end_date))}})</h4></caption>
                                <thead>
                                  <tr>
                                    <th class="text-center fix10pixcel">No</th>
                                    <th class="text-center fix120pixcel">Fullname</th>
                                    <th class="text-center fix120pixcel">Role</th>
                                    <th class="text-center fix50pixcel">Location</th>
                                    <th class="text-center fix120pixcel">Work time/day</th>
                                    <th class="text-center fix120pixcel">Standard time</th>
                                    <th class="text-center fix50pixcel">Min</th>
                                    <th class="text-center fix50pixcel">Max</th>
                                    <th class="text-center fix120pixcel">Actual time</th>
                                    <th class="text-center fix120pixcel">Under time</th>
                                    <th class="text-center fix120pixcel">Over time</th>
                                    @if(isset($countNumProject) && ($countNumProject > 0))
                                        @for($i=1;$i<($countNumProject+1);$i++)
                                            <th class="text-center fix200pixcel">Joined project</th>
                                        @endfor
                                    @endif
                                  </tr>
                                </thead>
                                <tbody>
                                    @foreach($allMember as $member)
                                        <tr>
                                            <td>{{ ++$number }}</td>
                                            <td  class="text-left">
                                                {{ $member->last_name . ' '. $member->first_name}}
                                            </td>
                                            <td class="text-left pj_name">{{ $member->position }}</td>
                                            <td>VN</td>
                                            <td>
                                                {{ $member->workTime }}
                                            </td>
                                            <td>{{ $member->standardTime }}</td>
                                            <td>{{ $member->minTime }}</td>
                                            <td>{{ $member->maxTime }}</td>
                                            <td class="total_effort">
                                                {{ $member->personalEntry }}
                                            </td>
                                            <td>
                                                @if($member->underTime >= 0)
                                                    {{ $member->underTime }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($member->overTime >= 0)
                                                    {{ $member->overTime }}
                                                @endif
                                            </td>
                                            <?php $count = 0; ?>
                                            @foreach($member->projectName as $projectName)
                                                <?php $count++; ?>
                                                <td  class="text-left">{{ $projectName }}</td>
                                            @endforeach
                                            @for($i=1;$i<($countNumProject+1-$count);$i++)
                                                <td></td>
                                            @endfor
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div><!-- /panel -->
                    @elseif(Request::get('reportType') == 'personal_detail_report')
                        @if(count($listUser) > 0)
                            <center><h4 class="wordbold">Report by personal detail</h4></center>
                            @foreach($listUser as $key => $val)
                                <div id="{{ $val }}"></div>
                                <div>
                                    <div id="name-of-user">
                                        <span>{{ $val }}</span>
                                        <hr id="hr-tag-nd">
                                    </div>
                                    <div class="table-responsive" id="scroll-x">
                                        <table class="table table-bordered table-hover table-striped" id="responsiveTable">
                                            <thead>
                                              <tr>
                                                <th class="text-center fix250pixcel">Project Name</th>
                                                <th class="text-center fix250pixcel">Ticket</th>
                                                @for ($i = strtotime($start_date); $i <= strtotime($end_date); $i = strtotime("+1 day", $i))
                                                    <?php $weekend =  date("w", $i); ?>
                                                    @if($weekend == 6 || $weekend == 0)
                                                        <th class="text-center weekend">{{ date("d/m", $i) }}</th>
                                                    @else
                                                        <th class="text-center">{{ date("d/m", $i) }}</th>
                                                    @endif
                                                @endfor
                                              </tr>
                                            </thead>
                                            <tbody>
                                            @if(count($datas) > 0)
                                                @foreach($datas as $data)
                                                    @if($data->user_id == $key)
                                                        <tr>
                                                            <td class="text-left">{{ $data->project_name }}</td>
                                                            <td class="text-left">{{ $data->ticket_name }}</td>
                                                            @for ($i = strtotime($start_date); $i <= strtotime($end_date); $i = strtotime("+1 day", $i))
                                                                <?php $weekend =  date("w", $i); ?>
                                                                <td <?php if($weekend == 6 || $weekend == 0) echo "style='background: #f2f4f7'"?>>
                                                                    @if($i == strtotime($data->spent_at))
                                                                        {{ $data->actual_hour }}
                                                                    @endif
                                                                </td>
                                                            @endfor
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <center>Cannot find data!</center>
                        @endif
                    @endif
                    @if(Request::get('reportType') == 'graph_report')
                    @elseif(Request::get('reportType') == 'personal_report')
                        <div class="page-right padding-md">
                          {{ $allMember->appends(['check_time'    => Request::get('check_time','1'),
                                                  'date'          => Request::get('date','this_month'),
                                                  'start_date'    => Request::get('start_date',''),
                                                  'end_date'      => Request::get('end_date',''),
                                                  'reportType'    => Request::get('reportType',''),
                                                  'project'       => Request::get('project',''),
                                                  'department'    => Request::get('department',''),
                                                  'division'      => Request::get('division',''),
                                                  'team'          => Request::get('team',''),
                                                  'status'        => Request::get('status',''),
                                                  'limit'         => Request::get('limit',5) ])->links() }}
                       </div>
                    @elseif(Request::get('reportType') == 'personal_detail_report')

                    @else
                        <div class="text-right">
                        {{ $listProjects->appends(['reportType'    => Request::get('reportType',1),
                                                   'check_time'    => Request::get('check_time',1),
                                                   'date'          => Request::get('date','this_month'),
                                                   'start_date'    => Request::get('start_date',''),
                                                   'end_date'      => Request::get('end_date',''),
                                                   'project'       => Request::get('project',''),
                                                   'department'    => Request::get('department',''),
                                                   'division'      => Request::get('division',''),
                                                   'team'          => Request::get('team',''),
                                                   'status'        => Request::get('status',''),
                                                   'limit'         => Request::get('limit',5) ])->links() }}
                        </div>
                    @endif
                </div><!-- /panel-body -->
                @endif
            </div><!-- /panel-body -->
        </div><!-- /panel-default -->
    </div><!-- /padding-md -->
@stop

@section('modal')

<div id="confirmUpdate" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center"><strong>Confirm Import File</strong></h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <form action="{{ URL::route('project.cost.import.after') }}" method="post">
                    {{ csrf_field() }}
                    <button type="button" class="btn btn-default" id="confirmButton">Confirm</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="listImport" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="overflow-y: initial !important">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center"><strong>Import project</strong></h4>
            </div>
            <div class="modal-body import-modal">
                @if(count($projects) > 0)
                    @if(Request::get('reportType') == 'personal_detail_report')
                        <center><span>Cannot import when search in personal detail report type. <br><br> You must change report type, and search to import!</span></center>
                    @else
                    <div class="panel panel-default">
                        <form class="form-horizontal no-margin form-border" id="formImport" novalidate>
                            <div class="panel-tab">
                                <ul class="wizard-steps wizard-demo" id="wizardDemo1">
                                    <li class="active" id="importStep1">
                                        <a href="#tableListProject" data-toggle="tab">Step 1</a>
                                    </li>
                                    <li class="disabled" id="importStep2">
                                        <a href="#importFileData">Step 2</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="panel-body">
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tableListProject">
                                        <div class="panel-footer clearfix">
                                            <div class="pull-left">
                                                <button type="button" class="btn btn-success btn-sm disabled prevStep" id="prevStep">Previous</button>
                                                <button type="button" class="btn btn-sm btn-success disabled nextStep" id="nextStep">Next</button>
                                            </div>
                                            <div class="pull-right">
                                                <a class="dowload_file_teamlate" href='{{ url('uploads/exportFile/Template_Cost_import.xlsx') }}'>Download File template</a>
                                            </div>
                                        </div>
                                        <table class="table table-striped importTable" id="responsiveTable">
                                            <thead>
                                                <tr>
                                                    <th class="text-center tenPercent">No</th>
                                                    <th>Project name</th>
                                                    <th class="text-center">Project ID</th>
                                                    <th class="tenPercent">
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $numberImport = 0;
                                                    $adminId      = Helpers::getAdminOrDirectorId();
                                                    $userId       = Helpers::getIdOfUserLogin();
                                                ?>
                                                @if(isset($projects))
                                                @foreach($projects as $project)
                                                    @if(in_array($userId, $adminId))
                                                        <tr>
                                                            <td>{{ ++$numberImport }}</td>
                                                            <td class="text-left">{{ $project->name }}</td>
                                                            <td>{{ $project->id }}</td>
                                                            <td class="text-left">
                                                                <label class="label-checkbox">
                                                                    <input type="checkbox" name="checkImport[]" value="{{ $project->id }}" class="chk-row">
                                                                    <span class="custom-checkbox"></span>
                                                                </label>
                                                            </td>
                                                        </tr>
                                                    @elseif($roleUser == "manager")
                                                        <tr>
                                                            <td>{{ ++$numberImport }}</td>
                                                            <td class="text-left">{{ $project->name }}</td>
                                                            <td>{{ $project->id }}</td>
                                                            <td class="text-left">
                                                                <label class="label-checkbox">
                                                                    <input type="checkbox" name="checkImport[]" value="{{ $project->id }}" class="chk-row">
                                                                    <span class="custom-checkbox"></span>
                                                                </label>
                                                            </td>
                                                        </tr>
                                                    @elseif(count($project->permissions) > 0)
                                                        @if(array_key_exists("user.import_cost", json_decode($project->permissions)))
                                                            <tr>
                                                                <td>{{ ++$numberImport }}</td>
                                                                <td class="text-left">{{ $project->name }}</td>
                                                                <td>{{ $project->id }}</td>
                                                                <td class="text-left">
                                                                    <label class="label-checkbox">
                                                                        <input type="checkbox" name="checkImport[]" value="{{ $project->id }}" class="chk-row">
                                                                        <span class="custom-checkbox"></span>
                                                                    </label>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endif
                                                @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="importFileData">
                                        <div class="form-group">
                                            <label class="control-label col-lg-2" for="xlsfile">Select file:</label>
                                            <div class="col-lg-10">
                                                <div class="upload-file">
                                                    <input type="file" name="xlsfile" id="upload-demo" class="upload-demo">
                                                    <label data-title="Select file" for="upload-demo">
                                                        <span data-title="No file selected..."></span>
                                                    </label>
                                                    <input type="hidden" id="fileType" name="fileType" value="cost"/>
                                                    <input type="hidden" name="team" id="team" value="{{Request::get('team','')}}">
                                                </div>
                                            </div><!-- /.col -->
                                        </div><!-- /form-group -->
                                    </div>
                                    <div class="panel-footer clearfix">
                                        <div class="pull-left">
                                            <button type="button" class="btn btn-success btn-sm disabled prevStep" id="prevStep">Previous</button>
                                            <button type="button" class="btn btn-sm btn-success disabled nextStep" id="nextStep">Next</button>
                                            <button type="button" class="btn btn-sm btn-success hidden" id="importFileButton">Import</button>
                                        </div>
                                        <div class="pull-right">
                                            <a class="dowload_file_teamlate" href='{{ url('uploads/exportFile/Template_Cost_import.xlsx') }}'>Download File template</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div><!-- /panel -->
                    @endif
                @else
                    <div class="text-center"><br><p>Sorry! You don't have role to import file into any project!</p></div>
                @endif
            </div>
        </div>
    </div>
</div>
<div id="export" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center"><strong>Export cost</strong></h4>
            </div>
            <div class="modal-body">
                @if(count($projects) > 0)
                <div class="panel panel-default">
                    <form class="form-horizontal no-margin form-border" action="{{Route('project.cost.export.cost')}}" method="post" id="formExport" novalidate>
                        {{ csrf_field() }}
                        <div class="panel-footer clearfix">
                            <input type="hidden" name="check_time" id="check_time" value="{{Request::get('check_time','')}}">
                            <input type="hidden" name="date"       id="date"       value="{{Request::get('date','')}}">
                            <input type="hidden" name="start_date" id="start_date" value="{{Request::get('start_date','')}}">
                            <input type="hidden" name="end_date"   id="end_date"   value="{{Request::get('end_date','')}}">
                            <input type="hidden" name="month"      id="month"      value="{{Request::get('month','')}}">
                            <input type="hidden" name="year"       id="year"       value="{{Request::get('year','')}}">
                            <input type="hidden" name="reportType" id="reportType" value="{{Request::get('reportType','')}}">
                            <input type="hidden" name="project"    id="project"    value="{{Request::get('project','')}}">
                            <input type="hidden" name="department" id="department" value="{{Request::get('department','')}}">
                            <input type="hidden" name="division"   id="division"   value="{{Request::get('division','')}}">
                            <input type="hidden" name="team"       id="team"       value="{{Request::get('team','')}}">
                            <input type="hidden" name="limit"      id="limit"      value="{{Request::get('limit','10')}}">
                            <input type="hidden" name="page"      id="page"      value="{{Request::get('page','1')}}">
                            <center>
                                <button type="submit" class="btn btn-success btn-sm" id="exportFile">Export</button>
                            </center>
                            <center>
                                <a class="dowload_file_teamlate" href='{{ url('uploads/exportFile/Reformat_Cost_and_Bug_Report_with_Co-well_Report_template.xlsm') }}'>Download File template</a>
                            </center>
                        </div>
                    </form>
                </div><!-- /panel -->
                @else
                    <div class="text-center"><br><p>Sorry! You don't have role to export file of any project!</p></div>
                @endif
            </div>
        </div>
    </div>
</div>
<div id="exportFileAfterImport" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center"><strong>Confirm Export File</strong></h4>
            </div>
            <div class="modal-body">
                <p>Upload successful</p>
                <p>Do you want download this file with ticket ID for next time?</p>
                <p>* Note: File will delete when popup close.</p>
            </div>
            <div class="modal-footer">
                <form action="{{ URL::route('project.cost.export.after.import') }}" method="post">
                    {{ csrf_field() }}
                    <input hidden="hidden" id="ticketID" name="ticketID" value="">
                    <button type="submit" class="btn btn-default" id="confirmExport">Export</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('script')
    <script type="text/javascript" src="{{ asset('js/select_date/select.date.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/common/reset_form.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/common/highcharts.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/common/cost_chart.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/import/import.cost.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/cost/cost.project.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/common/ajax_company_struct.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/jquery.inputmask.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/jquery.validate.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/common/validate_date.js') }}"></script>
    <script>
        $(function(){
            var slideDHeight=$(window).width() / 3;
            $('.import-modal').css('height',slideDHeight);
            $(window).resize(function(){
                var slideDHeight=$(window).width() / 3;
                $('.import-modal').css('height',slideDHeight);
            });
        });
    </script>
@stop