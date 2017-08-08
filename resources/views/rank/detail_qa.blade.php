<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4>{{$dataQas['name']}} : {{$dataQas['department_name']}}</h4>
</div>
<div class="modal-body col-md-12" id="content-project">
    <div class="col-md-6">
        <div class="panel panel-default bg-success">
            <div class="panel-body">
                <h3>Productivity</h3>
            </div>
            <div class="list-group">
                <a class="list-group-item">
                    <span class="m-left-xs">Test Case Creation/mm</span>
                    <span class="badge badge-danger">{{ round(Helpers::writeNumber($dataQas['testcase_create'], $dataQas['createTc_workload']/$mm),1) }}</span>
                </a>
                <a class="list-group-item">
                    <span class="m-left-xs">Test Case Excution/mm</span>
                    <span class="badge badge-danger">{{ round(Helpers::writeNumber($dataQas['testcase_test'], $dataQas['test_workload']/$mm),1) }}</span>
                </a>
                <a class="list-group-item">
                    <span class="m-left-xs">TASK/mm</span>
                    <span class="badge badge-danger">{{ round(Helpers::writeNumber($dataQas['task'], $dataQas['workload']/$mm),1) }}</span>
                </a>
            </div><!-- /list-group -->
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default bg-success">
            <div class="panel-body">
                <h3>Quality</h3>
            </div>
            <div class="list-group">
                <a class="list-group-item">
                    <span class="m-left-xs">Bug/1000TC</span>
                    <span class="badge badge-warning">{{ round(Helpers::writeNumber($dataQas['foundbug_weighted'], ($dataQas['testcase_test']/1000)),1) }}</span>
                </a>
                <a class="list-group-item">
                    <span class="m-left-xs">Bug/mm</span>
                    <span class="badge badge-warning">{{ round(Helpers::writeNumber($dataQas['foundbug_weighted'], $dataQas['workload']/$mm),1) }}</span>
                </a>
            </div><!-- /list-group -->
        </div>
    </div>
</div>
<div class="modal-footer">
</div>