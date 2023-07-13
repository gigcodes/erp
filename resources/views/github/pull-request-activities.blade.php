
<div class="modal-header">
    <h5 class="modal-title">Pull Request Activities (<span>{{ $totalCount }}</span>)</h5>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body" >
    <input type="hidden" id="repo"/>
    <input type="hidden" id="pullNumber"/>
    <table id="repository-table" class="table table-bordered">
        <thead>
            <tr>
                <th>Activity ID</th>
                <th>User</th>
                <th>Event</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activities as $activity)
                @if (isset($activity['id']))
                <tr>
                    <td>{{$activity['id']}}</td>
                    <td>{{$activity['user']['login']}}</td>
                    <td>{{$activity['event']}}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    <!-- Display pagination links -->
    {{ $activities->links() }}
</div>