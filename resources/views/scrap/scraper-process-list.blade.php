@php
    $key = 0;
@endphp
@foreach ($scraper_process as $sp)
    <tr>
        <td>{{ ++$key }}</td>
        <td>{{ $sp->scraper_name }}</td>
        <td>More Than 24 Hr</td>
        <td>{{Form::select('assigned_to', [''=>'Select']+$users, $sp->assigned_to, array('class'=>'form_control', 'id'=>'scraper_'.$key))}} <button class="btn-xs btn-secondary" onclick="saveAssignedTo('scraper_{{$key}}', '{{$sp->id}}')"></button></td>
    </tr>
@endforeach
@foreach ($scrapers as $scraper)
    <tr>
        <td>{{ ++$key }}</td>
        <td>{{ $scraper->scraper_name }}</td>
        <td>Not Run In Last 24 Hr</td>
        <td>
			{{Form::select('assigned_to', [''=>'Select']+$users, $scraper->assigned_to, array('class'=>'form_control', 'id'=>'scraper_'.$key))}} 
			<button class="btn-xs btn-secondary" onclick="saveAssignedTo('scraper_{{$key}}', '{{$scraper->id}}')">Assign</button>
		</td>
   </tr>
@endforeach