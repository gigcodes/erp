@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Ui Check')

@section('styles')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<style type="text/css">
	.preview-category input.form-control {
		width: auto;
	}

	#loading-image {
		position: fixed;
		top: 50%;
		left: 50%;
		margin: -50px 0px 0px -50px;
	}

	.dis-none {
		display: none;
	}

	.pd-5 {
		padding: 3px;
	}

	.toggle.btn {
		min-height: 25px;
	}

	.toggle-group .btn {
		padding: 2px 12px;
	}

	.latest-remarks-list-view tr td {
		padding: 3px !important;
	}
	#latest-remarks-modal .modal-dialog {
		 max-width: 1100px;
		width:100%;
	}
	.btn-secondary{
		border: 1px solid #ddd;
		color: #757575;
		background-color: #fff !important;
	}
	.modal {
		overflow-y:auto;
	}
	body.overflow-hidden{
		overflow: hidden;
	}

	span.user_point_none button, span.admin_point_none button{
		pointer-events: none;
		cursor: not-allowed;
	}table tr:last-child td {
		 border-bottom: 1px solid #ddd !important;
	 }
	 select.globalSelect2 + span.select2 {
    width: calc(100% - 26px) !important;
}

</style>
@endsection

@section('large_content')

<div id="myDiv">
	<img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
</div>

<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Ui Check</h2>
    </div>
    <br>
	<div class="col-lg-12 margin-tb">
		<div class="row">
			<div class="col-md-6 pull-right">
				<form>
					<div class="col-md-4">
						<select name="store_webs" id="store_webiste" class="form-control select2">
							<option value="">-- Select a website --</option>
							@forelse($all_store_websites as $asw)
								<option value="{{ $asw->id }}" 
								@if($search_website == $asw->id) 
									selected	
								@endif>{{ $asw->title }}</option>
							@empty
							@endforelse
						</select>
					</div>
					<div class="col-md-4">
						<select name="categories" id="store-categories" class="form-control select2">
							<option value="">-- Select a categories --</option>
							@forelse($site_development_categories  as $ct)
								<option value="{{ $ct->id }}" 
								@if($search_category == $ct->id) 
								selected	
								@endif>{{ $ct->title }}</option>
							@empty
							@endforelse
						</select>
					</div>
					<div class="col-md-4">
						<button type="button" class="btn btn-secondary custom-filter">Search</button>
						<a href="/uicheck" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
					</div>
				</form>				
			</div>
		</div>
		<div class="row mb-4">
			<div class="col-md-12">
				<div class="pull-right mt-4">
					@if (auth()->user()->isAdmin())
						<a class="btn btn-secondary" data-toggle="modal" data-target="#newTypeModal">Create Type</a>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
