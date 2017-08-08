<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4>DM Ranking ({{count($resultDm)}})</h4>
</div>
<div class="modal-body col-md-12" id="content-project">
    <table class="table table-striped" id="dataTableDm">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Divison</th>
                <th>Name</th>
                <th>Productivity Rank</th>
                <th>Quality Rank</th>
        	</tr>
        </thead>
        <tbody>
            @foreach($resultDm as $item)
            <tr>
                <td style="text-align: center;">{{$item['rank'] + 1}}</td>
                <td class="text-left">{{$item['department_name']}}</td>
                <td class="text-left"><div data-toggle="tooltip" title="">{{$item['name']}}</div></td>
                <td style="text-align: center;">{{$item['rankP'] + 1}}</td>
                <td style="text-align: center;">{{$item['rankQ'] + 1}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="modal-footer">
</div>