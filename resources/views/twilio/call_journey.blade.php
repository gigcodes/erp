@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection
@section('large_content')
   <div class = "row">
		<div class="col-lg-12 margin-tb">
			<h2 class="page-heading">Twilio Call Journey</h2>
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
								<th>Phone</th>
								<th>Account Sid</th>
								<th>Call Sid</th>
								<th>Call Entered</th>
								<th>Handled by chatbot</th>
								<th>Called in working hours</th>
								<th>Agent Available</th>
								<th>Agent online</th>
								<th>Call Answered</th>
							</tr>
							@foreach ($callJourney as $key=>$log )
								<tr>
									<td>{{ $key+1 }}</td>
									<td>{{$log->phone}}</td> 
									<td>{{$log->account_sid}}</td> 
									<td>{{$log->call_sid}}</td>
									<td>@if($log->call_entered == 1) Yes @else No @endif</td>
									<td>@if($log->handled_by_chatbot == 1) Yes @else No @endif</td>
									<td>@if($log->called_in_working_hours == 1) Yes @else No @endif</td>
									<td>@if($log->agent_available == 1) Yes @else No @endif</td>
									<td>@if($log->agent_online == 1) Yes @else No @endif</td>
									<td>@if($log->call_answered == 1) Yes @else No @endif</td>
								</tr>
							@endforeach
						</table>
						{{ $callJourney->links() }}
                    </div>
                </div>
            </div>
		</div>
	</div>	
@endsection
