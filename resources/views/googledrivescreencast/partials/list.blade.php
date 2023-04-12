@foreach ($data as $key => $file)
    <tr>
        <td>{{ ++$i }}</td>
        <td>{{ $file->file_name }}</td>
        <td>#DEVTASK-{{ $file->developer_task_id }}</td>
        <td>{{ $file->file_creation_date }}</td>
        <td>
                <input class="fileUrl" type="text" value="{{env('GOOGLE_DRIVE_FILE_URL').$file->google_drive_file_id}}/view?usp=share_link" />
                <button class="copy-button btn btn-secondary" data-message="{{env('GOOGLE_DRIVE_FILE_URL').$file->google_drive_file_id}}/view?usp=share_link">Copy</button>
        </td>   
        <td>{{ $file->remarks }}</td> 
        <td>{{ $file->created_at }}</td>
        <td>
                <a class="btn btn-image" href="{{env('GOOGLE_DRIVE_FILE_URL').$file->google_drive_file_id}}/view?usp=share_link" target="_blank">
                    <img src="{{asset('images/docs.png')}}" />
                    Open</a>
                    @if(Auth::user()->isAdmin())
                    {!! Form::open(['method' => 'DELETE','route' => ['google-drive-screencast.destroy', $file->google_drive_file_id],'style'=>'display:inline']) !!}
                    <button type="submit" class="btn btn-image" onclick="return confirm('{{ __('Are you sure you want to delete?') }}')"><img src="/images/delete.png" /></button>
                    {!! Form::close() !!}
                    <button style="padding:3px;" type="button" class="btn btn-image filepermissionupdate d-inline border-0" data-toggle="modal" data-readpermission="{{ $file->read }}" data-writepermission="{{ $file->write}}" data-fileid="{{ $file->google_drive_file_id}}" data-target="#updateGoogleFilePermissionModal" data-id="{{ $file->id }}"><img width="2px;" src="/images/edit.png"/></button>
                    @endif
        </td>
    </tr>
@endforeach