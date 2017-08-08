@extends('layouts.master')
@section('title','Organization')
@section('breadcrumbs','Organization')
@section('style')
    <link href="{{ asset('css/custom/list.department.css') }}" rel="stylesheet">
@stop
@section('content')
<!-- <div> -->
<!--     @if ($errors->any()) -->
<!--         <div class="alert alert-danger alert-block"> -->
<!--             <button type="button" class="close" data-dismiss="alert"> -->
<!--                 <i class="fa fa-times"></i> -->
<!--             </button> -->
<!--             @if ($message = $errors->first(0, ':message')) -->
<!--                  {{ $message }} -->
<!--             @else -->
<!--                 Đã xảy ra lỗi! -->
<!--             @endif -->
<!--         </div> -->
<!--     @endif -->
<!-- </div> -->
<div class="padding-md">
    <div class="main-header clearfix">
        <div class="page-title">
            <h3 class="no-margin">Organization</h3>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="padding-md">
                <h4 class="headline clearfix">
                    <div class="pull-right">
                      <a class="btn btn-success" href="{{ URL::route('department.create') }}">Create</a>
                      <a class="btn btn-primary" href="/get_department">Sync</a>
                    </div>
                </h4>
                <span class="line"></span>
                <div class="row">
                    <div class="col-sm-12">
                        <div>
                            <table class="table table-bordered table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">Parent group</th>
                                        <th class="text-center">Manager</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($departments) > 0)
                                        @foreach($departments as $de)
                                            <tr>
                                                <td id="de-name">{{ $de->name }}</td>
                                                <td></td>
                                                <td id="manager-name">{{ Helpers::getDepartmentManager($de->manager_id) }}</td>
                                                <td id="action-icon">
                                                    <a href="{{ URL::route('department.edit',$de->id) }}"><i class="fa fa-edit fa-lg"></i></a>|
                                                    <a href="javascript:void(0);" name="{{ $de->id }}" dataId="{{$de->id}}" class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                </td>
                                            </tr>
                                            @if(count($divisions) > 0)
                                                @foreach($divisions as $di)
                                                    @if($di->parent_id == $de->id)
                                                        <tr>
                                                            <td id="di-name">
                                                                <i class="fa fa-minus" aria-hidden="true"></i>&nbsp;&nbsp;
                                                                {{ $di->name }}
                                                            </td>
                                                            <td>{{ $de->name }}</td>
                                                            <td id="manager-name">{{ Helpers::getDepartmentManager($di->manager_id) }}</td>
                                                            <td id="action-icon">
                                                                <a href="{{ URL::route('department.edit',$di->id) }}"><i class="fa fa-edit fa-lg"></i></a>|
                                                                <a href="javascript:void(0);" name="{{ $di->id }}" dataId="{{$di->id}}" class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                            </td>
                                                        </tr>
                                                        @if(count($teams) > 0)
                                                            @foreach($teams as $team)
                                                                @if($team->parent_id == $di->id)
                                                                    <tr>
                                                                        <td id="team-name">
                                                                            <i class="fa fa-minus" aria-hidden="true"></i>
                                                                            <i class="fa fa-minus" aria-hidden="true"></i>&nbsp;&nbsp;
                                                                            {{ $team->name }}
                                                                        </td>
                                                                        <td>{{ $di->name }}</td>
                                                                        <td id="manager-name">{{ Helpers::getDepartmentManager($team->manager_id) }}</td>
                                                                        <td id="action-icon">
                                                                            <a href="{{ URL::route('department.edit',$team->id) }}"><i class="fa fa-edit fa-lg"></i></a>|
                                                                            <a href="javascript:void(0);" name="{{ $team->id }}" dataId="{{$team->id}}" class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('modal')
<div id="deleteModal" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>Delete department</h4>
            </div>
            <div class="modal-body">
                <p>Do you want delete department?</p>
            </div>
            <div class="modal-footer">
                <form method="post" action="{{ Route('department.delete') }}">
                {{csrf_field()}}
                <input type="hidden" value="0" id="data-id" name="id" />
                <input type="hidden" value="{{Request::get('page', 1)}}" name="page" />
                <button class="btn btn-sm btn-success" name="deletey" type="submit">Delete</button>
                <button class="btn btn-sm btn-danger" data-dismiss="modal" type="button">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
@section('script')
<script type="text/javascript" src="{{ asset('/js/project_version/version.js') }}"></script>
@stop

