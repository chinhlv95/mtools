@extends('layouts.master')
@section('title', 'User Mapping')

@section('breadcrumbs','User Mapping')
@section('style')
    <link href="{{ asset('css/custom/date-form.css') }}" rel="stylesheet">

    <link href="{{ asset('css/jquery-ui.min.css') }}" rel="stylesheet">
@stop
@section('content')
<div class="panel panel-default">
    <div class="panel-heading" id="form_heading">Mapping User By Emails</div>
    <!-- common area -->
    <div class="panel-body">
        <!-- search area -->
        <form action="{{ URL::route('user-mapping.show') }}" method="get" class="form-horizontal">
             <div class="form-group">
                        <label class="col-md-3 control-label text-left" for="source">Source</label>
                        <div class="col-md-6">
                            <select class="form-control" name="source" id="source">
                            @if(Request::get('source') == null)
                                @foreach($sources as $key=>$value)
                                    <option value="{{$key}}" <?php if($key == 5) echo "selected";?>>{{$value}}</option>
                                @endforeach
                            @else
                                @foreach($sources as $key=>$value)
                                    <option value="{{$key}}" <?php if(Request::get('source') == $key) echo "selected";?>>{{$value}}</option>
                                @endforeach
                            @endif
                            </select>
                        </div>
                </div>
                <div class="form-group">
                        <label class="col-md-3 control-label text-left" for="searchUser">Search</label>
                        <div class="col-md-6">
                           <input type="text" name="search" class="form-control" id="searchUser" value="{{Request::get('search')}}" placeholder="Enter email or name or member code">
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
    <hr>
    <!-- data display area -->
    <div class="panel-body">
        <div class="panel-body">
                <div class="col-md-6">
                    @if(count($users) == 0)
                        <span class="text-left"><strong>Total number of records: No Result</strong></span>
                    @else
                        <span class="text-left"><strong>Total number of records: {{ $users->total() }}</strong></span>
                    @endif
                </div>
                <div class="col-md-6">
                    <form method="get" class="pull-right">
                        <input type="hidden" name="source" value="{{Request::get('source','')}}">
                        <input type="hidden" name="search" value="{{Request::get('search','')}}">
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
            <div class="panel panel-default">
                <!-- table data -->
                <div class="panel-body">
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="totalSummary">
                            <div class="table-responsive" id="scroll-x">
                                <table class="table table-bordered table-hover table-striped projectReportTable" id="responsiveTable">
                                 <caption><h5>Mapping User By Email</h5></caption>
                                    <thead>
                                        <tr>
                                                <th with="10%">No</th>
                                                <th with="20%">Member Code</th>
                                                 <th with="20%">Full Name</th>
                                                <th with="30%">Email Need Mapping</th>
                                                <th with="30%">Main Email</th>
                                                <th with="10%">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if($users != null)
                                            @foreach($users as $user)
                                                 <tr class="record">
                                                    <td>{{ ++$number }}</td>
                                                    <td  class="text-left">{{$user['member_code']}}</td>
                                                    <td>{{$user['full_name']}}</td>
                                                    <td>{{$user['email']}}</td>
                                                    <?php $mainEmail = Helpers::getMainEmail($user['related_id']);?>
                                                    <td>{{$mainEmail}}</td>
                                                    <td class="action text-center form-inline">
                                                     <a data-toggle="modal" class="update" mappingEmail="{{$user['email']}}" mainEmail = "{{$mainEmail}}" userId = "{{$user['id']}}" ><i class="fa fa-pencil fa-lg"></i></a>
                                                    </td>

                                                </tr>
                                             @endforeach
                                         @else
                                         <tr><td class="text-left" colspan="17">Empty Data!</td></tr>
                                     @endif
                                    </tbody>
                                </table>

                                <div class="text-right">
                                    {{ $users->appends([
                                          'source'          => Request::get('source',''),
                                          'search'          => Request::get('search',''),
                                          'limit'         => Request::get('limit',10)
                                      ])->links() }}
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
@stop
@section('modal')
<div id="emailMappingModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center"><strong>User Mapping</strong></h4>
            </div>
            <div class="modal-body">
                 <div class="panel-body" id="form_body">
                    <form method="post" id="frm_update" action="{{ URL::route('user-mapping.update')}}" class="form-horizontal">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label class="col-lg-3 control-label" for="source">
                                Source
                            </label>
                            <div class="col-lg-6">
                                <input type="text" name="source" class="form-control" id="source" value="{{$sourceName}}" disabled="disabled">
                                <input type="hidden" name="source" value="{{Request::get('source','')}}">
                                <input type="hidden" name="search" value="{{Request::get('search','')}}">
                            </div>

                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label" for="main_email">
                                Main Email
                            </label>
                            <div class="col-lg-6">
                                <input type="email" name="main_email" class="form-control" id="main_email">
                                <input type="hidden" id="main_email_hidden" class="form-control" value="{{$autoData}}" disabled="disabled">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">
                                Email Need Mapping
                            </label>
                            <div class="col-lg-6">
                                <input type="email" name="related_email" class="form-control" id="related_email" value="" readonly="readonly">
                                <input type="hidden" name="user_id" id="user_id" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="col-lg-6 text-right">
                                <button type="submit" class="btn btn-primary btn-update-source">Update</button>
                            </div>
                            <div class="col-lg-6 text-left">
                                <button class="btn btn-sm btn-danger" data-dismiss="modal" type="button">Close</button>
                            </div>
                        </div>
                     </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('script')
    <script type="text/javascript" src="{{ asset('js/select_date/select.date.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/quality_report/tooltip_report.js')}}"></script>
    <script type="text/javascript" src="{{ asset('/js/jquery_ui/jquery-ui.js')}}"></script>
    <script type="text/javascript" src="{{ asset('/js/user_mapping/autocomplete_email.js')}}"></script>
@stop