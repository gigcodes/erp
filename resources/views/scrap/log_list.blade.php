@extends('layouts.app')
@section('favicon' , 'task.png')


@section('content')
<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Scrapper Log List <span class="count-text"></span></h2>
    </div>
    <br>
    <div class="col-lg-12 margin-tb">
		<table class="table table-bordered table-striped sort-priority-scrapper">
			<thead>
				<tr>
					<th>Scrapper name</th>
					<th>log_messages</th>
					<th>Created at</th>
				</tr>
			</thead>
			<tbody class="conent">
				@foreach ($logDetails as $log)
					<tr>
						<td>{{ $log->scrapper_name }}</td>
						<td>{{ $log->log_messages }}</td>
						<td>{{ $log->created_at }}</td>
					</tr>
				@endforeach
		   </tbody>
		   {{$logDetails->links()}}
		</table>
	</div>
</div>
@endsection