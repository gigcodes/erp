<table class="table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Created Date</th>
            <th>Last Updated</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($history as $data)
            <tr>
                <td>{{ $data->title }}</td>
                <td>{{ $data->builder_created_date }}</td>
                <td>{{ $data->builder_last_updated }}</td>
                <td>
                    <a href="{{ route('uicheck.get-builder-html', $data->id) }}" target="blank" class="btn btn-sm btn-primary">View</a>
                    <a href="{{ route('uicheck.get-builder-download-html', $data->id) }}" class="btn btn-sm btn-success">Download</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>