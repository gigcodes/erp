@foreach ($scrapers as $scraper)
    
<tr>
    <td>{{ $scraper->id }}</td>
    <td>{{ $scraper->scraper_name }}</td>
    <td>@if($scraper->mainSupplier) {{ $scraper->mainSupplier->supplier }} @endif</td>
    <td>{{ $scraper->start_time }}</td>
    <td>{{ $scraper->end_time }}</td>
    <td>{{ $scraper->run_gap }}</td>
    <td>{{ $scraper->time_out }}</td>
    <td>{{ $scraper->starting_urls }}</td>
    <td>{{ $scraper->designer_url_selector }}</td>
    <td>{{ $scraper->product_url_selector }}</td>
    <td>
        <button class="btn btn-secondary" onclick="editSupplier({{ $scraper}})">Edit</button>
        @if($scraper->mapping->count() != 0) <button class="btn btn-secondary" ><a href="/scrap/generic-scraper/mapping/{{ $scraper->id }}" target="_blank" style="color: white;">Mapping</a></button>@endif

    </td>

</tr>
                
@endforeach
