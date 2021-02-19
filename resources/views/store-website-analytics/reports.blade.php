<table class="table table-bordered" id="store_website-analytics-report-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Request</th>
            <th>Response</th>
            <th>Type</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody class="searchable">
        @foreach($reports as $key => $report)
            <tr>
                <td>{{$report->id}}</td>
                <td>{{$report->request}}</td>
                <td>{{$report->response}}</td>
                <td>{{$report->type}}</td>
                <td>{{$report->created_at}}</td>
            </tr>
        @endforeach
    </tbody>
</table>