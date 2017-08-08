@extends('layouts.master')
@section('title','List member assigned')
@section('breadcrumbs','Project Management - List members')
@section('style')
    <link href="{{ asset('css/custom/bootstrap-3-vert-offset-shim.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom/popup.css') }}" rel="stylesheet">
@stop
@section('content')
<div id="container">
  <div class="padding-md">
    <div class="main-header clearfix">
      <div class="page-title">
        <h3 class="no-margin">Project Management - List members</h3>
      </div>
    </div>
    <div class="panel panel-default">
        @if ($user->hasAccess('user.assign_member'))
        <div class="panel-heading padding-md">
            <div class="row">
                <div class="col-lg-12">
                    <a href="#">
                        <button class="btn btn-success" id="btnAddMember" project_id="{{ $project_id }}">Add member</button>
                    </a>
                </div>
            </div><!-- row -->
        </div>
        @elseif($permission != null)
            @if(strrpos($permission->permissions, "user.assign_member") !== false)
            <div class="panel-heading padding-md">
                <div class="row">
                    <div class="col-lg-12">
                        <a href="#">
                            <button class="btn btn-success" id="btnAddMember" project_id="{{ $project_id }}">Add member</button>
                        </a>
                    </div>
                </div><!-- row -->
            </div>
            @endif
        @endif
          <div class="panel-body">
            <div class="row padding-md analys-tarbar">
                <div class="col-md-6 text-left">
                    @if($data->total() > 0)
                        <label>Total number of records: <strong>{{ $data->total() }}</strong></label>
                    @else
                         <label>Total number of records: <strong>No Result</strong> </label>
                    @endif
                </div>
                <div class="col-md-6 text-right">
                    <div id="dataTable_length">
                        <form method="get">
                            <label for="choose_item">Item display on page:</label>
                            <select id="choose_item" name="limit" class="form-control input-sm inline-block" size="1" onchange="this.form.submit()">
                                @foreach($paginate as $key=>$values)
                                    @if(Request::get('limit',10) == $values)
                                        <option value="{{$key}}" selected>{{$values}}</option>
                                    @else
                                        <option value="{{$key}}">{{$values}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
              <table class="table table-bordered table-hover table-striped">
                <thead>
                  <tr >
                    <th width="5%" class="text-center">No</th>
                    <th width="25%" class="text-center">User</th>
                    <th width="30%" class="text-center">Email</th>
                    <th width="20%" class="text-center">Role</th>
                    <th width="10%" class="text-center">Active Status</th>
                    <th width="10%" class="text-center">Action</th>
                  </tr>
                </thead>
                <tbody>
                    <?php $stt = !empty(Request::get('page')) ? ((Request::get('page','1') - 1) * $limit + 1) : 1 ?>
                    @forelse($data as $item)
                    <tr>
                        <td class="text-center">
                            {{$stt++}}
                        </td>
                        <td>
                            <span class="text-muted tooltip-test" data-toggle="tooltip" data-original-title="Email : {{ $item->email }} ">
                                {{$item->last_name.' '.$item->first_name}}
                            </span>
                        </td>
                        <td>{{ $item->email }}</td>
                        <td>
                            {{ $item->role }}
                        </td>
                        @if($item->status == 0)
                            <td class="text-center">Inactive</td>
                            <td class="text-center">
                            @if ($user->hasAccess('user.inactive_member'))
                                <a href="javascript:void(0);" class="restore" restore="{{ $item->id }}" role_id="{{ $item->role_id }}"  data-toggle="tooltip" title="Restore member"><i class="fa fa-check-circle fa-lg"></i></a>
                            @elseif ($permission != null)
                                @if(strrpos($permission->permissions, "user.inactive_member") === false)
                                <a href="javascript:void(0);" class="restore" restore="{{ $item->id }}" role_id="{{ $item->role_id }}"  data-toggle="tooltip" title="Restore member"><i class="fa fa-check-circle fa-lg"></i></a>
                                @endif
                            @endif
                            </td>
                        @else
                            <td class="text-center">Active</td>
                            <td class="text-center">
                            @if ($user->hasAccess('user.edit_member'))
                                <a href="javascript:void(0);" member="{{$item->last_name.' '.$item->first_name}}" data-toggle="tooltip" title="Edit role" class="editMember" code="{{ $item->id }}" role_id="{{ $item->role_id }}"><i class="fa fa-edit fa-lg"></i></a> |
                            @elseif($permission != null)
                                @if(strrpos($permission->permissions, "user.edit_member") !== false)
                                <a href="javascript:void(0);" member="{{$item->last_name.' '.$item->first_name}}" data-toggle="tooltip" title="Edit role" class="editMember" code="{{ $item->id }}" role_id="{{ $item->role_id }}"><i class="fa fa-edit fa-lg"></i></a> |
                                @endif
                            @endif
                            @if ($user->hasAccess('user.inactive_member'))
                                <a href="javascript:void(0);" class="remove" remove="{{ $item->id }}" role_id="{{ $item->role_id }}" data-toggle="tooltip" title="Remove member"><i class="fa fa-times-circle fa-lg"></i></a> |
                            @elseif($permission != null)
                                @if(strrpos($permission->permissions, "user.inactive_member") !== false)
                                <a href="javascript:void(0);" class="remove" remove="{{ $item->id }}" role_id="{{ $item->role_id }}" data-toggle="tooltip" title="Remove member"><i class="fa fa-times-circle fa-lg"></i></a> |
                                @endif
                            @endif
                            @if ($user->hasAccess('user.delete_member'))
                                <a href="javascript:void(0);" class="delete" delete="{{ $item->id }}" role_id="{{ $item->role_id }}" data-toggle="tooltip" title="Delete member"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a>
                            @elseif($permission != null)
                                @if(strrpos($permission->permissions, "user.delete_member") !== false)
                                <a href="javascript:void(0);" class="delete" delete="{{ $item->id }}" role_id="{{ $item->role_id }}" data-toggle="tooltip" title="Delete member"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a>
                                @endif
                            @endif
                            </td>
                            @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="14">
                            <p class="text-left">empty</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
              </table>
                <div class="text-right">
                    {{ $data->appends([
                        'key'    => Request::get('key',''),
                        'limit'  => Request::get('limit',$limit),
                        'status' => $status
                       ])->links() }}
                </div>
            </div><!-- /table-responsive -->
          </div><!-- /panel-body -->
        </div><!-- /panel -->
  </div><!-- /padding-md -->
</div><!-- /main-container -->
@stop

@section('modal')
<div id="deleteModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4><center><strong>Delete member</b></strong></h4>
            </div>
            <div class="modal-body">
                <p>Do you want delete this member from project? If you delete, all data related this member in project will not be showed.</p>
            </div>
            <div class="modal-footer">
                <form id="frmDeleteMember" method="post" action="{{ URL::route( 'projects.members.delete' , ['project_id' => $project_id] ) }}">
                {{csrf_field()}}
                <input type="hidden" name="project_id" value="{{ $project_id }}">
                <input type="hidden" value="0" id="dataId" name="id" />
                <input type="hidden" name="ex_role" value="">
                <input type="hidden" value="{{ $data->count() }}" name="last_record" />
                <input type="hidden" value="{{Request::get('page')}}" name="page" />
                <input type="hidden" value="{{Request::get('limit', 10)}}" name="limit" />
                <input type="hidden" value="2" name="action" />
                <button class="btn btn-sm btn-success" name="btnDelete" type="submit">Delete</button>
                <button class="btn btn-sm btn-danger" data-dismiss="modal" type="button">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="removeModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4><center><strong>Remove member</b></strong></h4>
            </div>
            <div class="modal-body">
                <p>Do you want remove this member from project?</p>
            </div>
            <div class="modal-footer">
                <form id="frmRemoveMember" method="post" action="{{ URL::route( 'projects.members.delete' , ['project_id' => $project_id] ) }}">
                {{csrf_field()}}
                <input type="hidden" name="ex_role" value="">
                <input type="hidden" name="project_id" value="{{ $project_id }}">
                <input type="hidden" value="0" id="dataIdRemove" name="id" />
                <input type="hidden" value="{{ $data->count() }}" name="last_record" />
                <input type="hidden" value="{{Request::get('page')}}" name="page" />
                <input type="hidden" value="{{Request::get('limit', 10)}}" name="limit" />
                <input type="hidden" value="0" name="action" />
                <button class="btn btn-sm btn-success" name="btnDelete" type="submit">Remove</button>
                <button class="btn btn-sm btn-danger" data-dismiss="modal" type="button">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="restoreModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4><center><strong>Restore member</strong></center></h4>
            </div>
            <div class="modal-body">
                <p>Do you want restore this member from project?</p>
            </div>
            <div class="modal-footer">
                <form id="frmRestoreMember" method="post" action="{{ URL::route( 'projects.members.delete' , ['project_id' => $project_id] ) }}">
                {{csrf_field()}}
                <input type="hidden" name="ex_role" value="">
                <input type="hidden" name="project_id" value="{{ $project_id }}">
                <input type="hidden" value="0" id="dataIdRestore" name="id" />
                <input type="hidden" value="{{ $data->count() }}" name="last_record" />
                <input type="hidden" value="{{Request::get('page')}}" name="page" />
                <input type="hidden" value="{{Request::get('limit', 10)}}" name="limit" />
                <input type="hidden" value="1" name="action" />
                <button class="btn btn-sm btn-success" name="btnDelete" type="submit">Restore</button>
                <button class="btn btn-sm btn-danger" data-dismiss="modal" type="button">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- popup add member -->
<div id="addMemberPopup" class="modal">
    <!-- Modal content -->
    <div class="popup-content">
        <h2>Project : {{$project->name}} - Add New Member<span class="close-popup-add">×</span></h2>
        <div class="main-content">
                <div class="row">
                    <div class="col-lg-12 search">
                        <label for="searchMember">Search member</label>
                        <input class="form-control" id="searchMember" name="searchTo" type="text">
                        <span class="icon_search"><i class="fa fa-search fa-lg"></i></span>
                        <span class="error-message help-block">
                            <strong></strong>
                        </span>
                    </div>
                </div>
                <div class="row" id="membersList">
                    @forelse ($users as $key => $value)
                        <div class="col-lg-4 vert-offset-top-1">
                            <label class="label-checkbox">
                                <input type="checkbox"
                                <?php if(in_array($value['id'], $fillter)): ?>
                                    checked="checked" disabled="disabled" name="existed_members[]"
                                <?php endif; ?>
                                 name="members[]" value="{{ $value['id'] }}">
                                <span class="custom-checkbox"></span>
                                {{ $value['last_name']." ".$value['first_name']. " - " . $value['member_code'] }}
                            </label>
                        </div>
                    @empty
                       <p>empty</p>
                    @endforelse
                </div>
                <div class="page-right padding-md">
                    <ul class="pagination" id="pagination_member">
                        <li>
                            <a href="#" class="prev paging" data-page="1" pageTo = "" project_id="{{ $project_id }}">
                                <span>«</span>
                            </a>
                        </li>
                        <?php $x = 0;?>
                        @while($x <= $paged)
                            <?php $x++; ?>
                            <li>
                                <a href="#" class="paging pageTo = "{{ $x }}" project_id="{{ $project_id }}" data-page="{{$x}}">
                                    <span>{{ $x }}</span>
                                </a>
                            </li>
                        @endwhile
                        <li>
                            <a href="#" class="next paging" data-page="" pageTo = "" project_id="{{ $project_id }}">
                                <span>»</span>
                            </a>
                        </li>
                    </ul>
                 </div>
               <form id="frmAddNewMember" action="{{ Route('projects.members.store',['project_id' => $project_id]) }}" method="post">
                {{ csrf_field() }}
                <input type="hidden" id="project_id" name="project_id" value="{{ $project_id }}" type="text">
                <div class="row roleList">
                    <div class="col-lg-6">
                        <h2 class="role">Selected Member</h2>
                        <div class="selected_area">

                        </div>
                    </div>
                    <div class="col-lg-6">
                        <h2 class="role">Roles</h2>
                        @forelse ($roles as $key => $value)
                            <div class="col-lg-4 vert-offset-top-1">
                                <label class="label-checkbox">
                                    <input type="radio" name="roles" value="{{ $value['id'] }}">
                                    <span class="custom-radio"></span>
                                    {{ $value['name']}}
                                </label>
                            </div>
                        @empty
                           <p>empty</p>
                        @endforelse
                    </div>
                </div>

                <div class="row vert-offset-top-2">
                    <div class="col-lg-6 text-right">
                        <button type="button" class="btn btn-success" id="btnAddMemberPopup">Add</button>
                    </div>
                    <div class="col-lg-6 text-left">
                        <button type="button" class="btn btn-primary btn-cancel-popup">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- popup add member -->

<!-- popup edit member -->
<div id="editMemberPopup" class="modal">
    <!-- Modal content -->
    <div class="popup-content">
        <h2><span class="member_name"></span>Edit Role<span class="close-popup-edit">×</span></h2>
        <div class="main-content">
            <form id="frmEditMember" action="{{ Route('projects.members.update',['project_id' => $project_id]) }}" method="post">
                {{ csrf_field() }}
                <div class="row">
                    @forelse ($roles as $key => $value)
                        <div class="col-lg-4 vert-offset-top-1">
                            <label class="label-checkbox">
                                <input type="radio" name="roles" value="{{ $value['id'] }}">
                                <span class="custom-radio"></span>
                                {{ $value['name']}}
                            </label>
                        </div>
                    @empty
                       <p>empty</p>
                    @endforelse
                </div>
                <input type="hidden" name="code" value="">
                <input type="hidden" name="ex_role" value="">
                <input type="hidden" name="project_id" value="{{ $project_id }}">
                <input type="hidden" name="limit" value="{{ Request::get('limit', 10) }}">
                <input type="hidden" name="page" value="{{ Request::get('page') }}">
                <div class="row vert-offset-top-2">
                    <div class="col-lg-6 text-right">
                        <button type="submit" class="btn btn-primary" id="btnUpdateMemberPopup">Update</button>
                    </div>
                    <div class="col-lg-6 text-left">
                        <button type="button" class="btn btn-info btn-close-popup-edit">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- popup add member -->
@stop
@section('script')
<script type="text/javascript" src="{{ asset('/js/common/popup.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/assign_member/assign.js') }}"></script>
@stop