@if (isset($googleDoc))
    @forelse ($googleDoc as $file)
        <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{$file->name}}</td>
            <td>{{$file->created_at}}</td>
            <td>
                @if($file->type === 'spreadsheet')
                    <input class="fileUrl" type="text" value="{{env('GOOGLE_EXCEL_FILE_URL').$file->docId.'/edit' }}" />
                    <button class="copy-button btn btn-secondary" data-message="{{env('GOOGLE_EXCEL_FILE_URL').$file->docId.'/edit' }}">Copy</button>
                @endif
                @if($file->type === 'doc')
                    <input class="fileUrl" type="text" value="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}" />
                    <button class="copy-button btn btn-secondary"  data-message="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}">Copy</button>
                @endif
                @if($file->type === 'ppt')
                    <input class="fileUrl" type="text" value="{{env('GOOGLE_SLIDES_FILE_URL').$file->docId.'/edit'}}" />
                    <button class="copy-button btn btn-secondary" data-message="{{env('GOOGLE_SLIDES_FILE_URL').$file->docId.'/edit'}}">Copy</button>
                @endif
                @if($file->type === 'xps')
                    <input class="fileUrl" type="text" value="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}" />
                    <button class="copy-button btn btn-secondary" data-message="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}">Copy</button>
                @endif
                @if($file->type === 'txt')
                    <input class="fileUrl" type="text" value="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}" />
                    <button class="copy-button btn btn-secondary" data-message="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}">Copy</button>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="2">No record found</td></tr>
    @endforelse
@else
    <tr><td colspan="2">No record found</td></tr>    
@endif