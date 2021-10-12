@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link href="{{ asset('flow/style.css') }}" rel="stylesheet">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
	  #Collector { list-style-type: none; margin: 0; padding: 0; width: 100%; }
	  #Collector cross { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
	  #Collector li span { position: absolute; margin-left: -1.3em; }
    </style>
@endsection

@section('content')

    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Flows</h2>
                </div>
            </div>
        </div>
    </div>
	<div class="row mb-3">
        <div class="mt-3 col-md-12">
			<a class="ml-2">
				<button type="button" data-toggle="modal" data-target="#flowModal" class="btn btn-secondary btn-xs">Flow</button>
			</a>
			<a class="ml-2" style="display:none;">
				<button type="button" data-toggle="modal" data-target="#flowTypeModal" class="btn btn-secondary btn-xs">Flow Type</button>
			</a>
        </div>
	</div>
	<div class="row mb-3">
		<div class="mt-3 col-md-12">
		    <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">Website</th>
                            <th scope="col" class="text-center">Flow Name</th>
                            <th scope="col" class="text-center">Description</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-center task_queue_list">
                        @foreach($flows as $key => $flow)
                            <tr class="worker_row_{{$flow->id}}">
								<td>{{ $flow['title'] }}</td>
								<td>{{ $flow['flow_name'] }}</td>
								<td>{{ $flow['flow_description'] }}</td>
                                <td>
									<a href="#" onclick="showFlow('{{$flow->id}}')">  Flow Detail</a>
									<i style="cursor: pointer;" class="fa fa-trash trigger-delete" data-route="{{route('flow-delete')}}" data-id="{{$flow->id}}" aria-hidden="true"></i>
								</td>
							</tr>
                        @endforeach
                    </tbody>
            </table>
        </div>
    </div>
	<div class="modal fade" id="flowModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
				<div class="modal-header p-0 pt-3 pb-3 pl-3 pr-3">
					<h5 class="modal-title">Flow</h5>
					<button type="button" class="close pt-0 pb-0" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						{{ Form::open(array('url'=>route('flow-create'), 'id'=>'flow-create', 'class'=>'ajax-submit w-100')) }}
							<div class="col-md-3">
								{{ Form::select('store_website_id', $websites, null, array('class'=>'form-control','placeholder' => 'Website')) }}
							</div>
							<div class="col-md-3">
								{{ Form::select('flow_name', ['add_to_cart'=>'Add to cart', 'wishlist'=>'Wish List', 'delivered_order'=>'Delivered order'], null, array('class'=>'form-control', 'placeholder'=>'Flow Name')) }}
							</div>
							<div class="col-md-4">
								<input type="text" class="form-control " name="flow_description" placeholder="Flow Description"/>
							</div>
							<div class="col-md-2">
								<input type="hidden" name="id" value="">
								<button type="submit" class="btn btn-secondary btn-xs pull-right mt-2">Create</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="flowTypeModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
				<div class="modal-header">
					<h5 class="modal-title">Flow Type</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					{{ Form::open(array('url'=>route('flow-type-create'), 'id'=>'flow-create', 'class'=>'ajax-submit')) }}
						<div class="row">
							<div class="col-md-9">
								<label>Flow Type</label>
								<input type="text" class="form-control " name="type" placeholder="Flow Description"/>
							</div>
							<div class="col-md-3">
								<input type="hidden" name="id" value="">
								<button type="submit" class="btn btn-secondary">Create</button>
							</div>
						</div>
					</form>
					
				</div>
			</div>
		</div>
	</div>
	
	<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
				<div class="modal-header">
					<h5 class="modal-title">Set Email Message</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" id="">
				{{ Form::open(array('url'=>route('flow-action-message'), 'class'=>'ajax-submit', 'novalidate'=>'true', 'id'=>'message_content_form')) }}
					<div id="message_content">
					</div>
				</form>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="flowPathModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
				<div class="modal-header">
					<h5 class="modal-title">Flow Type</h5>
					<a href="#" id="sendEmail"> Add Email </a>
					<a href="#" id="dateTime"> Add Time Delay </a>
					<a href="#" id="whatsapp"> &nbsp;Add Whatsapp </a> 
					<a href="#" id="sms"> &nbsp;Add SMS</a> 
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					{{ Form::open(array('url'=>route('flow-actions-update'), 'class'=>'ajax-submit')) }}
						<div class="row" id='Collector'> 
							
						</div>
						<button type="submit" class="btn btn-secondary mt-3">Create</button>
					</form>
				</div>
			</div>
		</div>
	</div> 
