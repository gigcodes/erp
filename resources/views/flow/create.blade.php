<input type="hidden" id="flow_id" name="flow_id" value="{{$flowDetail['id']}}">
<input type="hidden" id="path_id" name="path_id" value="{{$flowPathId}}">
@foreach($flowActions as $flowAction)
	@if($flowAction['type'] == 'Time Delay')
		<div class="col-md-12 cross cross_first" >
			<input type="hidden" name="action_id[]" value="{{$flowAction['id']}}">
			<div class="col-md-6 cross_first_label_time">
				<label>Time Delay</label>
				{{ Form::number("time_delay[".$flowAction['id']."]", $flowAction['time_delay'], array('class'=>'form-control', 'required')) }}
				<input type="hidden" name="action_type[{{$flowAction['id']}}]" value="Time Delay">
			</div>
			<div class="col-md-4 cross_first_label">
				<label>Time Delay Type</label>
				{{ Form::select("time_delay_type[".$flowAction['id']."]", ['days'=>'Days', 'hours'=>'Hours', 'minutes'=>'Minutes'], $flowAction['time_delay_type'], array('class'=>'form-control')) }}
			</div>
			<div class="col-md-2 cross_first_remove">
				<i style="cursor: pointer;" class="fa fa-trash trigger-delete" data-route="{{route('flow-action-delete')}}" data-id="{{$flowAction->id}}" aria-hidden="true"></i>
			</div>
		</div>
	@elseif($flowAction['type'] == 'Send Message') 
		@php $messageDetail = \App\FlowMessage::where('action_id', $flowAction['id'])->first(); @endphp
		<div class="col-md-12 cross cross_sec">
			<input type="hidden" name="action_id[]" value="{{$flowAction['id']}}">
			<div class="col-md-10 cross_sec_label">
				<input type="hidden" name="action_type[{{$flowAction['id']}}]" value="Send Message">
				<label>Email  <a href="{{url('link_template')}}"></a></label>
				<div class="cross_sub_label">
					<label>@if($messageDetail['subject']) {{$messageDetail['subject']}} @else Email #1 Subject @endif <a href="{{url('link_template')}}"></a></label>
				</div>
			</div>
			<div class="col-md-2 cross_sec_remove">
				<i class="fa fa-pencil-square-o" aria-hidden="true" onclick="showMessagePopup('{{$flowAction['id']}}')"></i>
				<div class="col-md-2 cross_first_remove">
				<i style="cursor: pointer;" class="fa fa-trash trigger-delete" data-route="{{route('flow-action-delete')}}" data-id="{{$flowAction->id}}" aria-hidden="true"></i>
			</div>
			</div>
		</div>
	@elseif($flowAction['type'] == 'Whatsapp') 
		<div class="col-md-12 cross cross_first" >
			<input type="hidden" name="action_id[]" value="{{$flowAction['id']}}">
			<div class="col-md-10 cross_first_label_time">
				<label>Whatsapp Message</label>
				{{ Form::text("message_title[".$flowAction['id']."]", $flowAction['message_title'], array('class'=>'form-control', 'required')) }}
				<input type="hidden" name="action_type[{{$flowAction['id']}}]" value="Whatsapp">
			</div>
			<div class="col-md-2 cross_first_remove">
				<i style="cursor: pointer;" class="fa fa-trash trigger-delete" data-route="{{route('flow-action-delete')}}" data-id="{{$flowAction->id}}" aria-hidden="true"></i>
			</div>
		</div>
	@elseif($flowAction['type'] == 'SMS') 
		<div class="col-md-12 cross cross_first" >
			<input type="hidden" name="action_id[]" value="{{$flowAction['id']}}">
			<div class="col-md-10 cross_first_label_time">
				<label>SMS</label>
				{{ Form::text("message_title[".$flowAction['id']."]", $flowAction['message_title'], array('class'=>'form-control', 'required')) }}
				<input type="hidden" name="action_type[{{$flowAction['id']}}]" value="SMS">
			</div>
			<div class="col-md-2 cross_first_remove">
				<i style="cursor: pointer;" class="fa fa-trash trigger-delete" data-route="{{route('flow-action-delete')}}" data-id="{{$flowAction->id}}" aria-hidden="true"></i>
			</div>
		</div>
	@endif
@endforeach