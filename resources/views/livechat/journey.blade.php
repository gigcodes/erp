@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection
@section('large_content')
   <div class = "row">
		<div class="col-lg-12 margin-tb">
			<h2 class="page-heading">Chatbot log Journey</h2>
		</div>
	</div>
	
    <div class="row">
        <div class="col-lg-12 margin-tb">
			<div class="panel-group" style="margin-bottom: 5px;">
                <div class="panel mt-3 panel-default">
                    <div class="panel-body">
						<table class="table table-bordered table-striped">
							<tr>
								<th>#</th>
								<th>Chat id</th>
								<th>Chat entered</th>
								<th>Message received</th>
								<th>Reply found in database</th>
								<th>Reply searched in watson</th>
								<th>Reply</th>
								<th>Response sent to customer</th>
								
								
							</tr>
							@foreach ($watsonJourney as $key=>$log )
								<tr>
									<td>{{ $key+1 }}</td>
									<td>{{$log->chat_id}}</td> 
									<td>@if($log->chat_entered == 1) Yes @else No @endif</td>
									<td>{{$log->message_received}}</td>
									<td>@if($log->reply_found_in_database == 1) Yes @else No @endif</td>
									<td>@if($log->reply_searched_in_watson == 1) Yes @else No @endif</td>
									<td>{{$log->reply}}</td>
									<td>@if($log->response_sent_to_cusomer == 1) Yes @else No @endif</td>
								</tr>
							@endforeach
						</table>
						{{ $watsonJourney->links() }}
                    </div>
                </div>
            </div>
		</div>
	</div>	
@endsection
