@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
<style type="text/css">
	.preview-category input.form-control {
		width: auto;
	}
	tbody tr .Website-task-warp{
		overflow: hidden !important;
		white-space: normal !important;
		word-break: break-all;
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
					<a target="_blank" href="/store-website/api-token" class="btn btn-secondary" data-toggle="modal1" data-target="#store-api-token1"> Api Token Update</a>
					&nbsp;
					<button class="btn btn-secondary" data-toggle="modal" data-target="#store-create-tag"> Create Tag </button>
					&nbsp;
					<button class="btn btn-secondary get_store_tags"> List Tags </button>
					&nbsp;
					<button class="btn btn-secondary attached_store_tags"> Attached Tags </button>
					&nbsp;

					@if($storeWebsites->count() > 0)
					<button class="btn btn-secondary" data-toggle="modal" data-target="#admin-passwords"> Admin Passwords</button>
					@endif
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
		@if ($errors->any())
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
		@endif
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

<div id="download_db_env_logs" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg" style="max-width: 95%;width: 100%;">
		<!-- Modal content-->
		<div class="modal-content ">
			<div class="modal-header">
				<h4 class="modal-title">Download Database/Env Logs</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="table-responsive mt-3">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>ID</th>
								<th>User</th>
								<th>Type</th>
								<th>Command</th>
								<th>Output</th>
								<th>Date</th>
							</tr>
						</thead>
						<tbody id="download_db_env_logs_tbody">

						</tbody>
					</table>
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

<div class="modal fade" id="store-list-tag" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><b>Website Tag</b></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">				
				<div class="row">
					<div class="col-lg-12">
						
						<div class="row">
							<table class="table table-border">
								<tbody>
									@if(!empty($tags))
										@foreach($tags as $key => $val)
											<tr>
												<td>Tag</td>
												<td><b>{{ $val->tags ?? '' }}</b></td>
											</tr>
										@endforeach
									@endif
								</tbody>
							</table>
						</div>
							
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="store-create-tag" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><b>Website Tag</b></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">				
				<div class="row">
					<div class="col-lg-12">
						<form action="{{ route('store-website.create_tags') }}" method="post">
							<?php echo csrf_field(); ?>
							<div class="row">
								<div class="col-md-12">
									<div class="table-responsive mt-3">
										<div class="form-group">
											<label>Tag Name</label>
											<input type="text" class="form-control" name="tag" placeholder="Enter The Tag">
										</div>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group">
										<button type="submit" class="btn btn-secondary submit_create_tag float-right float-lg-right">Update</button>
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

<div class="modal fade" id="store-attach-tag" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><b>Website Tag</b></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">				
				<div class="row">
					<div class="col-lg-12">
						<form action="{{ route('store-website.attach_tags') }}" method="post">
							<?php echo csrf_field(); ?>
							<input type="hidden" name="store_id" id="store_id" value="">
							<div class="row">
								<div class="col-md-12">
									<select class="form-control" name="tag_attached">
										@if(!empty($tags))
											@foreach($tags as $key => $val)
												<option value="{{ $val->id}}"> {{ $val->tags }}</option>
											@endforeach
										@endif
									</select>
								</div>
								<br>
								&nbsp;
								<div class="col-md-12">
									<div class="form-group">
										<button type="submit" class="btn btn-secondary attach_tag_ajax submit float-right float-lg-right">Update</button>
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
	<div class="modal-dialog modal-xl">
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
						<div style="display:flex !important; float:right !important;"><input type="text" class="form-control api-token-search" name="search"
																	 placeholder="Search">
							&nbsp;
							<button style="display: inline-block;width: 10%"
									class="btn btn-sm btn-image btn-secondary btn-search-api-token">
								<img src="/images/search.png" style="cursor: default;">
							</button>
							&nbsp;
							<button style="display: inline-block;width: 10%"
									class="btn btn-sm btn-image btn-secondary btn-refresh-api-token">
								<img src="/images/resend2.png" style="cursor: default;">
							</button>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<form action="/store-website/generate-api-token" method="post">
							<?php echo csrf_field(); ?>
							<div class="row">
								<div class="col-md-12">
									<div class="table-responsive mt-3">
										<table class="table table-bordered overlay api-token-table"  >
											<thead>
											<tr>
												<th>Id</th>
												<th width="15%">Title</th>
												<th width="45%">Api Token</th>
												<th width="30%">Server Ip</th>
											</tr>
											</thead>
											<tbody>
												@include('storewebsite::api-token')
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

<div class="modal fade" id="show-attach-tags" role="dialog">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><b>Website Tags</b></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12">
						<table class="table-border table">
							<thead>
								<tr>
									<th>Website</th>
									<th>Tag</th>
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

<div class="modal fade" id="admin-passwords" role="dialog">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><b>Admin Password</b></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12">
						<div style="display:flex !important; float:right !important;">
							<input type="text" class="form-control admin-password-search" name="search" placeholder="Search">
							&nbsp;
							<button style="display: inline-block;width: 10%"
									class="btn btn-sm btn-image btn-secondary btn-search-admin-password">
								<img src="/images/search.png" style="cursor: default;">
							</button>
							&nbsp;
							<button style="display: inline-block;width: 10%"
									class="btn btn-sm btn-image btn-secondary btn-refresh-admin-password">
								<img src="/images/resend2.png" style="cursor: default;">
							</button>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<form action="/store-website/generate-admin-password" method="post">
							<?php echo csrf_field(); ?>
							<div class="row">
								<div class="col-md-12">
									<div class="table-responsive mt-3">
										<table class="table table-bordered overlay admin-password-table" id="tblAdminPassword">
											<thead>
											<tr>
												<th>Id</th>
												<th width="30%">Website</th>
												<th width="30%">Username</th>
												<th width="30%">Password</th>												
											</tr>
											</thead>
											<tbody>
												@include('storewebsite::admin-password')
											</tbody>
										</table>
									</div>
								</div>

								<div class="col-md-12">
									<div class="form-group">
										<div class="col-md-12">
											<div class="row float-right">												
												<button type="button" data-id="" class="btn btn-add-admin-password float-right" style="border:1px solid">
										            <i class="fa fa-plus" aria-hidden="true"></i>
									            </button>
									            &nbsp;
												<button type="submit" class="btn btn-secondary submit float-right float-lg-right">Update Admin Password</button>
											</div>
										</div>
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

	function Showactionbtn(id){
		$(".action-btn-tr-"+id).toggleClass('d-none')
	}

	//Post the tag with website and attach
	$(document).on("click", ".attach_tag_ajax", function(e) {
		e.preventDefault();
		var url 		=  "{{ route('store-website.attach_tags') }}";

		var formData 	=	$(this).closest('form').serialize();

		$('#loading-image-preview').show();
		$.ajax({
			url 	: 	url,
			method 	: 	'POST',
			data 	: 	formData,
			success : 	function(resp){
				
				if (resp.code == 200) {					
					toastr["success"](resp.message);
				} else {
					toastr["error"](resp.message);
				}
				$('#loading-image-preview').hide();
				$('#store-attach-tag').modal('hide');
			},
			error 	: 	function(err){
				$('#loading-image-preview').hide();
				$('#store-create-tag').modal('hide');
				toastr["error"](err.message);
			}
		})
	});

	//Get a list of store tags
	$(document).on("click", ".get_store_tags", function(e) {
		e.preventDefault();
		var url 		=  "{{ route('store-website.list_tags') }}";

		$('#loading-image-preview').show();
		$.ajax({
			url 	: 	url,
			method 	: 	'GET',
			success : 	function(resp){
				
				if (resp.code == 200) {
					var html = '';
					for(var key in resp.data){
						var innerData  = resp.data[key];
						html += '<tr> <td>Tag</td> <td><b>'+innerData.tags +'</b></td> </tr>';
					}

					$('#store-list-tag tbody').html(html);
				$('#loading-image-preview').hide();
					$('#store-list-tag').modal('show');
					
				} else {
					toastr["error"](resp.message);
				}


			},
			error 	: 	function(err){
				$('#loading-image-preview').hide();
				$('#store-create-tag').modal('hide');
				toastr["error"](err.message);
			}
		})

	});

	//get list of website with their tags
	$(document).on("click", ".attached_store_tags", function(e) {
		e.preventDefault();
		var url 		=  "{{ route('store-website.attach_tags_store') }}";

		$('#loading-image-preview').show();
		$.ajax({
			url 	: 	url,
			method 	: 	'GET',
			success : 	function(resp){
				
				
			console.log(resp);
			if (resp.code == 200) {
				var response 	=	resp.data;
				var html 		= 	'';

				for(var key in response){
					html 	+= '<tr> <td>'+ response[key]['website']+'</td> <td>'+ response[key]['tags']['tags']+'</td> </tr>';
				}

				$('#show-attach-tags table tbody').html(html);
				toastr["success"](resp.message);
			} else {
				toastr["error"](resp.message);
			}
			$('#loading-image-preview').hide();
			$('#show-attach-tags').modal('show');
		
			},
			error 	: 	function(err){
				$('#loading-image-preview').hide();
				$('#store-create-tag').modal('hide');
				toastr["error"](err.message);
			}
		})

	});

	//Create tags 
	$(document).on("click", ".submit_create_tag", function(e) {
		e.preventDefault();
		var url 		=  "{{ route('store-website.create_tags') }}";
		var formData 	=	$(this).closest('form').serialize();

		$('#loading-image-preview').show();
		$.ajax({
			url 	: 	url,
			method 	: 	'POST',
			data 	: 	formData,
			success : 	function(resp){
				$('#loading-image-preview').hide();
				$('#store-create-tag').modal('hide');
				if (resp.code == 200) {
					toastr["success"](resp.message);
				} else {
					toastr["error"](resp.message);
				}

			},
			error 	: 	function(err){
				$('#loading-image-preview').hide();
				$('#store-create-tag').modal('hide');
				toastr["error"](err.message);
			}
		})

	});
	$(document).on("click", ".open-build-process-history", function(href) {
		$.ajax({
			url: '/store-website/' + $(this).data('id') + '/build-process/history',
			success: function(data) {
				$('#buildHistory').html(data);
				$('#buildHistoryModal').modal('show');
			},
		});
	});

	$(document).on("click", ".sync_stage_to_master", function(href) {
		$.ajax({
			url: '/store-website/' + $(this).data('id') + '/sync-stage-to-master',
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
			url: '/store-website/'+ $(this).data('id') +'/magento-setting-update-history',
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
				url: '/store-website/'+ $(this).data('id') +'/magento-dev-update-script-history',
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

	$(document).on("click", ".run_file_permissions", function(href) {
		
		$.ajax({
			type: 'POST',
			url: '/store-website/'+ $(this).data('id') +'/run-file-permissions',
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
				toastr['success'](response.message, 'success');
			}else{
				toastr['error'](response.message, 'error');
			}
			
		}).fail(function (response) {
			$("#loading-image").hide();
			toastr['error']("Sorry, something went wrong", 'error');
		});
	
	});

	$(document).on("click", ".clear_cloudflare_caches", function(href) {
		
		$.ajax({
			type: 'POST',
			url: '/store-website/'+ $(this).data('id') +'/clear-cloudflare-caches',
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
				toastr['success'](response.message, 'success');
			}else{
				toastr['error'](response.message, 'error');
			}
			
		}).fail(function (response) {
			$("#loading-image").hide();
			toastr['error']("Sorry, something went wrong", 'error');
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
				url: '/store-website/'+ $(this).data('id') +'/magento-dev-script-update',
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

	$(document).on("click",".btn-copy-server-ip",function() {
		var serverip = $(this).data('value');
		var $temp = $("<input>");
		$("body").append($temp);
		$temp.val(serverip).select();
		document.execCommand("copy");
		$temp.remove();
		alert("Copied!");
	});

	$(document).on('click','.btn-search-api-token',function(){
		src = '/store-website/get-api-token'
		search = $('.api-token-search').val()
		$.ajax({
			url: src,
			dataType: "json",
			type: "GET",
			data: {
				search : search,
			},
			beforeSend: function () {
				$("#loading-image").show();
			},
		}).done(function (data) {
			$("#loading-image").hide();
			$(".api-token-table tbody").empty().html(data.tbody);
		}).fail(function (jqXHR, ajaxOptions, thrownError) {
			alert('No response from server');
		});
	})

	$(document).on('click','.btn-refresh-api-token',function(){
		src = '/store-website/get-api-token'
		$.ajax({
			url: src,
			dataType: "json",
			type: "GET",
			data: {

			},
			beforeSend: function () {
				$("#loading-image").show();
			},

		}).done(function (data) {
			$("#loading-image").hide();
			$(".api-token-search").val("");
			$(".api-token-table tbody").empty().html(data.tbody);



		}).fail(function (jqXHR, ajaxOptions, thrownError) {
			alert('No response from server');
		});

	})

	$(document).on('click','.btn-search-admin-password',function(){
		src = '/store-website/get-admin-password'
		search = $('.admin-password-search').val()
		$.ajax({
			url: src,
			dataType: "json",
			type: "GET",
			data: {
				search : search,
			},
			beforeSend: function () {
				$("#loading-image").show();
			},
		}).done(function (data) {
			$("#loading-image").hide();
			$(".admin-password-table tbody").empty().html(data.tbody);
		}).fail(function (jqXHR, ajaxOptions, thrownError) {
			alert('No response from server');
		});
	})

	$(document).on('click','.btn-download-db-env',function(){
		var id=$(this).attr('data-id');
		var type=$(this).attr('data-type');
		if( id === undefined || id === '' || type === undefined || type === '' ){
			toastr["error"]("Something Went Wrong, Please Try Again Later!");
			return false;
		}

		var url = "{{url('/')}}/store-website/" + id + "/download/" + type;

		$.ajax({
		url: url,
		type: "GET",
		dataType: "json", // Change this based on your server response
		success: function(response) {
		if (response.status === 'success') {
			toastr.success(response.message, 'Success');
			console.log(response);
		} else {
			toastr.error(response.message, 'Error');
		}
		},
		error: function(xhr, status, error) {
		toastr.error("Something Went Wrong, Please Try Again Later!", 'Error');
		console.error(error);
		}
	});
});

	$(document).on("click", ".btn-download-db-env-logs", function(href) {
		$.ajax({
			type: 'POST',
			url: '/store-website/'+ $(this).data('id') +'/download-db-env-logs',
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
				
				$('#download_db_env_logs_tbody').html(response.data);
			 	$('#download_db_env_logs').modal('show');
				toastr['success'](response.message, 'success');
			}
		}).fail(function (response) {
			$("#loading-image").hide();
			console.log("Sorry, something went wrong");
		});
	});

	$(document).on('click','.btn-refresh-admin-password',function(){
		src = '/store-website/get-admin-password'
		$.ajax({
			url: src,
			dataType: "json",
			type: "GET",
			data: {

			},
			beforeSend: function () {
				$("#loading-image").show();
			},

		}).done(function (data) {
			$("#loading-image").hide();
			$(".admin-password-search").val("");
			$(".admin-password-table tbody").empty().html(data.tbody);



		}).fail(function (jqXHR, ajaxOptions, thrownError) {
			alert('No response from server');
		});

	})	

    // add more admin password
    var j = 1;
    $('.btn-add-admin-password').click(function(){        
        
        $('#tblAdminPassword').append('<tr id="rowAdminPassword'+j+'"><td width="10%"></td><td width="30%"><select name="store_website_id[new:'+j+']" class="form-control websiteMode"><option value="">-- Select a website--</option>@foreach($storeWebsites as $key => $storeWebsite)<option value="{{ $storeWebsite->id }}">{{ $storeWebsite->title }}</option>@endforeach</select></td><td width="30%"><input type="text" class="form-control" name="username[new:'+j+']" placeholder="Enter username"/></td><td width="30%"><input type="text" class="form-control" name="password[new:'+j+']" placeholder="Enter password"/></td></tr>');
        
        j++;
    });
</script>

@endsection