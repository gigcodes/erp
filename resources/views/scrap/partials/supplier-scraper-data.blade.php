@foreach ($scrapers as $scraper)
    
<tr>
    <td>{{ $scraper->id }}</td>
    <td>{{ $scraper->scraper_name }}</td>
    <td>{{ $scraper->start_time }}</td>
    <td>{{ $scraper->end_time }}</td>
    <td>{{ $scraper->run_gap }}</td>
    <td>{{ $scraper->time_out }}</td>
    <td>{{ $scraper->starting_urls }}</td>
    <td>{{ $scraper->designer_url_selector }}</td>
    <td>{{ $scraper->product_url_selector }}</td>
    <td><button class="btn btn-secondary" onclick="editSupplier({{ $scraper}})">Edit</button></td>

</tr>
                
@endforeach
