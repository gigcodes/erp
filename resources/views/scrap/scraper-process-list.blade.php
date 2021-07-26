@php
    $key = 0;
@endphp
@foreach ($scraper_process as $sp)
    <tr>
        <td>{{ ++$key }}</td>
        <td>{{ $sp->scraper_name }}</td>
        <td>More Than 24 Hr</td>
    </tr>
@endforeach
@foreach ($scrapers as $scraper)
    <tr>
        <td>{{ ++$key }}</td>
        <td>{{ $scraper->scraper_name }}</td>
        <td>Not Run In Last 24 Hr</td>
    </tr>
@endforeach