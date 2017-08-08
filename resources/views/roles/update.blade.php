@extends('layouts.master')
@section('title', 'Roles Update')
@section('breadcrumbs','Roles Update')
@section('style')
    <link rel="stylesheet" href="{{ asset('/css/custom/date-form.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/custom/bootstrap-3-vert-offset-shim.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/roles/style.css') }}">
@stop
@section('content')
<div class="panel panel-default">
    <div class="panel-heading" id="form_heading">Update role and permission</div>
    <div class="panel-body">
        <form class="form-horizontal" name="frmAddRole" id="frmAddRole" method="post" action="{{ Route('setting.roles.update') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="roleName" class="col-lg-2 control-label">Role Name <span class="field-asterisk">(*)</span></label>
                <div class="col-lg-3 {{ $errors->has('roleName') ? ' has-error' : '' }} {{ !empty(session('uniqueMsg')) ? 'has-error' : '' }} {{ !empty(session('requedMsg')) ? 'has-error' : '' }}">
                    <input type="text" name="roleName" value="{{ $role->name }}"
                    <?php if(in_array($role->name, $staticRoleNames)): ?>
                        readonly="readonly"
                    <?php endif; ?>
                     class="form-control input-sm" id="roleName" maxlength="50" autofocus>
                     @if ($errors->has('roleName'))
                        <span class="error-message help-block">
                            <strong>{{ $errors->first('roleName') }}</strong>
                        </span>
                      @endif

                      @if (session('uniqueMsg'))
                        <span class="error-message help-block">
                            <strong>{{ session('uniqueMsg') }}</strong>
                        </span>
                      @endif

                      @if (session('requedMsg'))
                        <span class="error-message help-block">
                            <strong>{{ session('requedMsg') }}</strong>
                        </span>
                      @endif
                </div>
            </div>
            <input type="hidden" name="role_id" value="{{ $role_id }}">
            <input type="hidden" name="slug" value="{{ old('slug') }}" class="form-control input-sm" id="slug" readonly>
            <div class="row permission">
                <div class="col-lg-6"><strong>Add Permission</strong></div>
                <div class="col-lg-6 text-right">
                   <strong>
                    <a href="#" id="checkPerAll">Check All</a> /
                    <a href="#" id="UncheckPerAll">Uncheck All</a>
                  </strong>
                </div>
            </div>
            <div class="panel-heading">Structure management</div>
            <div class="panel-body">
                <div class="structure_group row">
                    <div class="col-lg-3">
                        <?php //dd($permission); ?>
                        @foreach($structerGroup[0] as $key => $value)
                            @if($key == 'view_list_project')
                                <div class="checkbox">
                                    <label class="label-checkbox">
                                        <input type="checkbox"
                                        <?php
                                            if(!empty($permission) && array_key_exists('user.'.$key, $permission)): ?>
                                                checked="checked"
                                        <?php  endif; ?>
                                        name="permission[]" root="view_list_project" value="{{ $key }}">{{ $value }}
                                    </label>
                                </div>
                                <?php break; ?>
                             @endif
                         @endforeach
                     </div>
                     <div class="col-lg-3">
                         @foreach($structerGroup[0] as $key => $value)
                             @if($key != 'view_list_project' && $key != 'update_project_info')
                                <div class="checkbox">
                                    <label class="label-checkbox">
                                        <input type="checkbox"
                                        <?php
                                            if(!empty($permission) && array_key_exists('user.'.$key, $permission)): ?>
                                                checked="checked"
                                        <?php  endif; ?>
                                         name="permission[]" disabled="disabled" <?php if($key == 'view_project_info'){ ?> root="view_project_info" <?php } ?> parent="view_list_project" value="{{ $key }}">{{ $value }}
                                    </label>
                                </div>
                             @endif
                         @endforeach
                     </div>
                     <div class="col-lg-3 vert-offset-top-7">
                        @foreach($structerGroup[0] as $key => $value)
                             @if($key == 'update_project_info')
                                <div class="checkbox">
                                    <label class="label-checkbox">
                                        <input disabled="disabled"
                                        <?php
                                            if(!empty($permission) && array_key_exists('user.'.$key, $permission)): ?>
                                                checked="checked"
                                        <?php  endif; ?>
                                         name="permission[]" type="checkbox" parent="view_project_info" value="{{ $key }}">{{ $value }}
                                    </label>
                                </div>
                                <?php break; ?>
                             @endif
                         @endforeach
                        @foreach($structerGroup[1] as $key => $value)
                             @if($key == 'view_version')
                                <div class="checkbox">
                                    <label class="label-checkbox">
                                            <input type="checkbox"
                                            <?php
                                            if(!empty($permission) && array_key_exists('user.'.$key, $permission)): ?>
                                                checked="checked"
                                            <?php  endif; ?>
                                             name="permission[]" root="view_version" parent="view_project_info" disabled="disabled" value="{{ $key }}">{{ $value }}
                                    </label>
                                </div>
                                <?php break; ?>
                             @endif
                         @endforeach
                         <div class="vert-offset-top-6">
                             @foreach($structerGroup[2] as $key => $value)
                                 @if($key == 'view_kpt')
                                    <div class="checkbox">
                                        <label class="label-checkbox">
                                                <input type="checkbox"
                                                <?php
                                                    if(!empty($permission) && array_key_exists('user.'.$key, $permission)): ?>
                                                        checked="checked"
                                                <?php  endif; ?>
                                                 name="permission[]" root="view_kpt" parent="view_project_info" disabled="disabled" value="{{ $key }}">{{ $value }}
                                        </label>
                                    </div>
                                    <?php break; ?>
                                 @endif
                             @endforeach
                         </div>
                         <div class="vert-offset-top-6">
                             @foreach($structerGroup[3] as $key => $value)
                                 @if($key == 'view_list_risk')
                                    <div class="checkbox">
                                        <label class="label-checkbox">
                                                <input type="checkbox"
                                                    <?php
                                                        if(!empty($permission) && array_key_exists('user.'.$key, $permission)): ?>
                                                            checked="checked"
                                                    <?php  endif; ?>
                                                 name="permission[]" root="view_list_risk" parent="view_project_info" disabled="disabled" value="{{ $key }}">{{ $value }}
                                        </label>
                                    </div>
                                    <?php break; ?>
                                 @endif
                             @endforeach
                         </div>
                         <div class="vert-offset-top-5">
                             @foreach($structerGroup[4] as $key => $value)
                                 @if($key == 'view_member')
                                    <div class="checkbox">
                                        <label class="label-checkbox">
                                                <input type="checkbox"
                                                    <?php
                                                        if(!empty($permission) && array_key_exists('user.'.$key, $permission)): ?>
                                                            checked="checked"
                                                    <?php  endif; ?>
                                                 name="permission[]" root="view_member" parent="view_project_info" disabled="disabled" value="{{ $key }}">{{ $value }}
                                        </label>
                                    </div>
                                    <?php break; ?>
                                 @endif
                             @endforeach
                         </div>
                     </div>

                     <div class="col-lg-3 vert-offset-top-9">
                        @foreach($structerGroup[1] as $key => $value)
                             @if($key != 'view_version')
                                <div class="checkbox">
                                    <label class="label-checkbox">
                                            <input type="checkbox"
                                                <?php
                                                    if(!empty($permission) && array_key_exists('user.'.$key, $permission)): ?>
                                                        checked="checked"
                                                <?php  endif; ?>
                                             name="permission[]" grandNode="view_project_info" parent="view_version" disabled="disabled" value="{{ $key }}">{{ $value }}
                                    </label>
                                </div>
                             @endif
                         @endforeach
                     </div>

                     <div class="col-lg-3 vert-offset-top-1">
                        @foreach($structerGroup[2] as $key => $value)
                             @if($key != 'view_kpt')
                                <div class="checkbox">
                                    <label class="label-checkbox">
                                            <input type="checkbox"
                                                <?php
                                                    if(!empty($permission) && array_key_exists('user.'.$key, $permission)): ?>
                                                        checked="checked"
                                                <?php  endif; ?>
                                             name="permission[]" grandNode="view_project_info" parent="view_kpt" disabled="disabled" value="{{ $key }}">{{ $value }}
                                    </label>
                                </div>
                             @endif
                         @endforeach
                     </div>

                     <div class="col-lg-3 vert-offset-top-1">
                        @foreach($structerGroup[3] as $key => $value)
                             @if($key != 'view_list_risk')
                                <div class="checkbox">
                                    <label class="label-checkbox">
                                            <input type="checkbox"
                                                <?php
                                                    if(!empty($permission) && array_key_exists('user.'.$key, $permission)): ?>
                                                        checked="checked"
                                                <?php  endif; ?>
                                             name="permission[]" grandNode="view_project_info" parent="view_list_risk" disabled="disabled" value="{{ $key }}">{{ $value }}
                                    </label>
                                </div>
                             @endif
                         @endforeach
                     </div>

                     <div class="col-lg-3 vert-offset-top-1">
                        @foreach($structerGroup[4] as $key => $value)
                             @if($key != 'view_member')
                                <div class="checkbox">
                                    <label class="label-checkbox">
                                            <input type="checkbox"
                                                <?php
                                                    if(!empty($permission) && array_key_exists('user.'.$key, $permission)): ?>
                                                        checked="checked"
                                                <?php  endif; ?>
                                             name="permission[]" grandNode="view_project_info" parent="view_member" disabled="disabled" value="{{ $key }}">{{ $value }}
                                    </label>
                                </div>
                             @endif
                         @endforeach
                     </div>
                  </div>
                </div>
                <!-- file group -->
                <div class="panel-heading">File management</div>
                    <div class="panel-body">
                        <div class="cost_group row">
                            <div class="col-lg-3">
                                @foreach($fileGroup[0] as $key => $value)
                                    <div class="checkbox">
                                        <label class="label-checkbox">
                                                <input type="checkbox" name="permission[]"
                                                <?php
                                                    if(!empty($permission) && array_key_exists('user.'.$key, $permission)): ?>
                                                        checked="checked"
                                                <?php  endif; ?>
                                                <?php if($key == 'view_file_management'): ?>
                                                     root="view_file_management"
                                                <?php endif; ?>
                                                value="{{ $key }}">{{ $value }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                     </div>
                <!-- file group -->
                <!-- cost group -->
                <div class="panel-heading">Cost management</div>
                    <div class="panel-body">
                      <div class="cost_group row">
                        <div class="col-lg-3">
                            @foreach($costGroup[0] as $key => $value)
                                <div class="checkbox">
                                    <label class="label-checkbox">
                                            <input type="checkbox" name="permission[]"
                                            <?php
                                                if(!empty($permission) && array_key_exists('user.'.$key, $permission)): ?>
                                                    checked="checked"
                                            <?php  endif; ?>
                                            <?php if($key == 'view_personal_cost'): ?>
                                                 root="view_personal_cost"
                                            <?php endif; ?>
                                            <?php if($key == 'view_project_cost'): ?>
                                                 root="view_project_cost"
                                            <?php endif; ?>
                                            value="{{ $key }}">{{ $value }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-lg-3 vert-offset-top-2">
                            @foreach($costGroup[1] as $key => $value)
                                <div class="checkbox">
                                    <label class="label-checkbox">
                                            <input type="checkbox"
                                                <?php
                                                    if(!empty($permission) && array_key_exists('user.'.$key, $permission)): ?>
                                                        checked="checked"
                                                <?php  endif; ?>
                                             name="permission[]" disabled="disabled" parent="view_project_cost" value="{{ $key }}">{{ $value }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                      </div>
                     </div>
                <!-- cost group -->

                <!-- defect group -->
                <div class="panel-heading">Defect management</div>
                    <div class="panel-body">
                      <div class="defect_group row">
                        <div class="col-lg-3">
                            @foreach($defectGroup as $key => $value)
                                <?php if($key == 'view_defect'): ?>
                                <div class="checkbox">
                                    <label class="label-checkbox">
                                        <input type="checkbox"
                                            <?php
                                                if(!empty($permission) && array_key_exists('user.'.$key, $permission)): ?>
                                                    checked="checked"
                                            <?php  endif; ?>
                                         name="permission[]" root="view_defect" value="{{ $key }}">{{ $value }}
                                     </label>
                                </div>
                                <?php endif; break; ?>
                            @endforeach
                        </div>
                        <div class="col-lg-3 vert-offset-top-2">
                            @foreach($defectGroup as $key => $value)
                                <?php if($key != 'view_defect'): ?>
                                    <div class="checkbox">
                                        <label class="label-checkbox">
                                                <input type="checkbox"
                                                    <?php
                                                        if(!empty($permission) && array_key_exists('user.'.$key, $permission)): ?>
                                                            checked="checked"
                                                    <?php  endif; ?>
                                                 name="permission[]" disabled="disabled" parent="view_defect" value="{{ $key }}">{{ $value }}
                                        </label>
                                    </div>
                                <?php endif; ?>
                            @endforeach
                        </div>
                      </div>
                     </div>
                     <!-- defect group -->

                    <!-- pq group -->
                    <div class="panel-heading">Productivity and quality</div>
                    <div class="panel-body">
                      <div class="pq_group row">
                        <div class="col-lg-3">
                            @foreach($pqGroup as $key => $value)
                                <div class="checkbox">
                                    <label class="label-checkbox">
                                            <input type="checkbox" name="permission[]"
                                            <?php
                                                if(!empty($permission) && array_key_exists('user.'.$key, $permission)): ?>
                                                    checked="checked"
                                            <?php  endif; ?>
                                            <?php if($key == 'view_quality_report_by_project'): ?>
                                                 root="view_quality_report_by_project"
                                            <?php endif; ?>
                                            <?php if($key == 'view_quality_report_by_member'): ?>
                                                 root="view_quality_report_by_member"
                                            <?php endif; ?>
                                             value="{{ $key }}">{{ $value }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                      </div>
                     </div>
                    <!-- pq group -->

                    <!-- pq group -->
                    <div class="panel-heading">Administration</div>
                    <div class="panel-body">
                      <div class="admin_group row">
                        <div class="col-lg-3">
                            @foreach($adminGroup[0] as $key => $value)
                                <div class="checkbox">
                                    <label class="label-checkbox">
                                            <input type="checkbox" name="permission[]"
                                            <?php
                                                if(!empty($permission) && array_key_exists('user.'.$key, $permission)): ?>
                                                    checked="checked"
                                            <?php  endif; ?>
                                            <?php if($key == 'admin_setting'): ?>
                                                 root="admin_setting"
                                            <?php endif; ?>
                                            <?php if($key == 'view_roles'): ?>
                                                 root="view_roles"
                                            <?php endif; ?>
                                            value="{{ $key }}">{{ $value }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-lg-3 vert-offset-top-2">
                            @foreach($adminGroup[1] as $key => $value)
                                <div class="checkbox">
                                    <label class="label-checkbox">
                                            <input type="checkbox" name="permission[]"
                                                <?php
                                                    if(!empty($permission) && array_key_exists('user.'.$key, $permission)): ?>
                                                        checked="checked"
                                                <?php  endif; ?>
                                             disabled="disabled" parent="view_roles" value="{{ $key }}">{{ $value }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                      </div>
                     </div>
                    <!-- pq group -->
                            <div class="col-lg-12 text-center">
                                <button type="submit" class="btn btn-primary" id="btnUpdate">Update</button>
                                <button type="button" class="btn btn-danger" id="btnReset">Reset</button>
                                <a href="{{ Route('setting.roles') }}">
                                    <button type="button" class="btn btn-info">Cancel</button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
@section('script')
    <script type="text/javascript" src="{{ asset('/js/roles/config.js') }}"></script>
@stop