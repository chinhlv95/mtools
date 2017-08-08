@extends('layouts.master')
@section('title', 'Roles Setting')

@section('breadcrumbs','Roles')
@section('style')
    <link rel="stylesheet" href="{{ asset('/css/custom/date-form.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/roles/style.css') }}">
    <style>
        .table_scroll{
            overflow-x:auto; 
        }
    </style>
@stop
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading" id="form_heading">Roles and permissions</div>
        <div class="panel-body">
            <div class="table_scroll">
                <table class="table table-striped tblRoles" id="dataTable">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="10%" class="text-center">Role</th>
                            <th width="20%" class="text-center">Created By</th>
                            <th width="15%" class="text-center">Created date</th>
                            <th width="15%" class="text-center">Updated By</th>
                            <th width="15%" class="text-center">Update date</th>
                            <th width="25%" class="form-inline text-right">
                                <a href="{{ URL::route('setting.roles.create')}}">
                                    <button type="button" class="btn btn-success btn-sm" id="success-notification">Add New</button>
                                </a>
                                <a href="#">
                                    <button type="button" class="btn btn-danger btn-sm btn_delete_all" id="danger-notification">Delete</button>
                                </a>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="checkbox">
                                    <label class="label-checkbox">
                                        <input type="checkbox" class="check_box_all">
                                        <span class="custom-checkbox"></span>
                                    </label>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        @forelse ($roles as $key => $value)
                        <tr>
                            <td class="text-center">{{ $i++ }}</td>
                            <td class="text-left">{{ $value['name'] }}</td>
                            <td class="text-left">{{ $log[$key][0] }}</td>
                            <td class="text-center">{{ date_format(date_create($value['created_at']),'d/m/Y') }}</td>
                            <td class="text-left">{{ $log[$key][1] }}</td>
                            <td class="text-center">{{ date_format(date_create($value['updated_at']),'d/m/Y') }}</td>
                            <td class="action text-right form-inline">
                                <a href="{{ Route('setting.roles.edit',['roles_id' => $value['id']]) }}" name="updateRole"><i class="fa fa-pencil fa-lg"></i></a>
                                <?php if($value['name'] != 'Admin'): ?>
    <!--                                 <a href="{{ Route('setting.roles.edit',['roles_id' => $value['id']]) }}" name="updateRole"><i class="fa fa-pencil fa-lg"></i></a> -->
                                <?php endif; ?>
                                <?php if(!in_array($value['name'], $staticRoleNames)): ?>
                                     | <a href="#" class="btnDelete" name="{{ $value['id'] }}" value="{{ $value['id'] }}"><i class="fa fa-trash-o fa-lg"></i></a> |
                                    <div class="checkbox">
                                        <label class="label-checkbox">
                                            <input type="checkbox" name="deleteCheck[]" class="check_box_delete" value="{{ $value['id'] }}">
                                            <span class="custom-checkbox"></span>
                                        </label>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3">
                                <p>empty</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('modal')
<div id="deleteModal" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>Delete</h4>
            </div>
            <div class="modal-body"><p></p></div>
            <div class="modal-footer">
                <form method="post" action="{{Route('setting.roles.destroy')}}">
                {{csrf_field()}}
                <input type="hidden" value="0" id="roleId" name="id" />
                <input type="hidden" value="0" id="deleteAll" name="ids" />
                <button class="btn btn-sm btn-success btnDeleteModal" name="delete" type="submit">Delete</button>
                <button class="btn btn-sm btn-danger" data-dismiss="modal" type="button">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('script')
    <script type="text/javascript" src="{{ asset('/js/roles/config.js') }}"></script>
@stop