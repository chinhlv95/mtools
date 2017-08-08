@extends('layouts.master')
@section('title', 'Content Mapping')

@section('breadcrumbs','Content Mapping')
@section('style')
    <link href="{{ asset('css/custom/date-form.css') }}" rel="stylesheet">
@stop
@section('content')
<div class="padding-md">
    <div class="panel panel-default">
        <div class="panel-heading" id="form_heading">Content Mapping</div>
        <div class="panel-body" id="form_body">
            <form method="get" id="search_form" action="{{ URL::route('content-management.show') }}" class="form-horizontal" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="col-lg-3 control-label">
                        Setting type:
                    </label>
                    <div class="col-lg-6">
                        <select class="form-control" name="type" id="type">
                            @foreach($setting_type as $key => $value)
                                <option <?php if(!empty($type_id) && $type_id == $key): ?> selected="selected" <?php endif; ?> value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">
                        Source:
                    </label>
                    <div class="col-lg-6">
                        <select class="form-control" name="source" id="source">
                            @foreach($source as $key => $value)
                                <option <?php if(!empty($selected_source) && $selected_source == $key): ?> selected="selected" <?php endif; ?> value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="col-lg-6 text-right">
                        <button type="button" class="btn btn-primary btn-search">Search</button>
                    </div>
                    <div class="col-lg-6 text-left">
                        <button type="button" class="btn btn-success btn-create">Create</button>
                    </div>
                </div>
             </form>
             <form method="get" id="create_form" action="{{ URL::route('content-management.create') }}" enctype="multipart/form-data">
                <input type="hidden" id="source_id" name="source_id" value="0">
                <input type="hidden" id="type_id" name="type_id" value="0">
            </form>
        </div>

        <div class="panel-body">
            <div class="table-responsive" id="scroll-x">
                <table class="table table-bordered table-hover table-striped tbl-source" id="responsiveTable">
                    <thead>
                        <th with="10%">No</th>
                        <th with="40%">Source @if(!empty($selected_source)){{ $selected_source }}@endif</th>
                        <th with="40%">Related Name</th>
                        <th with="10%">Action</th>
                    </thead>
                    <tbody class="tbody">
                        <?php $stt = 1; ?>
                        @if(!empty($result))
                            @foreach ($result as $key => $value)
                                <tr>
                                    <td>{{ $stt++ }}</td>
                                    <td>{{ $value['name'] }}</td>
                                    <td>
                                       @foreach($map_name as $k => $v)
                                        @if($value['related_id'] == $v['key'] && $v['key'] != 0)
                                            {{ $v['name'] }}
                                            @break
                                        @endif
                                       @endforeach
                                    </td>
                                    <td class="action text-center form-inline">
                                        <a href="{{ Route('content-management.edit',['item_id' => $value['id'],'source_id' => $value['source_id'],'type_id' => $type_id]) }}"><i class="fa fa-pencil fa-lg"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                         @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
@section('script')
    <script type="text/javascript" src="{{ asset('/js/content_management/run.js') }}"></script>
@stop