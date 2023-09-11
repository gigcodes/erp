<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th> No</th>
            <th width="8%">Name</th>
            <th width="12%">Email</th>
            <th width="20%">Join Time</th>
            <th width="25%">Leave Time</th>
            <th width="20%">Duration</th>
            <th width="25%">Created Date</th>
        </tr>
    </thead>
    <tbody class="show-search-password-list">
        @foreach($participants as $participant)
        <tr>
            <td>{{ $participant->id }}</td>
            <td>{{ $participant->name }}</td>
            <td>{{ $participant->email}}</td>
            <td>{{ $participant->join_time}}</td>
            <td>{{ $participant->leave_time}}</td>
            <td>{{ $participant->duration}}</td>
            <td>{{ $participant->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="pagination-container-participation"></div>