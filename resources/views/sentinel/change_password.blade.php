@extends('layouts.master')
@section('title', 'Change Password')
@section('breadcrumbs','Change Password')
@section('content')
<div class="padding-md">
    <div class="main-header clearfix">
        <div class="page-title">
            <h3 class="no-margin">
                Change Password - {{ $user->last_name.' '.$user->first_name }} {{ $user->member_code}}
            </h3>
        </div>
    </div>
    <div class="panel panel-body panel-default">
        <div class="panel-body">
             {{ Form::open(['url' => Route('change.password'), 'class' => 'form-change-password form-horizontal', 'autocomplete' => 'off']) }}
                <div class="col-md-12">
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Your old password:<span class="field-asterisk">*</span></label>
                            <div class="col-lg-7">
                                <input type="password" name="old_password" value="" class="form-control input-sm"/>
                            </div><!-- /.col -->
                        </div><!-- /form-group -->
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Your new password:</label>
                            <div class="col-lg-7">
                                <input type="password" name="new_password" value="" class="form-control input-sm"/>
                            </div><!-- /.col -->
                        </div><!-- /form-group -->
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Confirm your new password:</label>
                            <div class="col-lg-7">
                                <input type="password" name="new_password_confirm" value="" class="form-control input-sm"/>
                            </div><!-- /.col -->
                        </div><!-- /form-group -->
                    </div>
                </div>
               <div class="col-sm-5 col-sm-offset-4">
                    <input type="hidden" name="check_button" id="check_button" value=""/>
                    <button id="change_password" type="submit" class="btn btn-success">Change Password</button>
                    <a href="{{URL::to('/')}}" class="btn btn-primary" role="button">Cancel</a>
                </div>
             {{ Form::close() }}
         </div>
    </div>
</div>
@stop
@section('script')
    <script type="text/javascript" src="{{asset('/js/user_management/editUser.js')}}"></script>
    <script type="text/javascript" src="{{asset('/js/select2/select2.min.js')}}"></script>
@stop
