@extends('cms::layouts.login')

@section('title','Quên mật khẩu')
@section('breadcrumbs','Quên mật khẩu')
@section('content')
<div class="panel-body">
  <h5>Nhập email để lấy lại mật khẩu</h5>
    {{ Form::open(['class' => 'form-horizontal']) }}
    <div class="form-group {{ $errors->has('email') ? ' has-error' : null }}">
            {{ Form::email('email', null, ['class' => 'form-control', 'placeholder'=>'email@example.com']) }}
            <p class="help-block">{{ $errors->first('email') }}</p>
    </div>
    <div>
        <div  class="col-sm-2 pull-right">
            <a href="{{URL::to('/')}}" class="btn btn-primary btn-sm bounceIn"> Hủy bỏ</a>
        </div>
        <div class="col-sm-2 pull-right">
            <button type="submit" class="btn btn-success btn-sm bounceIn">Gửi</button>
        </div>
    </div>
    {{ Form::close() }}
</div>
@stop
