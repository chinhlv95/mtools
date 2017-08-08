@extends('layouts.master')
@section('title', 'Profile')
@section('breadcrumbs','Profile')
@section('content')
<div class="padding-md">
    <div class="main-header clearfix">
        <div class="page-title">
            <h3 class="no-margin">
                Profile - {{ $user->last_name.' '.$user->first_name }} {{ $user->member_code}}
            </h3>
        </div>
    </div>
    <div class="panel panel-body panel-default">
        <div class="panel-body">
             {{ Form::open(['url' => Route('edit.profile'), 'class' => 'form-change-password form-horizontal', 'autocomplete' => 'off']) }}
                 <div class="col-md-12">
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-lg-3 control-label">First name:<span class="field-asterisk">*</span></label>
                            <div class="col-lg-7">
                                <input type="text" name="first_name" value="{{old('first_name') ? old('first_name') : $user->first_name}}" class="form-control input-sm"/>
                            </div><!-- /.col -->
                        </div><!-- /form-group -->
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Last name:</label>
                            <div class="col-lg-7">
                                <input type="text" name="last_name" value="{{old('last_name') ? old('last_name') : $user->last_name}}" class="form-control input-sm"/>
                            </div><!-- /.col -->
                        </div><!-- /form-group -->
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Email:</label>
                            <div class="col-lg-7">
                                <input type="email" name="main_email" class="form-control" id="main_email" value="{{$user->email}}" readonly="readonly">
                            </div><!-- /.col -->
                        </div><!-- /form-group -->
                        <?php //$source = $listSources[$user->source_id];?>
                        @if(!empty($source))
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Source:</label>
                                <div class="col-lg-7">
                                    <input type="email" name="user_source" class="form-control" id="user_source" value="{{$source}}" readonly="readonly">
                                </div><!-- /.col -->
                            </div><!-- /form-group -->
                        @endif
                        @if(!empty(old('sub_user')))
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Sub user:</label>
                                <select multiple="multiple" name="sub_user[]" id="sub_user" class="col-lg-7">
                                    @foreach($userNotSub as $user)
                                        <option value="{{$user['id']}}" @if(in_array($user['id'], old('sub_user'))) selected @endif>
                                            {{$user['first_name']}} - {{$user['user_name']}} - {{$user['email']}} - {{$user['source']}}
                                        </option>
                                    @endforeach
                                </select>
                            </div><!-- /form-group -->
                        @else
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Sub user:</label>
                                <select multiple="multiple" name="sub_user[]" id="sub_user" class="col-lg-7">
                                    @foreach($userNotSub as $user)
                                        <option value="{{$user['id']}}" @if(in_array($user['id'], $subUsers)) selected @endif>
                                            {{$user['first_name']}} - {{$user['user_name']}} - {{$user['email']}} - {{$user['source']}}
                                        </option>
                                    @endforeach
                                </select>
                            </div><!-- /form-group -->
                        @endif
                    </div>
                </div>
               <div class="col-sm-5 col-sm-offset-5">
                    <input type="hidden" name="check_button" id="check_button" value=""/>
                    <button id="profile" type="submit" class="btn btn-success">Update</button>
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
    <script type="text/javascript">
		$(document).ready(function() {
			$('button.closeMessage').click(function() {
				$('div.alert').hide();
			});
            //var source_hidden = <?php //echo $source_hidden;?>;
//             var insert_sub_user = $('.insert_sub_user');
//             $('.add_sub_user').click(function() {
//                 insert_sub_user.append('<div class="form-group">'
//                         + '<label class="col-lg-3 control-label">Sub user:</label>'
//                         + '<div class="col-lg-7">'
//                         + '<input type="text" name="sub_user[]" class="form-control" id="sub_user" value="">'
//                         + '</div>'
//                         + '</div>'
//                         + '<div class="form-group">'
//                         + '<label class="col-lg-3 control-label">From system:</label>'
//                         + '<div class="col-lg-7">'
//                         + '<select name="sub_source[]" class="form-control">');
//                 $.each(source_hidden, function(keySource, value){
//                     $('.insert_sub_user select').last().append('<option value='+keySource+'>'+value+'</option>');
//                 });
//                 insert_sub_user.append('</select>'
//                         + '</div>'
//                         + '</div>');
//             });
		});
	</script>
@stop
