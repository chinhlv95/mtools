@extends('layouts.master')
@section('title', 'Change your password')
@section('breadcrumbs','Change your password')
@section('content')
<div class="col-xs-4"></div>
<div class="col-xs-4">
	<div class="login-wrapper">
		<div class="text-center">
	        <h2 class="fadeInUp animation-delay8" style="font-weight:bold">
	            <span class="text-success">Process</span> <span style="color:#ccc; text-shadow:0 1px #fff">Admin</span>
	        </h2>
	    </div>
	    <div class="login-widget animation-delay1">
	    <div class="panel panel-default">
	        <div class="panel-heading clearfix">
	            <div class="pull-left">
	                <i class="fa fa-key fa-lg"></i> Change your password
	            </div>
	        </div>
	        <div class="panel-body">
	            {{ Form::open(['url' => Route('change.password'), 'class' => 'form-change-password', 'autocomplete' => 'off']) }}
	                <div class="form-group">
	                    <label> Your old password:<span class="field-asterisk">*</span></label>
	                    {{ Form::password('old_password', ['class' => 'form-control input-sm bounceIn animation-delay4']) }}
	                </div>
	                <div class="form-group">
	                    <label>Your new password:<span class="field-asterisk">*</span></label>
	                    {{ Form::password('new_password', ['class' => 'form-control input-sm bounceIn animation-delay4']) }}
	                </div>
	                <div class="form-group">
	                    <label>Confirm your new password:<span class="field-asterisk">*</span></label>
	                    {{ Form::password('new_password_confirm', ['class' => 'form-control input-sm bounceIn animation-delay4']) }}
	                </div>
	                <div class="seperator"></div>
	                <div>
	                    <div class="col-sm-4 pull-right">
	                        <a href="{{URL::to('/')}}" class="btn btn-primary btn-sm bounceIn"> Cancel</a>
	                    </div>
	                    <div class="col-sm-4 col-sm-offset-2">
	                        <button type="submit" name="loginButton" id="loginButton" class="btn btn-success btn-sm bounceIn animation-delay5"><i class="fa fa-sign-in"></i> Change your password</button>
	                    </div>
	                </div>
	            {{ Form::close() }}
	        </div>
	    </div>
    </div><!-- /panel -->
</div>

@stop
@section('script')
	<script type="text/javascript">
		$(document).ready(function() {
			$('button.closeMessage').click(function() {
				$('div.alert').hide();
			});
		});
	</script>
@stop
