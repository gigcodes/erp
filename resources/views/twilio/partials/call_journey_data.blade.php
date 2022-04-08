@foreach ($call_Journeies as $key => $call_Journey )
	<tr>
		<td>{{ $key+1 }}</td>
		<td>{{$call_Journey->phone}}</td> 
		<td>{{ isset($call_Journey->twilio_credential->id)?$call_Journey->twilio_credential->id:"-"}}</td> 
		<td>{{$call_Journey->account_sid}}</td> 
		<td>{{$call_Journey->call_sid}}</td>
		<td>@if($call_Journey->call_entered == 1) Yes @else No @endif</td>
		<td>@if($call_Journey->handled_by_chatbot == 1) Yes @else No @endif</td>
		<td>@if($call_Journey->called_in_working_hours == 1) Yes @else No @endif</td>
		<td>@if($call_Journey->agent_available == 1) Yes @else No @endif</td>
		<td>@if($call_Journey->agent_online == 1) Yes @else No @endif</td>
		<td>@if($call_Journey->call_answered == 1) Yes @else No @endif</td>
	</tr>
@endforeach