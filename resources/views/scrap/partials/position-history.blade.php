<table class="table table-bordered table-striped sort-priority-scrapper">
    <thead>
        <tr>
            <th width="20%">Scraper name</th>
            <th width="60%">Comment</th>
            <th width="20%">Created at</th>
        </tr>
    </thead>
    <tbody class="conent">
        @foreach ($histories as $history)
            <tr>
                <td>{{ $history->scraper_name }}</td>
                <td>{{ $history->comment }}</td>
                <td>{{ $history->created_at }}</td>
            </tr>
        @endforeach
   </tbody>
   {{$histories->appends(request()->except('page'))->links()}} 
</table> 