<div id="time_delay" style="display:none;">
	<div class="col-md-12 cross cross_first border-bottom bg-light text-dark pt-3 pb-3  m-0" >
		<div class="form-group row m-0">
			<label  class="col-lg-2 col-form-label">Time Delay</label>
			<div class="col-lg-2">
				{{ Form::number('time_delay', null, array('class'=>'form-control', 'required')) }}
				<input type="hidden" name="action_type[]" value="Time Delay">
			</div>
			<label  class="col-lg-3 col-form-label">Time Delay Type</label>
			<div class="col-lg-2">
			{{ Form::select('time_delay_type', ['days'=>'Days', 'hours'=>'Hours', 'minutes'=>'Minutes'], null, array('class'=>'form-control')) }}
			</div>
			<div class="col-lg-3 text-right pt-3">
				<i class="fa fa-trash fa-lg"></i>  
			</div>
		</div>
	</div>
</div>
<div id="send_message" style="display:none;"> 
	<div class="col-md-12 cross cross_sec border-bottom bg-white text-dark pt-3 pb-3  m-0">
		<div class="col-md-10  text-dark">
			<input type="hidden" name="action_type" value="Send Message">
			<label> <i class="fa fa-envelope"></i> Here will be Email <a href="{{url('link_template')}}"></a></label>
			<div class="cross_sub_label">
				<label> <i class="fa fa-envelope"></i> Email #1 Subject  <a href="{{url('link_template')}}"></a></label>
			</div>
		</div>
		<div class="col-md-2 cross_sec_remove pt-3 text-right">
			<i class="fa fa-pencil-square-o fa-lg p-0" aria-hidden="true"></i>
			<i class="fa fa-remove fa-lg"></i>
		</div>
	</div>
</div>
<div id="whatsapp" style="display:none;">
	<div class="col-md-12 cross cross_first border-bottom bg-light text-dark pt-3 pb-3  m-0" >
		<div class="form-group row m-0">
			<label  class="col-lg-3 col-form-label">Whatsapp Message</label>
			<div class="col-lg-8">
				{{ Form::text("message_title", null, array('class'=>'form-control', 'required')) }}
				<input type="hidden" name="action_type[]" value="Whatsapp">
			</div>
			<div class="col-lg-1 text-right pt-3">
				<i class="fa fa-trash fa-lg"></i>  
			</div>
		</div>
	</div>
</div>
<div id="sms" style="display:none;">
	<div class="col-md-12 cross cross_first border-bottom bg-light text-dark pt-3 pb-3  m-0" >
		<div class="form-group row m-0">
			<label  class="col-lg-3 col-form-label">SMS</label>
			<div class="col-lg-8">
				{{ Form::text("message_title", null, array('class'=>'form-control', 'required')) }}
				<input type="hidden" name="action_type[]" value="SMS">
			</div>
			<div class="col-lg-1 text-right pt-3">
				<i class="fa fa-trash fa-lg"></i>  
			</div>
		</div>
		<div class="col-md-11 cross_first_label_time">
			<label></label>
			
		</div>
		<div class="col-md-1 cross_first_remove pt-3 text-right">
			
		</div>
	</div>
</div>
@endsection

@section('scripts')

<script src="https://cdn.tiny.cloud/1/8lx26kd08fse8eckrno8tqi4pkf298s9d9hunvvzy4ri6ru4/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: '#html_content'
    //menubar: false
  });
  
</script>
<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': "{{ csrf_token() }}"
    }
});

