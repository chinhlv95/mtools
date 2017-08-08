@extends('layouts.master')
@section('title','Version management')
@section('breadcrumbs','Version management')
@section('style')
    <link href="{{ asset('css/custom/css/scroll_risk.css') }}" rel="stylesheet">
@stop
@section('content')
<div class="padding-md">
    <div class="main-header clearfix">
      <div class="page-title">
        <h3 class="no-margin">Version management</h3>
      </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading padding-md">
        <div class="row">
                <div class="col-sm-12"><a class="btn btn-success" href="{{ URL::route('version.create',$project_id) }}">Create</a></div><!-- col -->
        </div><!-- row -->
        <div class="row" style="margin-top: 20px">
        {!! Form::open(array('method' => 'GET', 'url'=>Route('version.index', $project_id), 'id'=>'form-search-users', 'role'=>'search')) !!}
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="form-group">
                        <input type="text" class="form-control" name="keyword" value="{{Request::get('keyword', '')}}">
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="form-group">
                        <button class="btn btn-info" type="submit">Search</button>
                    </div>
                </div>
                {!! Form::close() !!}
        </div>
        <div class="panel-body">
            <div class="col-md-6">
                @if($version->total() == 0)
                    <span class="text-left"><strong>Total number of records: No Result</strong></span>
                @else
                    <span class="text-left"><strong>Total number of records: {{ $version->total() }}</strong></span>
                @endif
            </div>
            <div class="col-md-6">
                <form method="get" class="pull-right">
                    <input type="hidden" value="{{Request::get('keyword', '')}}" name="keyword" />
                    <label for="choose_item">Item display on page: &nbsp; &nbsp;</label>
                    <select id="choose_item" name="limit" class="form-control input-sm inline-block" size="1" onchange="this.form.submit()">
                      @if(!empty($paginate))
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
            <div class="table-responsive" >
                <table class="table table-bordered table-hover table-striped tbl-project" id="responsiveTable">
                    <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Name</th>
                        <th width="15">Status</th>
                        <th width="25%">Description</th>
                        <th width="15%">Start date</th>
                        <th width="15%">End date</th>
                        <th width="10%">Action</th>
                    </tr>
                    </thead>
                    <tbody class="tbody">
                    <?php $flag = false; ?>
                    @forelse ($version as $data)
                        <tr>
                           <td>{{ ++$stt }}</td>
                           <td class="text-left"><div class="scroll">{{ $data->name }}</div></td>
                           <td class="text-left">
                            @if ($data->status !=0)
                              {{ $status[$data->status] }}
                            @endif
                           </td>
                           <td class="text-left">
                               <div class="scroll">{{$data->description}}</div>
                           </td>
                           <td class="text-left">
                            @if( empty($data['start_date']) || $data->start_date == '0000-00-00 00:00:00')
                            @else
                               {{ date('d/m/Y',strtotime(str_replace('/', '-', $data['start_date']))) }}
                            @endif
                           </td>
                           <td class="text-left">
                            @if( empty($data['end_date']) || $data->end_date == '0000-00-00 00:00:00')
                            @else
                               {{ date('d/m/Y',strtotime(str_replace('/', '-', $data['end_date']))) }}
                            @endif
                           </td>
                           <td id = "action">
                                <a href="{{ URL::route('version.edit',[$project_id, $data->id]) }}"><i class="fa fa-edit fa-lg"></i></a>|
                                <a href="javascript:void(0);" name="{{ $data->id }}" dataId="{{$data->id}}" class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                            </td>
                         </tr>
                    @empty
                    <tr>
                        <td colspan="14">
                            <p>empty</p>
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="page-right">
                    {{
                        $version->appends(array(
                            'keyword' => Request::get('keyword',''),
                            'limit' => Request::get('limit',10)
                            )
                        )->links()
                    }}
                </div><!-- paging -->
            </div><!-- /table-responsive -->
        </div><!-- /panel-body -->
    </div><!-- /panel -->
</div>
@stop
@section('modal')
<div id="deleteModal" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>Delete project version</h4>
            </div>
            <div class="modal-body">
                <p>Do you want delete project version?</p>
            </div>
            <div class="modal-footer">
                <form method="post" action="{{ Route('version.delete', $project_id) }}">
                {{csrf_field()}}
                <input type="hidden" value="0" id="data-id" name="id" />
                <input type="hidden" value="{{Request::get('keyword', '')}}" name="keyword" />
                <input type="hidden" value="{{ $count }}" name="count" />
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
    <script type="text/javascript" src="{{ asset('/js/project_version/version.js') }}"></script>
@stop