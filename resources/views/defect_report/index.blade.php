@extends('layouts.master')
@section('title', 'Defect Report')

@section('breadcrumbs','Defect Report')
@section('style')
    <link href="{{ asset('css/custom/date-form.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom/cost.css') }}" rel="stylesheet">
    <link href="{{ asset('css/defect_report/defect_report.css') }}" rel="stylesheet">
@stop
@section('content')
<div class="ajax-loader" style="display:none">
    <img src="{{ asset('/img/ajax_loader.gif') }}" class="img-responsive" />
</div>
<div class="padding-md">
    <div class="alert alert-danger hide" id="errorMessage">
        <button type="button" class="close closeMessage">
            <i class="fa fa-times"></i>
        </button>
        <span id="message"></span>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" id="form_heading">View Project - Defect Report</div>
            <div class="panel-body" id="form_body">
                <form method="get" id="search_form" class="form-horizontal" enctype="multipart/form-data">
                    <div class="info-left col-md-6">
                        <div class="form-group">
                                <label class="col-md-4 control-label">
                                    Bug:
                                </label>
                                <div class="col-md-6">
                                    <select class="form-control" name="type_bug">
                                        @foreach($bug_type as $key=>$value)
                                            <option value="{{$key}}" <?php if(Request::get('type_bug') == $key) echo 'selected'; ?>>{{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                        </div>
                        <div class="form-group">
                                <label class="col-md-4 control-label">
                                    Report type:
                                </label>
                                <div class="col-md-6">
                                    <select class="form-control" name="report_type" id="report_type">
                                        <option value=""> -- All --</option>
                                        @foreach($report_type as $key=>$value)
                                            <option value="{{$key}}" <?php if(Request::get('report_type') == $key) echo "selected";?>>{{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                        </div>
                        <div class="form-group">
                                <label class="col-md-4 control-label">
                                    <span class="col-sm-12">
                                        <input type="radio" id="default_time" name="check_time" value="1" <?php if(Request::get('check_time') == 1) echo "checked='checked'";?>>
                                        Choose from list:
                                    </span>
                                </label>
                                <div class="col-md-6">
                                    <select class="form-control" name="date" id="select_defalt_time">
                                        @foreach($select_date as $key=>$value)
                                            <option value="{{$key}}" <?php if(Request::get('date')==$key) echo "selected";?>>{{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                        </div>
                        <div class="form-group">
                                <label class="col-md-4 control-label">
                                    <span class="col-sm-12">
                                        <input type="radio" id="choose_time" class="col-md-4" name="check_time" value="2" <?php if(Request::get('check_time') == 2) echo "checked='checked'";?>>
                                        Choose start, end:
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
                                            <input class="form-control" id="end_date" data-inputmask="'alias': 'date'" value="{{Request::get('end_date') ? Request::get('end_date','') : $endDateDefault}}" name="end_date" type="text" <?php if(Request::get('check_time','1') == 1) echo "disabled='disabled'";?> onpaste="return false;">
                                            <span class="input-group-addon open-enddate"><i class="fa fa-calendar open-enddate"></i></span>
                                        </div>
                                        <div style="display: inline-block;">
                                            <label id="end_date-error" class="error" for="end_date"></label>
                                        </div>
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
                            <label class="col-md-4 control-label" for="department">Department:</label>
                            <div class="col-md-6">
                                <select class="form-control" name="department" id="department_id">
                                    <option  value="-1"> -- All --</option>
                                        @foreach($departments as $item)
                                            <option value="{{$item['id']}}" <?php if(Request::get('department') == $item['id']) echo "selected"?>>{{$item['name']}}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="division">Division:</label>
                            <div class="col-md-6">
                                <select class="form-control" name="division" id="division_id">
                                    <option value="-1"> -- All --</option>
                                    @foreach($divisions as $item)
                                        <option value="{{$item['id']}}" <?php if(Request::get('division') == $item['id']) echo "selected"?>>{{$item['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="team">Team:</label>
                            <div class="col-md-6">
                                <select class="form-control" name="team" id="team_id">
                                    <option value="-1"> -- All --</option>
                                    @foreach($teams as $item)
                                        <option value="{{$item['id']}}"<?php if(Request::get('team') == $item['id']) echo "selected"?>>{{$item['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="project">Project: <span class="field-asterisk">*</span></label>
                            <div class="col-md-6">
                                <select class="form-control" name="project" id="project_id">
                                    <option value="-1"> -- All --</option>
                                    @foreach($projects as $item)
                                        <option value="{{$item['id']}}" <?php if(Request::get('project') == $item['id']) echo "selected"?>>{{$item['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="team">Units of time</label>
                            <div class="col-md-6">
                                <select class="form-control" name="units_time" id="units_time" <?php if(Request::get('report_type') == "summary") echo "disabled='disabled'"?>>
                                    @foreach($units_date as $key=>$value)
                                        <option value="{{$key}}" <?php if(Request::get('units_time') == $key) echo "selected";?>>{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 text-center">
                        <button type="submit" id="search" class="btn btn-primary">Search</button>
                        <button type="button" class="btn btn-danger" id="configreset">Reset</button>
                        @if($isAdmin == 1 || (isset($projects[0]->permissions) && array_key_exists("user.import_cost", json_decode($projects[0]->permissions))))
                            <button type="button" class="btn btn-info" data-toggle="modal" id="importBtn" data-target="#listImport" data-backdrop="static" data-keyboard="false">Import</button>
                        @endif
                        @if($isAdmin == 1 || (isset($projects[0]->permissions) && array_key_exists("user.export_cost", json_decode($projects[0]->permissions))))
                            <button type="button" class="btn btn-success export_bug" data-toggle="modal" data-target="#export">Export</button>
                        @endif
                    </div>
                </form>
            </div><!-- /panel-body -->
            <hr>
            @if(Request::get('project'))
                @if(Request::get('type_bug') == 2)
                <div class="panel-body">
                    <div class="col-sm-12">
                        <h4>Weight bug: Low=1, Medium=2, High=3, Serious=5, Fatal=8</h4>
                    </div>
                </div>
                @endif
                @if($requestReportType == 'summary' || empty($requestReportType))
                <div class="panel-body">
                    <div class="col-sm-6">
                        <div class="table-responsive" id="scroll-x">
                            <div class="wordbold panel-heading title_report">
                                By root cause ({{date("d/m/Y",strtotime($start_date))}} - {{date("d/m/Y",strtotime($end_date))}})
                            </div>
                            <table class="table table-bordered table-hover table-striped root_cause" id="responsiveTable">
                                <thead>
                                    <tr>
                                        <th width="15%" >Root cause</th>
                                        @foreach($desToolTips as $key=>$value)
                                            <th  width="15%"><a class="title" data-toggle="tooltip" title="{{$value}}">{{$key}}</a></th>
                                        @endforeach
                                        <th width="10%">Grand Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $totalCol1 = 0;
                                        $totalCol2 = 0;
                                        $totalCol3 = 0;
                                        $totalCol4 = 0;
                                        $totalCol5 = 0;
                                    ?>
                                    @foreach($name_root_cause as $key=>$value)
                                        <?php
                                            $low= 0;
                                            $medium = 0;
                                            $high = 0;
                                            $serious = 0;
                                            $fatal = 0;
                                        ?>
                                        @foreach($tickets_status as $ticket)
                                            @if($ticket['bug_weight_related'] == 1 && $ticket['root_cause_related'] == $value)
                                                <?php
                                                    ++$low;
                                                ?>
                                            @endif
                                            @if($ticket['bug_weight_related'] == 2 && $ticket['root_cause_related'] == $value)
                                                <?php ++$medium ?>
                                            @endif
                                            @if($ticket['bug_weight_related'] == 3 && $ticket['root_cause_related'] == $value)
                                                <?php ++$high ?>
                                            @endif
                                            @if($ticket['bug_weight_related'] == 4 && $ticket['root_cause_related'] == $value)
                                                <?php ++$serious ?>
                                            @endif
                                            @if($ticket['bug_weight_related'] == 5 && $ticket['root_cause_related'] == $value)
                                                <?php ++$fatal ?>
                                            @endif
                                        @endforeach
                                    <tr class="data">
                                        <td class="name">{{ App\Models\RootCause::find($value)->name }}</td>
                                        @if(Request::get('type_bug') == 2)
                                            <td >{{$low = $low * $bug_weight[1]}}</td>
                                            <td >{{$medium =$medium * $bug_weight[2]}}</td>
                                            <td >{{$high =$high * $bug_weight[3]}}</td>
                                            <td >{{$serious =$serious * $bug_weight[4]}}</td>
                                            <td >{{$fatal =$fatal * $bug_weight[5]}}</td>
                                            <td >{{$low +  $medium + $high + $serious+ $fatal}}</td>
                                        @else
                                            <td >{{$low}}</td>
                                            <td>{{$medium}}</td>
                                            <td >{{$high}}</td>
                                            <td >{{$serious}}</td>
                                            <td >{{$fatal}}</td>
                                            <td >{{$low +  $medium + $high + $serious+ $fatal}}</td>
                                        @endif
                                    </tr>
                                        <?php
                                            $totalCol1 += $low;
                                            $totalCol2 += $medium;
                                            $totalCol3 += $high;
                                            $totalCol4 += $serious;
                                            $totalCol5 += $fatal;
                                        ?>
                                    @endforeach
                                    <tr>
                                        <td>Grand Total</td>
                                        <td>{{$totalCol1}}</td>
                                        <td>{{$totalCol2}}</td>
                                        <td>{{$totalCol3}}</td>
                                        <td>{{$totalCol4}}</td>
                                        <td>{{$totalCol5}}</td>
                                        <td>{{$totalCol1 + $totalCol2 + $totalCol3 + $totalCol4 +$totalCol5}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div id="graph_wrap">
                            <div id="col_root_casue"></div>
                        </div>
                    </div>
                </div><!-- /panel-body -->
                <div class="panel-body">
                    <div class="col-sm-6">
                        <div class="table-responsive" id="scroll-x">
                            <div class="wordbold panel-heading title_report">
                                By bug status({{date("d/m/Y",strtotime($start_date))}} - {{date("d/m/Y",strtotime($end_date))}})
                            </div>
                            <table class="table table-bordered table-hover table-striped by_status" id="responsiveTable">
                                <thead>
                                    <tr>
                                        <th width="15%" >Status</th>
                                        @foreach($desToolTips as $key=>$value)
                                            <th class="title" width="15%"><a data-toggle="tooltip" title="{{$value}}">{{$key}}</a></th>
                                        @endforeach
                                        <th width="10%">Grand Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $totalCol1 = 0;
                                        $totalCol2 = 0;
                                        $totalCol3 = 0;
                                        $totalCol4 = 0;
                                        $totalCol5 = 0;
                                    ?>
                                    @foreach($tickets_status_name as $key=>$value)
                                        <?php
                                            $low= 0;
                                            $medium = 0;
                                            $high = 0;
                                            $serious = 0;
                                            $fatal = 0;
                                        ?>
                                        @foreach($tickets_status as $ticket)
                                            @if($ticket['bug_weight_related'] == 1 && $ticket['status_related'] == $value)
                                                <?php
                                                    ++$low;
                                                ?>
                                            @endif
                                            @if($ticket['bug_weight_related'] == 2 && $ticket['status_related'] == $value)
                                                <?php ++$medium ?>
                                            @endif
                                            @if($ticket['bug_weight_related'] == 3 && $ticket['status_related'] == $value)
                                                <?php ++$high ?>
                                            @endif
                                            @if($ticket['bug_weight_related'] == 4 && $ticket['status_related'] == $value)
                                                <?php ++$serious ?>
                                            @endif
                                            @if($ticket['bug_weight_related'] == 5 && $ticket['status_related'] == $value)
                                                <?php ++$fatal ?>
                                            @endif
                                        @endforeach
                                    <tr class="data">
                                        <td class="name">{{ App\Models\Status::find($value)->name }}</td>
                                        @if(Request::get('type_bug') == 2)
                                            <td>{{$low = $low * $bug_weight[1]}}</td>
                                            <td>{{$medium =$medium * $bug_weight[2]}}</td>
                                            <td>{{$high =$high * $bug_weight[3]}}</td>
                                            <td>{{$serious =$serious * $bug_weight[4]}}</td>
                                            <td>{{$fatal =$fatal * $bug_weight[5]}}</td>
                                        @else
                                            <td>{{$low}}</td>
                                            <td>{{$medium}}</td>
                                            <td>{{$high}}</td>
                                            <td>{{$serious}}</td>
                                            <td>{{$fatal}}</td>
                                        @endif
                                        <td>{{$low +  $medium + $high + $serious+ $fatal}}</td>
                                    </tr>
                                        <?php
                                            $totalCol1 += $low;
                                            $totalCol2 += $medium;
                                            $totalCol3 += $high;
                                            $totalCol4 += $serious;
                                            $totalCol5 += $fatal;
                                        ?>
                                    @endforeach
                                    <tr>
                                        <td>Grand Total</td>
                                        <td>{{ $totalCol1 }}</td>
                                        <td>{{ $totalCol2 }}</td>
                                        <td>{{ $totalCol3 }}</td>
                                        <td>{{ $totalCol4 }}</td>
                                        <td>{{ $totalCol5 }}</td>
                                        <td>{{ $totalCol1 + $totalCol2 + $totalCol3 + $totalCol4 +$totalCol5 }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div id="graph_wrap">
                            <div id="collumn_chart"></div>
                        </div>
                    </div>
                </div><!-- /panel-body -->


                <div class="panel-body">
                    <div class="col-sm-6">
                        <div class="table-responsive" id="scroll-x">
                            <div class="wordbold panel-heading title_report">
                                By who found defect ({{date("d/m/Y",strtotime($start_date))}} - {{date("d/m/Y",strtotime($end_date))}})
                            </div>
                            <table class="table table-bordered table-hover table-striped who_found" id="responsiveTable">
                                <thead>
                                    <tr>
                                        <th width="15%" >Member</th>
                                        @foreach($desToolTips as $key=>$value)
                                            <th  width="15%"><a class="title" data-toggle="tooltip" title="{{$value}}">{{$key}}</a></th>
                                        @endforeach
                                        <th  width="10%">Grand Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $totalCol1 = 0;
                                        $totalCol2 = 0;
                                        $totalCol3 = 0;
                                        $totalCol4 = 0;
                                        $totalCol5 = 0;
                                    ?>
                                    @foreach($name_found_bug as $key=>$value)
                                        <?php
                                            $low= 0;
                                            $medium = 0;
                                            $high = 0;
                                            $serious = 0;
                                            $fatal = 0;
                                        ?>
                                        @foreach($ticketsFoundBug as $ticket)
                                            @if($ticket['bug_weight_related'] == 1 && $ticket['users_related_id'] == $value)
                                                <?php
                                                    ++$low;
                                                ?>
                                            @endif
                                            @if($ticket['bug_weight_related'] == 2 && $ticket['users_related_id'] == $value)
                                                <?php ++$medium ?>
                                            @endif
                                            @if($ticket['bug_weight_related'] == 3 && $ticket['users_related_id'] == $value)
                                                <?php ++$high ?>
                                            @endif
                                            @if($ticket['bug_weight_related'] == 4 && $ticket['users_related_id'] == $value)
                                                <?php ++$serious ?>
                                            @endif
                                            @if($ticket['bug_weight_related'] == 5 && $ticket['users_related_id'] == $value)
                                                <?php ++$fatal ?>
                                            @endif
                                        @endforeach
                                    <tr class="data">
                                        <td class="name">{{ App\Models\User::find($value)->last_name . ' '. App\Models\User::find($value)->first_name }}</td>
                                        @if(Request::get('type_bug') == 2)
                                            <td >{{$low = $low * $bug_weight[1]}}</td>
                                            <td >{{$medium =$medium * $bug_weight[2]}}</td>
                                            <td >{{$high =$high * $bug_weight[3]}}</td>
                                            <td >{{$serious =$serious * $bug_weight[4]}}</td>
                                            <td >{{$fatal =$fatal * $bug_weight[5]}}</td>
                                        @else
                                            <td >{{$low}}</td>
                                            <td>{{$medium}}</td>
                                            <td >{{$high}}</td>
                                            <td >{{$serious}}</td>
                                            <td >{{$fatal}}</td>
                                        @endif
                                        <td >{{$low +  $medium + $high + $serious+ $fatal}}</td>
                                    </tr>
                                        <?php
                                            $totalCol1 += $low;
                                            $totalCol2 += $medium;
                                            $totalCol3 += $high;
                                            $totalCol4 += $serious;
                                            $totalCol5 += $fatal;
                                        ?>
                                    @endforeach
                                    <tr>
                                        <td>Grand Total</td>
                                        <td>{{$totalCol1}}</td>
                                        <td>{{$totalCol2}}</td>
                                        <td>{{$totalCol3}}</td>
                                        <td>{{$totalCol4}}</td>
                                        <td>{{$totalCol5}}</td>
                                        <td>{{$totalCol1 + $totalCol2 + $totalCol3 + $totalCol4 +$totalCol5}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div id="graph_wrap">
                            <div id="col_chart_found"></div>
                        </div>
                    </div>
                </div><!-- /panel-body -->

                <div class="panel-body">
                    <div class="col-sm-6">
                        <div class="table-responsive" id="scroll-x">
                            <div class="wordbold panel-heading title_report">
                                By who fix defect ({{date("d/m/Y",strtotime($start_date))}} - {{date("d/m/Y",strtotime($end_date))}})
                            </div>
                            <table class="table table-bordered table-hover table-striped who_fix" id="responsiveTable">
                                <thead>
                                    <tr>
                                        <th width="15%" >Member</th>
                                        @foreach($desToolTips as $key=>$value)
                                            <th class="title" width="15%"><a data-toggle="tooltip" title="{{$value}}">{{$key}}</a></th>
                                        @endforeach
                                        <th width="10%">Grand Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $totalCol1 = 0;
                                        $totalCol2 = 0;
                                        $totalCol3 = 0;
                                        $totalCol4 = 0;
                                        $totalCol5 = 0;
                                    ?>
                                    @foreach($name_fix_bug as $key=>$value)
                                        <?php
                                            $low= 0;
                                            $medium = 0;
                                            $high = 0;
                                            $serious = 0;
                                            $fatal = 0;
                                        ?>
                                        @foreach($ticketsFixBug as $ticket)
                                            @if($ticket['bug_weight_related'] == 1 && $ticket['users_related_id'] == $value)
                                                <?php
                                                    ++$low;
                                                ?>
                                            @endif
                                            @if($ticket['bug_weight_related'] == 2 && $ticket['users_related_id'] == $value)
                                                <?php ++$medium ?>
                                            @endif
                                            @if($ticket['bug_weight_related'] == 3 && $ticket['users_related_id'] == $value)
                                                <?php ++$high ?>
                                            @endif
                                            @if($ticket['bug_weight_related'] == 4 && $ticket['users_related_id'] == $value)
                                                <?php ++$serious ?>
                                            @endif
                                            @if($ticket['bug_weight_related'] == 5 && $ticket['users_related_id'] == $value)
                                                <?php ++$fatal ?>
                                            @endif
                                        @endforeach
                                    <tr class="data">
                                        <td class="name">{{ App\Models\User::find($value)->last_name . ' '. App\Models\User::find($value)->first_name }}</td>
                                        @if(Request::get('type_bug') == 2)
                                            <td >{{$low = $low * $bug_weight[1]}}</td>
                                            <td >{{$medium =$medium * $bug_weight[2]}}</td>
                                            <td >{{$high =$high * $bug_weight[3]}}</td>
                                            <td >{{$serious =$serious * $bug_weight[4]}}</td>
                                            <td >{{$fatal =$fatal * $bug_weight[5]}}</td>
                                        @else
                                            <td >{{$low}}</td>
                                            <td>{{$medium}}</td>
                                            <td >{{$high}}</td>
                                            <td >{{$serious}}</td>
                                            <td >{{$fatal}}</td>
                                        @endif
                                        <td >{{$low +  $medium + $high + $serious+ $fatal}}</td>
                                    </tr>
                                        <?php
                                            $totalCol1 += $low;
                                            $totalCol2 += $medium;
                                            $totalCol3 += $high;
                                            $totalCol4 += $serious;
                                            $totalCol5 += $fatal;
                                        ?>
                                    @endforeach
                                    <tr>
                                        <td>Grand Total</td>
                                        <td>{{$totalCol1}}</td>
                                        <td>{{$totalCol2}}</td>
                                        <td>{{$totalCol3}}</td>
                                        <td>{{$totalCol4}}</td>
                                        <td>{{$totalCol5}}</td>
                                        <td>{{$totalCol1 + $totalCol2 + $totalCol3 + $totalCol4 +$totalCol5}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div id="graph_wrap">
                            <div id="col_chart_fix"></div>
                        </div>
                    </div>
                </div><!-- /panel-body -->
                <div class="panel-body">
                    <div class="col-sm-6">
                        <div class="table-responsive" id="scroll-x">
                            <div class="wordbold panel-heading title_report">
                                By who made defect ({{date("d/m/Y",strtotime($start_date))}} - {{date("d/m/Y",strtotime($end_date))}})
                            </div>
                            <table class="table table-bordered table-hover table-striped who_make" id="responsiveTable">
                                <thead>
                                    <tr>
                                        <th width="15%" >Member</th>
                                        @foreach($desToolTips as $key=>$value)
                                            <th class="title" width="15%"><a data-toggle="tooltip" title="{{$value}}">{{$key}}</a></th>
                                        @endforeach
                                        <th width="10%">Grand Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $totalCol1 = 0;
                                        $totalCol2 = 0;
                                        $totalCol3 = 0;
                                        $totalCol4 = 0;
                                        $totalCol5 = 0;
                                    ?>
                                    @foreach($name_make_bug as $key=>$value)
                                        <?php
                                            $low= 0;
                                            $medium = 0;
                                            $high = 0;
                                            $serious = 0;
                                            $fatal = 0;
                                        ?>
                                        @foreach($ticketsMakeBug as $ticket)
                                            @if($ticket['bug_weight_related'] == 1 && $ticket['users_related_id'] == $value)
                                                <?php
                                                    ++$low;
                                                ?>
                                            @endif
                                            @if($ticket['bug_weight_related'] == 2 && $ticket['users_related_id'] == $value)
                                                <?php ++$medium ?>
                                            @endif
                                            @if($ticket['bug_weight_related'] == 3 && $ticket['users_related_id'] == $value)
                                                <?php ++$high ?>
                                            @endif
                                            @if($ticket['bug_weight_related'] == 4 && $ticket['users_related_id'] == $value)
                                                <?php ++$serious ?>
                                            @endif
                                            @if($ticket['bug_weight_related'] == 5 && $ticket['users_related_id'] == $value)
                                                <?php ++$fatal ?>
                                            @endif
                                        @endforeach
                                    <tr class="data">
                                        <td class="name">{{ App\Models\User::find($value)->last_name . ' '. App\Models\User::find($value)->first_name }}</td>
                                        @if(Request::get('type_bug') == 2)
                                            <td >{{$low = $low * $bug_weight[1]}}</td>
                                            <td >{{$medium =$medium * $bug_weight[2]}}</td>
                                            <td >{{$high =$high * $bug_weight[3]}}</td>
                                            <td >{{$serious =$serious * $bug_weight[4]}}</td>
                                            <td >{{$fatal =$fatal * $bug_weight[5]}}</td>
                                        @else
                                            <td >{{$low}}</td>
                                            <td>{{$medium}}</td>
                                            <td >{{$high}}</td>
                                            <td >{{$serious}}</td>
                                            <td >{{$fatal}}</td>
                                        @endif
                                        <td >{{$low +  $medium + $high + $serious+ $fatal}}</td>
                                    </tr>
                                        <?php
                                            $totalCol1 += $low;
                                            $totalCol2 += $medium;
                                            $totalCol3 += $high;
                                            $totalCol4 += $serious;
                                            $totalCol5 += $fatal;
                                        ?>
                                    @endforeach
                                    <tr>
                                        <td>Grand Total</td>
                                        <td>{{$totalCol1}}</td>
                                        <td>{{$totalCol2}}</td>
                                        <td>{{$totalCol3}}</td>
                                        <td>{{$totalCol4}}</td>
                                        <td>{{$totalCol5}}</td>
                                        <td>{{$totalCol1 + $totalCol2 + $totalCol3 + $totalCol4 +$totalCol5}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div id="graph_wrap">
                            <div id="col_chart_make"></div>
                        </div>
                    </div>
                </div><!-- /panel-body -->
                @endif
                @if($requestReportType == 'time' || empty($requestReportType))
                <div class="panel-body">
                    <div class="table-responsive" id="scroll-x">
                        <div class="wordbold panel-heading title_report">
                            By bug and bug after release({{date("d/m/Y",strtotime($start_date))}} - {{date("d/m/Y",strtotime($end_date))}})
                        </div>
                        <table class="table table-bordered table-hover table-striped" id="responsiveTable">
                            <thead>
                                <tr>
                                    <th class="text-center">Bug type</th>
                                    <th >Total bug</th>
                                    @if(Request::get('units_time') == 'day' || empty(Request::get('units_time','')))
                                        @for ($i = strtotime($start_date); $i <= strtotime($end_date); $i = strtotime("+1 day", $i))
                                            <?php $weekend =  date("w", $i); ?>
                                            @if($weekend == 6 || $weekend == 0)
                                                <th class="text-center weekend uat_bug">{{ date("d/m", $i) }}</th>
                                            @else
                                                <th class="text-center uat_bug">{{ date("d/m", $i) }}</th>
                                            @endif
                                        @endfor
                                    @elseif(Request::get('units_time') == 'week')
                                        <?php $period = Helpers::findWeekInPeriodOfTime($start_date, $end_date); ?>
                                        @for ($i = strtotime($period->start->format('Y-m-d H:i:s')); $i <= strtotime($period->end->format('Y-m-d H:i:s')); $i = strtotime("+7 day", $i))
                                                <th class="uat_bug">W {{ date("W/Y", $i) }}</th>
                                        @endfor
                                    @elseif(Request::get('units_time') == 'month')
                                        @for ($i = strtotime($start_date); $i <= strtotime($end_date); $i = strtotime("+1 month", $i))
                                                <th class="uat_bug">{{ date("M/Y", $i) }}</th>
                                        @endfor
                                    @elseif(Request::get('units_time') == 'year')
                                        @for ($i = strtotime($start_date); $i <= strtotime($end_date); $i = strtotime("+1 year", $i))
                                                <th class="uat_bug">{{ date("Y", $i) }}</th>
                                        @endfor
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="name_bug">
                                    <td class="name">Bug</td>
                                    <td>{{array_sum($array_bug)}}</td>
                                    <?php  $total_bug = 0;?>
                                    @foreach($array_bug as $key=>$value)
                                        <td class="data">{{$total_bug += $value}}</td>
                                    @endforeach
                                </tr>
                                <tr class="name_bug">
                                    <td class="name">UAT bug</td>
                                    <td>{{array_sum($array_uat)}}</td>
                                    <?php  $total_uat = 0;?>
                                    @foreach($array_uat as $key=>$value)
                                        <td class="data">{{$total_uat += $value}}</td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="graph_wrap">
                        <div id="line_chart_uat"></div>
                    </div>
                </div><!-- /panel-body -->
                 <div class="panel-body">
                    <div class="table-responsive" id="scroll-x">
                        <div class="wordbold panel-heading title_report">
                            By KPI bug({{date("d/m/Y",strtotime($start_date))}} - {{date("d/m/Y",strtotime($end_date))}})
                        </div>
                        <table class="table table-bordered table-hover table-striped" id="responsiveTable">
                            <thead>
                                <tr>
                                    <th class="text-center"></th>
                                    @if(Request::get('units_time') == 'day' || empty(Request::get('units_time','')))
                                        @for ($i = strtotime($start_date); $i <= strtotime($end_date); $i = strtotime("+1 day", $i))
                                            <?php $weekend =  date("w", $i); ?>
                                            @if($weekend == 6 || $weekend == 0)
                                                <th class="text-center weekend found_close_bug">{{ date("d/m", $i) }}</th>
                                            @else
                                                <th class="text-center found_close_bug">{{ date("d/m", $i) }}</th>
                                            @endif
                                        @endfor
                                    @elseif(Request::get('units_time') == 'week')
                                        <?php $period = Helpers::findWeekInPeriodOfTime($start_date, $end_date); ?>
                                        @for ($i = strtotime($period->start->format('Y-m-d H:i:s')); $i <= strtotime($period->end->format('Y-m-d H:i:s')); $i = strtotime("+7 day", $i))
                                                <th class="uat_bug">W {{ date("W/Y", $i) }}</th>
                                        @endfor
                                    @elseif(Request::get('units_time') == 'month')
                                        @for ($i = strtotime($start_date); $i <= strtotime($end_date); $i = strtotime("+1 month", $i))
                                                <th class="found_close_bug">{{ date("M/Y", $i) }}</th>
                                        @endfor
                                    @elseif(Request::get('units_time') == 'year')
                                        @for ($i = strtotime($start_date); $i <= strtotime($end_date); $i = strtotime("+1 year", $i))
                                                <th class="found_close_bug">{{ date("Y", $i) }}</th>
                                        @endfor
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="name_found_close">
                                    <td class="name">New</td>
                                    @foreach($array_found as $key=>$value)
                                        <td class="data">{{$value}}</td>
                                    @endforeach
                                </tr>
                                <tr class="name_found_close">
                                    <td class="name">Close and reject</td>
                                    <?php $total_close = 0;?>
                                    @foreach($array_close as $key=>$value)
                                        <td class="data">{{$total_close += $value}}</td>
                                    @endforeach
                                </tr>
                                <tr class="name_found_close">
                                    <td class="name">Remaining</td>
                                    <?php  $total_found = 0;?>
                                    @foreach($array_found as $key=>$value)
                                        <td class="data">{{$total_found += $array_found[$key] - $array_close[$key]}}</td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div id="graph_wrap">
                        <div id="line_chart_open_close"></div>
                    </div>
                    <img id="image_1" name="type_bug" />
                </div><!-- /panel-body -->
                @endif
            </div><!-- /panel-default -->
        @endif
    </div><!-- /padding-md -->
</div>
@stop
@section('modal')
<div id="listImport" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center"><strong>Import bug</strong></h4>
            </div>
            <div class="modal-body">
                @if(count($projects) > 0)
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
                                    <table class="table table-striped importTable" id="responsiveTable">
                                        <thead>
                                            <tr>
                                                <th class="text-center tenPercent">No</th>
                                                <th>Project name</th>
                                                <th class="text-center">Project ID</th>
                                                <th class="tenPercent">
                                                    <label class="label-checkbox">
                                                        <input type="checkbox" id="chk-all">
                                                        <span class="custom-checkbox"></span>
                                                    </label>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $numberImport = 0;
                                                $adminId      = Helpers::getAdminOrDirectorId();
                                                $userId       = Helpers::getIdOfUserLogin();
                                            ?>
                                            @foreach($projectImport as $project)
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
                                                    @if(array_key_exists("user.import_defect", json_decode($project->permissions)))
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
                                                <input type="hidden" id="fileType" name="fileType" value="bug"/>
                                                <input type="hidden" name="team" value="{{Request::get('team')}}">
                                                <input type="hidden" name="project" value="{{Request::get('project')}}">
                                            </div>
                                        </div><!-- /.col -->
                                    </div><!-- /form-group -->
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer clearfix">
                            <div class="pull-left">
                                <button type="button" class="btn btn-success btn-sm disabled prevStep" id="prevStep">Previous</button>
                                <button type="button" class="btn btn-sm btn-success disabled nextStep" id="nextStep">Next</button>
                                <button type="button" class="btn btn-sm btn-success hidden" id="importFileButton">Import</button>
                                <a href='{{ url('uploads/exportFile/Template_Bug_import.xlsx') }}'>Download File template</a>
                            </div>
                            <div class="pull-right" style="width:30%">
                                <div class="progress progress-striped active m-top-sm m-bottom-none">
                                    <div class="progress-bar progress-bar-success" id="wizardProgress" style="width:50%;"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div><!-- /panel -->
                @else
                    <div class="text-center"><br><p>Sorry! You don't have role to import file into any project!</p></div>
                @endif
            </div>
        </div>
    </div>
</div>

<div id="export" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Export defect report</h4>
            </div>
             <div class="modal-body">
                @if(count($projects) > 0)
                    <div class="panel panel-default">
                        <form class="form-horizontal no-margin form-border" action="{{Route('defect.report.export')}}" method="post" id="formExport" novalidate>
                            {{ csrf_field() }}
                            <div class="panel-body">
                            </div>
                            <div class="panel-footer clearfix">
                                <input type="hidden" name="department" value="{{Request::get('department')}}">
                                <input type="hidden" name="division" value="{{Request::get('division')}}">
                                <input type="hidden" name="team" value="{{Request::get('team')}}">
                                <input type="hidden" name="check_time" value="{{Request::get('check_time')}}">
                                <input type="hidden" name="date" value="{{Request::get('date')}}">
                                <input type="hidden" name="start_date" value="{{Request::get('start_date')}}">
                                <input type="hidden" name="end_date" value="{{Request::get('end_date')}}">
                                <input type="hidden" name="report_type" value="{{Request::get('report_type')}}">
                                <input type="hidden" name="project" value="{{Request::get('project')}}">
                                <input type="hidden" name="units_time" value="{{Request::get('units_time')}}">
                                <input type="hidden" name="type_bug" value="{{Request::get('type_bug')}}">
                                <center>
                                    <button type="submit" class="btn btn-success btn-sm" id="exportFile">Export</button>
                                </center>
                                <center>
                                    <a href='{{ url('uploads/exportFile/Reformat_Cost_and_Bug_Report_with_Co-well_Report_template.xlsm') }}'>Download File template</a>
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

<div id="confirmUpdate" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirm Import File</h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <form action="{{ URL::route('project.bug.import.after') }}" method="post">
                    {{ csrf_field() }}
                    <button type="button" class="btn btn-default" id="confirmButton">Confirm</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="exportFileAfterImport" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirm Export File</h4>
            </div>
            <div class="modal-body">
                <p>Upload successful</p>
                <p>Do you want download this file with ticket ID for next time?</p>
                <p>* Note: File will delete when popup close.</p>
            </div>
            <div class="modal-footer">
                <form action="{{ URL::route('project.bug.export.after.import') }}" method="post">
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
    <script type="text/javascript" src="{{ asset('/js/select_date/select.date.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/common/reset_form.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/cost/cost.project.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/import/import.bug.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/common/highcharts.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/common/exporting.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/common/offline-exporting.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/common/report_chart.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/export_defect_report/defect.view.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/export_defect_report/defect.export.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/common/ajax_company_struct.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/jquery.inputmask.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/jquery.validate.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/common/validate_date.js') }}"></script>
@stop

