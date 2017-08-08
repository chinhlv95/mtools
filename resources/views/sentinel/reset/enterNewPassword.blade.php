@extends('cms::layouts.login')

@section('title', 'Nhập mật khẩu mới')
@section('breadcrumbs', 'Nhập mật khẩu mới')

@section('content')
    <div class="panel-body">
        {{ Form::open(['class' => 'form-horizontal']) }}
            <div class="form-group {{ $errors->has('password') ? ' has-error' : null }}">
                <label for="password" class="control-label">Mật khẩu mới</label>
                    {{ Form::password('password', ['class' => 'form-control']) }}
                    <p class="help-block">{{ $errors->first('password') }}</p>
            </div>
            <div class="form-group {{ $errors->has('password_confirmation') ? ' has-error' : null }}">
                <label for="password-confirmation" class="control-label">Nhập lại mật khẩu</label>
                    {{ Form::password('password_confirmation', ['class' => 'form-control']) }}
                    <p class="help-block">{{ $errors->first('password_confirmation') }}</p>
            </div>
            <div class="form-group text-center">
                {{ Form::submit('OK', ['class' => 'btn btn-success btn-sm bounceIn']) }}
                <a href="{{URL::to('/')}}" class="btn btn-primary btn-sm bounceIn"> Hủy bỏ</a>
            </div>
        {{ Form::close() }}
    </div><!-- /panel -->
@stop
