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
    <div class="panel-heading" id="form_heading">Report Productivity And Quality Project Member</div>
    <!-- common area -->
    <div class="panel-body">
        <!-- search area -->
        <form action="{{ URL::route('quality-report.project.member') }}" method="get" class="form-horizontal">
            <div class="info-left col-md-6">
                <div class="form-group">
                    <label class="col-md-4 control-label text-left" for="date">Choose from list</label>
                    <div class="col-md-6">
                        <select class="form-control" name="date" id="select_defalt_time">
                            @foreach($select_date as $key => $value)
                            <option value="{{$key}}" <?php if(Request::get('date')==$key) echo "selected";?>>{{$value}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                        <label class="col-md-4 control-label text-left" for="reportType">Units of time</label>
                        <div class="col-md-6">
                            <select class="form-control" name="reportType">
                                @foreach($reportType as $key => $value)
                                    <option value="{{$key}}" <?php if(Request::get('reportType') == $key) echo "selected";?>>{{$value}}</option>
                                @endforeach
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
                                    <div class="table-dev">
                                        <table class="table table-bordered table-hover table-striped developerData" id="devResponsiveTable">
                                            <caption><h5>DEVELOPERS ({{date("d/m/Y",strtotime($start_date))}} - {{date("d/m/Y",strtotime($end_date))}})</h5></caption>
                                            @if($timeName != null)
                                            <thead>
                                                <tr>
                                                    <th class="info_data">No</th>
                                                    <th class="info_data"></th>
                                                    <th class="info_data"></th>
                                                    @foreach($timeName as $tName)
                                                    <th colspan="9" class="info_data">{{$tName->time_name}}</th>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td rowspan="2" ></td>
                                                    <td rowspan="2">MEMBER (Dev)</td>
                                                    <td rowspan="2">Projects</td>
                                                    @for ($i = 1; $i <= count($timeName); $i++)
                                                    <td colspan="4">Common Data</td>
                                                    <td colspan="3">Productivity</td>
                                                    <td colspan="2">Quality</td>
                                                    @endfor
                                                </tr>
                                                <tr>
                                                @for ($i = 1; $i <= count($timeName); $i++)
                                                    <td class="info_data">LOC</td>
                                                    <td class="info_data">WORKLOAD (mm)</td>
                                                    <td class="info_data">BUG (weighted)</td>
                                                    <td class="info_data">TASK</td>
                                                    <td class="productivity">KLOC / MM</td>
                                                    <td class="productivity">BUG / MM</td>
                                                    <td class="productivity">TASK / MM</td>
                                                    <td class="quality">BUG / KLOC</td>
                                                    <td class="quality">BUG / WORKLOAD</td>
                                                @endfor
                                                </tr>
                                            </thead>
                                            <tbody>
                                                 <?php $number = 0; ?>
                                                @foreach($devData as $key => $value)

                                                <?php $countDev = count($value); $flag = 0; ?>
                                                    @foreach($value as $dev)
                                                    <tr>
                                                        @if($flag == 0)
                                                        <td rowspan="{{$countDev}}">{{++$number }}</td>
                                                        <td rowspan="{{$countDev}}">{{$key}}</td>
                                                        <?php $flag = 1; ?>
                                                        @endif
                                                        <td>{{$dev['project_name']}}</td>
                                                        <td>{{$dev['common_data']['loc']}}</td>
                                                        <td>{{$dev['common_data']['workload']}}</td>
                                                        <td>{{$dev['common_data']['bug']}}</td>
                                                        <td>{{$dev['common_data']['task']}}</td>
                                                        <td>{{$dev['quality']['weightedBugPerKloc']}}</td>
                                                        <td>{{$dev['quality']['weightedBugperWl']}}</td>
                                                        <td>{{$dev['productivity']['kLocPerMm']}}</td>
                                                        <td>{{$dev['productivity']['weightedBugPerMm']}}</td>
                                                        <td>{{$dev['productivity']['taskPerMm']}}</td>
                                                    </tr>
                                                    @endforeach

                                                @endforeach

                                            </tbody>
                                            @endif

                                        </table>
                                    </div>
                                    <br>
                                    <br>
                                    <div class="table-qa">
                                        <table class="table table-bordered table-hover table-striped qaTable" id="qaResponsiveTable">
                                        <caption><h5>QUALITY ASSURANCE ({{date("d/m/Y",strtotime($start_date))}} - {{date("d/m/Y",strtotime($end_date))}})</h5></caption>
                                            @if($timeName != null)
                                            <thead>
                                                <tr>
                                                    <th class="info_data">No</th>
                                                    <th class="info_data"></th>
                                                    <th class="info_data"></th>
                                                    @foreach($timeName as $tName)
                                                    <th colspan="5" class="info_data">{{$tName->time_name}}</th>
                                                    @endforeach
                                                </tr>
                                                <tr >
                                                    <td rowspan="2"></td>
                                                    <td rowspan="2">MEMBER (QA)</td>
                                                    <td rowspan="2">Projects</td>
                                                    @for ($i = 1; $i <= count($timeName); $i++)
                                                    <td colspan="3">Common Data</td>
                                                    <td>Productivity</td>
                                                    <td>Quality</td>
                                                    @endfor
                                                </tr>
                                                <tr>
                                                    @for ($i = 1; $i <= count($timeName); $i++)
                                                    <th class="info_data">WORKLOAD for create (mm)</th>
                                                    <th class="info_data">WORKLOAD for test (mm)</th>
                                                    <th class="info_data">BUG (weighted)</th>
                                                    <th class="productivity">BUG / MM</th>
                                                    <th class="quality">BUG / WORKLOAD</th>
                                                    @endfor
                                                </tr>
                                            </thead>
                                             <tbody>
                                               <?php $number1 = 0; ?>
                                                @foreach($qaData as $key1 => $value1)
                                                <?php $countQa = count($value1); $flag1 = 0; ?>
                                                    @foreach($value1 as $qa)
                                                    <tr>
                                                        @if($flag1 == 0)
                                                        <td rowspan="{{$countQa}}">{{++$number1 }}</td>
                                                        <td rowspan="{{$countQa}}">{{$key1}}</td>
                                                        <?php $flag1 = 1; ?>
                                                        @endif
                                                        <td>{{$qa['project_name']}}</td>
                                                        <td>{{$qa['common_data']['tWorkLoad']}}</td>
                                                        <td>{{$qa['common_data']['makeTcWorkLoad']}}</td>
                                                        <td>{{$qa['common_data']['bug']}}</td>
                                                        <td>{{$qa['quality']['weightedBugperWl']}}</td>
                                                        <td>{{$qa['productivity']['weightedBugPerMm']}}</td>
                                                    </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                            @endif
                                        </table>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
<!-- table data -->
            </div>
            <!-- data display area -->

        @endif
    </div>

@stop
@section('script')
    <script type="text/javascript" src="{{ asset('js/select_date/select.date.js') }}"></script>
   <script type="text/javascript" src="{{ asset('/js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/dataTables.bootstrap.min.js')}}"></script>
@stop