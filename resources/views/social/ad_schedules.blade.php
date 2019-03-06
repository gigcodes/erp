@extends('layouts.app')


@section('large_content')
<div class="row">
	<div class="col-lg-12 margin-tb">
		<div class="pull-left">
			<h2>Ad Reports/Schedules</h2>
		</div>
	</div>
</div>


@if ($message = Session::get('message'))
<div class="alert alert-success">
	<p>{{ $message }}</p>
</div>
@endif

	

<div class="mt-4">
	<table class="table">
		<tr>
			<th>S.N</th>
			<th>Ad Set #</th>
			<th>Name</th>
			<th>Type Of Ad</th>
			<th>Target Audience</th>
			<th>Status</th>
			<th>Created At</th>
			<th>Updated At</th>
		</tr>
		@foreach($ads as $key=>$ad)
			<tr>
				<td>{{ $key+1 }}</td>
				<td>{{ $ad['adset_id'] }}</td>
				<td>{{ $ad['name'] }}</td>
				<td>N/A</td>
				<td>
					@foreach($ad['targeting'] as $key=>$value)
						<span style="display:block"><strong>{{ucfirst($key)}}:</strong> {{title_case($value ?? 'N/A')}}</span>
					@endforeach
				</td>
				<td>{{ $ad['status'] }}</td>
				<td>{{ \Carbon\Carbon::createFromTimeString($ad['created_time'])->diffForHumans() }}</td>
				<td>{{ \Carbon\Carbon::createFromTimeString($ad['updated_time'])->diffForHumans() }}</td>
			</tr>
		@endforeach
	</table>
</div>

@endsection