jQuery(document).ready(function($){
	 $('#dateTime').on('click', function(){
        var time = $('#time_delay').html();
		$('#Collector').append(time);
		var flowId = $('#flow_id').val();console.log(flowId);
		var pathId = $('#path_id').val();
		saveFlowAction(flowId, pathId, 'Time Delay');
    });
	
	$('#sendEmail').on('click', function(){
       var sendEmail = $('#send_message').html();
       $('#Collector').append(sendEmail);
	    var flowId = $('#flow_id').val();
		var pathId = $('#path_id').val();
	   saveFlowAction(flowId, pathId, 'Send Message');
    });
	
	$('#whatsapp').on('click', function(){
       var sendWhatapp = $('#whatsapp').html();
       $('#Collector').append(sendWhatapp);
	    var flowId = $('#flow_id').val();
		var pathId = $('#path_id').val();
	   saveFlowAction(flowId, pathId, 'Whatsapp');
    });
	
	$('#sms').on('click', function(){
       var sendSms = $('#sms').html();
       $('#Collector').append(sendSms);
	    var flowId = $('#flow_id').val();
		var pathId = $('#path_id').val();
	   saveFlowAction(flowId, pathId, 'SMS');
    });

  /*  $(document).on('click','.cross div i',function(){
        // event.preventDefault();
       
    });*/
	
	function saveFlowAction(flowId, pathId, action_type) {
		var data = {"_token":"{{ csrf_token() }}",'flow_id':flowId, 'path_id':pathId, 'action_type':action_type};
		console.log(data);
		$.ajax({
                type: 'POST',
				url: "{{route('flow-update')}}",
				data: data,
				//processData: false,
				//contentType: false,
				success: function(data) { 
					if(data.statusCode == 500) { 
						toastr["error"](data.message);
					} else {
						$('#Collector').html(data.message);
					}
				},
				done:function(data) {
					console.log('success '+data);
				}
        });
	}
});
	function showFlow(flow_id) { 
		$.get(window.location.origin+"/flow/detail/"+flow_id, function(data){ 
			$('#Collector').html(data);
			$('#flowPathModal').modal('show');
			$('#flowPathModal').addClass('in');
		});
	}
	function showMessagePopup(actionId) { 
		$.get(window.location.origin+"/flow/action/message/"+actionId, function(data){ 
			$('#message_content').html(data);
			$('#flow_message_action_id').val(actionId);
			$('#flowPathModal').modal('hide');
			$('#flowPathModal').removeClass('in');
			$('#messageModal').modal('show');
			$('#messageModal').addClass('in');
			tinymce.init({
				selector: '#html_content'
			});
		});
	}
	$('.ajax-submit').on('submit', function(e) { 
			e.preventDefault(); 
			$.ajax({
                type: $(this).attr('method'),
				url: $(this).attr('action'),
				data: new FormData(this),
				processData: false,
				contentType: false,
				success: function(data) { 
					if(data.statusCode == 500) { 
						toastr["error"](data.message);
					} else {
						toastr["success"](data.message);
						setTimeout(function(){
                         location.reload();
                        }, 1000);
					}
				},
				done:function(data) {
					console.log('success '+data);
				}
            });
	});
	

  $(function() {
    $( "#Collector" ).sortable();
    $( "#Collector" ).disableSelection();
  });
 
    //$('.trigger-delete').on('click', function(e) {
	$(document).on('click','.trigger-delete',function(e){
			var id = $(this).attr('data-id');
			e.preventDefault(); 
			var option = { _token: "{{ csrf_token() }}", id:id };
			var route = $(this).attr('data-route');
			$("#loading-image").show();
			$.ajax({
				type: 'post',
				url: route,
				data: option,
				success: function(response) {
					$("#loading-image").hide();
					if(response.code == 200) {
						$(this).closest('tr').remove();
                        toastr["success"](response.message); 
                    }else if(response.statusCode == 500){
                        toastr["error"](response.message);
                    }
					setTimeout(function(){
                        location.reload();
                    }, 1000);
				},
				error: function(data) {
					$("#loading-image").hide();
					alert('An error occurred.');
				}
			}); 
		});
 
</script>
@endsection