@if (isset($googleDoc))
    @forelse ($googleDoc as $file)
        <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{$file->name}}</td>
            <td>{{$file->created_at}}</td>
            <td>
                @if($file->type === 'spreadsheet')
                    <a href ="{{env('GOOGLE_EXCEL_FILE_URL').$file->docId.'/edit' }}" target="_blank"><input class="fileUrl" type="text" value="{{env('GOOGLE_EXCEL_FILE_URL').$file->docId.'/edit' }}" />
                    <button class="copy-button btn btn-secondary" data-message="{{env('GOOGLE_EXCEL_FILE_URL').$file->docId.'/edit' }}">Copy</button>
                @endif
                @if($file->type === 'doc')
                    <a href ="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}" target="_blank"><input class="fileUrl" type="text" value="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}"/></a>
                    <button class="copy-button btn btn-secondary"  data-message="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}">Copy</button>
                @endif
                @if($file->type === 'ppt')
                <a href ="{{env('GOOGLE_SLIDES_FILE_URL').$file->docId.'/edit'}}" target="_blank"><input class="fileUrl" type="text" value="{{env('GOOGLE_SLIDES_FILE_URL').$file->docId.'/edit'}}" /></a>
                    <button class="copy-button btn btn-secondary" data-message="{{env('GOOGLE_SLIDES_FILE_URL').$file->docId.'/edit'}}">Copy</button>
                @endif
                @if($file->type === 'xps')
                <a href ="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}" target="_blank"><input class="fileUrl" type="text" value="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}" /></a>
                    <button class="copy-button btn btn-secondary" data-message="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}">Copy</button>
                @endif
                @if($file->type === 'txt')
                <a href ="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}" target="_blank"> <input class="fileUrl" type="text" value="{{env('GOOGLE_DOC_FILE_URL').$file->docId.'/edit'}}"/></a>
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