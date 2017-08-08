@extends('layouts.master')
@section('title', 'Content Mapping')

@section('breadcrumbs','Update Mapping')
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
        <div class="panel-heading" id="form_heading">Update Mapping</div>
        <div class="panel-body" id="form_body">
            <form method="post" id="frm_update" action="{{ URL::route('content-management.update') }}" class="form-horizontal">
                {{ csrf_field() }}
                <div class="form-group">
                    <label class="col-lg-3 control-label">
                        Setting type:
                    </label>
                    <div class="col-lg-6">
                        <select class="form-control" name="type" disabled="disabled">
                            @foreach($setting_type as $key => $value)
                                <option @if($type_id == $key || !empty(old('selected_source'))) selected="selected" @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">
                        Releated Name <span class="field-asterisk">(*)</span>:
                    </label>
                    <div class="col-lg-6 {{ $errors->has('releated_name') ? ' has-error' : '' }} {{ !empty(session('uniqueMsg')) ? 'has-error' : '' }}">
                        <input type="text" name="releated_name" value="@if(!empty(old('releated_name'))) {{ old('releated_name') }} @else {{$data[0]['name']}} @endif" class="form-control input-sm" maxlength="50">
                        <input type="hidden" name="id" value="{{$id}}">
                        <input type="hidden" name="type_id" value="{{$type_id}}">
                        <input type="hidden" name="source_id" value="{{$source_id}}">
                        <p class="help-block">{{ ($errors->has('releated_name') ? $errors->first('releated_name') : '') }}</p>
                        @if (session('uniqueMsg'))
                            <span class="error-message help-block">
                                <strong>{{ session('uniqueMsg') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="col-lg-6 text-right">
                        <button type="button" class="btn btn-primary btn-update-source">Update</button>
                    </div>
                    <div class="col-lg-6 text-left">
                        <a href="{{ Route('content-management.show',['type' => $type_id, 'source' => $source_id]) }}">
                            <button type="button" class="btn btn-info">Cancel</button>
                        </a>
                    </div>
                </div>
             </form>
        </div>
    </div>
</div>
@stop
@section('script')
    <script type="text/javascript" src="{{ asset('/js/content_management/run.js') }}"></script>
@stop