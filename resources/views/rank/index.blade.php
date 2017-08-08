@extends('layouts.master')

@section('title','Ranking')

@section('breadcrumbs','Ranking')

@section('style')
<style>
#scroll-x {
    overflow-x: auto;
}
.titleKpi{
    font-weight: bold;
    color: #fff;
    background-color: #65cea7 !important
}
.view-all-detail{
    float:right;
    color: #fff;
}
#dataTableDev_paginate,#dataTable_paginate,
#dataTableQa_paginate,#dataTableBrse_paginate,
#dataTableQal_paginate,#dataTableDm_paginate
{
    position: absolute;
    top:-40px;
    right:0px;
}
</style>
<link href="{{ asset('css/jquery.dataTables_themeroller.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="panel panel-default">
    <div class="panel-heading" id="form_heading">Ranking</div>
    <!-- common area -->
    <div class="panel-body">
        <!-- search area -->
        <form action="{{ URL::route('rank.index') }}" id="search_form" method="get" class="form-horizontal">
            <div class="info-left col-md-6">
                <div class="form-group">
                    <label class="col-md-4 control-label" for="year">Select Year:</label>
                    <div class="col-md-6">
                        <select class="form-control" name="year" id="year">
                            @if(!empty($years))
                                @foreach($years as $key => $value)
                                        <option value="{{$key}}" <?php if(Request::get('year') == $key) echo "selected"?>>{{$value}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>
            <div class="info-left col-md-6">
                <div class="form-group">
                    <label class="col-md-4 control-label" for="month">Select Month:</label>
                    <div class="col-md-6">
                        <select class="form-control" name="month[]" id="monthReport" multiple>
                            <?php $getMonths = Request::get('month');?>
                            @if(!empty($months))
                                @if(!empty($getMonths))
                                    @foreach($months as $key => $value)

                                        <option value="{{$key}}"
                                         <?php
                                         if (!empty($getMonths)) {
                                             foreach ($getMonths as $item) {
                                                 if($item == $key) echo "selected";
                                             }
                                         }
                                        ?>>{{$value}}</option>
                                    @endforeach
                                @else
                                    @foreach($months as $key => $value)
                                        <option value="{{$key}}" <?php if($key == 0) echo 'selected'?>>{{$value}}</option>
                                    @endforeach
                                @endif
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
        <div class="panel-body">
                <div class="col-md-6">
                </div>
                <div class="col-md-6">
                    <form method="get" class="pull-right">
                        @if(!empty(Request::get('month','')))
                            @foreach(Request::get('month') as $key=>$value)
                                <input type="hidden" name="month[]"  value="{{$value}}">
                            @endforeach
                        @endif
                        <input type="hidden" name="year"  value="{{Request::get('year')}}">
                        <label for="choose_item">Item display on page: &nbsp; &nbsp;</label>
                        <select id="choose_item" name="limit" class="form-control input-md inline-block" size="1" onchange="this.form.submit()">
                            @foreach($rankTop as $key=>$value)
                                <option value="{{$key}}" {{Request::get('limit') == $key ? "selected" : ''}}>{{$value}}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <!-- table data -->
                <caption><h5 class="text-center">RANKING PROJECT</h5></caption>
                <div class="panel-body">
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="totalSummary">
                            <div class="table-responsive col-md-6" id="scroll-x">
                                @if(isset($resultProjects))
                                    <?php
                                        $countPro = count($resultProjects);
                                        usort($resultProjects, function ($item1, $item2) {
                                                                return $item1['rank'] > $item2['rank'];
                                                            });
                                        $topBest = array_slice($resultProjects,0,Request::get('limit',5),true);
                                        usort($resultProjects, function ($item1, $item2) {
                                            return $item1['rank'] < $item2['rank'];
                                        });
                                        $topWorst = array_slice($resultProjects,0,Request::get('limit',5),true);
                                    ?>
                                <table class="table table-bordered table-hover table-striped projectReportTable" id="responsiveTable">
                                    <thead>
                                        <tr>
                                            <th colspan="9" style="background-color: #3ac73a;">TOP BEST OF {{count($resultProjects)}} PROJECTs
                                                <a href="javascript:;" code="asc" class="view-all-detail view-project-all">
                                                    <span class="menu-icon">
                                                    <i class="fa fa fa-link fa-lg"></i>
                                                    </span>
                                                    <span class="text">
                                                    View all
                                                    </span>
                                                </a>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="info_data">Top</th>
                                            <th class="info_data">Divison</th>
                                            <th class="info_data">Name</th>
                                            <th class="info_data">Language</th>
                                            <th class="info_data">Status</th>
                                            <th class="info_data">Productivity Rank</th>
                                            <th class="info_data">Quality Rank</th>
                                            <th class="info_data">Rank</th>
                                            <th class="info_data">View</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php $stt = 1;?>
                                            @foreach($topBest as $item)
                                                <tr class="record odd" role="row">
                                                    <td class="sorting_1">{{$stt++}}</td>
                                                    <td class="text-left">{{$item['department_name']}}</td>
                                                    <td class="text-left"><div data-toggle="tooltip" title="">{{$item['name']}}</div></td>
                                                    <td>{{$languages[$item['language_id']]}}</td>
                                                    <td>{{$projectTypes[$item['type_id']]}}</td>
                                                    <td>{{$item['rankP'] + 1}}</td>
                                                    <td>{{$item['rankQ'] + 1}}</td>
                                                    <td>{{$item['rank'] + 1}}</td>
                                                    <td><a class="info-project" dataId="{{$item['project_id']}}"><i class="fa fa-camera fa-lg"></i></a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                </table>
                                @endif
                                <div class="text-right">
                                </div>
                            </div>
                            <div class="table-responsive col-md-6" id="scroll-x">
                                @if(isset($resultProjects))
                                <table class="table table-bordered table-hover table-striped projectReportTable" id="responsiveTable">
                                    <thead>
                                        <tr>
                                            <th colspan="9">TOP WORST OF {{count($resultProjects)}} PROJECTs
                                                <a href="javascript:;" code="desc" class="view-all-detail view-project-all" >
                                                    <span class="menu-icon">
                                                    <i class="fa fa fa-link fa-lg"></i>
                                                    </span>
                                                    <span class="text">
                                                    View all
                                                    </span>
                                                </a>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="info_data">Top</th>
                                            <th class="info_data">Divison</th>
                                            <th class="info_data">Name</th>
                                            <th class="info_data">Language</th>
                                            <th class="info_data">Status</th>
                                            <th class="info_data">Productivity Rank</th>
                                            <th class="info_data">Quality Rank</th>
                                            <th class="info_data">Rank</th>
                                            <th class="info_data">View</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php $stt = 1;?>
                                            @foreach($topWorst as $item)
                                                <tr class="record odd" role="row">
                                                    <td class="sorting_1">{{$stt++}}</td>
                                                    <td class="text-left">{{$item['department_name']}}</td>
                                                    <td class="text-left"><div data-toggle="tooltip" title="">{{$item['name']}}</div></td>
                                                    <td>{{$languages[$item['language_id']]}}</td>
                                                    <td>{{$projectTypes[$item['type_id']]}}</td>
                                                    <td>{{$item['rankP'] + 1}}</td>
                                                    <td>{{$item['rankQ'] + 1}}</td>
                                                    <td>{{$item['rank'] + 1}}</td>
                                                    <td><a class="info-project" dataId="{{$item['project_id']}}"><i class="fa fa-camera fa-lg"></i></a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                </table>
                                @endif
                                <div class="text-right">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
<!-- table data -->
            </div>
            <div class="panel panel-default">
                <!-- table data -->
                <caption><h5 class="text-center">RANKING DEV</h5></caption>
                <div class="panel-body">
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="totalSummary">
                            <div class="table-responsive col-md-6" id="scroll-x">
                                @if(isset($resultDevs))
                                    <?php
                                        $countDev = count($resultDevs);
                                        usort($resultDevs, function ($item1, $item2) {
                                                                return $item1['rank'] > $item2['rank'];
                                                            });
                                        $topBest = array_slice($resultDevs,0,Request::get('limit',5),true);
                                        usort($resultDevs, function ($item1, $item2) {
                                            return $item1['rank'] < $item2['rank'];
                                        });
                                        $topWorst = array_slice($resultDevs,0,Request::get('limit',5),true);
                                    ?>
                                <table class="table table-bordered table-hover table-striped projectReportTable" id="responsiveTable">
                                    <thead>
                                        <tr>
                                            <th colspan="7" style="background-color: #3ac73a;">TOP BEST OF {{$countDev}} DEVELOPERs
                                                <a href="javascript:;" code="asc" class="view-all-detail view-dev-all">
                                                    <span class="menu-icon">
                                                    <i class="fa fa fa-link fa-lg"></i>
                                                    </span>
                                                    <span class="text">
                                                    View all
                                                    </span>
                                                </a>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="info_data">Top</th>
                                            <th class="info_data">Divison</th>
                                            <th class="info_data">Name</th>
                                            <th class="info_data">Productivity Rank</th>
                                            <th class="info_data">Quality Rank</th>
                                            <th class="info_data">Rank</th>
                                            <th class="info_data">View</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php $stt = 1;?>
                                            @foreach($topBest as $item)
                                                <tr class="record odd" role="row">
                                                    <td class="sorting_1">{{$stt++}}</td>
                                                    <td class="text-left">{{$item['department_name']}}</td>
                                                    <td class="text-left"><div data-toggle="tooltip" title="">{{$item['name']}}</div></td>
                                                    <td>{{$item['rankP'] + 1}}</td>
                                                    <td>{{$item['rankQ'] + 1}}</td>
                                                    <td>{{$item['rank'] + 1}}</td>
                                                    <td><a class="info-dev" dataId="{{$item['user_id']}}"><i class="fa fa-camera fa-lg"></i></a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                </table>
                                @endif
                                <div class="text-right">
                                </div>
                            </div>
                            <div class="table-responsive col-md-6" id="scroll-x">
                                @if(isset($resultDevs))
                                <table class="table table-bordered table-hover table-striped projectReportTable" id="responsiveTable">
                                    <thead>
                                        <tr>
                                            <th colspan="7">TOP WORST OF {{$countDev}} DEVELOPERs
                                                <a href="javascript:;" code="desc" class="view-all-detail view-dev-all">
                                                    <span class="menu-icon">
                                                    <i class="fa fa fa-link fa-lg"></i>
                                                    </span>
                                                    <span class="text">
                                                    View all
                                                    </span>
                                                </a>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="info_data">Top</th>
                                            <th class="info_data">Divison</th>
                                            <th class="info_data">Name</th>
                                            <th class="info_data">Productivity Rank</th>
                                            <th class="info_data">Quality Rank</th>
                                            <th class="info_data">Rank</th>
                                            <th class="info_data">View</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php $stt = 1;?>
                                            @foreach($topWorst as $item)
                                                <tr class="record odd" role="row">
                                                    <td class="sorting_1">{{$stt++}}</td>
                                                    <td class="text-left">{{$item['department_name']}}</td>
                                                    <td class="text-left"><div data-toggle="tooltip" title="">{{$item['name']}}</div></td>
                                                    <td>{{$item['rankP'] + 1}}</td>
                                                    <td>{{$item['rankQ'] + 1}}</td>
                                                    <td>{{$item['rank'] + 1}}</td>
                                                    <td><a class="info-dev" dataId="{{$item['user_id']}}"><i class="fa fa-camera fa-lg"></i></a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                </table>
                                @endif
                                <div class="text-right">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
<!-- table data -->
            </div>
            <div class="panel panel-default">
                <!-- table data -->
                <caption><h5 class="text-center">RANKING QA</h5></caption>
                <div class="panel-body">
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="totalSummary">
                            <div class="table-responsive col-md-6" id="scroll-x">
                                @if(isset($resultQas))
                                    <?php
                                        $countQa = count($resultQas);
                                        usort($resultQas, function ($item1, $item2) {
                                                                return $item1['rank'] > $item2['rank'];
                                                            });
                                        $topBest = array_slice($resultQas,0,Request::get('limit',5),true);
                                        usort($resultQas, function ($item1, $item2) {
                                            return $item1['rank'] < $item2['rank'];
                                        });
                                        $topWorst = array_slice($resultQas,0,Request::get('limit',5),true);
                                    ?>
                                <table class="table table-bordered table-hover table-striped projectReportTable" id="responsiveTable">
                                    <thead>
                                        <tr>
                                            <th colspan="7" style="background-color: #3ac73a;">TOP BEST OF {{$countQa}} QAs
                                                <a href="javascript:;" code="asc" class="view-all-detail view-qa-all">
                                                    <span class="menu-icon">
                                                    <i class="fa fa fa-link fa-lg"></i>
                                                    </span>
                                                    <span class="text">
                                                    View all
                                                    </span>
                                                </a>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="info_data">Top</th>
                                            <th class="info_data">Divison</th>
                                            <th class="info_data">Name</th>
                                            <th class="info_data">Productivity Rank</th>
                                            <th class="info_data">Quality Rank</th>
                                            <th class="info_data">Rank</th>
                                            <th class="info_data">View</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php $stt = 1;?>
                                            @foreach($topBest as $item)
                                                <tr class="record odd" role="row">
                                                    <td class="sorting_1">{{$stt++}}</td>
                                                    <td class="text-left">{{$item['department_name']}}</td>
                                                    <td class="text-left"><div data-toggle="tooltip" title="">{{$item['name']}}</div></td>
                                                    <td>{{$item['rankP'] + 1}}</td>
                                                    <td>{{$item['rankQ'] + 1}}</td>
                                                    <td>{{$item['rank'] + 1}}</td>
                                                    <td><a class="info-qa" dataId="{{$item['user_id']}}"><i class="fa fa-camera fa-lg"></i></a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                </table>
                                @endif
                                <div class="text-right">
                                </div>
                            </div>
                            <div class="table-responsive col-md-6" id="scroll-x">
                                @if(isset($resultQas))
                                <table class="table table-bordered table-hover table-striped projectReportTable" id="responsiveTable">
                                    <thead>
                                        <tr>
                                            <th colspan="7">TOP WORST OF {{$countQa}} QAs
                                                <a href="javascript:;" code="desc" class="view-all-detail view-qa-all">
                                                    <span class="menu-icon">
                                                    <i class="fa fa fa-link fa-lg"></i>
                                                    </span>
                                                    <span class="text">
                                                    View all
                                                    </span>
                                                </a>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="info_data">Top</th>
                                            <th class="info_data">Divison</th>
                                            <th class="info_data">Name</th>
                                            <th class="info_data">Productivity Rank</th>
                                            <th class="info_data">Quality Rank</th>
                                            <th class="info_data">Rank</th>
                                            <th class="info_data">View</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php $stt = 1;?>
                                            @foreach($topWorst as $item)
                                                <tr class="record odd" role="row">
                                                    <td class="sorting_1">{{$stt++}}</td>
                                                    <td class="text-left">{{$item['department_name']}}</td>
                                                    <td class="text-left"><div data-toggle="tooltip" title="">{{$item['name']}}</div></td>
                                                    <td>{{$item['rankP'] + 1}}</td>
                                                    <td>{{$item['rankQ'] + 1}}</td>
                                                    <td>{{$item['rank'] + 1}}</td>
                                                    <td><a class="info-qa" dataId="{{$item['user_id']}}"><i class="fa fa-camera fa-lg"></i></a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                </table>
                                @endif
                                <div class="text-right">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
<!-- table data -->
            </div>
            <div class="panel panel-default">
                <!-- table data -->
                <caption><h5 class="text-center">RANKING BSE</h5></caption>
                <div class="panel-body">
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="totalSummary">
                            <div class="table-responsive col-md-6" id="scroll-x">
                                @if(isset($resultBres))
                                    <?php
                                        $countBres = count($resultBres);
                                        usort($resultBres, function ($item1, $item2) {
                                                                return $item1['rank'] > $item2['rank'];
                                                            });
                                        $topBest = array_slice($resultBres,0,Request::get('limit',5),true);
                                        usort($resultBres, function ($item1, $item2) {
                                            return $item1['rank'] < $item2['rank'];
                                        });
                                        $topWorst = array_slice($resultBres,0,Request::get('limit',5),true);
                                    ?>
                                <table class="table table-bordered table-hover table-striped projectReportTable" id="responsiveTable">
                                    <thead>
                                        <tr>
                                            <th colspan="7" style="background-color: #3ac73a;">TOP BEST OF {{$countBres}} BSEs
                                                <a href="javascript:;" code="asc" class="view-all-detail view-brse-all">
                                                    <span class="menu-icon">
                                                    <i class="fa fa fa-link fa-lg"></i>
                                                    </span>
                                                    <span class="text">
                                                    View all
                                                    </span>
                                                </a>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="info_data">Top</th>
                                            <th class="info_data">Divison</th>
                                            <th class="info_data">Name</th>
                                            <th class="info_data">Productivity Rank</th>
                                            <th class="info_data">Quality Rank</th>
                                            <th class="info_data">Rank</th>
                                            <th class="info_data">View</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php $stt = 1;?>
                                            @foreach($topBest as $item)
                                                <tr class="record odd" role="row">
                                                    <td class="sorting_1">{{$stt++}}</td>
                                                    <td class="text-left">{{$item['department_name']}}</td>
                                                    <td class="text-left"><div data-toggle="tooltip" title="">{{$item['name']}}</div></td>
                                                    <td>{{$item['rankP'] + 1}}</td>
                                                    <td>{{$item['rankQ'] + 1}}</td>
                                                    <td>{{$item['rank'] + 1}}</td>
                                                    <td></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                </table>
                                @endif
                                <div class="text-right">
                                </div>
                            </div>
                            <div class="table-responsive col-md-6" id="scroll-x">
                                @if(isset($resultQas))
                                <table class="table table-bordered table-hover table-striped projectReportTable" id="responsiveTable">
                                    <thead>
                                        <tr>
                                            <th colspan="7">TOP WORST OF {{$countBres}} BSEs
                                                <a href="javascript:;" code="desc" class="view-all-detail view-brse-all">
                                                    <span class="menu-icon">
                                                    <i class="fa fa fa-link fa-lg"></i>
                                                    </span>
                                                    <span class="text">
                                                    View all
                                                    </span>
                                                </a>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="info_data">Top</th>
                                            <th class="info_data">Divison</th>
                                            <th class="info_data">Name</th>
                                            <th class="info_data">Productivity Rank</th>
                                            <th class="info_data">Quality Rank</th>
                                            <th class="info_data">Rank</th>
                                            <th class="info_data">View</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php $stt = 1;?>
                                            @foreach($topWorst as $item)
                                                <tr class="record odd" role="row">
                                                    <td class="sorting_1">{{$stt++}}</td>
                                                    <td class="text-left">{{$item['department_name']}}</td>
                                                    <td class="text-left"><div data-toggle="tooltip" title="">{{$item['name']}}</div></td>
                                                    <td>{{$item['rankP'] + 1}}</td>
                                                    <td>{{$item['rankQ'] + 1}}</td>
                                                    <td>{{$item['rank'] + 1}}</td>
                                                    <td></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                </table>
                                @endif
                                <div class="text-right">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
<!-- table data -->
            </div>
            <div class="panel panel-default">
                <!-- table data -->
                <caption><h5 class="text-center">RANKING QAL</h5></caption>
                <div class="panel-body">
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="totalSummary">
                            <div class="table-responsive col-md-6" id="scroll-x">
                                @if(isset($resultQal))
                                    <?php
                                        $countQal = count($resultQal);
                                        usort($resultQal, function ($item1, $item2) {
                                                                return $item1['rank'] > $item2['rank'];
                                                            });
                                            $topBest = array_slice($resultQal,0,Request::get('limit',5),true);
                                            usort($resultQal, function ($item1, $item2) {
                                            return $item1['rank'] < $item2['rank'];
                                        });
                                        $topWorst = array_slice($resultQal,0,Request::get('limit',5),true);
                                    ?>
                                <table class="table table-bordered table-hover table-striped projectReportTable" id="responsiveTable">
                                    <thead>
                                        <tr>
                                            <th colspan="7" style="background-color: #3ac73a;">TOP BEST OF {{$countQal}} QALs
                                                <a href="javascript:;" code="asc" class="view-all-detail view-qal-all">
                                                    <span class="menu-icon">
                                                    <i class="fa fa fa-link fa-lg"></i>
                                                    </span>
                                                    <span class="text">
                                                    View all
                                                    </span>
                                                </a>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="info_data">Top</th>
                                            <th class="info_data">Divison</th>
                                            <th class="info_data">Name</th>
                                            <th class="info_data">Productivity Rank</th>
                                            <th class="info_data">Quality Rank</th>
                                            <th class="info_data">Rank</th>
                                            <th class="info_data">View</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php $stt = 1;?>
                                            @foreach($topBest as $item)
                                                <tr class="record odd" role="row">
                                                    <td class="sorting_1">{{$stt++}}</td>
                                                    <td class="text-left">{{$item['department_name']}}</td>
                                                    <td class="text-left"><div data-toggle="tooltip" title="">{{$item['name']}}</div></td>
                                                    <td>{{$item['rankP'] + 1}}</td>
                                                    <td>{{$item['rankQ'] + 1}}</td>
                                                    <td>{{$item['rank'] + 1}}</td>
                                                    <td></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                </table>
                                @endif
                                <div class="text-right">
                                </div>
                            </div>
                            <div class="table-responsive col-md-6" id="scroll-x">
                                @if(isset($resultQas))
                                <table class="table table-bordered table-hover table-striped projectReportTable" id="responsiveTable">
                                    <thead>
                                        <tr>
                                            <th colspan="7">TOP WORST OF {{$countQal}} QALs
                                                <a href="javascript:;" code="desc" class="view-all-detail view-qal-all">
                                                    <span class="menu-icon">
                                                    <i class="fa fa fa-link fa-lg"></i>
                                                    </span>
                                                    <span class="text">
                                                    View all
                                                    </span>
                                                </a>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="info_data">Top</th>
                                            <th class="info_data">Divison</th>
                                            <th class="info_data">Name</th>
                                            <th class="info_data">Productivity Rank</th>
                                            <th class="info_data">Quality Rank</th>
                                            <th class="info_data">Rank</th>
                                            <th class="info_data">View</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php $stt = 1;?>
                                            @foreach($topWorst as $item)
                                                <tr class="record odd" role="row">
                                                    <td class="sorting_1">{{$stt++}}</td>
                                                    <td class="text-left">{{$item['department_name']}}</td>
                                                    <td class="text-left"><div data-toggle="tooltip" title="">{{$item['name']}}</div></td>
                                                    <td>{{$item['rankP'] + 1}}</td>
                                                    <td>{{$item['rankQ'] + 1}}</td>
                                                    <td>{{$item['rank'] + 1}}</td>
                                                    <td></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                </table>
                                @endif
                                <div class="text-right">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
<!-- table data -->
            </div>
            <div class="panel panel-default">
                <!-- table data -->
                <caption><h5 class="text-center">RANKING DM</h5></caption>
                <div class="panel-body">
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="totalSummary">
                            <div class="table-responsive col-md-6" id="scroll-x">
                                @if(isset($resultDm))
                                    <?php
                                        $countDm = count($resultDm);
                                        usort($resultDm, function ($item1, $item2) {
                                                                return $item1['rank'] > $item2['rank'];
                                                            });
                                            $topBest = array_slice($resultDm,0,Request::get('limit',5),true);
                                            usort($resultDm, function ($item1, $item2) {
                                            return $item1['rank'] < $item2['rank'];
                                        });
                                            $topWorst = array_slice($resultDm,0,Request::get('limit',5),true);
                                    ?>
                                <table class="table table-bordered table-hover table-striped projectReportTable" id="responsiveTable">
                                    <thead>
                                        <tr>
                                            <th colspan="7" style="background-color: #3ac73a;">TOP BEST OF {{$countDm}} DMs
                                                <a href="javascript:;" code="asc" class="view-all-detail view-dm-all">
                                                    <span class="menu-icon">
                                                    <i class="fa fa fa-link fa-lg"></i>
                                                    </span>
                                                    <span class="text">
                                                    View all
                                                    </span>
                                                </a>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="info_data">Top</th>
                                            <th class="info_data">Divison</th>
                                            <th class="info_data">Name</th>
                                            <th class="info_data">Productivity Rank</th>
                                            <th class="info_data">Quality Rank</th>
                                            <th class="info_data">Rank</th>
                                            <th class="info_data">View</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php $stt = 1;?>
                                            @foreach($topBest as $item)
                                                <tr class="record odd" role="row">
                                                    <td class="sorting_1">{{$stt++}}</td>
                                                    <td class="text-left">{{$item['department_name']}}</td>
                                                    <td class="text-left"><div data-toggle="tooltip" title="">{{$item['name']}}</div></td>
                                                    <td>{{$item['rankP'] + 1}}</td>
                                                    <td>{{$item['rankQ'] + 1}}</td>
                                                    <td>{{$item['rank'] + 1}}</td>
                                                    <td></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                </table>
                                @endif
                                <div class="text-right">
                                </div>
                            </div>
                            <div class="table-responsive col-md-6" id="scroll-x">
                                @if(isset($resultQas))
                                <table class="table table-bordered table-hover table-striped projectReportTable" id="responsiveTable">
                                    <thead>
                                        <tr>
                                            <th colspan="7">TOP WORST OF {{$countDm}} DMs
                                                <a href="javascript:;" code="desc" class="view-all-detail view-dm-all">
                                                    <span class="menu-icon">
                                                    <i class="fa fa fa-link fa-lg"></i>
                                                    </span>
                                                    <span class="text">
                                                    View all
                                                    </span>
                                                </a>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="info_data">Top</th>
                                            <th class="info_data">Divison</th>
                                            <th class="info_data">Name</th>
                                            <th class="info_data">Productivity Rank</th>
                                            <th class="info_data">Quality Rank</th>
                                            <th class="info_data">Rank</th>
                                            <th class="info_data">View</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php $stt = 1;?>
                                            @foreach($topWorst as $item)
                                                <tr class="record odd" role="row">
                                                    <td class="sorting_1">{{$stt++}}</td>
                                                    <td class="text-left">{{$item['department_name']}}</td>
                                                    <td class="text-left"><div data-toggle="tooltip" title="">{{$item['name']}}</div></td>
                                                    <td>{{$item['rankP'] + 1}}</td>
                                                    <td>{{$item['rankQ'] + 1}}</td>
                                                    <td>{{$item['rank'] + 1}}</td>
                                                    <td></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                </table>
                                @endif
                                <div class="text-right">
                                </div>
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
@endsection
@section('modal')
<div id="show_info_project" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog" style="width: 70%">
        <div class="modal-content" id="content-project">
        </div>
    </div>
</div>
<div id="show_info_dev" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog" style="width: 70%">
        <div class="modal-content" id="content-dev">
        </div>
    </div>
</div>
 
<div id="show_info_qa" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog" style="width: 70%">
        <div class="modal-content" id="content-qa">
        </div>
    </div>
</div>

<div id="show_info_all_project" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog" style="width: 70%">
        <div class="modal-content" id="content-qa">
            @include('rank.detail_all')
        </div>
    </div>
</div>

<div id="show_info_all_dev" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog" style="width: 70%">
        <div class="modal-content" id="content-qa">
            @include('rank.detail_all_dev')
        </div>
    </div>
</div>

<div id="show_info_all_qa" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog" style="width: 70%">
        <div class="modal-content" id="content-qa">
            @include('rank.detail_all_qa')
        </div>
    </div>
</div>

<div id="show_info_all_brse" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog" style="width: 70%">
        <div class="modal-content" id="content-qa">
            @include('rank.detail_all_brse')
        </div>
    </div>
</div>

<div id="show_info_all_qal" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog" style="width: 70%">
        <div class="modal-content" id="content-qa">
            @include('rank.detail_all_qal')
        </div>
    </div>
</div>

<div id="show_info_all_dm" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog" style="width: 70%">
        <div class="modal-content" id="content-qa">
            @include('rank.detail_all_dm')
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
     $(document).ready(function(){
         $.ajaxSetup({
             headers : {
                 'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
             }
         });
         $('#dataTable').dataTable( {
            "bJQueryUI": true,
            "sPaginationType": "full_numbers",
            "bInfo": false,
            "language": {
                "lengthMenu": "Item display on page:   _MENU_",
                "paginate": {
                    "previous":     "<<",
                    "next":         ">>"
                }
            },
            "dom": '<"top"flp<"clear">>rt<"bottom"ip<"clear">>'
        });
         $('#dataTableDev').dataTable( {
             "bJQueryUI": true,
             "sPaginationType": "full_numbers",
             "bInfo": false,
             "language": {
                 "lengthMenu": "Item display on page:   _MENU_",
                 "paginate": {
                     "previous":     "<<",
                     "next":         ">>"
                 }
             },
             "dom": '<"top"flp<"clear">>rt<"bottom"ip<"clear">>'
         });
         $('#dataTableQa').dataTable( {
             "bJQueryUI": true,
             "sPaginationType": "full_numbers",
             "bInfo": false,
             "language": {
                 "lengthMenu": "Item display on page:   _MENU_",
                 "paginate": {
                     "previous":     "<<",
                     "next":         ">>"
                 }
             },
             "dom": '<"top"flp<"clear">>rt<"bottom"ip<"clear">>'
         });
         $('#dataTableBrse').dataTable( {
             "bJQueryUI": true,
             "sPaginationType": "full_numbers",
             "bInfo": false,
             "language": {
                 "lengthMenu": "Item display on page:   _MENU_",
                 "paginate": {
                     "previous":     "<<",
                     "next":         ">>"
                 }
             },
             "dom": '<"top"flp<"clear">>rt<"bottom"ip<"clear">>'
         });
         $('#dataTableQal').dataTable( {
             "bJQueryUI": true,
             "sPaginationType": "full_numbers",
             "bInfo": false,
             "language": {
                 "lengthMenu": "Item display on page:   _MENU_",
                 "paginate": {
                     "previous":     "<<",
                     "next":         ">>"
                 }
             },
             "dom": '<"top"flp<"clear">>rt<"bottom"ip<"clear">>'
         });
         $('#dataTableDm').dataTable( {
             "bJQueryUI": true,
             "sPaginationType": "full_numbers",
             "bInfo": false,
             "language": {
                 "lengthMenu": "Item display on page:   _MENU_",
                 "paginate": {
                     "previous":     "<<",
                     "next":         ">>"
                 }
             },
             "dom": '<"top"flp<"clear">>rt<"bottom"ip<"clear">>'
         });

         $('.view-project-all').click(function(){
             var oTable = $('#dataTable').dataTable();
             oTable.fnSort( [ [0,$(this).attr('code')] ] );
             $('#show_info_all_project').modal({'show': true});
         });
         $('.view-dev-all').click(function(){
             var oTable = $('#dataTableDev').dataTable();
             oTable.fnSort( [ [0,$(this).attr('code')] ] );
             $('#show_info_all_dev').modal({'show': true});
         });
         $('.view-qa-all').click(function(){
             var oTable = $('#dataTableQa').dataTable();
             oTable.fnSort( [ [0,$(this).attr('code')] ] );
             $('#show_info_all_qa').modal({'show': true});
         });
         $('.view-brse-all').click(function(){
             var oTable = $('#dataTableBrse').dataTable();
             oTable.fnSort( [ [0,$(this).attr('code')] ] );
             $('#show_info_all_brse').modal({'show': true});
         });
         $('.view-qal-all').click(function(){
             var oTable = $('#dataTableQal').dataTable();
             oTable.fnSort( [ [0,$(this).attr('code')] ] );
             $('#show_info_all_qal').modal({'show': true});
         });
         $('.view-dm-all').click(function(){
             var oTable = $('#dataTableDm').dataTable();
             oTable.fnSort( [ [0,$(this).attr('code')] ] );
             $('#show_info_all_dm').modal({'show': true});
         });


         $('.info-project').click(function(){
             $('#show_info_project').modal({'show': true});
             $.ajax({
                 url:"{{Route('rank.infoProject')}}",
                 type:'post',
                 data:{
                    id:$(this).attr('dataId'),
                    year:"{{Request::get('year')}}",
                    month:<?php echo json_encode(Request::get('month')); ?>,
                 },
                 success:function(result){
                     $('#content-project').html(result);
                 }
             });
         });
         $('.info-dev').click(function(){
             $('#show_info_dev').modal({'show': true});
             $.ajax({
                 url:"{{Route('rank.infoDev')}}",
                 type:'post',
                 data:{
                    id:$(this).attr('dataId'),
                    year:"{{Request::get('year')}}",
                    month:<?php echo json_encode(Request::get('month')); ?>,
                 },
                 success:function(result){
                     $('#content-dev').html(result);
                 }
             });
         });

         $('.info-qa').click(function(){
             $('#show_info_qa').modal({'show': true});
             $.ajax({
                 url:"{{Route('rank.infoQa')}}",
                 type:'post',
                 data:{
                    id:$(this).attr('dataId'),
                    year:"{{Request::get('year')}}",
                    month:<?php echo json_encode(Request::get('month')); ?>,
                 },
                 success:function(result){
                     $('#content-qa').html(result);
                 }
             });
         });
     });
</script>
@endsection