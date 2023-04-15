<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>User</th>
            <th>{{ $seoType == 'user' ? 'User' : 'Price' }} History</th>
            <th>Date</th>
        </tr>
    </thead>

    @foreach ($seoHistory as $history)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $history->user->name ?? '-' }}</td>
            @if($history->type == 'user')
                <td>{{ $history->msgUser->name ?? '' }}</td>
            @else
                <td>{{ $history->message }}</td>
            @endif
            <td>{{ $history->created_at }}</td>
        </tr>
    @endforeach
</table>