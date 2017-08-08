<?php
    $kloc = Helpers::writeNumber($dataProjects->loc, 1000);
    $kTestcase = Helpers::writeNumber($dataProjects->tested_tc, 1000);
    $workload = Helpers::writeNumber($dataProjects->actual_hour, $mm);
    $bugBeforeRelease = Helpers::writeNumberInPercent($dataProjects->weighted_bug, ($dataProjects->weighted_bug + $dataProjects->weighted_uat_bug));
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4>Project name :{{$dataProjects->project_name}} | Team :{{$dataProjects->department_name}} | BSE :{{$dataProjects->last_name}} {{$dataProjects->first_name}}</h4>
</div>
<div class="modal-body col-md-12" id="content-project">
    <div class="col-md-12">
        <div class="panel panel-default bg-success">
            <div class="panel-body">
                <h3>Project Information</h3>
            </div>
            <div class="col-md-12" style="padding: 0px;">
                <div class="list-group col-md-4">
                    <a class="list-group-item">
                        <span class="m-left-xs">Project name</span>
                        <span class="badge badge-info">{{$dataProjects->project_name}}</span>
                    </a>
                    <a class="list-group-item">
                        <span class="m-left-xs">Status</span>
                        <span class="badge badge-info">{{ $status_id[$dataProjects->status]}}</span>
                    </a>
                    <a class="list-group-item">
                        <span class="m-left-xs">Plan Start Date</span>
                        <span class="badge badge-info">{{ date('d/m/Y',strtotime(str_replace('/', '-', $dataProjects->plant_start_date)))}}</span>
                    </a>
                    <a class="list-group-item">
                        <span class="m-left-xs">Plan Effort( hour)</span>
                        <span class="badge badge-info">{{ round($estimate_project,1)}}</span>
                    </a>
                </div><!-- /list-group -->
                <div class="list-group col-md-4">
                    <a class="list-group-item">
                        <span class="m-left-xs">BSE/ Project Manager</span>
                        <span class="badge badge-info">{{$dataProjects->last_name}} {{$dataProjects->first_name}}</span>
                    </a>
                    <a class="list-group-item">
                        <span class="m-left-xs">Project language</span>
                        <span class="badge badge-info">{{ $language[$dataProjects->language_id]}}</span>
                    </a>
                    <a class="list-group-item">
                        <span class="m-left-xs">Plan End Date</span>
                        <span class="badge badge-info">{{date('d/m/Y',strtotime(str_replace('/', '-', $dataProjects->plant_end_date)))}}</span>
                    </a>
                    <a class="list-group-item">
                        <span class="m-left-xs">Actual Effort( hour)</span>
                        <span class="badge badge-info">{{ round($actual_project,1) }}</span>
                    </a>
                </div><!-- /list-group -->
                <div class="list-group col-md-4">
                <a class="list-group-item">
                    <span class="m-left-xs">Unit test</span>
                    <span class="badge badge-info">{{ $resource[$dataProjects->unit_test] }}</span>
                </a>
                <a class="list-group-item">
                    <span class="m-left-xs">Test first</span>
                    <span class="badge badge-info">{{ $resource[$dataProjects->test_first]}}</span>
                </a>
                <a class="list-group-item">
                    <span class="m-left-xs">Detail design</span>
                    <span class="badge badge-info">{{ $resource[$dataProjects->detail_design]}}</span>
                </a>
                <a class="list-group-item">
                    <?php $effort = $estimate_project - $actual_project;?>
                    <span class="m-left-xs">Effort Deviation( hour)</span>
                    <span class="badge {{$effort >= 0 ? 'badge-info' : 'badge-danger' }}">{{round($effort,1)}}</span>
                </a>
            </div><!-- /list-group -->
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default bg-success">
            <div class="panel-body">
                <h3>Productivity</h3>
            </div>
            <div class="list-group">
                <a class="list-group-item">
                    <span class="m-left-xs">KLOC/mm</span>
                    <span class="badge badge-danger">{{ round(Helpers::writeNumber($kloc, $workload),1)}}</span>
                </a>
                <a class="list-group-item">
                    <span class="m-left-xs">TC/mm</span>
                    <span class="badge badge-danger">{{ round(Helpers::writeNumber($dataProjects->tested_tc, $workload),1)}}</span>
                </a>
                <a class="list-group-item">
                    <span class="m-left-xs">TASK/mm</span>
                    <span class="badge badge-danger">{{ round(Helpers::writeNumber($dataProjects->task, $workload),1)}}</span>
                </a>
            </div><!-- /list-group -->
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default bg-success">
            <div class="panel-body">
                <h3>Quality</h3>
            </div>
            <div class="list-group">
                <a class="list-group-item">
                    <span class="m-left-xs">Bug/KLOC</span>
                    <span class="badge badge-warning">{{ round(Helpers::writeNumber($dataProjects->weighted_bug, $kloc),1)}}</span>
                </a>
                <a class="list-group-item">
                    <span class="m-left-xs">Bug after release/KLOC</span>
                    <span class="badge badge-warning">{{ round(Helpers::writeNumber($dataProjects->weighted_uat_bug, $kloc),1) }}</span>
                </a>
                <a class="list-group-item">
                    <span class="m-left-xs">Bug / 1000TC</span>
                    <span class="badge badge-warning">{{ round(Helpers::writeNumber($dataProjects->weighted_bug, $kTestcase),1) }}</span>
                </a>
                <a class="list-group-item">
                    <span class="m-left-xs">% Bug before release</span>
                    <span class="badge badge-warning">{{ round($bugBeforeRelease,1) }}</span>
                </a>
                <a class="list-group-item">
                    <span class="m-left-xs">Bug / mm</span>
                    <span class="badge badge-warning">{{ round(Helpers::writeNumber($dataProjects->weighted_bug, $workload),1) }}</span>
                </a>
            </div><!-- /list-group -->
        </div>
    </div>
    <div class="col-md-12" style="overflow-x: scroll;">
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped" id="">
            <thead>
                    <tr>
                        <th rowspan="2" ></th>
                        <th rowspan="2">Unit</th>
                        @foreach ($month_project_kpi as $data)
                           <th colspan="2">
                               <div class="" data-toggle="tooltip" title="{{ $data['name'] }}">
                                    {{ str_limit($data['name'], 16) }}
                               </div>
                           </th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach ($month_project_kpi as $data)
                            <th>Target</th>
                            <th>Actual</th>
                        @endforeach
                    </tr>
            </thead>
           <tbody>
               <tr>
                   <td class="titleKpi">Cost</td>
                   <td class="titleKpi"></td>
                    <td class="titleKpi" colspan="{{ count($month_project_kpi)*2 }}"></td>
               </tr>
                <tr class="name_cost_efficiency">
                    <td class="name"><a href="#" data-toggle="tooltip" data-placement="right" data-html="true"
                            title="{{trans('message.kpi_tooltip.cost_efficiency.obj')}} &#13{{trans('message.kpi_tooltip.cost_efficiency.formula')}}">Cost efficiency</a>
                    </td>
                    <td>
                        <div>%</div>
                    </td>
                        @foreach ($month_project_kpi as $data)
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
                        @endforeach
                </tr>
                <tr class="name_fix_cost">
                    <td class="name"><a href="#" data-toggle="tooltip" data-placement="right" data-html="true"
                            title="{{trans('message.kpi_tooltip.fixing_bug_cost.obj')}} &#13{{trans('message.kpi_tooltip.fixing_bug_cost.formula')}}">Fixing cost</a>
                    </td>
                    <td>
                       <div>%</div>
                    </td>
                        @foreach ($month_project_kpi as $data)
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
                </tr>
                <tr>
                    <td class="titleKpi">Quality</td>
                    <td class="titleKpi"></td>
                    <td class="titleKpi" colspan="{{ count($month_project_kpi)*2 }}"></td>
                </tr>
                <tr class="name_leakage">
                    <td class="name"><a href="#" data-toggle="tooltip" data-placement="right" data-html="true"
                            title="{{trans('message.kpi_tooltip.leakage.obj')}} &#13{{trans('message.kpi_tooltip.fixing_bug_cost.formula')}}">Leakage</a>
                    </td>
                    <td>
                        <div>Wdef/mm</div>
                    </td>
                        @foreach ($month_project_kpi as $data)
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
                </tr>
                <tr class="name_UAT_bug_number">
                    <td class="name"><a href="#" data-toggle="tooltip" data-placement="right" data-html="true"
                            title="{{trans('message.kpi_tooltip.bug_after_release_num.obj')}} &#13{{trans('message.kpi_tooltip.bug_after_release_num.formula')}}">Bug after release (number)</a>
                    </td>
                    <td>
                        <div>Number</div>
                    </td>
                        @foreach ($month_project_kpi as $data)
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
                </tr>
                <tr class="name_UAT_bug_weight">
                    <td class="name"><a href="#" data-toggle="tooltip" data-placement="right" data-html="true"
                            title="{{trans('message.kpi_tooltip.bug_after_release_wei.obj')}} &#13{{trans('message.kpi_tooltip.bug_after_release_wei.formula')}}">Bug after release (weight)</a>
                    </td>
                    <td>
                        <div>Weight</div>
                    </td>
                        @foreach ($month_project_kpi as $data)
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
                </tr>
                <tr class="name_customer_survey">
                    <td class="name"><a href="#" data-toggle="tooltip" data-placement="right" data-html="true"
                            title="{{trans('message.kpi_tooltip.customer_survey.obj')}} &#13{{trans('message.kpi_tooltip.customer_survey.formula')}}">Customer survey</a>
                    </td>
                    <td>
                        <div>Point</div>
                    </td>
                        @foreach ($month_project_kpi as $data)
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
                </tr>
                <tr class="name_defect_remove_efficiency">
                    <td class="name"><a href="#" data-toggle="tooltip" data-placement="right" data-html="true"
                            title="{{trans('message.kpi_tooltip.defect_remove_efficiency.obj')}} &#13{{trans('message.kpi_tooltip.defect_remove_efficiency.formula')}}">Defect remove efficiency</a>
                    </td>
                    <td>
                        <div>%</div>
                    </td>
                        @foreach ($month_project_kpi as $data)
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
                </tr>
                <tr class="name_defect_rate">
                    <td class="name"><a href="#" data-toggle="tooltip" data-placement="right" data-html="true"
                            title="{{trans('message.kpi_tooltip.defect_rate.obj')}} &#13{{trans('message.kpi_tooltip.defect_rate.formula')}}">Defect rate</a>
                    </td>
                    <td>
                        <div>Wdef/mm</div>
                    </td>
                        @foreach ($month_project_kpi as $data)
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
                </tr>
                <tr>
                    <td class="titleKpi">Productivity</td>
                    <td class="titleKpi"></td>
                    <td class="titleKpi" colspan="{{ count($month_project_kpi)*2 }}"></td>
                </tr>
                <tr class="name_code_productivity">
                    <td class="name"><a href="#" data-toggle="tooltip" data-placement="right" data-html="true"
                            title="{{trans('message.kpi_tooltip.code_productivity.obj')}} &#13{{trans('message.kpi_tooltip.code_productivity.formula')}}">Code productivity</a>
                    </td>
                    <td>
                        <div>LOC/mm</div>
                    </td>
                        @foreach ($month_project_kpi as $data)
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
                </tr>
                <tr class="name_create_test_productivity">
                    <td class="name"><a href="#" data-toggle="tooltip" data-placement="right" data-html="true"
                            title="{{trans('message.kpi_tooltip.created_test_case_productivity.obj')}} &#13{{trans('message.kpi_tooltip.created_test_case_productivity.formula')}}">Create testcase productivity</a>
                    </td>
                    <td>
                        <div>TC/mm</div>
                    </td>
                        @foreach ($month_project_kpi as $data)
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
                </tr>
                <tr class="name_tested_productivity">
                    <td class="name"><a href="#" data-toggle="tooltip" data-placement="right" data-html="true"
                            title="{{trans('message.kpi_tooltip.tested_producactivity.obj')}} &#13{{trans('message.kpi_tooltip.tested_producactivity.formula')}}">Tested productivity</a>
                    </td>
                    <td>
                        <div>Tested/mm</div>
                    </td>
                        @foreach ($month_project_kpi as $data)
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
                </tr>
            </tbody>
        </table>
    </div>
    </div>
</div>
<div class="modal-footer">
</div>