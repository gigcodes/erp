<input type="hidden" id="flow_id" name="flow_id" value="{{$flowDetail['id']}}">
<input type="hidden" id="path_id" name="path_id" value="{{$flowPathId}}">
@foreach($flowActions as $flowAction)
	@if($flowAction['type'] == 'Time Delay')
		<div class="col-md-12 cross cross_first border-bottom bg-light text-dark pt-3 pb-3  m-0" >
			<input type="hidden" name="action_id[]" value="{{$flowAction['id']}}">
			<div class="form-group row m-0">
				<label  class="col-lg-2 col-form-label">Time Delay</label>
				<div class="col-lg-2">
					{{ Form::number("time_delay[".$flowAction['id']."]", $flowAction['time_delay'], array('class'=>'form-control', 'required')) }}
					<input type="hidden" name="action_type[{{$flowAction['id']}}]" value="Time Delay">
				</div>
				<label  class="col-lg-3 col-form-label">Time Delay Type</label>
				<div class="col-lg-2">
				{{ Form::select("time_delay_type[".$flowAction['id']."]", ['days'=>'Days', 'hours'=>'Hours', 'minutes'=>'Minutes'], $flowAction['time_delay_type'], array('class'=>'form-control')) }}
				</div>
				<div class="col-lg-3 text-right pt-3">
					<i style="cursor: pointer;" class="fa fa-trash trigger-delete fa-lg" data-route="{{route('flow-action-delete')}}" data-id="{{$flowAction->id}}" aria-hidden="true"></i>
				</div>
			</div>
		</div>
	@elseif($flowAction['type'] == 'Send Message') 
		@php $messageDetail = \App\FlowMessage::where('action_id', $flowAction['id'])->first(); @endphp
		<div class="col-md-12 cross cross_sec border-bottom bg-white text-dark pt-3 pb-3  m-0">
			<input type="hidden" name="action_id[]" value="{{$flowAction['id']}}">
			<div class="col-md-10  text-dark">
				<input type="hidden" name="action_type[{{$flowAction['id']}}]" value="Send Message">
				<label><i class="fa fa-envelope"></i> Email  <a href="{{url('link_template')}}"></a></label>
				<div class="cross_sub_label">
					<label><i class="fa fa-envelope"></i> @if($messageDetail['subject']) {{$messageDetail['subject']}} @else Email #1 Subject @endif <a href="{{url('link_template')}}"></a></label>
				</div>
			</div>
			<div class="col-md-2 cross_sec_remove pt-3 text-right">
				<i class="fa fa-pencil-square-o fa-lg p-0" aria-hidden="true" onclick="showMessagePopup('{{$flowAction['id']}}')"></i>
				<i style="cursor: pointer;" class="fa fa-trash trigger-delete fa-lg cross_first_remove" data-route="{{route('flow-action-delete')}}" data-id="{{$flowAction->id}}" aria-hidden="true"></i>
			</div>
		</div>
	@elseif($flowAction['type'] == 'Whatsapp') 
		<div class="col-md-12 cross cross_first border-bottom bg-light text-dark pt-3 pb-3  m-0" >
			<input type="hidden" name="action_id[]" value="{{$flowAction['id']}}">
			<div class="form-group row m-0">
				<label  class="col-lg-3 col-form-label">Whatsapp Message</label>
				<div class="col-lg-8">
					{{ Form::text("message_title[".$flowAction['id']."]", $flowAction['message_title'], array('class'=>'form-control', 'required')) }}
					<input type="hidden" name="action_type[{{$flowAction['id']}}]" value="Whatsapp">
				</div>
				<div class="col-lg-1 text-right pt-3">
					<i style="cursor: pointer;" class="fa fa-trash fa-lg trigger-delete" data-route="{{route('flow-action-delete')}}" data-id="{{$flowAction->id}}" aria-hidden="true"></i>
				</div>
			</div>
		</div>
	@elseif($flowAction['type'] == 'SMS') 
		<div class="col-md-12 cross cross_first border-bottom bg-light text-dark pt-3 pb-3  m-0" >
			<input type="hidden" name="action_id[]" value="{{$flowAction['id']}}">
			<div class="form-group row m-0">
				<label  class="col-lg-3 col-form-label">SMS</label>
				<div class="col-lg-8">
					{{ Form::text("message_title[".$flowAction['id']."]", $flowAction['message_title'], array('class'=>'form-control', 'required')) }}
					<input type="hidden" name="action_type[{{$flowAction['id']}}]" value="SMS">
				</div>
				<div class="col-lg-1 text-right pt-3">
				<i style="cursor: pointer;" class="fa fa-trash fa-lg trigger-delete" data-route="{{route('flow-action-delete')}}" data-id="{{$flowAction->id}}" aria-hidden="true"></i>
				</div>
			</div>
		</div>
	@endif
@endforeach