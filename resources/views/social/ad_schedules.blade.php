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
	<div id="exTab2">
		<ul class="nav nav-tabs">
			<li class="active">
				<a  href="#1" data-toggle="tab">Ads List</a>
			</li>
			<li><a href="#2" data-toggle="tab">Calandar</a>
			</li>
		</ul>

		<div class="tab-content ">
			<div class="tab-pane active" id="1">
				<table class="table mt-1 table-striped" id="myTable">
					<thead>
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
					</thead>
					<tbody>
						@foreach($ads as $key=>$ad)
							<tr data-adId="{{$ad['id']}}">
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
					</tbody>
				</table>
			</div>
			<div class="tab-pane" id="2">
				<div id="calendar"></div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
	<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script>
		$(document).ready(function() {
			$('#calendar').fullCalendar({
				header: {
					right: "month,agendaWeek,agendaDay, today prev,next",
				},
				events: '{{ action('SocialController@getAdSchedules') }}'
			});

			$(document).ready( function () {
				$('#myTable').DataTable();
			} );
		});
	</script>
@endsection