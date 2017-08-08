@extends('layouts.master')
@section('title','List ticket')
@section('breadcrumbs','List ticket')
@section('style')

@stop
@section('content')
    <div class="padding-md">
        <div class="panel panel-default">
            <div class="panel-heading" id="form_heading">List Ticket</div>
            <div class="panel-body" id="form_body">
                <form method="get" action="{{ URL::route('project.cost.index') }}" id="search_form" class="form-horizontal" enctype="multipart/form-data">
                    <div class="info-left col-md-6">
                        <div class="form-group">
                            <label class="col-md-4 control-label text-left" for="version">Version</label>
                            <div class="col-md-6">
                                <select class="form-control" name="version">
                                    <option value=""> -- All -- </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label text-left" for="function">Function</label>
                            <div class="col-md-6">
                                <select class="form-control" name="function">
                                    <option value=""> -- All -- </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label text-left" for="tracker">Tracker</label>
                            <div class="col-md-6">
                                <select class="form-control" name="tracker">
                                    <option value=""> -- All -- </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label text-left" for="status">Status</label>
                            <div class="col-md-6">
                                <select class="form-control" name="status">
                                    <option value=""> -- All -- </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="info-right col-md-6">
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="createdBy">Created by</label>
                            <div class="col-md-6">
                                <select class="form-control" name="createdBy">
                                    <option value=""> -- All --</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="author">Author</label>
                            <div class="col-md-6">
                                <select class="form-control" name="author">
                                    <option value=""> -- All --</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="assignTo">Assign to</label>
                            <div class="col-md-6">
                                <select class="form-control" name="assignTo">
                                    <option value=""> -- All --</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary">Search</button>
                        <button type="button" class="btn btn-danger" id="configreset">Reset</button>
                    </div>
                </form>
            </div><!-- /panel-body -->
            <hr/>
            <div class="panel-body">
                <div class="panel-body">
                    <div class="col-md-6">
                        <span class="text-left"><strong>Total number of records: No Result</strong></span>
                    </div>
                    <div class="col-md-6">
                        <form method="get" class="pull-right">
                            <input type="hidden" name="version"   value="{{Request::get('version','')}}">
                            <input type="hidden" name="function"  value="{{Request::get('function','')}}">
                            <input type="hidden" name="tracker"   value="{{Request::get('tracker','')}}">
                            <input type="hidden" name="status"    value="{{Request::get('status','')}}">
                            <input type="hidden" name="createdBy" value="{{Request::get('createdBy','')}}">
                            <input type="hidden" name="author"    value="{{Request::get('author','')}}">
                            <input type="hidden" name="assignTo"  value="{{Request::get('assignTo','')}}">
                            <label for="choose_item">Item display on page: &nbsp; &nbsp;</label>
                            <select id="choose_item" name="limit" class="form-control input-md inline-block" size="1" onchange="this.form.submit()">

                            </select>
                        </form>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive" id="scroll-x">
                        <table class="table table-bordered table-hover table-striped" id="">
                            <thead>
                              <tr>
                                <th class="text-center">Process Tool ID</th>
                                <th class="text-center">Version/Release</th>
                                <th class="text-center">Page/Function</th>
                                <th class="text-center">Tracker</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Created by</th>
                                <th class="text-center">Author</th>
                                <th class="text-center">Assign to</th>
                              </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <div class="text-right">
                        </div>
                    </div><!-- /table-responsive -->
                </div><!-- /panel-body -->
            </div><!-- /panel-body -->
        </div><!-- /panel-default -->
    </div><!-- /padding-md -->
@stop

@section('modal')

@stop

@section('script')

@stop
