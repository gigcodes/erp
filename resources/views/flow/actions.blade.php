<input type="hidden" id="path_id" name="path_id" value="{{$flowPathId}}">
@foreach($flowActions as $flowAction)
		@if($flowAction['type'] == 'Time Delay')
			<div class="col-md-12 cross cross_first" data-type="time_delay" >
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
			<div class="col-md-12 cross cross_sec" data-type="send_message">
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
			<div class="col-md-12 cross cross_first" data-type="whatsapp">
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
			<div class="col-md-12 cross cross_first" data-type="sms" >
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
		@elseif($flowAction['type'] == 'Condition') 
			<div class="col-md-12 cross cross_first yes_no_conditions" data-type="condition" id="collector_{{$flowAction['id']}}" data-action_id="{{$flowAction['id']}}">
				<input type="hidden" name="action_id[]" value="{{$flowAction['id']}}">
				<div class="col-md-10 cross_first_label_time">
					<label>Condition</label>
					{{Form::select("condition[".$flowAction['id']."]", [''=>'Select Condition','customer has ordered before'=>'Customer has ordered before'], $flowAction['condition'], array('class'=>'form-control condition_select','required', 'data-action_id'=>$flowAction['id']))}}
					<input type="hidden" name="action_type[{{$flowAction['id']}}]" value="SMS">				
								
				</div>			
				<div class="col-md-2 cross_first_remove">
					<i style="cursor: pointer;" class="fa fa-trash trigger-delete" data-route="{{route('flow-action-delete')}}" data-id="{{$flowAction->id}}" aria-hidden="true"></i>
				</div>	
				<div class="col-md-12 erp_yes_no_condition"> 
					<div class="erp_yes_no_condition_inner">
						@php $pathId = \App\FlowPath::where('parent_action_id', $flowAction['id'])->where('path_for', 'yes')->pluck('id')->first(); @endphp
						<div class="col-md-6 erp_yes_no_condition_left " style="border-right:1px solid black;" id="yes_{{$flowAction['id']}}" data-path_id="{{$pathId}}">
							<label>Yes</label>
							@if($pathId != null)	
								@php 
								$actionDataYes = \App\FlowAction::leftJoin('flow_types', 'flow_types.id', '=', 'flow_actions.type_id')
								->select('flow_actions.*', 'flow_types.type')->where(['path_id'=>$pathId])->orderBy('rank')
								->get();
								@endphp
								@include('flow.actions', ['flowActions'=>$actionDataYes, 'flowPathId'=>$pathId])
							@endif
						</div>
						@php $pathId = \App\FlowPath::where('parent_action_id', $flowAction['id'])->where('path_for', 'no')->pluck('id')->first(); @endphp
						<div class="col-md-6 erp_yes_no_condition_right " id="no_{{$flowAction['id']}}" data-path_id="{{$pathId}}">
							<label>No</label>
							@if($pathId != null)	
							    @php	
								$actionDataNo = \App\FlowAction::leftJoin('flow_types', 'flow_types.id', '=', 'flow_actions.type_id')
								->select('flow_actions.*', 'flow_types.type')->where(['path_id'=>$pathId])->orderBy('rank')
								->get(); 
								@endphp
								@include('flow.actions', ['flowActions'=>$actionDataNo, 'flowPathId'=>$pathId])
							@endif
						</div>
					</div>
				</div>			
			</div>
		@endif	
	@endforeach