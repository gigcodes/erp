@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
<style type="text/css">
	.preview-category input.form-control {
		width: auto;
	}
</style>

<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
		<h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
	</div>
	<br>
	<div class="col-lg-12 margin-tb">
		<div class="row">
			<div class="col col-md-9">
				<div class="row">
					<button style="display: inline-block;" class="btn pl-5 btn-sm btn-image btn-add-action">
						<img src="/images/add.png" style="cursor: default;">
					</button>
					<button style="display: inline-block;" class="btn btn-sm ml-5 btn-secondary open-store-magento-user-lising">
						User Listing
					</button> &nbsp;
					<button class="btn btn-secondary" data-toggle="modal" data-target="#store-generate-pem-file"> Store Generate Reindex</button>
					&nbsp;
					<button class="btn btn-secondary magento-setting-update"> Magento Setting Update</button>
					&nbsp;
					<button class="btn btn-secondary" data-toggle="modal" data-target="#store-api-token"> Api Token Update</button>


				</div>
			</div>
			<div class="col">
				<div class="h" style="margin-bottom:10px;">
					<form class="form-inline message-search-handler" method="post">
						<div class="row">
							<div class="col">
								<div class="form-group">
									<?php echo Form::text("keyword", request("keyword"), ["class" => "form-control", "placeholder" => "Enter keyword"]) ?>
								</div>
								<div class="form-group">
									<label for="button">&nbsp;</label>
									<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-secondary btn-search-action">
										<img src="/images/search.png" style="cursor: default;">
									</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="margin-tb" id="page-view-result">

		</div>
	</div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div class="common-modal modal" role="dialog">
	<div class="modal-dialog" role="document" style="width: 1000px; max-width: 1000px;">
	</div>
</div>

@include("storewebsite::templates.list-template")
@include("storewebsite::templates.duplicate-data")
@include("storewebsite::templates.create-website-template")

<div id="userPasswordHistory" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Password History</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="table-responsive mt-3">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>User Name</th>
								<th>Old Password</th>
								<th>New Password</th>
							</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
</div>

<div id="buildHistoryModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Build History</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="table-responsive mt-3">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Created At</th>
								<th>Status</th>
								<th>Build Detail</th>
								<th>Created By</th>
							</tr>
						</thead>
						<tbody id="buildHistory">

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
</div>

<div id="magentoSettingUpdateHistoryModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content ">
			<div class="modal-header">
				<h4 class="modal-title">Magento Setting Update Rersponse History</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="table-responsive mt-3">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Created At</th>
								<th>Response</th>
							</tr>
						</thead>
						<tbody id="magentoSettingUpdateHistory">

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
</div>

<div id="magentoDevScriptUpdateHistoryModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content ">
			<div class="modal-header">
					<h4 class="modal-title">Magento Setting Update Rersponse History</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<div class="table-responsive mt-3">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th width="20%">Created At</th>
									<th width="20%">Website</th>
									<th width="30%">Response</th>
									<th width="30%">Command Name</th>
								</tr>
							</thead>
							<tbody id="magentoDevScriptUpdateHistory">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="store-generate-pem-file" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Store Generate Reindex</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12">
						<form action="/store-website/generate-reindex" method="post">
							<?php echo csrf_field(); ?>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="meta_title">Server List</label>
										<select class="form-control select2" name="for_server">
											<option value="BRANDS">BRANDS</option>
											<option value="AVOIRCHIC">AVOIRCHIC</option>
											<option value="OLABELS">OLABELS</option>
											<option value="SOLOLUXURY">SOLOLUXURY</option>
											<option value="SUVANDNAT">SUVANDNAT</option>
											<option value="THEFITEDIT">THEFITEDIT</option>
											<option value="THESHADESSHOP">THESHADESSHOP</option>
											<option value="UPEAU">UPEAU</option>
											<option value="VERALUSSO">VERALUSSO</option>
										</select>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group">
										<button type="submit" class="btn btn-secondary submit-generete-file-btn">Generate</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="store-api-token" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><b>Store Api Token</b></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12">
						<form action="/store-website/generate-api-token" method="post">
							<?php echo csrf_field(); ?>
							<div class="row">
								<div class="col-md-12">
									<div class="table-responsive mt-3">
										<table class="table table-bordered overlay"  >
											<thead>
											<tr>
												<th>Id</th>
												<th width="20%">Title</th>
												<th width="70%">Api Token</th>
											</tr>
											</thead>
											<tbody>
											@foreach($storeWebsites as $storeWebsite)
											<tr>
												<td >
													{{$storeWebsite->id}}
												</td>
												<td width="20%">{{$storeWebsite->title}}</td>
												<td width="70%">
													<div style="display: flex">
													<input type="text" class="form-control" name="api_token[{{$storeWebsite->id}}]" value="{{$storeWebsite->api_token}}">
													<button type="button" data-id="" class="btn btn-copy-api-token btn-sm" data-value="{{$storeWebsite->api_token}}">
														<i class="fa fa-clone" aria-hidden="true"></i>
													</button>
													</div>
												</td>
											</tr>
												@endforeach
											</tbody>
										</table>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group">
										<button type="submit" class="btn btn-secondary submit float-right float-lg-right">Update Api Token</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="execute_bash_command_select_folderModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Send Bash Command</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="table-responsive mt-3">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Command Name</th>
								<th>Send</th>
							</tr>
						</thead>
						<tbody id="execute_select_folder_tbody">

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
</div>