<div id="newTypeModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add new status</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form style="padding:10px;" action="{{ route('uicheck.type.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <input type="text" class="form-control" name="name" placeholder="Name" value="{{ old('name') }}" required>

                    @if ($errors->has('name'))
                        <div class="alert alert-danger">{{$errors->first('name')}}</div>
                    @endif
                </div>

                <button type="submit" class="btn btn-secondary ml-3">Add Type</button>
            </form>
        </div>
    </div>
</div>
@if (Session::has('message'))
	{{ Session::get('message') }}
@endif
<br />
<div class="row mt-2">
	<div class="col-md-12 margin-tb infinite-scroll">
		<table class="table table-bordered " id="uicheck_table">
			<thead>
				<tr>
					<th><input type="checkbox" id="checkAll" title="click here to select all" /></th>
					<th width="10%">Categories</th>
					<th>Website</th>
					@if (Auth::user()->hasRole('Admin'))						
						<th>Assign To</th>
					@endif
					<th>Issue</th>
					<th>Communication</th>
					<th>Developer Status</th>
					<th>Type</th>
					<th>Admin Status</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>
<div class="custom-website-dropdown" style="display:none;">
	{{ json_encode($all_store_websites) }}
</div>
<div class="custom-user-dropdown" style="display:none;">
	{{ json_encode($allUsers) }}
</div>
<div class="custom-type-dropdown" style="display:none;">
	{{ json_encode($allTypes) }}
</div>
<div class="custom-status-dropdown" style="display:none;">
	{{ json_encode($allStatus) }}
</div>


<div id="dev_status_model" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h2>Developer Status History</h2>
				<button type="button" class="close" data-dismiss="modal">×</button>
			</div>
			<div class="modal-body" id="">
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>ID</th>
								<th>User Name</th>
								<th>Old Status</th>
								<th>Status</th>
								
							</tr>
						</thead>
						<tbody id="dev_status_tboday">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="admin_status_model" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h2>Admin Status History</h2>
				<button type="button" class="close" data-dismiss="modal">×</button>
			</div>
			<div class="modal-body" id="">
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>ID</th>
								<th>User Name</th>
								<th>Old Status</th>
								<th>Status</th>
								
							</tr>
						</thead>
						<tbody id="admin_status_tboday">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@include('uicheck.upload-document-modal')
@include("partials.plain-modal")
<div id="issue_model" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h2>U I Issue History</h2>
				<button type="button" class="close" data-dismiss="modal">×</button>
			</div>
			<div class="modal-body" id="">
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>ID</th>
								<th>User Name</th>
								<th>Old Issue</th>
								<th>Issue</th>
								
							</tr>
						</thead>
						<tbody id="issue_tboday">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="message_model" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h2>U I Message History</h2>
				<button type="button" class="close" data-dismiss="modal">×</button>
			</div>
			<div class="modal-body" id="">
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>ID</th>
								<th>User Name</th>
								<th>Message</th>
								<th>Date</th>
								
							</tr>
						</thead>
						<tbody id="message_tboday">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@if (Auth::user()->hasRole('Admin'))
<input type="hidden" id="user-type" value="Admin">
@else
<input type="hidden" id="user-type" value="Not Admin">
@endif
@endsection


@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
// START Print Table Using datatable
	var oTable;
	
	$(document).ready(function() {
		if($("#user-type").val()=="Admin"){
			var columns= [{
					data: null,
					width : "5%",
					render: function (data, type, full, meta) {
						return '<input type="checkbox" id="checkAll" title="click here to select all" /><a href="javascript:;" data-id="'+full.uicheck_id+'" class="upload-document-btn"><img width="15px" src="/images/attach.png" alt="" style="cursor: default;"><a><a href="javascript:;" data-id="'+full.uicheck_id+'" class="list-document-btn"><img width="15px" src="/images/archive.png" alt="" style="cursor: default;"><a>';
					}
				},
				{
					data: 'title',
					render: function (data, type, full, meta) {
						return data;
					}
				},
				{
					data: null,
					render: function (data, type, row, meta) {
						$('.globalSelect2').select2();
						var text = $(".custom-website-dropdown").text();
						var html = '<select name="website_id"  class="save-item-select globalSelect2 website_id" data-category="'+row.id+'" data-id="'+row.uicheck_id+'" data-site_development_id="'+row.site_id+'">';
							html += '<option value="">--Select--</option>';
							
							text = $.parseJSON(text);
							$.each(text, function (i, obj) {
								var selected = "";
								if(row.websiteid == obj.id){
									selected = "selected='selected'";
								}
								html += '<option value="'+obj.id+'" '+selected+' >'+obj.website+'</option>';
							});
						html += '</select>';
                		return html;
					}
				},
				{
					data: null,
					render: function (data, type, full, meta) {
						$('.globalSelect2').select2();
						var text = $(".custom-user-dropdown").text();
						var html = '<select name="user_id" data-id="'+full.uicheck_id+'" class="save-item-select globalSelect2 user_id">';
							html += '<option value="">--Select--</option>';
							text = $.parseJSON(text);
							$.each(text, function (i, obj) {
								var selected = "";
								if(full.accessuser == obj.id){
									selected = "selected='selected'";
								}
								html += '<option '+selected+' value="'+obj.id+'">'+obj.name+'</option>';
							});						
						html += '</select>';
                		return html;						
					}
				},
				{
					data: 'issue',
					render: function (data, type, row, meta) {
						var html = '<div class="col-md-12 mb-1 p-0 d-flex pt-2 mt-1"><input style="margin-top: 0px;width:87% !important;" type="text" class="form-control " id="issue-'+row.uicheck_id+'" name="issue-'+row.uicheck_id+'" placeholder="Issues" value="'+row.issue+'"><div style="margin-top: 0px;" class="d-flex p-0"><button class="btn pr-0 btn-xs btn-image issue" data-category="'+row.id+'" data-id="'+row.uicheck_id+'" data-site_development_id="'+row.site_id+'"><img src="/images/filled-sent.png" /></button></div><button type="button" class="btn btn-xs show-issue-history" title="Show Issue History" data-id="'+row.uicheck_id+'"><i data-id="'+row.uicheck_id+'" class="fa fa-info-circle"></i></button></div>';
						return html;
					}
				},
				{
					data: null,
					render: function (data, type, row, meta) {
						var html = '<div class="col-md-12 mb-1 p-0 d-flex pl-4 pt-2 mt-1 msg" style="width: 100%;"><input type="text" style="width: 100%; float: left;" class="form-control quick-message-'+row.uicheck_id+' input-sm" name="message" placeholder="Message" value=""><div class="d-flex p-0"><button style="float: left;padding: 0 0 0 5px" class="btn btn-sm btn-image uicheck-message" title="Send message" data-category="'+row.id+'" data-taskid="'+row.uicheck_id+'"  data-id="'+row.uicheck_id+'" data-site_development_id="'+row.site_id+'"><img src="/images/filled-sent.png" style="cursor: default;"></button></div><button type="button" class="btn btn-xs btn-image show-message-history load-body-class" data-object="uicheck" data-id="'+row.uicheck_id+'" title="Load messages" data-category="'+row.id+'" data-site_development_id="'+row.site_id+'" data-dismiss="modal"><img src="/images/chat.png" alt=""></button></div>';						
						return html;						
					}
				},
				{
					data: null,
					render: function (data, type, row, meta) {
						var text = $(".custom-status-dropdown").text();
						var html = '<select name="developer_status"  class="form-control save-item-select width-auto globalSelect2 developer_status" data-type="developer_status" data-category="'+row.id+'" data-id="'+row.uicheck_id+'" data-site_development_id="'+row.site_id+'">';
							html += '<option value="">--Select--</option>';
							text = $.parseJSON(text);
							$.each(text, function (i, obj) {
								var selected = "";
								if(row.dev_status_id == i){
									selected = "selected='selected'";
								}
								html += '<option value="'+i+'" '+selected+'>'+obj+'</option>';
							});
							html += '</select>';
							html += '<button type="button" class="btn btn-xs show-dev-status-history" title="Show Developer Status History" data-id="'+row.uicheck_id+'"><i data-id="'+row.uicheck_id+'" class="fa fa-info-circle"></i></button>';						
						return html;						
					}
				},
				{
					data: null,
					render: function (data, type, row, meta) {
						$('.globalSelect2').select2();
						var text = $(".custom-type-dropdown").text();
						var html = '<select name="type_id"  class="save-item-select globalSelect2 type_id" data-category="'+row.id+'" data-id="'+row.uicheck_id+'" data-site_development_id="'+row.site_id+'">';
							html += '<option value="">--Select--</option>';
							
							text = $.parseJSON(text);
							$.each(text, function (i, obj) {
								var selected = "";
								if(row.uicheck_type_id == obj.id){
									selected = "selected='selected'";
								}
								html += '<option value="'+obj.id+'" '+selected+' >'+obj.name+'</option>';
							});
						html += '</select>';
                		return html;					
					}
				},
				{
					data: null,
					render: function (data, type, row, meta) {
						var text = $(".custom-status-dropdown").text();
						var html = '<select name="admin_status"  class="form-control save-item-select width-auto globalSelect2 admin_status" data-type="admin_status" data-category="'+row.id+'" data-id="'+row.uicheck_id+'" data-site_development_id="'+row.site_id+'">';
							html += '<option value="">--Select--</option>';
							text = $.parseJSON(text);
							$.each(text, function (i, obj) {
								var selected = "";
								if(row.admin_status_id == i){
									selected = "selected='selected'";
								}
								html += '<option value="'+i+'" '+selected+'>'+obj+'</option>';
							});
						html += '</select>';
						html += '<button type="button" class="btn btn-xs show-admin-status-history" title="Show" data-id="'+row.uicheck_id+'"><i data-id="'+row.uicheck_id+'" class="fa fa-info-circle"></i></button>';						
						return html;						
					}
				}
			];		
		}else{
			var columns= [{
					data: null,
					width : "5%",
					render: function (data, type, full, meta) {
						return '<input type="checkbox" id="checkAll" title="click here to select all" /><a href="javascript:;" data-id="'+full.uicheck_id+'" class="upload-document-btn"><img width="15px" src="/images/attach.png" alt="" style="cursor: default;"><a><a href="javascript:;" data-id="'+full.uicheck_id+'" class="list-document-btn"><img width="15px" src="/images/archive.png" alt="" style="cursor: default;"><a>';
					}
				},
				{
					data: 'title',
					render: function (data, type, full, meta) {
						return data;
					}
				},
				{
					data: null,
					render: function (data, type, row, meta) {
						$('.globalSelect2').select2();
						var text = $(".custom-website-dropdown").text();
						var html = '<select name="website_id"  class="save-item-select globalSelect2 website_id" data-category="'+row.id+'" data-id="'+row.uicheck_id+'" data-site_development_id="'+row.site_id+'">';
							html += '<option value="">--Select--</option>';
							
							text = $.parseJSON(text);
							$.each(text, function (i, obj) {
								var selected = "";
								if(row.websiteid == obj.id){
									selected = "selected='selected'";
								}
								html += '<option value="'+obj.id+'" '+selected+' >'+obj.website+'</option>';
							});
						html += '</select>';
                		return html;
					}
				},
				{
					data: 'issue',
					render: function (data, type, row, meta) {
						var html = '<div class="col-md-12 mb-1 p-0 d-flex pt-2 mt-1"><input style="margin-top: 0px;width:87% !important;" type="text" class="form-control " id="issue-'+row.uicheck_id+'" name="issue-'+row.uicheck_id+'" placeholder="Issues" value="'+row.issue+'"><div style="margin-top: 0px;" class="d-flex p-0"><button class="btn pr-0 btn-xs btn-image issue" data-category="'+row.id+'" data-id="'+row.uicheck_id+'" data-site_development_id="'+row.site_id+'"><img src="/images/filled-sent.png" /></button></div><button type="button" class="btn btn-xs show-issue-history" title="Show Issue History" data-id="'+row.uicheck_id+'"><i data-id="'+row.uicheck_id+'" class="fa fa-info-circle"></i></button></div>';
						return html;
					}
				},
				{
					data: null,
					render: function (data, type, row, meta) {
						var html = '<div class="col-md-12 mb-1 p-0 d-flex pl-4 pt-2 mt-1 msg" style="width: 100%;"><input type="text" style="width: 100%; float: left;" class="form-control quick-message-'+row.uicheck_id+' input-sm" name="message" placeholder="Message" value=""><div class="d-flex p-0"><button style="float: left;padding: 0 0 0 5px" class="btn btn-sm btn-image uicheck-message" title="Send message" data-category="'+row.id+'" data-taskid="'+row.uicheck_id+'"  data-id="'+row.uicheck_id+'" data-site_development_id="'+row.site_id+'"><img src="/images/filled-sent.png" style="cursor: default;"></button></div><button type="button" class="btn btn-xs btn-image show-message-history load-body-class" data-object="uicheck" data-id="'+row.uicheck_id+'" title="Load messages" data-category="'+row.id+'" data-site_development_id="'+row.site_id+'" data-dismiss="modal"><img src="/images/chat.png" alt=""></button></div>';						
						return html;						
					}
				},
				{
					data: null,
					render: function (data, type, row, meta) {
						var text = $(".custom-status-dropdown").text();
						var html = '<select name="developer_status"  class="form-control save-item-select width-auto globalSelect2 developer_status" data-type="developer_status" data-category="'+row.id+'" data-id="'+row.uicheck_id+'" data-site_development_id="'+row.site_id+'">';
							html += '<option value="">--Select--</option>';
							text = $.parseJSON(text);
							$.each(text, function (i, obj) {
								var selected = "";
								if(row.dev_status_id == i){
									selected = "selected='selected'";
								}
								html += '<option value="'+i+'" '+selected+'>'+obj+'</option>';
							});
							html += '</select>';
							html += '<button type="button" class="btn btn-xs show-dev-status-history" title="Show Developer Status History" data-id="'+row.uicheck_id+'"><i data-id="'+row.uicheck_id+'" class="fa fa-info-circle"></i></button>';						
						return html;						
					}
				},
				{
					data: null,
					render: function (data, type, row, meta) {
						$('.globalSelect2').select2();
						var text = $(".custom-type-dropdown").text();
						var html = '<select name="type_id"  class="save-item-select globalSelect2 type_id" data-category="'+row.id+'" data-id="'+row.uicheck_id+'" data-site_development_id="'+row.site_id+'">';
							html += '<option value="">--Select--</option>';
							
							text = $.parseJSON(text);
							$.each(text, function (i, obj) {
								var selected = "";
								if(row.uicheck_type_id == obj.id){
									selected = "selected='selected'";
								}
								html += '<option value="'+obj.id+'" '+selected+' >'+obj.name+'</option>';
							});
						html += '</select>';
                		return html;					
					}
				},
				{
					data: null,
					render: function (data, type, row, meta) {
						var	html = '<button type="button" class="btn btn-xs show-admin-status-history" title="Show" data-id="'+row.uicheck_id+'"><i data-id="'+row.uicheck_id+'" class="fa fa-info-circle"></i></button>';						
						return html;						
					}
				}
			];
		}
		oTable = $('#uicheck_table').DataTable({
			lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
			responsive: true,
			searchDelay: 500,
			processing: true,
			serverSide: true,
			// sScrollX:true,
			searching: false,
			order: [
				[0, 'desc']
			],
			targets: 'no-sort',
			bSort: false,
			drawCallback: function() {
				$('.globalSelect2').select2();
			},
			ajax: {
				"url": "{{ route('uicheck') }}",
				data: function(d) {
					d.category_name = $('#store_webiste').val();
					d.sub_category_name = $('#store-categories').val();
					// d.subjects = $('input[name=subjects]').val();					
				},
			},
			columnDefs: [{
				targets: [],
				orderable: false,
				searchable: false
			}],
			columns: columns
		});

		$(document).on("click", ".custom-filter", function(e) {
			oTable.draw();
		});

		$(document).on("change", ".user_id", function(e) {
			e.preventDefault();
			var uicheck_id = $(this).data("id");
			var id = $(this).val();
			
			$.ajax({
				url: "{{route('uicheck.user.access')}}",
				type: 'POST',
				data: {
					id:id,
					uicheck_id: uicheck_id,
					"_token": "{{ csrf_token() }}",
				},
				beforeSend: function() {
					$(this).text('Loading...');
					
				},
				success: function(response) {
					if (response.code == 200) {
						toastr['success'](response.message);
						oTable.draw();						
					} else {
						toastr['error'](response.message);
					}
				}
			}).fail(function(response) {
				toastr['error'](response.responseJSON.message);
			});
		});
		
		$(document).on("change", ".website_id", function(e) {
			e.preventDefault();
			var id = $(this).data("id");
			var website_id = $(this).val();
			var site_development_id = $(this).data("site_development_id");
			var category = $(this).data('category');
			$.ajax({
				url: "{{route('uicheck.store')}}",
				type: 'POST',
				data: {
					id:id,
					website_id: website_id,
					site_development_id: site_development_id,
					category:category,
					"_token": "{{ csrf_token() }}",
				},
				beforeSend: function() {
					$(this).text('Loading...');
				},
				success: function(response) {
					if (response.code == 200) {
						toastr['success'](response.message);
						oTable.draw();
						$('.globalSelect2').select2();
					} else {
						toastr['error'](response.message);
					}
				}
			}).fail(function(response) {
				toastr['error'](response.responseJSON.message);
			});
		});

		$(document).on("change", ".type_id", function(e) {
			e.preventDefault();
			var uicheck_id = $(this).data("id");
			var type_id = $(this).val();
			$.ajax({
				url: "{{route('uicheck.type.save')}}",
				type: 'POST',
				data: {
					uicheck_id:uicheck_id,
					type : type_id,
					"_token": "{{ csrf_token() }}",
				},
				beforeSend: function() {
					$(this).text('Loading...');
				},
				success: function(response) {
					if (response.code == 200) {
						toastr['success'](response.message);
						oTable.draw();
						$('.globalSelect2').select2();
					} else {
						toastr['error'](response.message);
					}
				}
			}).fail(function(response) {
				toastr['error'](response.responseJSON.message);
			});
		});

		$(document).on("change", ".developer_status", function(e) {
			e.preventDefault();
			var id = $(this).data("id");
			var developer_status = $(this).val();
			var site_development_id = $(this).data("site_development_id");
			var category = $(this).data("category");

			$.ajax({
				url: "{{route('uicheck.store')}}",
				type: 'POST',
				data: {
					id:id,
					developer_status: developer_status,
					site_development_id: site_development_id,
					category:category,
					"_token": "{{ csrf_token() }}",
				},
				beforeSend: function() {
					$(this).text('Loading...');
				},
				success: function(response) {
					if (response.code == 200) {
						toastr['success'](response.message);
						//$("#create-quick-task").modal("hide");
						oTable.draw();
					} else {
						toastr['error'](response.message);
					}
				}
			}).fail(function(response) {
				toastr['error'](response.responseJSON.message);
			});
		});

		$(document).on("change", ".admin_status", function(e) {
			e.preventDefault();
			var id = $(this).data("id");
			var admin_status = $(this).val();
			var site_development_id = $(this).data("site_development_id");
			var category = $(this).data("category");

			$.ajax({
				url: "{{route('uicheck.store')}}",
				type: 'POST',
				data: {
					id:id,
					admin_status: admin_status,
					site_development_id: site_development_id,
					category:category,
					"_token": "{{ csrf_token() }}",
				},
				beforeSend: function() {
					$(this).text('Loading...');
				},
				success: function(response) {
					if (response.code == 200) {
						toastr['success'](response.message);
						//$("#create-quick-task").modal("hide");
						oTable.draw();
						
					} else {
						toastr['error'](response.message);
					}
				}
			}).fail(function(response) {
				toastr['error'](response.responseJSON.message);
			});
		});
	});
	// END Print Table Using datatable


// For Document Upload ajax
$(document).on("submit", "#upload-uicheck-documents", function(e) {
	e.preventDefault();
	var form = $(this);
	var postData = new FormData(form[0]);
	$.ajax({
		method: "post",
		url: `{{ route('uicheck.upload-document') }}`,
		data: postData,
		processData: false,
		contentType: false,
		dataType: "json",
		beforeSend: function() {
			$("#loading-image").show();
		},
		success: function(response) {
			if (response.code == 200) {
				toastr["success"]("Document Uploaded!", "Message")
				$("#upload-document-modal").modal("hide");
			} else {
				toastr["error"](response.error, "Message");
			}
			$("#loading-image").hide();
		}
	});
});

$(document).on("click", ".list-document-btn", function() {
	var id = $(this).data("id");
	$.ajax({
		method: "GET",
		url: "{{ action('UicheckController@getDocument') }}",
		data: {
			id: id
		},
		dataType: "json",
		success: function(response) {
			if (response.code == 200) {
				$("#blank-modal").find(".modal-title").html("Document List");
				$("#blank-modal").find(".modal-body").html(response.data);
				$("#blank-modal").modal("show");
			} else {
				toastr["error"](response.error, "Message");
			}
		}
	});
});

$(document).on("click",".upload-document-btn",function() {
	var id = $(this).data("id");
	$("#upload-document-modal").find("#hidden-identifier").val(id);    
	$("#upload-document-modal").modal("show");
});

$(document).on("click", ".issue", function(e) {
	e.preventDefault();
	var id = $(this).data("id");
    var issue = $("#issue-"+id).val();
    var site_development_id = $(this).data("site_development_id");
    var category = $(this).data("category");

	$.ajax({
		url: "{{route('uicheck.store')}}",
		type: 'POST',
		data: {
            id:id,
            issue: issue,
            site_development_id: site_development_id,
            category:category,
            "_token": "{{ csrf_token() }}",
        },
		beforeSend: function() {
			$(this).text('Loading...');
		},
		success: function(response) {
			if (response.code == 200) {
				toastr['success'](response.message);
				//$("#create-quick-task").modal("hide");
              //  location.reload();
			} else {
				toastr['error'](response.message);
			}
		}
	}).fail(function(response) {
		toastr['error'](response.responseJSON.message);
	});
});



$(document).on('click', '.send-message', function() {
    var thiss = $(this);
    var data = new FormData();
    var task_id = $(this).data('taskid');
    var message = $(this).closest('tr').find('.quick-message-field').val();
   // debugger;
    data.append("task_id", task_id);
    data.append("message", message);
    data.append("status", 1);
    data.append("object_id", "{{$log_user_id}}");

    if (message.length > 0) {
        if (!$(thiss).is(':disabled')) {
            $.ajax({
                url: '/whatsapp/sendMessage/uicheckMessage',
                type: 'POST',
                "dataType": 'json', // what to expect back from the PHP script, if anything
                "cache": false,
                "contentType": false,
                "processData": false,
                "data": data,
                beforeSend: function() {
                    $(thiss).attr('disabled', true);
                }
            }).done(function(response) {
                thiss.closest('tr').find('.quick-message-field').val('');
				toastr["success"]("Message successfully send!", "Message")

                // $.post( "/whatsapp/approve/customer", { messageId: response.message.id })
                //   .done(function( data ) {
                //
                //   }).fail(function(response) {
                //     console.log(response);
                //     alert(response.responseJSON.message);
                //   });

                $(thiss).attr('disabled', false);
            }).fail(function(errObj) {
                $(thiss).attr('disabled', false);

                alert("Could not send message");
                console.log(errObj);
            });
        }
    } else {
        alert('Please enter a message first');
    }
});

$(document).on("click", ".uicheck-message", function(e) {
	e.preventDefault();
	var id = $(this).data("id");
    var message = $(".quick-message-"+id).val();
    var site_development_id = $(this).data("site_development_id");
    var category = $(this).data("category");

	$.ajax({
		url: "{{route('uicheck.set.message.history')}}",
		type: 'POST',
		data: {
            id:id,
            message: message,
            site_development_id: site_development_id,
            category:category,
            "_token": "{{ csrf_token() }}",
        },
		beforeSend: function() {
			$(this).text('Loading...');
		},
		success: function(response) {
			if (response.code == 200) {
				$(".quick-message-"+id).val("");
				toastr['success'](response.message);
				//$("#create-quick-task").modal("hide");
              //  location.reload();
			} else {
				toastr['error'](response.message);
			}
		}
	}).fail(function(response) {
		toastr['error'](response.responseJSON.message);
	});
});

$(".select2").select2();

$("#checkAll").click(function(){
	$('input:checkbox').not(this).prop('checked', this.checked);
});

$(document).on("click", ".show-dev-status-history", function(e) {
	//debugger;
	e.preventDefault();
	var id = $(this).data("id");
    var developer_status = $(this).val();
    var site_development_id = $(this).data("site_development_id");
    var category = $(this).data("category");

	$.ajax({
		url: "{{route('uicheck.dev.status.history')}}",
		type: 'POST',
		data: {
            id:id,
            developer_status: developer_status,
            site_development_id: site_development_id,
            category:category,
            "_token": "{{ csrf_token() }}",
        },
		beforeSend: function() {
			$(this).text('Loading...');
		},
		success: function(response) {
			if (response.code == 200) {
				toastr['success'](response.message);
				$("#dev_status_tboday").html(response.html);
				$("#dev_status_model").modal("show");
                //location.reload();
			} else {
				toastr['error'](response.message);
			}
		}
	}).fail(function(response) {
		toastr['error'](response.responseJSON.message);
	});
});

$(document).on("click", ".show-admin-status-history", function(e) {
	e.preventDefault();
	var id = $(this).data("id");
    var developer_status = $(this).val();
    var site_development_id = $(this).data("site_development_id");
    var category = $(this).data("category");

	$.ajax({
		url: "{{route('uicheck.admin.status.history')}}",
		type: 'POST',
		data: {
            id:id,
            developer_status: developer_status,
            site_development_id: site_development_id,
            category:category,
            "_token": "{{ csrf_token() }}",
        },
		beforeSend: function() {
			$(this).text('Loading...');
		},
		success: function(response) {
			if (response.code == 200) {
				toastr['success'](response.message);
				$("#admin_status_tboday").html(response.html);
				$("#admin_status_model").modal("show");
                //location.reload();
			} else {
				toastr['error'](response.message);
			}
		}
	}).fail(function(response) {
		toastr['error'](response.responseJSON.message);
	});
});

$(document).on("click", ".show-issue-history", function(e) {
	e.preventDefault();
	var id = $(this).data("id");
    var developer_status = $(this).val();
    var site_development_id = $(this).data("site_development_id");
    var category = $(this).data("category");

	$.ajax({
		url: "{{route('uicheck.get.issue.history')}}",
		type: 'POST',
		data: {
            id:id,
            developer_status: developer_status,
            site_development_id: site_development_id,
            category:category,
            "_token": "{{ csrf_token() }}",
        },
		beforeSend: function() {
			$(this).text('Loading...');
		},
		success: function(response) {
			if (response.code == 200) {
				toastr['success'](response.message);
				$("#issue_tboday").html(response.html);
				$("#issue_model").modal("show");
                //location.reload();
			} else {
				toastr['error'](response.message);
			}
		}
	}).fail(function(response) {
		toastr['error'](response.responseJSON.message);
	});
});


$(document).on("click", ".show-message-history", function(e) {
	e.preventDefault();
	var id = $(this).data("id");
    var developer_status = $(this).val();
    var site_development_id = $(this).data("site_development_id");
    var category = $(this).data("category");

	$.ajax({
		url: "{{route('uicheck.get.message.history')}}",
		type: 'POST',
		data: {
            id:id,
            developer_status: developer_status,
            site_development_id: site_development_id,
            category:category,
            "_token": "{{ csrf_token() }}",
        },
		beforeSend: function() {
			$(this).text('Loading...');
		},
		success: function(response) {
			if (response.code == 200) {
				toastr['success'](response.message);
				$("#message_tboday").html(response.html);
				$("#message_model").modal("show");
				
                //location.reload();
			} else {
				toastr['error'](response.message);
			}
		}
	}).fail(function(response) {
		toastr['error'](response.responseJSON.message);
	});
});

$(document).on('click', '.expand-row-msg', function () {
      var name = $(this).data('name');
      var id = $(this).data('id');
      var full = '.expand-row-msg .show-short-'+name+'-'+id;
      var mini ='.expand-row-msg .show-full-'+name+'-'+id;
      $(full).toggleClass('hidden');
      $(mini).toggleClass('hidden');
    });

</script>

@endsection