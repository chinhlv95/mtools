@extends('layouts.master')
@section('title','Create New KPT')
@section('breadcrumbs','Create New KPT')
@section('style')
    <link href="{{ asset('css/custom/date-form.css') }}" rel="stylesheet">
@stop
@section('content')
<div class="padding-md col-sm-12">
    <form class="form-horizontal col-sm-12" method="post" action="{{ URL::route('kpt.post.new', $project_id ) }}">
        {{ csrf_field() }}
        <div class="panel panel-default">
            <div class="panel-heading" id="form_heading">Create new KPT</div>
            <div class="panel-body" id="form_body">
                <div class="form-group {{ $errors->has('version') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label" for="version">
                        version<span class="text-danger"> (*)</span>
                    </label>
                    <div class="col-sm-4">
                        <select class="form-control" name="version">
                            <option value=""> -- Select version -- </option>
                            @if(count($version) > 0)
                                @foreach($version as $key => $value)
                                    @if(old('version') == $value['id'])
                                        <option value="{{ $value['id'] }}" selected='selected'>{{ $value['name'] }}</option>
                                    @else
                                        <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <p class="help-block"></p>
                        @if ($errors->has('version'))
                            <span class="error-message help-block">{{ $errors->first('version') }}</strong></span>
                        @endif
                    </div>
                </div>
                <div class="form-group {{ $errors->has('category') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label" for="category">
                        Category<span class="text-danger"> (*)</span>
                    </label>
                    <div class="col-sm-4">
                        <select class="form-control" name="category">
                            @if(count($category) > 0)
                                @foreach($category as $key => $value)
                                    @if(old('category') == $value['id'])
                                        <option value="{{ $value['id'] }}" selected='selected'>{{ $value['value'] }}</option>
                                    @else
                                        <option value="{{ $value['id'] }}">{{ $value['value'] }}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <p class="help-block"></p>
                        @if ($errors->has('category'))
                            <span class="error-message help-block">{{ $errors->first('category') }}</span>
                        @endif
                    </div>
                </div>
                <div class="form-group {{ $errors->has('type') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label" for="type">
                        Type<span class="text-danger"> (*)</span>
                    </label>
                    <div class="col-sm-4">
                        <select class="form-control" name="type">
                            @if(count($types) > 0)
                                @foreach($types as $key => $value)
                                    @if(old('type') == $key)
                                        <option value="{{ $key }}" selected='selected'>{{ $value }}</option>
                                    @else
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <p class="help-block"></p>
                        @if ($errors->has('type'))
                            <span class="error-message help-block">{{ $errors->first('type') }}</span>
                        @endif
                    </div>
                </div>
                <div class="form-group {{ $errors->has('status') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label" for="status">
                        Status<span class="text-danger"> (*)</span>
                    </label>
                    <div class="col-sm-4">
                        <select class="form-control" name="status">
                            @if(count($status) > 0)
                                @foreach($status as $key => $value)
                                    @if(old('status') == $key)
                                        <option value="{{ $key }}" selected='selected'>{{ $value }}</option>
                                    @else
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <p class="help-block"></p>
                        @if ($errors->has('status'))
                            <span class="error-message help-block">{{ $errors->first('status') }} </span>
                        @endif
                    </div>
                </div>
                <div class="form-group {{ $errors->has('description') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label" for="description">
                        Description<span class="text-danger"> (*)</span>
                    </label>
                    <div class="col-sm-7">
                        <textarea class="form-control"
                                  name="description"
                                  placeholder="Description about your KPT"
                                  rows="5"
                                  maxlength="255">{{ old('description') }}</textarea>
                        <p class="help-block"></p>
                        @if ($errors->has('description'))
                            <span class="error-message help-block">{{ $errors->first('description') }}</span>
                        @endif
                    </div>
                </div>
                <div class="form-group {{ $errors->has('action') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label" for="action">
                        Action<span class="text-danger"> (*)</span>
                    </label>
                    <div class="col-sm-7">
                        <textarea class="form-control"
                                  name="action"
                                  placeholder="Your action"
                                  rows="5"
                                  maxlength="255">{{ old('action') }}</textarea>
                        <p class="help-block"></p>
                        @if ($errors->has('action'))
                            <span class="error-message help-block">{{ $errors->first('action') }}</span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <input type="text" name="project_id" value="{{ $project_id }}" hidden="true">
                </div>
            </div>
            <hr />
            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-sm-8 col-sm-offset-4">
                            <button type="submit" class="btn btn-success" id="save" name="save">Save</button>
                            <button type="submit" class="btn btn-success" name="save_and_continue">Save and continue</button>
                            <button type="button" class="btn btn-danger" id="configreset">Reset</button>
                            <a href="{{ URL::route('projects.kpt.list', $project_id) }}" class="btn btn-primary">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@stop

@section('script')
    <script src="{{ asset('js/common/reset_form.js') }}"></script>
    <script src="{{ asset('js/project_kpt/kpt.create.js') }}"></script>
@stop