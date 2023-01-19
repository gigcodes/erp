@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Task Summary')

@section('content')
    <style type="text/css">
        .preview-category input.form-control {
            width: auto;
        }
        .break{
            word-break: break-all !important;
        }
    </style>
	

<style>
th {border: 1px solid black;}
table{border-collapse: collapse;}
.ui-icon, .ui-widget-content .ui-icon {background-image: none;}

#bug_tracking_maintable {
	font-size:12px;
}
.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
	padding:5px;
}
#bug_tracking_maintable .btn {
	padding: 1px 3px 0px 4px !important;
	margin-top:0px !important;
}
.chat-list-history {
	z-index:999;
}
.scrollable {     
    max-height:550px;
    margin: 0;
    padding: 0;
    overflow: auto;
}
</style>
<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
		<h2 class="page-heading">Task Summary <span class="count-text"></span></h2>
	</div>
	<br>
	<div class="col-lg-12 margin-tb">
		<div class="row">
			
			<div class=" col-md-12">						
				<div class="row">
					<div style="width: 100%;text-align: center;margin-top: 20px;font-size: 16px;font-weight: bold;">
						Task Summary
					</div>
					<table class="table table-bordered" style="margin-left: 5%;margin-top:20px;margin-right: 5%;">
						<thead>
						<tr>
							<th style="text-align:center;"><b>Users</b></th>
							@foreach($taskStatus as $status)
								<th style="text-align:center;width: 5%;"><b><?= $status->name ?></b></th>
							@endforeach
						</tr>
						</thead>
					
						@foreach($arrName as $user)
						<tr>
							<td>{{ $user['name']; }} </td>
							@foreach($arrStatusCount[$user['userid']] as $key => $value)
								<td data-status={{$key}} style="text-align:right;">{{ $value }}<button style="margin-left:5px;" type="button" class="btn btn-xs show-task-history" title="Show Task List" data-userid={{$user["userid"]}} data-id={{$key}} data-type="severity" data-sevid="1"><i class="fa fa-info-circle"></i></button> </td>
							@endforeach
						</tr>
						@endforeach
					
					</table>
									
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-success" id="alert-msg" style="display: none;">
				<p></p>
			</div>
		</div>
	</div>
		<div class="col-md-12 margin-tb" id="page-view-result">

		</div>
	</div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
		50% 50% no-repeat;display:none;">
</div>

<div class="common-modal modal" role="dialog">
	<div class="modal-dialog" role="document">

	</div>

</div>
	
	
<div id="TaskHistoryModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content" style="width: 850px;padding: 0% 2% 2% 2%;">
			<div class="modal-header">
				<h3>Task List</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body scrollable">
				<table class="table" border="1" style="font-size: 13px;">
				<tr>
					<td style="text-align: center;"><b>Task Id</b></td>						
					<td style="text-align: center;"><b>Date</b></td>
					<td style="text-align: center;"><b>Task Subject</b></td>
					<td style="text-align: center;"><b>Task Details</b></td>							 
				</tr>
				<tbody class="tbltasklist">

				</tbody>
			</table>
			</div>
			
		</div>
	</div>
</div>

<script type="text/javascript">

	$(document).on('click', '.load-conv-modal', function() {
		$('#TaskHistoryModal').css('z-index',9);	
	});

	$('#chat-list-history').on("hide.bs.modal", function() {
		$('#TaskHistoryModal').css('z-index',99999999);	
	})

	$(document).on('click', '.show-task-history', function() {
		var taskStatusId = $(this).data('id');
		var userId = $(this).data('userid');
		$(".tbltasklist").html("");

		$.ajax({
			url: "{{ route('task-list') }}",
			data: {
				_token: "{{ csrf_token() }}",
				taskStatusId: taskStatusId,
				userId: userId
			},
			type: "post",
			datatype: "html",			  
			beforeSend: function()
			{
				$('#loading-image-preview').css("display","block");
			}
		})
		.done(function(response)
		{
			$('#loading-image-preview').css("display","none");			
			if(response.data.length == 0){
				console.log(response.data.length);
				return;
			}
			
			if(response.data.length > 0){
				var html ="";
				$.each(response.data, function (i,item){
					html+="<tr>"
					html+=" <td style='padding: 1%;'>"+ item.id +"</td>"  
					html+=" <td style='padding: 1%;'>"+ moment(item.created_at).format('DD-MM-YYYY');  +"</td>"
					html+=" <td style='padding: 1%;'>"+ item.task_subject +"</td>"
					html+=" <td style='padding: 1%;'>"+ item.task_details  +"</td>"
					html+="</tr>"
				})
				$('.tbltasklist').html(html);
			}
			$('.loading-image-preview').hide(); //hide loading animation once data is received
			$('#loading-image-preview').css("display","none");		
			$('#TaskHistoryModal').modal('toggle');
		})
		.fail(function(jqXHR, ajaxOptions, thrownError)
		{
			alert('No response from server');
		});
	});
</script>
	
@endsection