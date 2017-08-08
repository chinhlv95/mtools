@extends('layouts.master')
@section('title', 'Content Mapping')

@section('breadcrumbs','Content Mapping')
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
        <div class="panel-heading" id="form_heading">Content Mapping</div>
        <div class="panel-body" id="form_body">
            <form method="post" id="frm_create" action="{{ URL::route('content-management.store') }}" class="form-horizontal">
                {{ csrf_field() }}
                <div class="form-group">
                    <label class="col-lg-3 control-label">
                        Setting type:
                    </label>
                    <div class="col-lg-6">
                        <select class="form-control" name="type" id="type" disabled="disabled">
                            @foreach($setting_type as $key => $value)
                                <option <?php if(!empty($type_id) && $type_id == $key): ?> selected="selected" <?php endif; ?> value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">
                        Source:
                    </label>
                    <div class="col-lg-6">
                        <select class="form-control" name="source" id="source" disabled="disabled">
                            @foreach($source as $key => $value)
                                <option <?php if(!empty($selected_source) && $selected_source == $key): ?> selected="selected" <?php endif; ?> value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">
                        Releated Name <span class="field-asterisk">(*)</span>:
                    </label>
                    <div class="col-lg-6 {{ $errors->has('releated_name') ? ' has-error' : '' }} {{ !empty(session('uniqueMsg')) ? 'has-error' : '' }}">
                        <input type="text" name="releated_name" value="{{ old('releated_name') }}" class="form-control input-sm" maxlength="50">
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
                        <button type="button" class="btn btn-primary btn-create-source">Create</button>
                    </div>
                    <div class="col-lg-6 text-left">
                        <a href="{{ Route('content-management.index') }}">
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