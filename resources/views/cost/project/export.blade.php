<!DOCTYPE html>
<html>
    <body>
    @if((Request::get('reportType') == 'summary_report') || (Request::get('reportType') == null))
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                              <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Project</th>
                                <th class="text-center">Total effort</th>
                                <th class="text-center">Member ID</th>
                                <th class="text-center">Full name</th>
                                <th class="text-center">Role</th>
                                <th class="text-center">Location</th>
                                <th class="text-center">Work load</th>
                              </tr>
                            </thead>
                            <tbody>
                                @if($listProjects != null)
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
                                                                <td  rowspan ="{{ $count['count'] }}">{{ $total }}</td>
                                                            @else
                                                                <td rowspan ="{{ $count['count'] }}">0</td>
                                                            @endif
                                                            <?php $flag = 1; ?>
                                                        @else
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                        @endif

                                                        <td>{{ $member->user_name }}</td>
                                                        <td>{{ $member->last_name.' '.$member->first_name }}</td>
                                                        <td>{{ strtoupper($member->user_position) }}</td>
                                                        <td>{{ strtoupper(substr($member->email,-2)) }}</td>
                                                        <?php $personalTime = 0; ?>
                                                        <td class="text-center">
                                                            @foreach($entry as $e)
                                                                @if($e->email == $member->email && $eachProject->id == $e->all_project_id)
                                                                    <?php $personalTime = $e->actual_hour; ?>
                                                                @endif
                                                            @endforeach
                                                            {{ $member->personalTime }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @elseif($count['flagCount'] == 2)
                                            <tr>
                                                <td class="text-center td-gray-color">{{ ++$number }}</td>
                                                <td class="pj_name td-gray-color">
                                                    <a>{{ $eachProject->name }}</a>
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
        @elseif((Request::get('reportType') == 'position_report'))
        <table>
            <thead>
              <tr>
                <th class="text-center">No</th>
                <th class="text-center">Project</th>
                <th class="text-center">Total effort</th>
                <th class="text-center">BSE</th>
                <th class="text-center">BSE/JP</th>
                <th class="text-center">DEVL</th>
                <th class="text-center">DEV</th>
                <th class="text-center">QAL</th>
                <th class="text-center">QA</th>
                <th class="text-center">Comtor</th>
                <th class="text-center">JP support</th>
                <th class="text-center">Others</th>
              </tr>
            </thead>
            <tbody>
                @if($listProjects != null)
                    @foreach($listProjects as $eachProject)
                        <?php
                            $positionWork = Helpers::getActualTimeOfEachPosition($entry, $eachProject->id);
                            $total        = Helpers::entryOfEachProject($entry, $eachProject->id)
                        ?>
                        <tr>
                            <td>{{ ++$number }}</td>
                            <td class="t-left"><a>{{ $eachProject->name }}</a></td>
                            @if(isset($total))
                                <td class="text-center">{{ $total }}</td>
                            @else
                                <td class="text-center">0</td>
                            @endif
                            <td class="text-center">{{ $positionWork['bse'] }}</td>
                            <td class="text-center">{{ $positionWork['bsejp'] }}</td>
                            <td class="text-center">{{ $positionWork['devl'] }}</td>
                            <td class="text-center">{{ $positionWork['dev'] }}</td>
                            <td class="text-center">{{ $positionWork['qal'] }}</td>
                            <td class="text-center">{{ $positionWork['qa'] }}</td>
                            <td class="text-center">{{ $positionWork['comtor'] }}</td>
                            <td class="text-center">{{ $positionWork['jpsupport'] }}</td>
                            <td class="text-center">{{ $positionWork['other'] }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        @elseif(Request::get('reportType') == 'personal_detail_report')
            <div id="{{ $user }}"></div>
                                <div>
                                    <div id="name-of-user">
                                        <span>{{ $user }}</span>
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
        @elseif(Request::get('reportType') == 'personal_report')
            <table class="table table-bordered table-hover table-striped" id="responsiveTable">
                                <caption><h4>Report by personal</h4></caption>
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
                                                {{ $member->user_name }}
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
                                            @for($i=1;$i<(9-$count);$i++)
                                                <td></td>
                                            @endfor
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                @endif
    </body>
</html>