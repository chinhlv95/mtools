<!DOCTYPE html>
<html>
    <?php $path = public_path(); ?>
    <body>
        @if($requestReportType == 'summary' || empty($requestReportType))
        <h1>By root cause</h1>
        <table>
            <thead>
                <tr>
                    <th width="100" >Root cause</th>
                    @foreach($desToolTips as $key=>$value)
                    <th width="100"><a>{{$key}}</a></th>
                    @endforeach
                    <th width="10">Grand Total</th>
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
                $low = 0;
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
                <tr>
                    <td >{{ App\Models\RootCause::find($value)->name }}</td>
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

    <td><h1>Number defect report by status</h1></td>
    <table>
        <thead>
            <tr>
                <th width="15%" >Status</th>
                @foreach($desToolTips as $key=>$value)
                <th width="15%"><a>{{$key}}</a></th>
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
            $low = 0;
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
            <tr >
                <td>{{ App\Models\Status::find($value)->name }}</td>
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
                <td>{{$totalCol1}}</td>
                <td>{{$totalCol2}}</td>
                <td>{{$totalCol3}}</td>
                <td>{{$totalCol4}}</td>
                <td>{{$totalCol5}}</td>
                <td>{{$totalCol1 + $totalCol2 + $totalCol3 + $totalCol4 +$totalCol5}}</td>
            </tr>
        </tbody>
    </table>

    <h1>By Who found defect</h1>
    <table>
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
            $low = 0;
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
            <tr >
                <?php $name = explode('@', $value); ?>
                <td>{{ App\Models\User::find($value)->user_name }}</td>
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
    <h1>By Who fix defect</h1>
    <table>
        <thead>
            <tr>
                <th width="15%" >Member</th>
                @foreach($desToolTips as $key=>$value)
                <th width="15%"><a>{{$key}}</a></th>
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
            $low = 0;
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
            <tr>
                <?php $name = explode('@', $value); ?>
                <td>{{ App\Models\User::find($value)->user_name }}</td>
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
    <td><h1>By Who make defect</h1></td>
    <table>
        <thead>
            <tr>
                <th width="15%" >Member</th>
                @foreach($desToolTips as $key=>$value)
                <th width="15%"><a>{{$key}}</a></th>
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
            $low = 0;
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
            <tr>
                <?php $name = explode('@', $value); ?>
                <td>{{ App\Models\User::find($value)->user_name }}</td>
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
    @endif
    @if($requestReportType == 'time' || empty($requestReportType))
    <td><h1>Number defect report by bug and UAT bug</h1></td>
    <table>
        <thead>
            <tr>
                <th >Bug type</th>
                <th >Total bug</th>
                @if(Request::get('units_time') == 'day' || empty(Request::get('units_time','')))
                @for ($i = strtotime($start_date); $i <= strtotime($end_date); $i = strtotime("+1 day", $i))
                <?php $weekend = date("w", $i); ?>
                @if($weekend == 6 || $weekend == 0)
                <th>{{ date("d/m", $i) }}</th>
                @else
                <th>{{ date("d/m", $i) }}</th>
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
            <tr>
                <td>Bug</td>
                <?php $total_bug = 0; ?>
                <td>{{array_sum($array_bug)}}</td>
                @foreach($array_bug as $key=>$value)
                <td>{{$total_bug += $value}}</td>
                @endforeach
            </tr>
            <tr>
                <td >UAT bug</td>
                <?php $total_uat = 0; ?>
                <td>{{array_sum($array_uat)}}</td>
                @foreach($array_uat as $key=>$value)
                <td>{{$total_uat += $value}}</td>
                @endforeach
            </tr>
        </tbody>
    </table>
    <td><h1>KPI defect</h1></td>
    <table>
        <thead>
            <tr>
                <th></th>
                @if(Request::get('units_time') == 'day' || empty(Request::get('units_time','')))
                @for ($i = strtotime($start_date); $i <= strtotime($end_date); $i = strtotime("+1 day", $i))
                <?php $weekend = date("w", $i); ?>
                @if($weekend == 6 || $weekend == 0)
                <th>{{ date("d/m", $i) }}</th>
                @else
                <th>{{ date("d/m", $i) }}</th>
                @endif
                @endfor
                @elseif(Request::get('units_time') == 'week')
                <?php $period = Helpers::findWeekInPeriodOfTime($start_date, $end_date); ?>
                @for ($i = strtotime($period->start->format('Y-m-d H:i:s')); $i <= strtotime($period->end->format('Y-m-d H:i:s')); $i = strtotime("+7 day", $i))
                <th class="uat_bug">W {{ date("W/Y", $i) }}</th>
                @endfor
                @elseif(Request::get('units_time') == 'month')
                @for ($i = strtotime($start_date); $i <= strtotime($end_date); $i = strtotime("+1 month", $i))
                <th>{{ date("M/Y", $i) }}</th>
                @endfor
                @elseif(Request::get('units_time') == 'year')
                @for ($i = strtotime($start_date); $i <= strtotime($end_date); $i = strtotime("+1 year", $i))
                <th>{{ date("Y", $i) }}</th>
                @endfor
                @endif
            </tr>
        </thead>
        <tbody>
            <tr >
                <td >New</td>
                @foreach($array_found as $key=>$value)
                <td >{{$value}}</td>
                @endforeach
            </tr>
            <tr >
                <td >Close and reject</td>
                <?php $total_close = 0; ?>
                @foreach($array_close as $key=>$value)
                <td >{{$total_close += $value}}</td>
                @endforeach
            </tr>
            <tr>
                <td >Remaining</td>
                <?php $total_found = 0; ?>
                @foreach($array_found as $key=>$value)
                <td >{{$total_found += $array_found[$key] - $array_close[$key]}}</td>
                @endforeach
            </tr>
        </tbody>
    </table>
    @endif
</body>
</html>