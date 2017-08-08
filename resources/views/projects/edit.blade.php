<?php
use function WebDriver\selected;
?>
@extends('layouts.master')
@section('title','Update project')
@section('breadcrumbs','Update project')
@section('style')
<link href="{{ asset('/css/project/project.css') }}" rel="stylesheet">
@stop @section('content')
<div class="main-header clearfix">
    <div class="page-title">
        <h3 class="no-margin">Update project</h3>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-body padding-md">
        <?php
        $plan_start = date ( 'd/m/Y', strtotime ( str_replace ( '/', '-', $project ['plant_start_date'] ) ) );
        $plan_end = date ( 'd/m/Y', strtotime ( str_replace ( '/', '-', $project ['plant_end_date'] ) ) );
        $actual_start = date ( 'd/m/Y', strtotime ( str_replace ( '/', '-', $project ['actual_start_date'] ) ) );
        $actual_end = date ( 'd/m/Y', strtotime ( str_replace ( '/', '-', $project ['actual_end_date'] ) ) );
        ?>
           <form method="POST"
            action="{{Route('project.update',['id'=>$project['id']])}}"
            accept-charset="UTF-8" class="form-horizontal"
            enctype="multipart/form-data" id="frmUpdateProject">
            {{ csrf_field() }}
            <div class="panel-heading title-update">Common information</div>
            <div class="form-group row">
                <div
                    class="col-md-6 {{ $errors->has('department') ? ' has-error' : '' }}">
                    <label for="title" class="col-sm-5 control-label">Department:<span
                        class="field-asterisk">*</span></label>
                    <div class="col-sm-7">
                        <select class="form-control" name="department"
                            id="department_id" role="department">
                            <option value="-1">-- All --</option>
                            @if(!empty($departments))
                            @if(old('department')) @foreach($departments
                            as $de)
                            <option value="{{$de['id']}}"
                                {{ (old('department') ==$de['id']) ? 'selected' : '' }} >{{$de['name']}}</option>
                            @endforeach @else @foreach($departments as
                            $de)
                            <option value="{{$de['id']}}"
                                {{ ($department_id==$de['id']) ? 'selected' : '' }} >{{$de['name']}}</option>
                            @endforeach @endif @endif
                        </select>
                        <p class="help-block">{{
                            ($errors->has('department') ?
                            $errors->first('department') : '') }}</p>
                    </div>
                </div>
                <div
                    class="col-md-6 {{ $errors->has('name') ? ' has-error' : '' }}">
                    <label for="title" class="col-sm-5 control-label">Project
                        Name:<span class="field-asterisk">*</span>
                    </label>
                    <div class="col-sm-7">
                        <input class="form-control input-sm"
                            maxlength="255" name="name" type="text"
                            value="{{old('name') ? old('name') : $project['name']}}">
                        <p class="help-block">{{ ($errors->has('name') ?
                            $errors->first('name') : '') }}</p>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div
                    class="col-md-6 {{ $errors->has('division') ? ' has-error' : '' }}">
                    <label for="title" class="col-sm-5 control-label">Division:<span
                        class="field-asterisk">*</span></label>
                    <div class="col-sm-7">
                        <select class="form-control" name="division"
                            id="division_id" role="division">
                            <option value="-1">-- All --</option>
                            @if(!empty($divisions)) @if(old('division'))
                            @foreach($divisions as $de)
                            <option value="{{$de['id']}}"
                                {{ (old('division') ==$de['id']) ? 'selected' : '' }} >{{$de['name']}}</option>
                            @endforeach @else @foreach($divisions as
                            $de) @if($de['parent_id'] == $department_id
                            )
                            <option value="{{$de['id']}}"
                                {{ ($division_id==$de['id']) ? 'selected' : '' }} >{{$de['name']}}</option>
                            @endif @endforeach @endif @endif
                        </select>
                        <p class="help-block">{{
                            ($errors->has('division') ?
                            $errors->first('division') : '') }}</p>
                    </div>
                </div>
                <div
                    class="col-md-6 {{ $errors->has('type_id') ? ' has-error' : '' }}">
                    <label for="title" class="col-sm-5 control-label">Project
                        Type:<span class="field-asterisk">*</span>
                    </label>
                    <div class="col-sm-7">
                        <select class="form-control" name="type_id">
                            @if(old('type_id')) @foreach($type_id as
                            $key=>$value)
                            <option value="{{$key}}" {{old('type_id') ==$key ? 'selected' : ''}}>{{$value}}</option>
                            @endforeach @else @foreach($type_id as
                            $key=>$value)
                            <option value="{{$key}}" {{$project->type_id
                                == $key ? 'selected' : ''}}>{{$value}}</option>
                            @endforeach @endif
                        </select>
                        <p class="help-block">{{
                            ($errors->has('type_id') ?
                            $errors->first('type_id') : '') }}</p>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div
                    class="col-md-6 {{ $errors->has('department_id') ? ' has-error' : '' }}">
                    <label for="title" class="col-sm-5 control-label">Team:<span
                        class="field-asterisk">*</span></label>
                    <div class="col-sm-7">
                        <select class="form-control"
                            name="department_id" id="team_id"
                            role="team">
                            <option value="-1">-- All --</option>
                            @if(!empty($teams))
                            @if(old('department_id')) @foreach($teams as
                            $team)
                            <option value="{{ $team['id']}}"
                                {{ (old('department_id') ==$team['id']) ? 'selected' : '' }} >{{ $team['name'] }}</option>
                            @endforeach @else @foreach($teams as $team)
                            @if($team['parent_id'] == $division_id )
                            <option value="{{$team['id']}}"
                                <?php if($team['id']==$project->department_id){  echo 'selected="selected"'; } ?>>{{$team['name']}}</option>
                            @endif @endforeach @endif @endif
                        </select>
                        <p class="help-block">{{
                            ($errors->has('department_id') ?
                            $errors->first('department_id') : '') }}</p>
                    </div>
                </div>
                <div
                    class="col-md-6 {{ $errors->has('status') ? ' has-error' : '' }}">
                    <label for="title" class="col-sm-5 control-label">Status:<span
                        class="field-asterisk">*</span></label>
                    <div class="col-sm-7">
                        <select class="form-control" name="status">
                            @if(old('status')) @foreach($status as
                            $key=>$value)
                            <option value="{{$key}}" {{old('status') ==$key ? 'selected' : ''}}>{{$value}}</option>
                            @endforeach @else @foreach($status as
                            $key=>$value)
                            <option value="{{$key}}" {{$project->status
                                == $key ? 'selected' : ''}}>{{$value}}</option>
                            @endforeach @endif
                        </select>
                        <p class="help-block">{{ ($errors->has('status')
                            ? $errors->first('status') : '') }}</p>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div
                    class="col-md-6 {{ $errors->has('brse') ? ' has-error' : '' }}">
                    <label for="title" class="col-sm-5 control-label">BSE:<span
                        class="field-asterisk">*</span></label>
                    <div class="col-sm-7">
                        <select class="form-control" name="brse">
                            @if(old('brse'))
                                @foreach($brse as $key=>$value)
                                    <option value="{{$key}}" {{old('brse') == $key ? 'selected' : ''}}>{{$value}}</option>
                                @endforeach
                            @else
                                @foreach($brse as $key=>$value)
                                    <option value="{{$key}}" {{$project->brse == $key ? 'selected' : ''}}>{{$value}}</option>
                                @endforeach
                            @endif
                        </select>
                        <p class="help-block">{{ ($errors->has('brse') ?
                            $errors->first('brse') : '') }}</p>
                    </div>
                </div>
                <div
                    class="col-md-6 {{ $errors->has('plant_start_date') ? ' has-error' : '' }}">
                    <label for="title" class="col-sm-5 control-label">Plan
                        Start Date:<span class="field-asterisk">*</span>
                    </label>
                    <div class="col-sm-7">
                        @if(empty($project['plant_start_date']) ||
                        $project->plant_start_date == '0000-00-00')
                        <div class='input-group'>
                            {!!
                            Form::text('plant_start_date','',['class'=>'form-control
                            input-sm' , 'id' => 'start_date',
                            'onpaste'=>'return false']) !!} <span
                                class="input-group-addon open-startdate">
                                <span
                                class="glyphicon glyphicon-calendar open-startdate"></span>
                            </span>
                        </div>
                        @else
                        <div class='input-group'>
                            {!!
                            Form::text('plant_start_date',$plan_start,['class'=>'form-control
                            input-sm' , 'id' => 'start_date',
                            'onpaste'=>'return false']) !!} <span
                                class="input-group-addon open-startdate">
                                <span
                                class="glyphicon glyphicon-calendar open-startdate"></span>
                            </span>
                        </div>
                        @endif
                        <p class="help-block">{{
                            ($errors->has('plant_start_date') ?
                            $errors->first('plant_start_date') : '') }}</p>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div
                    class="col-md-6 {{ $errors->has('plant_end_date') ? ' has-error' : '' }} pull-right">
                    <label for="title" class="col-sm-5 control-label">Plan
                        End Date:<span class="field-asterisk">*</span>
                    </label>
                    <div class="col-sm-7">
                        @if(empty($project['plant_end_date']) ||
                        $project->plant_end_date == '0000-00-00')
                        <div class='input-group'>
                            {!!
                            Form::text('plant_end_date','',['class'=>'form-control
                            input-sm','id' => 'end_date',
                            'onpaste'=>'return false']) !!} <span
                                class="input-group-addon open-enddate">
                                <span
                                class="glyphicon glyphicon-calendar open-enddate"></span>
                            </span>
                        </div>
                        @else
                        <div class='input-group'>
                            {!!
                            Form::text('plant_end_date',$plan_end,['class'=>'form-control
                            input-sm','id' => 'end_date',
                            'onpaste'=>'return false']) !!} <span
                                class="input-group-addon open-enddate">
                                <span
                                class="glyphicon glyphicon-calendar open-enddate"></span>
                            </span>
                        </div>
                        @endif
                        <p class="help-block">{{
                            ($errors->has('plant_end_date') ?
                            $errors->first('plant_end_date') : '') }}</p>
                    </div>
                </div>
            </div>

            <div class="panel-heading title-update">Detail information</div>
            <div class="form-group row">
                <div
                    class="col-md-6 {{ $errors->has('language_id') ? ' has-error' : '' }}">
                    <label for="title" class="col-sm-5 control-label">Language
                        Type:</label>
                    <div class="col-sm-7">
                        @if ($project->language_id == 0) {!!
                        Form::select('language_id',
                        array()+$language_id, 3,
                        ['class'=>'form-control']) !!} @else {!!
                        Form::select('language_id',
                        array()+$language_id, null,
                        ['class'=>'form-control']) !!} @endif
                        <p class="help-block">{{
                            ($errors->has('language_id') ?
                            $errors->first('language_id') : '') }}</p>
                    </div>
                </div>
                <div
                    class="col-md-6 {{ $errors->has('process_apply') ? ' has-error' : '' }}">
                    <label for="title" class="col-sm-5 control-label">Process
                        apply:</label>
                    <div class="col-sm-7">
                        @if ($project->process_apply == 0) {!!
                        Form::select('process_apply',
                        array()+$process_apply, 2,
                        ['class'=>'form-control']) !!} @else {!!
                        Form::select('process_apply',
                        array()+$process_apply, null,
                        ['class'=>'form-control']) !!} @endif
                        <p class="help-block">{{
                            ($errors->has('process_apply') ?
                            $errors->first('process_apply') : '') }}</p>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div
                    class="col-md-6 {{ $errors->has('actual_start_date') ? ' has-error' : '' }}">
                    <label for="title" class="col-sm-5 control-label">Actual
                        start date:<span class="field-asterisk"></span>
                    </label>
                    <div class="col-sm-7">
                        @if(empty($project['actual_start_date']) ||
                        $project->actual_start_date == '0000-00-00')
                        <div class='input-group'>
                            {!! Form::text('actual_start_date'
                            ,'',['class'=>'form-control input-sm','id'
                            => 'actual_start_date', 'onpaste'=>'return
                            false']) !!} <span
                                class="input-group-addon open-actual-startdate">
                                <span
                                class="glyphicon glyphicon-calendar open-actual-startdate"></span>
                            </span>
                        </div>
                        @else
                        <div class='input-group'>
                            {!! Form::text('actual_start_date'
                            ,$actual_start,['class'=>'form-control
                            input-sm','id' => 'actual_start_date',
                            'onpaste'=>'return false']) !!} <span
                                class="input-group-addon open-actual-startdate">
                                <span
                                class="glyphicon glyphicon-calendar open-actual-startdate"></span>
                            </span>
                        </div>
                        @endif
                        <p class="help-block">{{
                            ($errors->has('actual_start_date') ?
                            $errors->first('actual_start_date') : '') }}</p>
                    </div>
                </div>
                <div
                    class="col-md-6 {{ $errors->has('actual_end_date') ? ' has-error' : '' }}">
                    <label for="title" class="col-sm-5 control-label">Actual
                        end date:</label>
                    <div class="col-sm-7">
                        @if(empty($project['actual_end_date']) ||
                        $project->actual_end_date == '0000-00-00')
                        <div class='input-group'>
                            <input type="text" name="actual_end_date"
                                value="{{old('actual_end_date')}}"
                                class="form-control input-sm"
                                id="actual_end_date"> <span
                                class="input-group-addon open-actualenddate">
                                <span
                                class="glyphicon glyphicon-calendar open-actualenddate"></span>
                            </span>
                        </div>
                        @else
                        <div class='input-group'>
                            {!! Form::text('actual_end_date'
                            ,$actual_end,['class'=>'form-control
                            input-sm','id'=>'actual_end_date']) !!} <span
                                class="input-group-addon open-actualenddate">
                                <span
                                class="glyphicon glyphicon-calendar open-actualenddate"></span>
                            </span>
                        </div>
                        @endif
                        <p class="help-block">{{
                            ($errors->has('actual_end_date') ?
                            $errors->first('actual_end_date') : '') }}</p>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label for="title" class="col-sm-5 control-label">Detail
                        design:</label>
                    <div class="col-sm-7">
                        @foreach (Config::get("constant.resource") as
                        $key=>$value) @if($key == 0) <label
                            class="radio-inline"> {!!
                            Form::radio('detail_design', $key, true) !!}
                            {{ $value }} </label> @else <label
                            class="radio-inline"> {!!
                            Form::radio('detail_design', $key) !!} {{
                            $value }} </label> @endif @endforeach
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="title" class="col-sm-5 control-label">Test
                        First:</label>
                    <div class="col-sm-7">
                        @foreach (Config::get("constant.resource") as
                        $keys=>$values) @if($keys == 0) <label
                            class="radio-inline"> {!!
                            Form::radio('test_first', $keys, true) !!}
                            {{ $values }} </label> @else <label
                            class="radio-inline"> {!!
                            Form::radio('test_first', $keys) !!} {{
                            $values }} </label> @endif @endforeach
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-6 ">
                    <label for="title" class="col-sm-5 control-label">Unit
                        test:</label>
                    <div class="col-sm-7">
                        @foreach (Config::get("constant.resource") as
                        $unit=>$units) @if($unit == 0) <label
                            class="radio-inline"> {!!
                            Form::radio('unit_test', $unit, true) !!} {{
                            $units }} </label> @else <label
                            class="radio-inline"> {!!
                            Form::radio('unit_test', $unit) !!} {{
                            $units }} </label> @endif @endforeach
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="title" class="col-sm-5 control-label">Scenario:</label>
                    <div class="col-sm-7">
                        @foreach (Config::get("constant.resource") as
                        $scenario=>$scenarios) @if($scenario == 0) <label
                            class="radio-inline"> {!!
                            Form::radio('scenario', $scenario, true) !!}
                            {{ $scenarios }} </label> @else <label
                            class="radio-inline"> {!!
                            Form::radio('scenario', $scenario) !!} {{
                            $scenarios }} </label> @endif @endforeach
                    </div>
                </div>
            </div>



            <div class="panel-heading title-update">Other</div>
            <div class="form-group">
                <label for="description" class="col-sm-3 control-label">Description</label>
                <div class="col-sm-9">
                    <textarea id="description" class="form-control"
                        name="description" rows="5" maxlength="255">{{ $project['description'] }}</textarea>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-3 control-label">Attached files</label>
                <div class="col-sm-3">
                    {!! Form::file('__files[]',
                    array('multiple'=>true,'id' => 'files','class' =>
                    'control-label')) !!} <input type="hidden"
                        name="project_id" id="project_id"
                        value="{{ $project['id'] }}">
                </div>
                <div class="col-sm-1">
                    <button id="upload_file" type="button"
                        class="btn btn-warning">Upload</button>
                </div>
                <div class="col-sm-5">
                    <p class="upload_message help-block"></p>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-3"></div>
                <div class="upload-result col-sm-6">
                    <div id="progress-wrp">
                        <div class="progress-bar"></div>
                        <div class="status">0%</div>
                    </div>
                    <div id="output">
                        <!-- error or success results -->
                        <table
                            class="table table-striped tbl-image-list"
                            id="responsiveTable">
                            <colgroup>
                                <col width="20">
                                <col width="15">
                                <col width="20">
                                <col width="20">
                                <col width="25">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <th style="width: 10%;">Thumbnail</th>
                                    <th style="width: 5%;">Size</th>
                                    <th style="width: 10%;">Uploaded at</th>
                                    <th style="width: 60%;">File Name</th>
                                    <th style="width: 15%;">Action</th>
                                </tr>
                                <tr>
                                    <td colspan="5"
                                        class="action_delete_all text-right">
                                        <button
                                            class="btn btn-xs btn-success"
                                            id="btn_delete_all"
                                            type="button">Delete</button>
                                        <input class="delete_item"
                                        type="checkbox" name="deleteAll"
                                        value="" id="deleteAll">
                                    </td>
                                </tr>
                                @if(!empty($fileUploaded))
                                @foreach($fileUploaded as $key => $val)
                                <tr>
                                    <td>
                                        @if(!in_array($val->extension,$notFileImages))
                                        <img
                                        src="{{ Image::url( $public_path.$val->name ,110,128,array('crop')) }}">
                                        @else <img
                                        src="/img/file_icon.png"> @endif
                                    </td>
                                    <td>{{ $val->size }} Kb</td>
                                    <td>{{ $val->updated_at }}</td>
                                    <td>{{ $val->name }}</td>
                                    <td class="action">
                                        <div class="inline">
                                            <a
                                                href="javascript:void(0);"
                                                name="{{ $val->name }}"
                                                dataId="{{ $val->id }}"
                                                class="delete"> <i
                                                class="fa fa-trash-o"
                                                aria-hidden="true"></i>
                                            </a> | <a
                                                href="{{ Route('projects.download', $val->name) }}"
                                                target="_blank"> <i
                                                class="fa fa-cloud-download fa-lg"></i>
                                            </a> | <input
                                                type="checkbox"
                                                id="delete_item"
                                                class="delete_item"
                                                project_id="{{ $project['id'] }}"
                                                file_name="{{ $val->name }}"
                                                name="deleteCheck[]"
                                                value="{{ $val->id }}">
                                        </div>
                                    </td>
                                </tr>
                                @endforeach @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-5 col-sm-offset-5">
                <button type="submit" class="btn btn-success">Update</button>
                <a href="{{ URL::route('projects.index') }}"
                    class="btn btn-danger" role="button">Cancel</a>
            </div>
        </form>
    </div>
    <!-- /panel-body -->
</div>
<!-- /panel -->
@stop @section('script')
<script src="{{ asset('/js/project/select_department.js') }}"></script>
<script src="{{ asset('/js/project/project.js') }}"></script>
<script type="text/javascript"
    src="{{ asset('/js/project_version/version.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/project/edit.js') }}"></script>
@stop @section('modal')
<div id="deleteModal" class="modal fade bs-example-modal-sm"
    tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>Delete File</h4>
            </div>
            <div class="modal-body">
                <p>Do you want delete File?</p>
            </div>
            <div class="modal-footer">
                <form id="frmDeleteFile" method="post">
                    <input type="hidden" value="0" id="file-id"
                        name="id" /> <input type="hidden" value="0"
                        id="file-name" name="file-name" /> <input
                        type="hidden" value="{{ $project['id'] }}"
                        id="project-id" name="project-id" />
                    <button class="btn btn-sm btn-success" name="delete"
                        id="btn-delete-file" data-dismiss="modal"
                        type="button">Delete</button>
                    <button class="btn btn-sm btn-danger"
                        data-dismiss="modal" type="button">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="deleteAllItemModal" class="modal fade bs-example-modal-sm"
    tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>Delete File</h4>
            </div>
            <div class="modal-body">
                <p>Do you want delete File?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" name="delete"
                    id="btn-delete-file" data-dismiss="modal"
                    type="button">Delete</button>
                <button class="btn btn-sm btn-danger"
                    data-dismiss="modal" type="button">Close</button>
            </div>
        </div>
    </div>
</div>
@stop
