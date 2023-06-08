<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th width="10%">Title</th>
            <th width="10%">Alert Date</th>
            <th width="10%">Event Type</th>
            <th width="8%">Is Read ?</th>
        </tr>
    </thead>
    <tbody class="show-search-password-list">
        @foreach($eventAlerts as $eventAlert)
        <tr>
            <td>{{ $eventAlert->title }}</td>
            <td>{{ $eventAlert->start}}</td>
            <td>{{ $eventAlert->event_type_name}}</td>
            <td><input type="checkbox" name="is_read"></td>
        </tr>
        @endforeach
    </tbody>
</table>