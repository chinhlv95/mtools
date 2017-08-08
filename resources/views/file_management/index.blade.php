@extends('layouts.master')
@section('title', 'File Management')

@section('breadcrumbs','File Management')
@section('style')
    <link href="{{ asset('css/custom/date-form.css') }}" rel="stylesheet">
@stop
@section('content')
<div class="padding-md">
    <div class="alert alert-danger hide" id="errorMessage">
        <button type="button" class="close closeMessage">
            <i class="fa fa-times"></i>
        </button>
        <span id="message"></span>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" id="form_heading">Admin Setting - File Management</div>
<!--            <div class="panel-body" id="form_body">
                <form method="get" id="search_form" class="form-horizontal" enctype="multipart/form-data">
                        <input type="hidden" name="limit" value="{{Request::get('limit',10)}}">
                        <div class="col-md-3 text-right">
                                <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                </form>
            </div> /panel-body -->
            <div class="panel-body">
              <div class="panel-body">
                <div class="col-md-6">
                        <span class="text-left"><strong>Total number of records: {{count($files)}}</strong></span>
                </div>
                <div class="col-md-6">
                    <form method="get" class="pull-right">
                        <label for="choose_item">Item display on page: &nbsp; &nbsp;</label>
                        <select id="choose_item" name="limit" class="form-control input-sm inline-block" size="1" onchange="this.form.submit()">
                            @foreach($paginate_number as $key=>$value)
                                <option value="{{$key}}" <?php if(Request::get('limit') == $value) echo 'selected';?>>{{$value}}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
                <div class="table-responsive" id="scroll-x">
                     <table class="table table-bordered table-hover table-striped tbl-project" id="responsiveTable">
                        <thead>
                        <tr>
                            <th width="10%" class="text-center">No</th>
                            <th width="15%" class="text-center">Date</th>
                            <th width="15%" class="text-center">File Name</th>
                            <th width="15%" class="text-center">User Import</th>
                            <th width="15%" class="text-center">Team</th>
                            <th width="10%" class="text-center">Type</th>
                            <th width="10%" class="text-center">Project Name</th>
                            <th width="10%" class="text-center">Download File</th>
                        </tr>
                        </thead>
                        <tbody class="tbody">
                            @if($files != null)
                            @foreach($files as $key => $values)
                                        <tr>
                                            <td rowspan="{{count($values)}}">{{++$stt}}</td>
                                            <td rowspan="{{count($values)}}">{{$key}}</td>
                                @foreach($values as $file)
                                            <td class="text-left">{{$file['name']}}</td>
                                            <td class="text-left">{{$file['last_name']." ".$file['first_name']}}</td>
                                            <td class="text-left">{{$file['departments_name']}}</td>
                                            <td class="text-left">{{$file['type']}}</td>
                                            <td class="text-left">{{$file['project_name']}}</td>
                                            <td>
                                                @if($file['type'] == 'Import')
                                                <a href='{{ url('uploads/importFile/'.$file['name']) }}'>Download File</a>
                                                @else
                                                <a href='{{ url('uploads/exportFile/'.$file['name']) }}'>Download File</a>
                                                @endif
                                            </td>
                                        </tr>
                                @endforeach
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div><!-- /table-responsive -->
                 <div class="text-right">
                     <div class="page-right padding-md">
                        {{ $files->appends([
                              'limit'         => Request::get('limit',10)
                            ])->links() 
                        }}
                       </div>
                </div>
            </div>
            <hr>
        </div><!-- /panel-default -->
    </div><!-- /padding-md -->
</div>
@stop
@section('modal')
<div id="editModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>Change role user</h4>
            </div>
            <div class="modal-body roles_body" style="max-height: calc(100vh - 120px);overflow-y: auto;">

            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
@stop