@extends('layouts.master')
@section('title','KPT management')
@section('breadcrumbs','KPT management')
@section('style')
    <link href="{{ asset('css/custom/date-form.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom/kpt-form.css') }}" rel="stylesheet">
@stop
@section('content')
<div class="padding-md">
    <div class="panel panel-default">
        <div class="panel-heading" id="form_heading">KPT management</div>
        <div class="panel-body" id="form_body">
            <form method="get" action="{{ URL::route('projects.kpt.list', $project_id ) }}"
                               id="search_form"
                               class="form-horizontal"
                               enctype="multipart/form-data">
                <div class="col-sm-12">
                    <div class="form-group col-md-4">
                        <label class="col-sm-4 control-label" for="version">Version</label>
                        <div class="col-sm-6">
                            <select class="form-control" name="version">
                                <option value=""> -- All --</option>
                                @if(!empty($version))
                                    @foreach($version as $version)
                                        @if (Request::get('version', '') == $version->id)
                                              <option value="{{ $version->id }}" selected>{{ $version->name }}</option>
                                        @else
                                              <option value="{{ $version->id }}">{{ $version->name }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="col-sm-4 control-label" for="category">Category</label>
                        <div class="col-sm-6">
                            <select class="form-control" name="category">
                                <option value=""> -- All --</option>
                                @if(!empty($category))
                                    @foreach($category as $category)
                                        @if (Request::get('category', '') == $category->id)
                                              <option value="{{ $category->id }}" selected>{{ $category->value }}</option>
                                        @else
                                              <option value="{{ $category->id }}">{{ $category->value }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="col-sm-4 control-label" for="type">Type</label>
                        <div class="col-sm-6">
                            <select class="form-control" name="type">
                                <option value=""> -- All --</option>
                                @if(!empty($category))
                                    @foreach($types as $key => $value)
                                        @if (Request::get('type', '') == $key)
                                              <option value="{{ $key }}" selected>{{ $value }}</option>
                                        @else
                                              <option value="{{ $key }}">{{ $value }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 text-center" id="divSearch">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="{{ URL::route('kpt.get.new', $project_id ) }}" class="btn btn-success" id="btn-new-kpt">New KPT</a>
                </div>
            </form>
        </div><!-- /panel-body -->
        <hr/>
        <div class="panel-body">
            <div class="panel-body">
                <div class="col-md-6">
                    @if($kpts->count() == 0)
                        <span class="text-left"><strong>Total number of records: 0</strong></span>
                    @else
                        <span class="text-left"><strong>Total number of records: {{ $kpts->total() }}</strong></span>
                    @endif
                </div>
                <div class="col-md-6">
                    <form method="get" class="pull-right">
                        <input type="hidden" name="version" value="{{Request::get('version','')}}">
                        <input type="hidden" name="category" value="{{Request::get('category','')}}">
                        <input type="hidden" name="type" value="{{Request::get('type','')}}">
                        <label for="choose_item">Item display on page: &nbsp; &nbsp;</label>
                        <select id="choose_item" name="limit" class="form-control input-sm inline-block" size="1" onchange="this.form.submit()">
                        @if(!empty($category))
                            @foreach($paginate as $key => $values)
                                @if(Request::get('limit', 10) == $values)
                                    <option value="{{$key}}" selected>{{$values}}</option>
                                @else
                                    <option value="{{$key}}">{{$values}}</option>
                                @endif
                            @endforeach
                        @endif
                        </select>
                    </form>
                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-hover table-striped" id="table_list_kpt">
                    <thead>
                      <tr>
                        <th width="5%"  class="text-center">#</th>
                        <th width="15%" class="text-center">Sprint/ version</th>
                        <th width="10%" class="text-center">Category</th>
                        <th width="8%" class="text-center">Type</th>
                        <th width="17%" class="text-center">Description</th>
                        <th width="17%" class="text-center">List plan action</th>
                        <th width="7%" class="text-center">Action</th>
                        <th width="8%"  class="text-center">Status</th>
                      </tr>
                    </thead>
                    <tbody>
                    @if(!empty($kpts))
                        @foreach($kpts as $kpt)
                          <tr>
                            <td class="text-center">{{ ++$number }}</td>
                            <td class="text-center">{{ $kpt->name }}</td>
                            <td class="text-center">{{ $kpt->value }}</td>
                            <td class="text-center">
                            @if(!empty($types))
                                @foreach($types as $key => $value)
                                    @if($kpt->type_id == $key)
                                            {{$value}}
                                    @endif
                                @endforeach
                            @endif
                            </td>
                            <td><div class="scroll">{{ $kpt->content }}</div></td>
                            <td><div class="scroll">{{ $kpt->action }}</div></td>
                            <td class="text-center">
                                <a href="{{ URL::route('kpt.get.edit', [$project_id, $kpt->id]) }}"><i class="fa fa-edit fa-lg"></i></a> |
                                <a href="javascript:void(0);" name="{{ $kpt->id }}" dataId="{{$kpt->id}}" class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                            </td>
                            <td class="text-center">
                            @if(!empty($status))
                                @foreach($status as $key => $value)
                                    @if($kpt->status == $key)
                                            {{$value}}
                                    @endif
                                @endforeach
                            @endif
                            </td>
                          </tr>
                          @endforeach
                      @endif
                    </tbody>
                  </table>
                  <div class="text-right">
                    {{ $kpts->appends(['version'    => Request::get('version',''),
                                       'category'   => Request::get('category',''),
                                       'type'       => Request::get('type',''),
                                       'limit'      => Request::get('limit','')
                                     ])
                       ->links() }}
                  </div>
                </div><!-- /table-responsive -->
              </div><!-- /panel-body -->
        </div>
    </div>
</div><!-- /padding-md -->
@stop

@section('modal')
<div id="deleteModal" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>Delete KPT</h4>
            </div>
            <div class="modal-body">
                <p>Do you want delete KPT?</p>
            </div>
            <div class="modal-footer">
                <form method="post" action="{{ Route('kpt.post.delete', $project_id) }}">
                {{csrf_field()}}
                <input type="hidden" value="0" id="kpt-id" name="id" />
                <input type="hidden" value="{{Request::get('page', 1)}}" name="page" />
                <button class="btn btn-sm btn-success" name="deletey" type="submit">Delete</button>
                <button class="btn btn-sm btn-danger" data-dismiss="modal" type="button">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('script')
    <script type="text/javascript" src="{{ asset('/js/project_kpt/kpt.index.js') }}"></script>
@stop