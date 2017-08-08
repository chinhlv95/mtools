
@extends('layouts.master')
@section('title','Personal cost')
@section('breadcrumbs','Personal cost')
@section('style')
    <link href="{{ asset('css/custom/date-form.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom/cost.css') }}" rel="stylesheet">
@stop
@section('content')
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
            <span id="message"></span>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading" id="form_heading">Personal Cost</div>
            <div class="panel-body" id="form_body">
                <form method="get" action="{{ URL::route('personal.cost.index') }}" id="search_form" class="form-horizontal" enctype="multipart/form-data">
                    <div class="info-left col-md-6">
                        <div class="form-group">
                            <label class="col-md-4 control-label text-left" for="reportType">Report Type</label>
                            <div class="col-md-6">
                                <select class="form-control" name="reportType">
                                    <option value=""> -- Select Report Type -- </option>
                                    @if (Request::get('reportType', '') == 1)
                                        <option value="1" selected="selected"> Daily Report </option>
                                    @else
                                        <option value="1"> Daily Report </option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
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
                        <div class="form-group">
                                <label class="col-md-4 control-label">
                                    <span class="col-sm-12">
                                        <input type="radio" id="choose_time" class="col-md-4" name="check_time" value="2" <?php if(Request::get('check_time') == 2) echo "checked='checked'";?>>
                                        Choose start, end
                                    </span>
                                </label>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <div class="input-group col-md-11" id="dateForm">
                                            <input class="form-control" id="start_date" data-inputmask="'alias': 'date'" value="{{Request::get('start_date','') ? Request::get('start_date','') : $firstDateDefault}}" name="start_date" type="text" <?php if(Request::get('check_time','1') == 1) echo "disabled='disabled'";?> onpaste="return false;">
                                            <span class="input-group-addon open-startdate"><i class="fa fa-calendar open-startdate"></i></span>
                                        </div>
                                        <div class="col-md-8 col-md-offset-3">
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <div class="input-group col-md-11" id="dateForm">
                                            <input class="form-control" id="end_date" data-inputmask="'alias': 'date'" value="{{Request::get('end_date','') ? Request::get('end_date','') : $endDateDefault}}" name="end_date" type="text" <?php if(Request::get('check_time','1') == 1) echo "disabled='disabled'";?> onpaste="return false;">
                                            <span class="input-group-addon open-enddate"><i class="fa fa-calendar open-enddate"></i></span>
                                        </div>
                                        <div class="col-md-8 col-md-offset-3"></div>
                                    </div>
                                </div>
                        </div>
                    </div>
                    <div class="info-right col-md-6">
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="department">Department</label>
                            <div class="col-md-6">
                                <select class="form-control" name="department" id="department_id">
                                    <option value="-1"> -- Select Department --</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="division">Division</label>
                            <div class="col-md-6">
                                <select class="form-control" name="division" id="division_id">
                                    <option value="-1"> -- Select Division --</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="team">Team</label>
                            <div class="col-md-6">
                                <select class="form-control" name="team" id="team_id">
                                    <option value="-1"> -- Select Team --</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="project">Project</label>
                            <div class="col-md-6">
                                <select class="form-control" name="project" id="project_id">
                                    <option value="-1"> -- Select Project --</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary">Search</button>
                        <button type="button" class="btn btn-danger" id="configreset">Reset</button>
                    </div>
                </form>
            </div><!-- /panel-body -->
            <hr/>
            <div class="panel-body">
                <div class="panel-body">
                    <div class="col-md-6">
                        @if(count($allMember) == 0)
                            <span class="text-left"><strong>Total number of records: No Result</strong></span>
                        @else
                            <span class="text-left"><strong>Total number of records: {{ count($allMember) }}</strong></span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <form method="get" class="pull-right">
                            <input type="hidden" name="check_time" value="{{Request::get('check_time','1')}}">
                            <input type="hidden" name="date"       value="{{Request::get('date','this_month')}}">
                            <input type="hidden" name="start_date" value="{{Request::get('start_date','')}}">
                            <input type="hidden" name="end_date"   value="{{Request::get('end_date','')}}">
                            <input type="hidden" name="reportType" value="{{Request::get('reportType','')}}">
                            <input type="hidden" name="project"    value="{{Request::get('project','')}}">
                            <input type="hidden" name="department" value="{{Request::get('department','')}}">
                            <input type="hidden" name="division"   value="{{Request::get('division','')}}">
                            <input type="hidden" name="team"       value="{{Request::get('team','')}}">
                            <label for="choose_item">Item display on page: &nbsp; &nbsp;</label>
                            <select id="choose_item" name="limit" class="form-control input-md inline-block" size="1" onchange="this.form.submit()">
                                @if(!empty($paginate))
                                    @foreach($paginate as $key => $values)
                                        @if(Request::get('limit', 10) == $values)
                                            <option value="{{$key}}" selected>{{$values}}</option>
                                        @else
                                            <option value="{{$key}}">{{$values}}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </form>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive" id="scroll-x">
                        <table class="table table-bordered table-hover table-striped" id="responsiveTable">
                            <thead>
                              <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Member ID</th>
                                <th class="text-center">Role</th>
                                <th class="text-center">Location</th>
                                <th class="text-center">Work time/day</th>
                                <th class="text-center">Standard time</th>
                                <th class="text-center">Min</th>
                                <th class="text-center">Max</th>
                                <th class="text-center">Actual time</th>
                                <th class="text-center">Under time</th>
                                <th class="text-center">Over time</th>
                                <th class="text-center">Joined project</th>
                                <th class="text-center">Joined project</th>
                                <th class="text-center">Joined project</th>
                                <th class="text-center">Joined project</th>
                                <th class="text-center">Joined project</th>
                                <th class="text-center">Joined project</th>
                                <th class="text-center">Joined project</th>
                                <th class="text-center">Joined project</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($allMember as $member)
                                    <tr>
                                        <td>{{ ++$number }}</td>
                                        <td  class="text-left">
                                            <?php $explodeEmail = explode('@',$member->email); ?>
                                            {{ $explodeEmail[0] }}
                                        </td>
                                        <td class="text-left pj_name">{{ $member->position }}</td>
                                        <td>VN</td>
                                        <td>
                                            <?php $workTime = 0; ?>
                                            @foreach($memberInProject as $memberData)
                                                @if($memberData->email == $member->email)
                                                    <?php $workTime += 8*($memberData->assign); ?>
                                                @endif
                                            @endforeach
                                            {{ $workTime }}
                                        </td>
                                        <?php
                                            $standardTime = $workTime*21;
                                            $minTime      = $standardTime*0.9;
                                            $maxTime      = $standardTime*1.1;
                                        ?>
                                        <td>{{ $standardTime }}</td>
                                        <td>{{ $minTime }}</td>
                                        <td>{{ $maxTime }}</td>
                                        <td class="total_effort">
                                            <?php
                                                $personalEntry = 0;
                                                foreach($allEntry as $entry){
                                                    if($entry->assign_to_email == $member->email){
                                                        $personalEntry += $entry->actual_hour;
                                                    }
                                                }
                                            ?>
                                            {{ $personalEntry }}
                                        </td>
                                        <?php
                                            $underTime = $standardTime - $personalEntry;
                                            $overTime  = $personalEntry - $maxTime;
                                        ?>
                                        <td>
                                            @if($underTime >= 0)
                                                {{ $underTime }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($overTime >= 0)
                                                {{ $overTime }}
                                            @endif
                                        </td>
                                        <?php $count = 0; ?>
                                        @foreach($memberInProject as $memberData)
                                            @if($memberData->email == $member->email)
                                                <?php $count++; ?>
                                                <td  class="text-left">{{ $memberData->project_name }}</td>
                                            @endif
                                        @endforeach
                                        @for($i=1;$i<(9-$count);$i++)
                                            <td></td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div><!-- /panel -->
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
                                                  'limit'         => Request::get('limit',10) ])->links() }}
                      </div>
                </div><!-- /panel-body -->
            </div><!-- /panel-body -->
        </div><!-- /panel-default -->
    </div><!-- /padding-md -->
@stop

@section('script')
    <script type="text/javascript" src="{{ asset('js/select_date/select.date.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/common/reset_form.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/common/highcharts.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/common/cost_chart.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/cost/cost.project.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/common/ajax_company_struct.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/jquery.inputmask.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/jquery.validate.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/common/validate_date.js') }}"></script>
@stop
