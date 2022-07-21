@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Ui Check')

@section('styles')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
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
						<select name="store_webs" class="form-control select2">
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
						<select name="categories" class="form-control select2">
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
						<button type="submit" class="btn btn-secondary">Search</button>
						<a href="/uicheck" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@if (Session::has('message'))
	{{ Session::get('message') }}
@endif
<br />
<div class="row mt-2">
	<div class="col-md-12 margin-tb infinite-scroll">
		<div class="row">
			<div class="table-responsive">
			<table class="table table-bordered" id="documents-table">
				<thead>
					<tr>
						<th><input type="checkbox" id="checkAll" title="click here to select all" /></th>
						<th width="10%">Categories</th>
                        <th>Website</th>
                        <th>Issue</th>
                        <th>Communication</th>
                        <th>Developer Status</th>
                        <th>Admin Status</th>
					</tr>
				</thead>
				<tbody class="infinite-scroll-pending-inner">
					@include("uicheck.data")
				</tbody>
			</table>
			</div>
		</div>
	</div>
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

@endsection


@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
<script type="text/javascript">

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
				//$("#create-quick-task").modal("hide");
                location.reload();
			} else {
				toastr['error'](response.message);
			}
		}
	}).fail(function(response) {
		toastr['error'](response.responseJSON.message);
	});
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
                location.reload();
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
                location.reload();
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