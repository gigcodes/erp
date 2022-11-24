{{--{{ dd($data) }}--}}
@foreach ($data as $key => $file)
    <tr>
        <td>{{ ++$i }}</td>
        <td>{{ $file->name }}</td>
        <td>{{ $file->created_at }}</td>
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