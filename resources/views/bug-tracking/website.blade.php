@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

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
</style>
	<div class="row" id="common-page-layout">
		<div class="col-lg-12 margin-tb">
			<h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
		</div>
		<br>
		<div class="col-lg-12 margin-tb">
			<div class="row">
				
				<div class=" col-md-10">					
					<div class="row">
						<table class="table table-bordered" style="margin-left: 19%;margin-top:20px;">
							<thead>
							<tr>
								<th style="text-align:center;"><b>Websites</b></th>
								<th style="text-align:center;"><b>Critical</b></th>
								<th style="text-align:center;"><b>High</b></th>
								<th style="text-align:center;"><b>Medium</b></th>
								<th style="text-align:center;"><b>Low</b></th>
							</tr>
							</thead>
						
						<?php 
							for($m=0;$m<count($bug_tracker);$m++) {
						?>
							<tr>
								<td><?php echo $bug_tracker[$m]['title']; ?> </td>
								<td style="text-align:right;">
									<?php if($bug_tracker[$m]['critical']>0) { ?>									
										<?php echo $bug_tracker[$m]['critical']; ?> <button style="margin-left:10px;" type="button" class="btn btn-xs show-bugs-history" title="Show Bug List" data-id="<?php echo $bug_tracker[$m]['website_id'];  ?>" data-sevid="1"><i class="fa fa-info-circle"></i></button>									
									<?php } else { echo " - "; } ?>	
								</td>
								<td style="text-align:right;">
									<?php if($bug_tracker[$m]['high']>0) { ?>									
										<?php echo $bug_tracker[$m]['high']; ?> <button style="margin-left:10px;" type="button" class="btn btn-xs show-bugs-history" title="Show Bug List" data-id="<?php echo $bug_tracker[$m]['website_id'];  ?>" data-sevid="2"><i class="fa fa-info-circle"></i></button>									
									<?php } else { echo " - "; } ?>	
								</td>
								<td style="text-align:right;">
									<?php if($bug_tracker[$m]['medium']>0) { ?>									
										<?php echo $bug_tracker[$m]['medium']; ?> <button style="margin-left:10px;" type="button" class="btn btn-xs show-bugs-history" title="Show Bug List" data-id="<?php echo $bug_tracker[$m]['website_id'];  ?>" data-sevid="3"><i class="fa fa-info-circle"></i></button>									
									<?php } else { echo " - "; } ?>	
								</td>
								<td style="text-align:right;">
									<?php if($bug_tracker[$m]['low']>0) { ?>									
										<?php echo $bug_tracker[$m]['low']; ?> <button style="margin-left:10px;" type="button" class="btn btn-xs show-bugs-history" title="Show Bug List" data-id="<?php echo $bug_tracker[$m]['website_id'];  ?>" data-sevid="4"><i class="fa fa-info-circle"></i></button>									
									<?php } else { echo " - "; } ?>	
								</td>
							</tr>	
						<?php	
							}
						?>
						
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
	
	
	<div id="BugWebsiteModal" class="modal fade" role="dialog">
															
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h3>Bugs List</h3>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<table class="table" border="1">
					<tr>
						<td style="text-align: center;"><b>Bug Id</b></td>						
						<td style="text-align: center;"><b>Date</b></td>
						<td style="text-align: center;"><b>Summary</b></td>							 
						<td style="text-align: center;"><b>Type</b></td>
						<td style="text-align: center;"><b>Action</b></td>
					</tr>
					<tbody class="tbhbugslist">

					</tbody>
				</table>
				</div>
				
			</div>
		</div>
	</div>


	<script type="text/javascript" src="{{ asset('/js/jsrender.min.js')}}"></script>
	<script type="text/javascript" src="{{ asset('/js/jquery.validate.min.js')}}"></script>
	<script src="{{ asset('/js/jquery-ui.js')}}"></script>
	<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/bug-tracker.js') }}"></script>
	<script type="text/javascript">
		$(document).on('click', '.show-bugs-history', function() {
			
			$(".tbhbugslist").html("");
			var id = $(this).data('id');
			var serv_id = $(this).data('sevid');
			$.ajax({
			   url: "/bug-tracking/website-history?id="+id+"&servid="+serv_id,
			   type: "get",
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
					//notify user if nothing to load
					//$('.ajax-loading').html("No more records!");
					return;
				}
				
				if(response.data.length >0){

					var html ="";

					$.each(response.data, function (i,item){
						html+="<tr>"
						html+=" <td>"+ item.id +"</td>"  
						html+=" <td>"+ item.created_at_date  +"</td>"	
						html+=" <td>"+ item.summary_short +"</td>"
						html+=" <td>"+ item.bug_type_id  +"</td>"
						html+=" <td><a href='/bug-tracking?bug_main_id="+item.id+"' target='_blank'><button type='button' title='Push' data-id='"+item.id+"' class='btn btn-push'><i class='fa fa-eye' aria-hidden='true'></i></button></a></td>"

						html+="</tr>"
					})

					$('.tbhbugslist').html(html);
				}
				
				

				$('.loading-image-preview').hide(); //hide loading animation once data is received
				$('#loading-image-preview').css("display","none");		
				$('#BugWebsiteModal').modal('toggle');	
				
				
			   
		   })
		   .fail(function(jqXHR, ajaxOptions, thrownError)
		   {
			  alert('No response from server');
		   });
		});
	</script>
	
@endsection