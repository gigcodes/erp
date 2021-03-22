<table class="table table-bordered table-striped sort-priority-scrapper">
    <thead>
        <tr>
            <th>#</th>
            <th>Scraper name</th>
            <th>Comment</th>
            <th>Created at</th>
        </tr>
    </thead>
    <tbody class="conent">
        @foreach ($histories as $history)
            <tr>
                <td>{{ $history->scraper_id }}</td>
                <td>{{ $history->scraper_name }}</td>
                <td>{{ $history->comment }}</td>
                <td>{{ $history->created_at }}</td>
            </tr>
        @endforeach
   </tbody>
   {{$histories->appends(request()->except('page'))->links()}} 
</table> 
