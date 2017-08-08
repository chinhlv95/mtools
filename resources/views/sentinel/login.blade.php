@extends('layouts.login')

@section('title', 'Đăng nhập')
@section('content')
    <div class="login-widget animation-delay1">
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <div class="pull-left">
                <i class="fa fa-lock fa-lg"></i> Login
            </div>
        </div>
        <div class="panel-body">
            {{ Form::open(['url' => Route('login'), 'class' => 'form-login', 'autocomplete' => 'off']) }}
	            @if ($message = Session::get('success'))
					    <div class="alert alert-success alert-block">
					        <button type="button" class="close" data-dismiss="alert">
					            <i class="glyphicon glyphicon-remove"></i>
					        </button>
					        <strong>{{ $message }}</strong>
					    </div>
				@endif
                <div class="form-group{{ $errors->has('email') ? ' has-error' : null }}">
                    <label>Email</label>
                    {{ Form::text('email', null, ['class' => 'form-control input-sm bounceIn animation-delay2']) }}
                    <p class="help-block">{{ $errors->first('email') }}</p>
                </div>
                <div class="form-group{{ $errors->has('password') ? ' has-error' : null }}">
                    <label>Password</label>
                    {{ Form::password('password', ['class' => 'form-control input-sm bounceIn animation-delay4']) }}
                    @if($errors->first('password'))
                        <p class="help-block">{{ $errors->first('password') }} </p>
                    @elseif($errors->any())
                    <p class="help-block text-danger"> {{ $errors->first(0, ':message')  }}</p>
                    @endif
                </div>
                <div class="form-group">
                    <label class="label-checkbox inline">
                        {{ Form::checkbox('remember', 0 , null, ['class'=>'regular-checkbox chk-delete']) }}
                        <span class="custom-checkbox info bounceIn animation-delay4"></span>
                        Remember me?
                    </label>
                </div>
                <div class="seperator"></div>
                <hr/>
                <div>
                    <div class="col-sm-4 pull-right">
                        <a href="{{URL::to('/')}}" class="btn btn-primary btn-sm bounceIn"> Cancel</a>
                    </div>
                    <div class="col-sm-4 pull-right">
                        <button type="submit" name="loginButton" id="loginButton" class="btn btn-success btn-sm bounceIn animation-delay5"><i class="fa fa-sign-in"></i> LOG IN</button>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div><!-- /panel -->
</div><!-- /login-widget -->


@stop
