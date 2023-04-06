{{--{{ dd($data) }}--}}
@foreach ($data as $key => $file)
<tr>
    <td>{{ ++$i }}</td>
    <td>{{ $file->name }}</td>
    <td>{{ $file->category }}</td>
    <td>{{ $file->created_at }}</td>
    <td>
        @if($file->type === 'spreadsheet')
        <input class="fileUrl" type="text" value="{{env('GOOGLE_EXCEL_FILE_URL').$file->docId.'/edit' }}" />
        <button class="copy-button btn btn-secondary"
            data-message="{{env('GOOGLE_EXCEL_FILE_URL').$file->docId.'/edit' }}">Copy</button>
        @endif
        @if($file->type === 'doc')
        <input class="fileUrl" type="text" value="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}" />
        <button class="copy-button btn btn-secondary"
            data-message="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}">Copy</button>
        @endif
        @if($file->type === 'ppt')
        <input class="fileUrl" type="text" value="{{env('GOOGLE_SLIDES_FILE_URL').$file->docId.'/edit'}}" />
        <button class="copy-button btn btn-secondary"
            data-message="{{env('GOOGLE_SLIDES_FILE_URL').$file->docId.'/edit'}}">Copy</button>
        @endif
        @if($file->type === 'xps')
        <input class="fileUrl" type="text" value="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}" />
        <button class="copy-button btn btn-secondary"
            data-message="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}">Copy</button>
        @endif
        @if($file->type === 'txt')
        <input class="fileUrl" type="text" value="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}" />
        <button class="copy-button btn btn-secondary"
            data-message="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}">Copy</button>
        @endif
    </td>
    <td>
        @if($file->type === 'spreadsheet')
        <a class="btn btn-image" href="{{env('GOOGLE_EXCEL_FILE_URL').$file->docId.'/edit' }}" target="_blank">
            <img src="{{ asset('images/sheets.png') }}" />
            Open
        </a>
        @endif
        @if($file->type === 'doc')
        <a class="btn btn-image" href="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}" target="_blank">
            <img src="{{asset('images/docs.png')}}" />
            Open</a>
        @endif
        @if($file->type === 'ppt')
        <a class="btn btn-image" href="{{env('GOOGLE_SLIDES_FILE_URL').$file->docId.'/edit'}}" target="_blank">
            <img src="{{asset('images/ppt.png')}}" />
            Open</a>
        @endif
        @if($file->type === 'xps')
        <a class="btn btn-image" href="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}" target="_blank">
            <img src="{{asset('images/xps.png')}}" />
            Open</a>
        @endif
        @if($file->type === 'txt')
        <a class="btn btn-image" href="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}" target="_blank">
            <img src="{{asset('images/docs.png')}}" />
            Open</a>
        @endif
        @if(Auth::user()->hasRole('Admin'))
        {!! Form::open(['method' => 'DELETE','route' => ['google-docs.destroy', $file->docId],'style'=>'display:inline']) !!}
        <button type="submit" class="btn btn-image" onclick="return confirm('{{ __('Are you sure you want to delete?') }}')"><img src="{{asset('/images/delete.png')}}" /></button>
        {!! Form::close() !!}

        <button style="padding:3px;" type="button" class="btn btn-image permissionupdate d-inline border-0" data-toggle="modal" data-readpermission="{{ $file->read }}" data-writepermission="{{ $file->write}}" data-docid="{{ $file->docId}}" data-target="#updateGoogleDocPermissionModal" data-id="{{ $file->id }}"><img width="2px;" src="{{asset('images/edit.png')}}"/></button>
        <button type="button" class="btn btn-image permissionview d-inline border-0" data-toggle="modal" data-readpermission="{{ $file->read }}" data-writepermission="{{ $file->write}}" data-docid="{{ $file->docId}}" data-target="#viewGoogleDocPermissionModal" data-id="{{ $file->id }}"><img width="2px;" src="{{asset('images/view.png')}}"/></button>
        <button style="padding:3px;" type="button" class="btn btn-image google-doc-update d-inline border-0" data-toggle="modal" data-action="{{ route('google-docs.edit', $file->id) }}" data-docid="{{ $file->docId}}" data-target="#updateGoogleDocModal" data-id="{{ $file->id }}"><img width="2px;" src="/images/edit.png"/></button>

        @endif
    </td>

</tr>
@endforeach
