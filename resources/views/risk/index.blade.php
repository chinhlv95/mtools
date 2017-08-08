@extends('layouts.master')
@section('title', 'Risk management')

@section('breadcrumbs','Risk management')

@section('style')
    <link href="{{ asset('css/custom/css/scroll_risk.css') }}" rel="stylesheet">
@stop
@section('content')

<div id="container">
    <div class="padding-md">
        <div class="main-header clearfix">
            <div class="page-title">
                <h3 class="no-margin">Risk management</h3>
              </div>
        </div>
        <div class="panel panel-default">
            <div class="padding-md">
                <div class="panel-body">
                    <div id="dataTable_filter" class="dataTables_filter col-md-12 form-horizontal">
                        <div class="col-md-2">
                            <a href="{{Route('risk.getCreate',['project_id'=>$project_id])}}" class="btn btn-success">New Risk</a>
                        </div>
                        <form method="get">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="category_id">Category</label>
                                    <div class="col-sm-9">
                                      <select name="category_id" class="form-control" onchange="this.form.submit()">
                                            <option value="">--All--</option>
                                        @foreach($categories as $category)
                                            @if($category->id == Request::get('category_id',0))
                                                <option value="{{$category->id}}" selected='selected'>{{$category->value}}</option>
                                            @else
                                                <option value="{{$category->id}}">{{$category->value}}</option>
                                            @endif
                                        @endforeach
                                      </select>
                                      <p class="help-block"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="strategy">Strategy</label>
                                    <div class="col-sm-9">
                                      <select name="strategy" class="form-control" onchange="this.form.submit()">
                                            <option value="">--All--</option>
                                            @foreach($strategies as $key=>$value)
                                                @if(Request::get('strategy',0) == $key)
                                                    <option value="{{$key}}" selected='selected'>{{$value}}</option>
                                                @else
                                                    <option value="{{$key}}">{{$value}}</option>
                                                @endif
                                            @endforeach
                                      </select>
                                      <p class="help-block"></p>
                                    </div>
                                </div>
                          </div>
                          <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="permissions">Status</label>
                                    <div class="col-sm-9">
                                      <select name="status" class="form-control">
                                            <option value="">--All--</option>
                                            @foreach($status as $key=>$value)
                                                @if(Request::get('status',0) == $key)
                                                    <option value="{{$key}}" selected='selected'>{{$value}}</option>
                                                @else
                                                    <option value="{{$key}}">{{$value}}</option>
                                                @endif
                                            @endforeach
                                      </select>
                                      <p class="help-block"></p>
                                    </div>
                                </div>
                          </div>
                          <div class="col-sm-1">
                                <button type="submit" class="btn btn-primary">Search</button>
                          </div>
                      </form>
                    </div>
                </div>
            <div class="panel-body">
                <div class="row padding-md analys-tarbar">
                    <div class="col-md-6 text-left">
                        @if($count > 0)
                            <label>Total number of records: <strong>{{$count}}</strong></label>
                        @else
                             <label>Total number of records: <strong>No Result</strong> </label>
                        @endif
                    </div>
                    <div class="col-md-6 text-right">
                        <div id="dataTable_length">
                            <form method="get">
                                <label for="choose_item">Item display on page:</label>
                                <input type="hidden" name="category_id" value="{{Request::get('category_id','')}}">
                                <input type="hidden" name="strategy" value="{{Request::get('strategy','')}}">
                                <input type="hidden" name="status" value="{{Request::get('status','')}}">
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
                <div class="panel-body">
                    <div class="table-responsive">
                        <table id="responsiveTable" class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">ID</th>
                                    <th width="20%">Risk title</th>
                                    <th width="10%">Category</th>
                                    <th width="5%">Propability(%)</th>
                                    <th width="10%">Impact (1-5)</th>
                                    <th width="5%">Level</th>
                                    <th width="10%">Strategy</th>
                                    <th width="20%">Mitigation plan</th>
                                    <th width="10%">Status</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody class="tbody">
                                    @if(!empty($risks))
                                        @foreach($risks as $risk)
                                            <tr>
                                                <td>
                                                    {{++$stt}}
                                                </td>
                                                <td>
                                                    <div class="scroll">
                                                        {{$risk->risk_title}}
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    {{$risk->value}}
                                                </td>
                                                <td>
                                                    {{$risk->propability}}
                                                </td>
                                                <td>
                                                    {{$risk->impact}}
                                                </td>
                                                <td>
                                                    {{$risk->propability*$risk->impact/100}}
                                                </td>
                                                <td class="text-center">
                                                    {{$strategies[$risk->strategy]}}
                                                </td>
                                                <td>
                                                    <div class="scroll">{{$risk->mitigration_plan}}</div>
                                                </td>
                                                <td class="text-center">
                                                    {{ $risk->status == 0 ? '' : $status[$risk->status]}}
                                                </td>
                                                <td>
                                                    <center>
                                                        <a name="edit{{$risk->id}}" href="{{Route('risk.getEdit',['project_id'=>$project_id,'riskId'=>$risk->id,'page'=>Request::get('page',1)])}}"><i class="fa fa-edit fa-lg"></i></a> |
                                                        <a href="#" name="delete{{$risk->id}}" dataId="{{$risk->id}}" class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                    </center>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="9">
                                                <p>empty</p>
                                            </td>
                                        </tr>
                                    @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="text-right">
                    <?php
                    $limit = Request::get('limit',10);
                    $category_id= Request::get('category_id','');
                    $strategy= Request::get('strategy','');
                    $status= Request::get('status','');
                    ?>
                    {{
                        $risks->appends(array(
                            'limit' => $limit,
                            'category_id'=>$category_id,
                            'strategy'=>$strategy,
                            'status'=>$status
                            )
                        )->links()
                    }}
                </div>
            </div>
             </div>
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
            <div class="modal-body">
                <p>Do you want delete?</p>
            </div>
            <div class="modal-footer">
                <form method="post" action="{{Route('risk.postDelete',['project_id'=>$project_id])}}">
                {{csrf_field()}}
                <input type="hidden" value="0" id="risk-id" name="id" />
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
<script type="text/javascript">
     $(document).ready(function(){
         $('.delete').click(function(){
             $('#risk-id').val($(this).attr('dataId'));
             $('#deleteModal').modal({'show': true});
         });

     });
 </script>
@stop
