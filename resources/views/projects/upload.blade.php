@foreach($fileUploaded as $key => $val)
    <tr>
        <td>
            @if(!in_array($val->extension,$notFileImages))
                <img src="{{ Image::url( $filePath.$val->name ,110,128,array('crop')) }}">
            @else
                <img src="/img/file_icon.png">
            @endif
        </td>
        <td>{{ $val->size }} Kb</td>
        <td>{{ $val->updated_at }}</td>
        <td>{{ $val->name }}</td>
        <td class="action">
            <div class="inline">
                <a href="javascript:void(0);" name="{{ $val->name }}" dataId="{{ $val->id }}" class="delete">
                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                </a> |
                <a href="{{ Route('projects.download', $val->name) }}" target="_blank">
                    <i class="fa fa-cloud-download fa-lg"></i>
                </a> |
                <input type="checkbox" id="delete_item" class="delete_item" file_name="{{ $val->name }}" name="deleteCheck[]" value="{{ $val->id }}">
            </div>
         </td>
    </tr>
@endforeach