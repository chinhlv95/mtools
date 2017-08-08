<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4>{{$dataDevs['name']}} : {{$dataDevs['department_name']}}</h4>
</div>
<div class="modal-body col-md-12" id="content-project">
    <div class="col-md-6">
        <div class="panel panel-default bg-success">
            <div class="panel-body">
                <h3>Productivity</h3>
            </div>
            <div class="list-group">
                <a class="list-group-item">
                    <span class="m-left-xs">KLOC/mm</span>
                    <span class="badge badge-danger">{{ round(Helpers::writeNumber($dataDevs['kloc'], $dataDevs['workload']/$mm),1)}}</span>
                </a>
                <a class="list-group-item">
                    <span class="m-left-xs">Assigned Bug/mm</span>
                    <span class="badge badge-danger">{{ round(Helpers::writeNumber($dataDevs['bug_weighted'], $dataDevs['workload']/$mm),1) }}</span>
                </a>
                <a class="list-group-item">
                    <span class="m-left-xs">TASK/mm</span>
                    <span class="badge badge-danger">{{ round(Helpers::writeNumber($dataDevs['task'], $dataDevs['workload']/$mm),1) }}</span>
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
                    <span class="m-left-xs">Made Bug/KLOC</span>
                    <span class="badge badge-warning">{{ round(Helpers::writeNumber($dataDevs['madebug_weighted'], $dataDevs['kloc']),1) }}</span>
                </a>
                <a class="list-group-item">
                    <span class="m-left-xs">Made Bug/mm</span>
                    <span class="badge badge-warning">{{ round(Helpers::writeNumber($dataDevs['madebug_weighted'], $dataDevs['workload']/$mm),1) }}</span>
                </a>
            </div><!-- /list-group -->
        </div>
    </div>
</div>
<div class="modal-footer">
</div>