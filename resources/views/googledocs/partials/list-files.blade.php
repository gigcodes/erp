{{--{{ dd($data) }}--}}
@php
    $enum = [
        "App\DeveloperTask" => "DEVTASK-",
        "App\Task" => "TASK-",
    ];
@endphp
@foreach ($data as $key => $file)
<tr>
    @if(Auth::user()->isAdmin())
        <td><input type="checkbox" name="google_doc_check" class="google_doc_check" value="{{ $file->id }}" data-file="{{ $file->docId }}" data-id="{{ $file->id }}"></td>
    @endif
    <td>{{ ++$i }}</td>
    <td class="expand-row" style="word-break: break-all">
        <span class="td-mini-container">
           {{ strlen($file->name) > 15 ? substr($file->name, 0, 15).'...' :  $file->name}}
        </span>
        <span class="td-full-container hidden">
            {{ $file->name }}
        </span>
    </td>
    <td>
        {{-- $googleDocCategory     --}}
        <select class="form-control select-multiple0 select-multiple2 update-category" name="type[]" data-docs_id="{{$file->id}}" data-placeholder="Select Category">
            <option>Select category</option>
            @if (isset($googleDocCategory) && count($googleDocCategory) > 0)
                @foreach ($googleDocCategory as $key => $category)
                    <option value="{{$key}}" {{$key == $file->category ? "selected" : ""}}>{{$category}}</option>
                @endforeach
            @endif
        </select>
    </td>
    <td>
        @if (isset($file->belongable_type))
            {{$enum[$file->belongable_type] ?? ""}}{{$file->belongable_id}}
        @else
            -
        @endif
    </td>
    <td>
        @if (isset($file->created_by))
            {{$file->user->name}}
        @else
            -
        @endif
    </td>
    <td>{{ $file->created_at }}</td>
    <td>
        @if($file->type === 'spreadsheet')
        <a href ="{{env('GOOGLE_EXCEL_FILE_URL').$file->docId.'/edit' }}" target="_blank" style="display:flex; gap:5px"><input class="fileUrl" type="text" value="{{env('GOOGLE_EXCEL_FILE_URL').$file->docId.'/edit' }}" />
        <button class="copy-button btn btn-secondary float-right"
            data-message="{{env('GOOGLE_EXCEL_FILE_URL').$file->docId.'/edit' }}"><i class="fa fa-copy"></i><</button>
        @endif
        @if($file->type === 'doc')
        <a href ="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}" target="_blank" style="display:flex; gap:5px"><input class="fileUrl" type="text" value="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}" />
    
        <button class="copy-button btn btn-secondary float-right" data-message="{{env('GOOGLE_SLIDES_FILE_URL').$file->docId.'/edit'}}"><i class="fa fa-copy"></i></button>
        @endif
        @if($file->type === 'ppt')
        <a href ="{{env('GOOGLE_SLIDES_FILE_URL').$file->docId.'/edit'}}" target="_blank" style="display:flex; gap:5px"><input class="fileUrl" type="text" value="{{env('GOOGLE_SLIDES_FILE_URL').$file->docId.'/edit'}}" />
        <button class="copy-button btn btn-secondary float-right"
            data-message="{{env('GOOGLE_SLIDES_FILE_URL').$file->docId.'/edit'}}" style="display:flex; gap:5px"><i class="fa fa-copy"></i></button>
        @endif
        @if($file->type === 'xps')
        <a href ="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}" target="_blank" style="display:flex; gap:5px"><input class="fileUrl" type="text" value="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}" />
        <button class="copy-button btn btn-secondary float-right"
            data-message="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}"><i class="fa fa-copy"></i></button>
        @endif
        @if($file->type === 'txt')
        <a href ="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}" target="_blank" style="display:flex; gap:5px"> <input class="fileUrl" type="text" value="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}" />
        <button class="copy-button btn btn-secondary float-right"
            data-message="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}"><i class="fa fa-copy"></i></button>
        @endif
    </td>
    <td>
        <div class="action" style="display:flex;">
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
         </div>
    </td>

</tr>
@endforeach


<script type="text/javascript">

$(document).on('click', '.expand-row', function () {
    var selection = window.getSelection();
    if (selection.toString().length === 0) {
        $(this).find('.td-mini-container').toggleClass('hidden');
        $(this).find('.td-full-container').toggleClass('hidden');
    }
});

</script>

