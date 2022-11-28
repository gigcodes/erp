{{--{{ dd($data) }}--}}
@foreach ($data as $key => $file)
    <tr>
        <td>{{ ++$i }}</td>
        <td>{{ $file->name }}</td>
        <td>{{ $file->created_at }}</td>
        <td>
            @if($file->type === 'spreadsheet')
                <input class="fileUrl" type="text" value="https://docs.google.com/spreadsheets/d/{{ $file->docId }}/edit" />
                <button class="copy-button btn btn-secondary" data-message="https://docs.google.com/spreadsheets/d/{{ $file->docId }}/edit">Copy</button>
            @endif
            @if($file->type === 'doc')
                <input class="fileUrl" type="text" value="https://docs.google.com/document/d/{{ $file->docId }}/edit" />
                <button class="copy-button btn btn-secondary" data-message="https://docs.google.com/document/d/{{ $file->docId }}/edit">Copy</button>
            @endif
            </td>
        <td>
            @if($file->type === 'spreadsheet')
                <a class="btn btn-image" href="https://docs.google.com/spreadsheets/d/{{ $file->docId }}/edit" target="_blank">
                    <img src="{{ asset('images/sheets.png') }}" />
                    Open
                </a>
            @endif
            @if($file->type === 'doc')
                <a class="btn btn-image" href="https://docs.google.com/document/d/{{ $file->docId }}/edit" target="_blank">
                    <img src="{{asset('images/docs.png')}}" />
                    Open</a>
            @endif
        </td>
    </tr>
@endforeach