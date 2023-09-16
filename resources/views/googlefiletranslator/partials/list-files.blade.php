@foreach ($data as $key => $file)
<tr>
    <td>{{ ++$i }}</td>
    <td>{{ $file->name }}</td>
    <td>{{ $file->updated_at }}</td>
    <td>{{ $file->created_at }}</td>
    <td>
        @if($file->download_status== 1)
        <a class="btn btn-image" href="{{ route('googlefiletranslator.download',$file->name) }}">Download File</a>
        @else 
        <button class="btn btn-image" onclick="showPermissionAlert()">Download File</button>
        @endif
        {!! Form::open(['method' => 'DELETE','route' => ['googlefiletranslator.destroy', $file->id],'style'=>'display:inline']) !!}
        <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
        {!! Form::close() !!}
        <a class="btn btn-image" href="{{ route('googlefiletranslator.list-page.view',$file->id) }}" target="_blank">View File</a>
    </td>
</tr>
@endforeach


<script>
    function showPermissionAlert() {
        alert('Permission required to download this file.');
    }
</script>