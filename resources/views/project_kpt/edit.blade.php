@extends('layouts.master')
@section('title','Edit KPT')
@section('breadcrumbs','Edit KPT')
@section('style')
    <link href="{{ asset('css/custom/date-form.css') }}" rel="stylesheet">
@stop
@section('content')
<div class="padding-md col-sm-12">
    <form class="form-horizontal col-sm-12" method="post" action="{{ URL::route('kpt.post.edit', [$project_id, $kpt->id] ) }}">
    {{ Form::token() }}
        <div class="panel panel-default">
            <div class="panel-heading" id="form_heading">Edit KPT</div>
            <div class="panel-body" id="form_body">
                <div class="form-group {{ $errors->has('release') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label" for="release">
                        Version<span class="text-danger"> (*)</span>
                    </label>
                    <div class="col-sm-4">
                        <select class="form-control" name="release">
                            <option value=""> -- Select release -- </option>
                            @if(old('release'))
                                @foreach($releases as $release)
                                    @if($release->id == old('release'))
                                        <option value="{{$release->id}}" selected='selected'>{{$release->name}}</option>
                                    @else
                                        <option value="{{$release->id}}">{{$release->name}}</option>
                                    @endif
                                @endforeach
                            @else
                                @foreach($releases as $release)
                                    @if($release->id == $kpt->release_id)
                                        <option value="{{$release->id}}" selected='selected'>{{$release->name}}</option>
                                    @else
                                        <option value="{{$release->id}}">{{$release->name}}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <p class="help-block"></p>
                        @if ($errors->has('release'))
                            <span class="help-block">{{ $errors->first('release') }}</strong></span>
                        @endif
                    </div>
                </div>
                <div class="form-group {{ $errors->has('category') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label" for="category">
                        Category<span class="text-danger"> (*)</span>
                    </label>
                    <div class="col-sm-4">
                        <select class="form-control" name="category">
                            @if(old('category'))
                                @foreach($categories as $category)
                                    @if($category->id == old('category'))
                                        <option value="{{$category->id}}" selected='selected'>{{$category->value}}</option>
                                    @else
                                        <option value="{{$category->id}}">{{$category->value}}</option>
                                    @endif
                                @endforeach
                            @else
                                @foreach($categories as $category)
                                    @if($category->id == $kpt->category_id)
                                        <option value="{{$category->id}}" selected='selected'>{{$category->value}}</option>
                                    @else
                                        <option value="{{$category->id}}">{{$category->value}}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <p class="help-block"></p>
                        @if ($errors->has('category'))
                            <span class="help-block">{{ $errors->first('category') }}</span>
                        @endif
                    </div>
                </div>
                <div class="form-group {{ $errors->has('type') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label" for="type">
                        Type<span class="text-danger"> (*)</span>
                    </label>
                    <div class="col-sm-4">
                        <select class="form-control" name="type">
                            @if(old('type'))
                                @foreach($types as $key => $value)
                                    @if($key == old('type'))
                                        <option value="{{$key}}" selected='selected'>{{$value}}</option>
                                    @else
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endif
                                @endforeach
                            @else
                                @foreach($types as $key => $value)
                                    @if($key == $kpt->type_id)
                                        <option value="{{$key}}" selected='selected'>{{$value}}</option>
                                    @else
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <p class="help-block"></p>
                        @if ($errors->has('type'))
                            <span class="help-block">{{ $errors->first('type') }}</span>
                        @endif
                    </div>
                </div>
                <div class="form-group {{ $errors->has('status') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label" for="status">
                        Status<span class="text-danger"> (*)</span>
                    </label>
                    <div class="col-sm-4">
                        <select class="form-control" name="status">
                            @if(old('status'))
                                @foreach($status as $key => $value)
                                    @if($key == old('status'))
                                        <option value="{{$key}}" selected='selected'>{{$value}}</option>
                                    @else
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endif
                                @endforeach
                            @else
                                @foreach($status as $key => $value)
                                    @if($key == $kpt->status)
                                        <option value="{{$key}}" selected='selected'>{{$value}}</option>
                                    @else
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <p class="help-block"></p>
                        @if ($errors->has('status'))
                            <span class="help-block">{{ $errors->first('status') }} </span>
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
                                  placeholder="Description about your Kpt"
                                  rows="5"
                                  maxlength="255">{{old('description')?old('description'): $kpt->content}}</textarea>
                        <p class="help-block"></p>
                        @if ($errors->has('description'))
                            <span class="help-block">{{ $errors->first('description') }}</span>
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
                                  maxlength="255">{{old('action')?old('action'): $kpt->action}}</textarea>
                        <p class="help-block"></p>
                        @if ($errors->has('action'))
                            <span class="help-block">{{ $errors->first('action') }}</span>
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
                            <button class="btn btn-success" name="save">Update</button>
                            <a href="{{ URL::route('projects.kpt.list', $project_id) }}" class="btn btn-danger">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@stop