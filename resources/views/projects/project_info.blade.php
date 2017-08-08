@extends('layouts.master')
@section('title','View project info')
@section('breadcrumbs','View project info')
@section('style')
<link href="{{ asset('/css/project/project.css') }}" rel="stylesheet">
<link href="{{ asset('/css/custom/cost.css') }}" rel="stylesheet">
@stop
@section('content')
<div class="padding-md">
    <div class="main-header clearfix">
      <div class="page-title">
        <h3 class="no-margin">View project info</h3>
      </div>
    </div>
    <div class="panel panel-default">
    <div class="panel-body">
        <div class="table-responsive" id="scroll-x">
            <div class="wordbold panel-heading">
                Summary
            </div>
        <table class="table table-bordered table-hover table-striped" id="dataTable">
          <thead hidden="hidden">
            <tr>
                <th width="20%"></th>
                <th width="20%"></th>
                <th width="20%"></th>
                <th width="10%"></th>
                <th width="20%"></th>
                <th width="10%"></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="wordbold">Project Name</td>
              <td>
               <a data-toggle="tooltip" title="{{ $project->name }}">
                {{ str_limit($project->name, 20) }}
              </a>
              </td>
              <td class="wordbold">BSE/ Project Manager</td>
              <td>
                  @if (!empty($brse[$project->brse]) )
                    {{ $brse[$project->brse] }}
                  @endif
              </td>
              <td class="wordbold">Unit test</td>
              <td>
                {{ $resource[$project->unit_test] }}
              </td>
            </tr>
            <tr>
              <td class="wordbold">Project Key</td>
              <td>{{ $project->project_key }}</td>
              <td class="wordbold">Project Type</td>
              <td>{{ $type[$project->type_id] }}</td>
              <td class="wordbold">Test first</td>
              <td>
                {{ $resource[$project->test_first] }}
              </td>
            </tr>
            <tr>
              <td class="wordbold">Status</td>
              <td>
                @if($project->status != 0)
                 {{ $status[$project->status] }}</td>
                @endif
              <td class="wordbold">Project language</td>
              <td>
               @if ($project->language_id !=0)
               {{ $language[$project->language_id]}}
               @endif
              </td>
              <td class="wordbold">Scenario</td>
              <td>
                {{ $resource[$project->scenario] }}
              </td>
            </tr>
            <tr>
              <td class="wordbold">Plan Start Date</td>
              <td>
               @if( empty($project['plant_start_date']) || $project->plant_start_date == '0000-00-00')
                @else
                   {{ date('d/m/Y',strtotime(str_replace('/', '-', $project['plant_start_date']))) }}
                @endif
              </td>
              <td class="wordbold">Plan End Date</td>
              <td>
               @if( empty($project['plant_end_date']) || $project->plant_end_date == '0000-00-00')
                @else
                   {{ date('d/m/Y',strtotime(str_replace('/', '-', $project['plant_end_date']))) }}
                @endif
              </td>
              <td class="wordbold">Detail design</td>
              <td>
                {{ $resource[$project->scenario] }}
              </td>
            </tr>
            <tr>
              <td class="wordbold">Actual Start Date</td>
              <td>
               @if( empty($project['actual_start_date']) || $project->actual_start_date == '0000-00-00')
                @else
                   {{ date('d/m/Y',strtotime(str_replace('/', '-', $project['actual_start_date']))) }}
                @endif
              </td>
              <td class="wordbold">Actual End Date</td>
              <td>
               @if( empty($project['actual_end_date']) || $project->actual_end_date == '0000-00-00')
                @else
                   {{ date('d/m/Y',strtotime(str_replace('/', '-', $project['actual_end_date']))) }}
                @endif
              </td>
              <td class="wordbold">Process apply</td>
              <td>
              @if ($project->process_apply != 0)
                {{ $process_apply[$project->process_apply] }}
              @endif
              </td>
            </tr>
            <tr>
              <td class="wordbold">Plan Effort</td>
              <td>
                @if(!empty($estimate_project))
                    {{ round($estimate_project,2) }}
                @else
                    {{0}}
                @endif
              </td>
              <td class="wordbold">Actual Effort</td>
              <td>
                    @if(!empty($actual_project))
                        {{ round($actual_project,2) }}
                    @else
                        {{0}}
                    @endif
              </td>
              <td class="wordbold">Effort Deviation</td>
              @if( ($estimate_project - $actual_project) < 0)
                <td style="color: red">
                  {{ round($estimate_project - $actual_project,2) }}
                </td>
              @else
                <td>
                  {{ round($estimate_project - $actual_project,2) }}
                </td>
              @endif
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="panel-body">
        <div class="wordbold panel-heading">
            Summary metrics
        </div>
        <div class="table-responsive" id="scroll-x">
            <table class="table table-bordered table-hover table-striped" id="">
                <thead>
                    <tr>
                        <th rowspan="2" class="width250"></th>
                        <th rowspan="2" class="width140">Unit</th>
                        @foreach ($week_project_kpi as $data)
                            <th colspan="2" class="width140">
                                <div class="" data-toggle="tooltip" title="{{ $data['name'] }}">
                                    {{ str_limit($data['name'], 8) }}
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
               </thead>
               <tbody>
                   <tr>
                       <td class="titleKpi">Cost</td>
                       <td class="titleKpi"></td>
                        @if(count($week_project_kpi) > 0)
                            <td class="titleKpi" colspan="{{ count($week_project_kpi)*2 }}"></td>
                        @endif
                   </tr>
                    <tr class="name_cost_efficiency">
                        <td class="wordbold name">Cost efficiency</td>
                        <td>
                            <div>%</div>
                        </td>
                        @if(count($week_project_kpi) > 0)
                            @foreach ($week_project_kpi as $data)
                                <td class="data-plan">
                                    @if (count($data['plan_cost_efficiency']) > 0)
                                        {{ $data['plan_cost_efficiency'] }}
                                    @endif
                                </td>
                                <td class="data">
                                    @if (count($data['actual_cost_efficiency']) > 0)
                                        {{ $data['actual_cost_efficiency'] }}
                                    @endif
                                </td>
                                <td class="description" hidden="hidden">
                                    @if (count($data['description']) > 0)
                                        {{ $data['description'] }}
                                    @endif
                                </td>
                            @endforeach
                        @endif
                    </tr>
                    <tr class="name_fix_cost">
                        <td class="wordbold name">Fixing cost</td>
                        <td>
                           <div>%</div>
                        </td>
                        @if(count($week_project_kpi) > 0)
                            @foreach ($week_project_kpi as $data)
                                <td class="data-plan">
                                    @if (count($data['plan_fix_code']) > 0)
                                        {{ $data['plan_fix_code'] }}
                                    @endif
                                </td>
                                <td class="data">
                                    @if (count($data['actual_fix_code']) > 0)
                                        {{ $data['actual_fix_code'] }}
                                    @endif
                                </td>
                            @endforeach
                        @endif
                    </tr>
                    <tr>
                        <td class="titleKpi">Quality</td>
                        <td class="titleKpi"></td>
                        @if(count($week_project_kpi) > 0)
                            <td class="titleKpi" colspan="{{ count($week_project_kpi)*2 }}"></td>
                        @endif
                    </tr>
                    <tr class="name_leakage">
                        <td class="wordbold name">Leakage</td>
                        <td>
                            <div>Wdef/mm</div>
                        </td>
                        @if(count($week_project_kpi) > 0)
                            @foreach ($week_project_kpi as $data)
                                <td class="data-plan">
                                    @if (count($data['plan_leakage']) > 0)
                                        {{ $data['plan_leakage'] }}
                                    @endif
                                </td>
                                <td class="data">
                                    @if (count($data['actual_leakage']) > 0)
                                        {{ $data['actual_leakage'] }}
                                    @endif
                                </td>
                            @endforeach
                        @endif
                    </tr>
                    <tr class="name_UAT_bug_number">
                        <td class="wordbold name">Bug after release (number)</td>
                        <td>
                            <div>Number</div>
                        </td>
                        @if(count($week_project_kpi) > 0)
                            @foreach ($week_project_kpi as $data)
                                <td class="data-plan">
                                    @if (count($data['plan_bug_after_release_number']) > 0)
                                        {{ $data['plan_bug_after_release_number'] }}
                                    @endif
                                </td>
                                <td class="data">
                                    @if (count($data['actual_bug_after_release_number']) > 0)
                                        {{ $data['actual_bug_after_release_number'] }}
                                    @endif
                                </td>
                            @endforeach
                        @endif
                    </tr>
                    <tr class="name_UAT_bug_weight">
                        <td class="wordbold name">Bug after release (weight)</td>
                        <td>
                            <div>Weight</div>
                        </td>
                        @if(count($week_project_kpi) > 0)
                            @foreach ($week_project_kpi as $data)
                                <td class="data-plan">
                                    @if (count($data['plan_bug_after_release_weight']) > 0)
                                        {{ $data['plan_bug_after_release_weight'] }}
                                    @endif
                                </td>
                                <td class="data">
                                    @if (count($data['actual_bug_after_release_weight']) > 0)
                                        {{ $data['actual_bug_after_release_weight'] }}
                                    @endif
                                </td>
                            @endforeach
                        @endif
                    </tr>
                    <tr class="name_customer_survey">
                        <td class="wordbold name">Customer survey</td>
                        <td>
                            <div>Point</div>
                        </td>
                       @if(count($week_project_kpi) > 0)
                            @foreach ($week_project_kpi as $data)
                                <td class="data-plan">
                                    @if (count($data['plan_customer_survey']) > 0)
                                        {{ $data['plan_customer_survey'] }}
                                    @endif
                                </td>
                                <td class="data">
                                    @if (count($data['actual_customer_survey']) > 0)
                                        {{ $data['actual_customer_survey'] }}
                                    @endif
                                </td>
                            @endforeach
                        @endif
                    </tr>
                    <tr class="name_defect_remove_efficiency">
                        <td class="wordbold name">Defect remove efficiency</td>
                        <td>
                            <div>%</div>
                        </td>
                       @if(count($week_project_kpi) > 0)
                            @foreach ($week_project_kpi as $data)
                                <td class="data-plan">
                                    @if (count($data['plan_defect_remove_efficiency']) > 0)
                                        {{ $data['plan_defect_remove_efficiency'] }}
                                    @endif
                                </td>
                                <td class="data">
                                    @if (count($data['actual_defect_remove_efficiency']) > 0)
                                        {{ $data['actual_defect_remove_efficiency'] }}
                                    @endif
                                </td>
                            @endforeach
                        @endif
                    </tr>
                    <tr class="name_defect_rate">
                        <td class="wordbold name">Defect rate</td>
                        <td>
                            <div>Wdef/mm</div>
                        </td>
                        @if(count($week_project_kpi) > 0)
                            @foreach ($week_project_kpi as $data)
                                <td class="data-plan">
                                    @if (count($data['plan_defect_rate']) > 0)
                                        {{ $data['plan_defect_rate'] }}
                                    @endif
                                </td>
                                <td class="data">
                                    @if (count($data['actual_defect_rate']) > 0)
                                        {{ $data['actual_defect_rate'] }}
                                    @endif
                                </td>
                            @endforeach
                        @endif
                    </tr>
                    <tr>
                        <td class="titleKpi">Productivity</td>
                        <td class="titleKpi"></td>
                        @if(count($week_project_kpi) > 0)
                            <td class="titleKpi" colspan="{{ count($week_project_kpi)*2 }}"></td>
                        @endif
                    </tr>
                    <tr class="name_code_productivity">
                        <td class="wordbold name">Code productivity</td>
                        <td>
                            <div>LOC/mm</div>
                        </td>
                        @if(count($week_project_kpi) > 0)
                            @foreach ($week_project_kpi as $data)
                                <td class="data-plan">
                                    @if (count($data['plan_code_productivity']) > 0)
                                        {{ $data['plan_code_productivity'] }}
                                    @endif
                                </td>
                                <td class="data">
                                    @if (count($data['actual_code_productivity']) > 0)
                                        {{ $data['actual_code_productivity'] }}
                                    @endif
                                </td>
                            @endforeach
                        @endif
                    </tr>
                    <tr class="name_create_test_productivity">
                        <td class="wordbold name">Create testcase productivity</td>
                        <td>
                            <div>TC/mm</div>
                        </td>
                        @if(count($week_project_kpi) > 0)
                            @foreach ($week_project_kpi as $data)
                                <td class="data-plan">
                                    @if (count($data['plan_test_case_productivity']) > 0)
                                        {{ $data['plan_test_case_productivity'] }}
                                    @endif
                                </td>
                                <td class="data">
                                    @if (count($data['actual_test_case_productivity']) > 0)
                                        {{ $data['actual_test_case_productivity'] }}
                                     @endif
                                </td>
                            @endforeach
                        @endif
                    </tr>
                    <tr class="name_tested_productivity">
                        <td class="wordbold name">Tested productivity</td>
                        <td>
                            <div>Tested/mm</div>
                        </td>
                        @if(count($week_project_kpi) > 0)
                            @foreach ($week_project_kpi as $data)
                                <td class="data-plan">
                                    @if (count($data['plan_tested_productivity']) > 0)
                                        {{ $data['plan_tested_productivity'] }}
                                    @endif
                                </td>
                                <td class="data">
                                    @if (count($data['actual_tested_productivity']) > 0)
                                        {{ $data['actual_tested_productivity'] }}
                                     @endif
                                </td>
                            @endforeach
                        @endif
                    </tr>
                </tbody>
            </table>
        </div><!-- /table-responsive -->
    </div>
    <div class="panel-body">
        <div class="table-responsive" id="scroll-x">
            <div class="wordbold panel-heading">
                Release/version
            </div>
        <table class="table table-bordered table-hover table-striped" id="dataTable">
          <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">Version</th>
                <th width="10%">Status</th>
                <th width="10%">Plan date</th>
                <th width="10%">Actual date</th>
                <th width="5%">Deviation</th>
                <th width="10%">Plan effort</th>
                <th width="10%">Actual effort</th>
                <th width="10%">Deviation effort</th>
                <th width="10%">Number of tasks</th>
                <th width="10%">Number of bug</th>
            </tr>
          </thead>
          <tbody>
          @forelse ($versionEstimate as $key=>$data)
            <tr>
              <td>{{ ++$stt }}</td>
              <td>{{ $data->name }}</td>
              <td>
                @if ($data->status !=0)
                  {{ $status[$data->status] }}
                @endif
              </td>
              <td>
                @if( !empty($data['start_date']) && $data->start_date != '0000-00-00 00:00:00')
                   {{ date('d/m/Y',strtotime(str_replace('/', '-', $data['start_date']))) }}
                @endif
              </td>
              <td>
                @if( !empty($data['end_date']) && $data->end_date != '0000-00-00 00:00:00')
                   {{ date('d/m/Y',strtotime(str_replace('/', '-', $data['end_date']))) }}
                @endif
              </td>
              <td>
               @if( !empty($data['end_date']) && $data->end_date != '0000-00-00 00:00:00' && !empty($data['start_date'] && $data->start_date != '0000-00-00 00:00:00' ))
                   {{ (strtotime($data->start_date) - strtotime($data->end_date)) / (60 * 60 * 24) }}
               @endif
              </td>
              <td>
                    <?php
                       if(empty($versionActual[$key]->actual_hour))
                           $actual_hour = 0;
                       else $actual_hour = $versionActual[$key]->actual_hour;
                       if(empty($data->estimate))
                           $estimate = 0;
                           else $estimate= $data->estimate;
                    ?>
                    {{$estimate}}
              </td>
              <td>
                    {{$actual_hour}}
              </td>
              <td style="{{($estimate - $actual_hour) < 0 ? 'color: red' :''}}">
                    {{$estimate - $actual_hour}}
              </td>
              <td>
              <?php
                     $number_tasks = App\Models\Ticket::
                               join('ticket_type','tickets.ticket_type_id','=','ticket_type.id')
                              ->where('project_id','=',$project_id)
                              ->where('tickets.version_id','=',$data->version_id)
                              ->whereIn('ticket_type.related_id',['1','2','3','4','5','6','7','8','11','12'])
                              ->count();
                ?>
               {{ $number_tasks }}
              </td>
              <td>
              <?php
              $number_bug = App\Models\Ticket::
                               join('ticket_type','tickets.ticket_type_id','=','ticket_type.id')
                              ->where('project_id','=',$project_id)
                              ->where('tickets.version_id','=',$data->id)
                              ->whereIn('ticket_type.related_id',['9','10'])
                              ->count();
              ?>
               {{ $number_bug }}
              </td>
            </tr>
          @empty
            <tr>
                <td colspan="14">
                    <p>empty</p>
                </td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
      <div class="page-right">
            {{ $versionEstimate->appends(array_except(Request::query(), 'paginate-version'))->links() }}
        </div><!-- page-right -->
    </div>
    <div class="panel-body">
        <div class="table-responsive" id="scroll-x">
            <div class="wordbold panel-heading">
                Category(sub-project)
            </div>
        <table class="table table-bordered table-hover table-striped" id="dataTable">
          <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Category</th>
                <th width="10%">Plan effort</th>
                <th width="10%">Actual effort</th>
                <th width="20%">Deviation effort</th>
                <th width="20%">Number of tasks</th>
                <th width="20%">Number of bug</th>
            </tr>
          </thead>
          <tbody>
          @forelse ($list_ticket['list_plan'] as $ticket)
            <tr>
              <td>{{ ++$sttCategory }}</td>
              <td>
              @if (!empty($ticket['category']))
                  <?php $category = substr($ticket['category'] ,-1) ;?>
                  @if($category == '}' )
                  <?php  $data = unserialize($ticket['category']) ?>
                        @if(isset($data[0]['name']))
                            {{ $data[0]['name'] }}
                        @else
                            {{$data['name']}}
                        @endif
                  @else
                  {{ $ticket['category'] }}
                  @endif
              @endif
              </td>
              <td>
                 {{$ticket['count_estimate']}}
              </td>
              <td>
                  <?php $check = false ?>
                  @foreach($list_ticket['list_actual'] as $item)
                    @if($ticket['category'] == $item['category'])
                        <?php $check = true ?>
                        {{$item['count_actual']}}
                        @break;
                    @endif
                  @endforeach
                  @if(!$check)
                        {{'0'}}
                  @endif
              </td>
                  <?php $check = false ?>
                  @foreach($list_ticket['list_actual'] as $item)
                    @if($ticket['category'] == $item['category'])
                        <?php $check = true ?>
                        <?php $deviation_effort = $ticket['count_estimate'] - $item['count_actual'] ?>
                        @break;
                    @endif
                  @endforeach
                  @if(!$check)
                        <?php $deviation_effort = $ticket['count_estimate'] - 0 ?>
                  @endif
              @if($deviation_effort < 0)
              <td style="color:red">
                   {{round($deviation_effort,2)}}
              </td>
              @else
              <td>
                   {{round($deviation_effort,2)}}
              </td>
              @endif
              <td>
              <?php
                     $number_tasks = App\Models\Ticket::
                               join('ticket_type','tickets.ticket_type_id','=','ticket_type.id')
                              ->where('project_id','=',$project_id)
                              ->where('tickets.category','=',$ticket->category)
                              ->whereIn('ticket_type.related_id',['1','2','3','4','5','6','7','8','11','12'])
                              ->count();
                ?>
               {{ $number_tasks }}
              </td>
              <td>
              <?php
              $number_bug = App\Models\Ticket::
                               join('ticket_type','tickets.ticket_type_id','=','ticket_type.id')
                              ->where('project_id','=',$project_id)
                              ->where('tickets.category','=',$ticket->category)
                              ->whereIn('ticket_type.related_id',['9','10'])
                              ->count();
              ?>
               {{ $number_bug }}
              </td>
            </tr>
          @empty
            <tr>
                <td colspan="14">
                    <p>empty</p>
                </td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
        <div class="page-right">
            {{ $list_ticket['list_plan']->appends(array_except(Request::query(), 'page'))->links() }}
        </div><!-- page-right -->
    </div>
    <div class="panel-body">
        <div class="table-responsive" id="scroll-x">
            <div class="wordbold panel-heading">
                Project Risk
            </div>
        <table class="table table-bordered table-hover table-striped" id="dataTable">
          <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Risk title</th>
                <th width="10%">Category</th>
                <th width="10%">Propability(%)</th>
                <th width="10%">Impact (1-5)</th>
                <th width="5%">Level</th>
                <th width="10%">Strategy</th>
                <th width="20%">Mitigation plan</th>
                <th width="10%">Status</th>
            </tr>
          </thead>
          <tbody>

          @forelse ($list_risk as $risk)
            <tr>
              <td>{{ ++$sttRisk }}</td>
              <td>
                {{ $risk->risk_title }}
              </td>
              <td>
                {{ $risk->value }}
              </td>
              <td>
                {{ $risk->propability }}
              </td>
              <td>
                {{ $risk->impact }}
              </td>
              <td>
                {{ $risk->propability*$risk->impact/100 }}
              </td>
              <td>
                @if ($risk ->strategy != 0 )
                  {{ $risk_strategy[$risk->strategy] }}
                @endif
              </td>
              <td>
              <a data-toggle="tooltip" title="{{ $risk->mitigration_plan }}">
                {{ str_limit($risk->mitigration_plan, 20) }}
              </a>
              </td>
              <td>
                @if ($risk->status != 0 )
                  {{ $status[$risk->status] }}
                @endif
              </td>
            </tr>
          @empty
            <tr>
                <td colspan="14">
                    <p>empty</p>
                </td>
            </tr>
          @endforelse

          </tbody>
        </table>
      </div>
      <div class="page-right">
        {{ $list_risk->appends(array_except(Request::query(), 'page'))->links() }}
        </div><!-- page-right -->
    </div>
    <div class="panel-body">
        <div class="table-responsive" id="scroll-x">
            <div class="wordbold panel-heading">
                Project Kpt
            </div>
        <table class="table table-bordered table-hover table-striped" id="dataTable">
          <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Sprint/ Release</th>
                <th width="20%">Category</th>
                <th width="10%">Type</th>
                <th width="20%">Description</th>
                <th width="20%">List plan action</th>
                <th width="10%">Status</th>
            </tr>
          </thead>
          <tbody>
          @forelse ($list_kpt as $kpt)
            <tr>
              <td>{{ ++$sttKpt }}</td>
              <td>{{ $kpt->name }}</td>
              <td>
                {{ $kpt->value }}
              </td>
              <td>
                {{ $types[$kpt->type_id] }}
              </td>
              <td>
              <a data-toggle="tooltip" title="{{ $kpt->content }}">
                {{ \Illuminate\Support\Str::words($kpt->content, $limit = 5) }}
              </a>
              </td>
              <td>
              <a data-toggle="tooltip" title="{{ $kpt->action }}">
                {{ \Illuminate\Support\Str::words($kpt->action, $limit = 5) }}
              </a>
              </td>
              <td>
              @if ($kpt->status != 0)
                {{ $status[$kpt->status] }}
              @endif
              </td>
            </tr>
          @empty
            <tr>
                <td colspan="14">
                    <p>empty</p>
                </td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
      <div class="page-right">
        {{ $list_kpt->appends(array_except(Request::query(), 'page'))->links() }}
        </div><!-- page-right -->
    </div>
  </div>
</div>
@stop

@section('modal')
<div id="deleteModal_risk" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>Delete</h4>
            </div>
            <div class="modal-body">
                <p>Do you want delete?</p>
            </div>
            <div class="modal-footer">
                <form method="post" action="{{Route('risk.postDelete',$project->id)}}">
                {{csrf_field()}}
                <input type="hidden" value="0" id="risk-id" name="id" />
                <button class="btn btn-sm btn-success" name="deletey" type="submit">Delete</button>
                <button class="btn btn-sm btn-danger" data-dismiss="modal" type="button">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="deleteModal_kpt" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>Delete KPT</h4>
            </div>
            <div class="modal-body">
                <p>Do you want delete KPT?</p>
            </div>
            <div class="modal-footer">
                <form method="post" action="{{ Route('kpt.post.delete', $project->id) }}">
                {{csrf_field()}}
                <input type="hidden" value="0" id="kpt-id" name="id" />
                <button class="btn btn-sm btn-success" name="deletey" type="submit">Delete</button>
                <button class="btn btn-sm btn-danger" data-dismiss="modal" type="button">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('script')
    <script type="text/javascript" src="{{ asset('/js/project/view_project.js') }}"></script>
@stop
