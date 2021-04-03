<table class="table table-bordered table-striped sort-priority-scrapper">
    <thead>
        <tr>
            <th>Server Id</th>
            <th>Scraper name</th>
            <th>Full Path</th>
            <th>Created at</th>
        </tr>
    </thead>
    <tbody class="conent">
        @foreach ($statusHistory as $statusHist)
            <tr>
                <td>{{ $statusHist->server_id }}</td>
                <td>{{ $statusHist->scraper_name }}</td>
                <td>{{ $statusHist->scraper_string }}</td>
                <td>{{ $statusHist->created_at }}</td>
            </tr>
        @endforeach
   </tbody>
</table> 
