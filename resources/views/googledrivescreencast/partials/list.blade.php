@foreach ($data as $key => $file)
    <tr>
        <td>{{ ++$i }}</td>
        <td>{{ $file->file_name }}</td>
        <td>{{ $file->created_at }}</td>
        <td>
                <input class="fileUrl" type="text" value="{{env('GOOGLE_DRIVE_FILE_URL').$file->google_drive_file_id}}/view?usp=share_link" />
                <button class="copy-button btn btn-secondary" data-message="{{env('GOOGLE_DRIVE_FILE_URL').$file->google_drive_file_id}}/view?usp=share_link">Copy</button>
            
        <td>
                <a class="btn btn-image" href="{{env('GOOGLE_DRIVE_FILE_URL').$file->google_drive_file_id}}/view?usp=share_link" target="_blank">
                    <img src="{{asset('images/docs.png')}}" />
                    Open</a>
                    @if(Auth::user()->isAdmin())
                    {!! Form::open(['method' => 'DELETE','route' => ['google-drive-screencast.destroy', $file->google_drive_file_id],'style'=>'display:inline']) !!}
                    <button type="submit" class="btn btn-image" onclick="return confirm('{{ __('Are you sure you want to delete?') }}')"><img src="/images/delete.png" /></button>
                    {!! Form::close() !!}
                    @endif
        </td>
    </tr>
@endforeach