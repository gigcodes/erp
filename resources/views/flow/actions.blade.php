<input type="hidden" id="path_id" name="path_id" value="{{$flowPathId}}">
@if(!isset($con))  @php $con=0;@endphp @endif
@foreach($flowActions as $key=>$flowAction)
		@if($flowAction['type'] == 'Time Delay')
		<div class="col-md-12 cross cross_first border-bottom bg-light text-dark pt-3 pb-3  m-0 " condtion_{{$con.$flowAction["type"]}}>
			<input type="hidden" name="action_id[]" value="{{$flowAction['id']}}">
			<div class="form-group row m-0">
				<div class="@if($con==1) action_con @else action_con_2  @endif one">
					<label  class="col-lg-2 col-form-label">Time Delay</label>
					<div class="col-lg-3">
						{{ Form::number("time_delay[".$flowAction['id']."]", $flowAction['time_delay'], array('class'=>'form-control', 'required')) }}
						<input type="hidden" name="action_type[{{$flowAction['id']}}]" value="Time Delay">
					</div>
				</div>
				<div class="@if($con==1) action_con @else action_con_2   @endif two">
					<label  class="col-lg-3 col-form-label">Time Delay Type</label>
					<div class="col-lg-3">
					{{ Form::select("time_delay_type[".$flowAction['id']."]", ['days'=>'Days', 'hours'=>'Hours', 'minutes'=>'Minutes'], $flowAction['time_delay_type'], array('class'=>'form-control')) }}
					</div>
				</div>
				<div class="col-lg-1 text-right pt-3">
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
						<label><i class="fa fa-envelope"></i> @if(isset($messageDetail) && $messageDetail['subject']) {{$messageDetail['subject']}} @else Email #1 Subject @endif <a href="{{url('link_template')}}"></a></label>
					</div>
				</div>
				<div class="col-md-2 cross_sec_remove pt-3 text-right">    
					<i class="fa fa-pencil-square-o fa-lg p-0" aria-hidden="true" onclick="showMessagePopup('{{$flowAction['id']}}')"></i>
					<i style="cursor: pointer;" class="fa fa-trash trigger-delete fa-lg cross_first_remove" data-route="{{route('flow-action-delete')}}" data-id="{{$flowAction->id}}" aria-hidden="true"></i>
				</div>
			</div>
		@elseif($flowAction['type'] == 'Whatsapp') 
			<div class="col-md-12 cross cross_first cross_task border-bottom bg-light text-dark pt-3 pb-3  m-0 " >
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
			<div class="col-md-12 cross cross_first  border-bottom bg-light text-dark pt-3 pb-3  m-0" >
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
		@elseif($flowAction['type'] == 'Condition') 
		     
			<div class="col-md-12 {{$con}} cross cross_first yes_no_conditions bg-light pt-3 pb-3 m-0 " data-type="condition" id="collector_{{$flowAction['id']}}" data-action_id="{{$flowAction['id']}}">
				<input type="hidden" name="action_id[]" value="{{$flowAction['id']}}">
				
				<div class="col-md-11 cross_first_label_time p-0 pl-3">
					{{Form::select("condition[".$flowAction['id']."]", [''=>'Select Condition','customer has ordered before'=>'Customer has ordered before','check_if_pr_merged'=>'Check if PR merged', 'check_scrapper_error_logs'=>'Check If scrapper has errors', 'check_if_design_task_done'=>'Check If design task done', 'check_if_development_task_done'=>'Check If development task done'], $flowAction['condition'], array('class'=>'form-control condition_select','required', 'data-action_id'=>$flowAction['id']))}}
					<input type="hidden" name="action_type[{{$flowAction['id']}}]" value="SMS">													
				</div>			
				<div class="col-md-1 cross_first_remove text-left pl-3 pt-3 p-0">
					<i style="cursor: pointer;" class="fa fa-trash fa-lg trigger-delete" data-route="{{route('flow-action-delete')}}" data-id="{{$flowAction->id}}" aria-hidden="true"></i>
				</div>
				
				<div class="col-md-12 erp_yes_no_condition actions mt-0"> 
					<div class="erp_yes_no_condition_inner p-1">
						@php $pathId = \App\FlowPath::where('parent_action_id', $flowAction['id'])->where('path_for', 'yes')->pluck('id')->first(); @endphp
						<div class="col-md-6 erp_yes_no_condition_left " style="border-right:1px solid black;" id="yes_{{$flowAction['id']}}" data-path_id="{{$pathId}}">
							<label class="mb-0">Yes</label>
							@if($pathId != null)	
								@php 
								$actionDataYes = \App\FlowAction::leftJoin('flow_types', 'flow_types.id', '=', 'flow_actions.type_id')
								->select('flow_actions.*', 'flow_types.type')->where(['path_id'=>$pathId])->orderBy('rank')
								->get();
								@endphp
								@include('flow.actions', ['flowActions'=>$actionDataYes, 'flowPathId'=>$pathId, 'con'=>1])
							@endif
						</div>
						@php $pathId = \App\FlowPath::where('parent_action_id', $flowAction['id'])->where('path_for', 'no')->pluck('id')->first(); @endphp
						<div class="col-md-6 erp_yes_no_condition_right " id="no_{{$flowAction['id']}}" data-path_id="{{$pathId}}">
							<label class="mb-0">No</label>
							@if($pathId != null)	
							    @php	
								$actionDataNo = \App\FlowAction::leftJoin('flow_types', 'flow_types.id', '=', 'flow_actions.type_id')
								->select('flow_actions.*', 'flow_types.type')->where(['path_id'=>$pathId])->orderBy('rank')
								->get(); 
								@endphp
								@include('flow.actions', ['flowActions'=>$actionDataNo, 'flowPathId'=>$pathId, 'con'=>1])
							@endif
						</div>
					</div>
				</div>			
			</div>
		@endif	
	@endforeach