<script type="text/javascript" src="{{ asset('/js/jsrender.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('/js/jquery-ui.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/store-website.js') }}"></script>

<script type="text/javascript">
	page.init({
		bodyView: $("#common-page-layout"),
		baseUrl: "<?php echo url("/"); ?>"
	});

	$(document).on("click", ".open-build-process-history", function(href) {
		$.ajax({
			url: 'store-website/' + $(this).data('id') + '/build-process/history',
			success: function(data) {
				$('#buildHistory').html(data);
				$('#buildHistoryModal').modal('show');
			},
		});
	});

	$(document).on("click", ".sync_stage_to_master", function(href) {
		$.ajax({
			url: 'store-website/' + $(this).data('id') + '/sync-stage-to-master',
			success: function(data) {
				if (data.code == 200) {
					toastr["success"](data.message);
				} else {
					toastr["error"](data.message);
				}
			},
		});
	});

	$(document).on("click", ".response_history", function(href) {
		$.ajax({
			type: 'POST',
			url: 'store-website/'+ $(this).data('id') +'/magento-setting-update-history',
			beforeSend: function () {
				$("#loading-image").show();
			},
			data: {
				_token: "{{ csrf_token() }}",
				id: $(this).data('id'),
			},
			dataType: "json"
		}).done(function (response) {
			$("#loading-image").hide();
			if (response.code == 200) {
				
				$('#magentoSettingUpdateHistory').html(response.data);
			 	$('#magentoSettingUpdateHistoryModal').modal('show');
				toastr['success'](response.message, 'success');
			}
		}).fail(function (response) {
			$("#loading-image").hide();
			console.log("Sorry, something went wrong");
		});
	});

	$(document).on("click", ".execute_bash_command_response_history", function(href) {
		
			$.ajax({
				type: 'POST',
				url: 'store-website/'+ $(this).data('id') +'/magento-dev-update-script-history',
				beforeSend: function () {
					$("#loading-image").show();
				},
				data: {
					_token: "{{ csrf_token() }}",
					id: $(this).data('id'),
				},
				dataType: "json"
			}).done(function (response) {
				$("#loading-image").hide();
				if (response.code == 200) {
					$('#magentoDevScriptUpdateHistory').html(response.data);
					$('#magentoDevScriptUpdateHistoryModal').modal('show');
					toastr['success'](response.message, 'success');
				}
			}).fail(function (response) {
				$("#loading-image").hide();
				console.log("Sorry, something went wrong");
			});
		
	});
	$(document).on("click", ".execute-bash-command-select-folder", function(href) {
		var folder_name = $(this).data('folder_name');
		var id = $(this).data('id');
		var html = '';
		$('#execute_select_folder_tbody').html("");
		let result =  Array.isArray(folder_name);
		if(result){
			$.each(folder_name,function(key,value){
				if(value){
					html = '<tr><td>'+value+'  </td><td><a style="padding:1px;" class="btn d-inline btn-image execute-bash-command" data-folder_name="'+value+'" href="#" class="ip_name'+key+'" data-id="'+id+'" title="Execute Bash Command"><img src="/images/send.png" style="color: gray; cursor: nwse-resize; width: 0px;"></a></td></tr>';
					$('#execute_select_folder_tbody').append(html);
				}
			});
			$('#execute_bash_command_select_folderModal').modal('show');
		} else {
			alert("Please Check Record Site Folder Name.");
		}
		
		
	});
	$(document).on("click", ".execute-bash-command", function(href) {
		if(confirm ("Do you want to run this script???")){
			
			$.ajax({
				type: 'POST',
				url: 'store-website/'+ $(this).data('id') +'/magento-dev-script-update',
				beforeSend: function () {
					$("#loading-image").show();
				},
				data: {
					_token: "{{ csrf_token() }}",
					id: $(this).data('id'),
					folder_name : $(this).data('folder_name')
				},
				dataType: "json"
			}).done(function (response) {
				$("#loading-image").hide();
				if (response.code == 200) {
					
					//$('#magentoSettingUpdateHistory').html(response.data);
					//$('#magentoSettingUpdateHistoryModal').modal('show');
					toastr['success'](response.message, 'success');
				}
			}).fail(function (response) {
				$("#loading-image").hide();
				toastr['error'](response.message, 'error');
				console.log("Sorry, something went wrong");
			});
		}
	});

	function fnc(value, min, max) {
		if (parseFloat(value) < (0).toFixed(2) || isNaN(value))
			return null;
		else if (parseFloat(value) > (100).toFixed(2))
			return "Between 0 To 100 !";
		else return value;
	}

	$(document).on('click', '.expand-row-msg', function () {
      var name = $(this).data('name');
      var id = $(this).data('id');
      var full = '.expand-row-msg .show-short-'+name+'-'+id;
      var mini ='.expand-row-msg .show-full-'+name+'-'+id;
      $(full).toggleClass('hidden');
      $(mini).toggleClass('hidden');
    });

	
	$(document).on('click', '.btn_expand_inactive', function () {
		var cls = $(this).parent();
		//console.log(cls.height());
		if(cls.height() != '297')
			$(cls).css('height', 'auto');
		else
			$(cls).css('height', '44px');
	});
	$(document).on("click",".btn-copy-api-token",function() {
		var apiToken = $(this).data('value');
		var $temp = $("<input>");
		$("body").append($temp);
		$temp.val(apiToken).select();
		document.execCommand("copy");
		$temp.remove();
		alert("Copied!");
	});
</script>

@endsection