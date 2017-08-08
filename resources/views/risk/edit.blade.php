@extends('layouts.master')

@section('breadcrumbs','Edit risk')

@section('title','Edit risk')

@section('content')
<div class="padding-md">
    <div class="main-header clearfix">
      <div class="page-title">
        <h3 class="no-margin">Edit risk</h3>
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading">&nbsp;</div>
      <div class="panel-body">
        <form class="form-horizontal container-web" action="{{Route('risk.postEdit',['id'=>$risk->id,'page'=>Request::get('page',1)])}}" method="post">
          {{ csrf_field() }}
          <div class="form-group ">
            <label class="col-sm-3 control-label" for="category_id">Category</label>
            <div class="col-sm-3">
              <select name="category_id" class="form-control">
                @if(old('category_id'))
                    @foreach($categories as $category)
                        @if($category->id == old('category_id'))
                            <option value="{{$category->id}}" selected='selected'>{{$category->value}}</option>
                        @else
                            <option value="{{$category->id}}">{{$category->value}}</option>
                        @endif
                    @endforeach
                @else
                    @foreach($categories as $category)
                        @if($category->id == $risk->category_id)
                            <option value="{{$category->id}}" selected='selected'>{{$category->value}}</option>
                        @else
                            <option value="{{$category->id}}">{{$category->value}}</option>
                        @endif
                    @endforeach
                @endif
              </select>
              <p class="help-block"></p>
            </div>
          </div>
          <div class="form-group {{ $errors->has('risk_title') ? ' has-error' : '' }}">
            <label class="col-sm-3 control-label" for="risk_title">Risk title<span class="text-danger"> (*)</span></label>
            <div class="col-sm-9">
              <input type="text" maxlength="255" name="risk_title" value="{{old('risk_title')?old('risk_title'): $risk->risk_title}}" class="form-control">
              @if ($errors->has('risk_title'))
                    <span class="error-message help-block">
                        <strong>{{ $errors->first('risk_title') }}</strong>
                    </span>
              @endif
            </div>
          </div>
            <div class="form-group row">
                <div class="col-6 {{ $errors->has('propability') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label" for="propability">Probability (%)<span class="text-danger"> (*)</span></label>
                    <div class="col-sm-3">
                      <input type="number" min="0" max="100" name="propability" value="{{old('propability')?old('propability'): $risk->propability}}" class="form-control propability">
                      @if ($errors->has('propability'))
                            <span class="error-message help-block">
                                <strong>{{ $errors->first('propability') }}</strong>
                            </span>
                      @endif
                    </div>
                </div>
                <div class="col-6">
                       <label class="col-sm-3 control-label" for="guideline_link">Reference link</label>
                        <div class="col-sm-3">
                            <input type="text" maxlength="255" name="guideline_link" value="{{old('guideline_link')?old('guideline_link'): $risk->guideline_link}}" class="form-control">
                            @if ($errors->has('guideline_link'))
                            <span class="error-message help-block">
                                <strong>{{ $errors->first('guideline_link') }}</strong>
                            </span>
                            @endif
                        </div>
                </div>
           </div>
          <div class="form-group">
            <label class="col-sm-3 control-label" for="impact">Impact</label>
                <div class="col-sm-3">
                  <select name="impact" class="form-control impact">
                        @if(old('impact'))
                            @foreach($impacts as $key=>$value)
                                @if(old('impact') == $key)
                                    <option value="{{$key}}" selected='selected'>{{$value}}</option>
                                @else
                                    <option value="{{$key}}">{{$value}}</option>
                                @endif
                            @endforeach
                        @else
                            @foreach($impacts as $key=>$value)
                                @if($risk->impact == $key)
                                    <option value="{{$key}}" selected='selected'>{{$value}}</option>
                                @else
                                    <option value="{{$key}}">{{$value}}</option>
                                @endif
                            @endforeach
                        @endif
                  </select>
                  <p class="help-block"></p>
                </div>
          </div>
          <div class="form-group">
            <label class="col-sm-3 control-label" for="level">Level</label>
            <div class="col-sm-3">
              <input type="text" value="{{old('level')}}" name="level" class="form-control level" readonly>
              <p class="help-block"></p>
            </div>
          </div>
          <div class="form-group">
              <label class="col-sm-3 control-label" for="strategy">Strategy</label>
            <div class="col-sm-3">
                <select name="strategy" class="form-control">
                        @if(old('strategy'))
                            @foreach($strategies as $key=>$value)
                                @if(old('strategy') == $key)
                                    <option value="{{$key}}" selected='selected'>{{$value}}</option>
                                @else
                                    <option value="{{$key}}">{{$value}}</option>
                                @endif
                            @endforeach
                        @else
                            @foreach($strategies as $key=>$value)
                                @if($risk->strategy == $key)
                                    <option value="{{$key}}" selected='selected'>{{$value}}</option>
                                @else
                                    <option value="{{$key}}">{{$value}}</option>
                                @endif
                            @endforeach
                        @endif
                </select>
              <p class="help-block"></p>
          </div>
          </div>
          <div class="form-group {{ $errors->has('mitigration_plan') ? ' has-error' : '' }}">
            <label class="col-sm-3 control-label" for="mitigration_plan">Mitigration plan<span class="text-danger"> (*)</span></label>
            <div class="col-sm-9">
              <textarea name="mitigration_plan" rows="3" class="form-control">{{old('mitigration_plan')? old('mitigration_plan') : $risk->mitigration_plan}}</textarea>
              @if ($errors->has('mitigration_plan'))
                    <span class="error-message help-block">
                        <strong>{{ $errors->first('mitigration_plan') }}</strong>
                    </span>
              @endif
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-3 control-label" for="task_id">Related Task ID</label>
            <div class="col-sm-9">
              <input type="text" maxlength="255" value="{{old('task_id')?old('task_id'): $risk->task_id}}" name="task_id" class="form-control">
              <p class="help-block"></p>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-3 control-label" for="permissions">Status</label>
            <div class="col-sm-3">
              <select name="status" class="form-control">
                    @if($key != 0)
                        @if(old('status'))
                            @foreach($status as $key=>$value)
                                @if(old('impact') == $key)
                                    <option value="{{$key}}" selected='selected'>{{$value}}</option>
                                @else
                                    <option value="{{$key}}">{{$value}}</option>
                                @endif
                            @endforeach
                        @else
                            @foreach($status as $key=>$value)
                                @if($risk->status == $key)
                                    <option value="{{$key}}" selected='selected'>{{$value}}</option>
                                @else
                                    <option value="{{$key}}">{{$value}}</option>
                                @endif
                            @endforeach
                        @endif
                    @endif
              </select>
              <p class="help-block"></p>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-9 col-sm-offset-3">
              <button class="btn btn-success" name="save" type="submit">Save</button>
              <a type="button" class="btn btn-danger" href="{{Route('projects.risk.index',['project_id'=>$project_id])}}">Cancel</a>
            </div>
          </div>
        </form>
      </div><!-- /panel-body -->
    </div><!-- /panel -->
</div>
@stop
@section('script')
    <script src="{{ asset('/js/project_risk/risk_sum_level.js') }}"></script>
    <script src="{{ asset('/js/common/reset_form.js') }}"></script>
    <script src="{{ asset('/js/project_risk/risk_check_values_propability.js') }}"></script>
@stop
