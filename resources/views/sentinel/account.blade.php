@extends('layouts.master')

@section('title', 'Profile')
@section('breadcrumbs', 'Profile')

@section('content')
<div class="main-header clearfix">
    <div class="page-title">
        <h3 class="no-margin">User</h3>
    </div>
    <div class="pull-right">
        <a class="btn btn-success" href="#">Edit</a>
        <a class="btn btn-danger" href="#">List</a>
    </div><!-- row -->
</div>

<div class="panel panel-default">
    <div class="panel-body">
        <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped">
            <tbody>
                <tr>
                    <th></th>
                    <th>
                            <img  alt="Ảnh" style="width:225px;height:225px;">
                    </th>
                </tr>
                <tr>
                    <th class="text-center">Name</th>
                    <th> aaa</th>
                </tr>
            </tbody>
        </table>
    </div><!-- /table-responsive -->
</div><!-- /panel-body -->
</div><!-- /panel -->
@stop
