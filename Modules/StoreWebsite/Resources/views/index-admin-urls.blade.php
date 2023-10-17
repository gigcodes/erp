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
		<h2 class="page-heading">{{$title}}</h2>
	</div>
	<br>

	<div class="col col-md-12">
		<div class="h pl-5 pr-5" style="margin-bottom:10px;">
			<form action="{{ url('/store-website/admin-urls/bulk-generate-admin-url') }}" class="form-inline message-search-handler" method="post" style="width: 100%; display: inline-block;">
				<div class="row">
					<div class="col col-md-12">
						<div class="col col-lg-10 pl-0">
							<div class="form-group" style="display: contents;">
								<b>Select Websites :</b></br>
								<select name="storewebsiteids[]" id="storewebsiteids" class="form-control globalSelect2" placeholder="Select a website" multiple="true">
									@foreach($storeWebsites as $key => $storeWebsite)
										<option value="{{ $storeWebsite->id }}">{{ $storeWebsite->title }}</option>
									@endforeach
								</select>
                            </div>
						</div>
						<div class="col col-lg-2 p-0">
							<div class="form-group">
								<button class="btn btn-primary" type="submit" style="margin-top: 18px;">
									Generate Admin Urls
								</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<!-- <div class="col-lg-12 margin-tb">
		<div class="col-lg-12 pl-5 pr-5">
			<div style="display: flex !important; float: right !important;">
				<div style="width: 150px;"> 
					<a href="#" class="btn btn-xs btn-secondary generate-admin-urls">Generate Admin Urls</a>
				</div>
			</div>
		</div>
	</div> -->

	<div class="col-lg-12 pl-5 pr-5">
		<form action="/store-website/generate-api-token" method="post">
			<?php echo csrf_field(); ?>
			
			<div class="col-md-12">
				<div class="table-responsive mt-3">
					<table class="table table-bordered overlay admin-password-table" id="tblAdminPassword">
						<thead>
						<tr>
							<!-- <th>Select</th> -->
							<th>Id</th>
							<th width="30%">Website</th>
							<th width="30%">Admin Url</th>
							<!-- <th width="30%">Store Directory</th>	
							<th width="30%">Server Ip</th> -->
							<th width="30%">Request Data</th>
							<th width="30%">Response Data</th>
							<th width="30%">Created By</th>
							<th width="30%">Created Date</th>
							<th>Action</th>											
						</tr>
						</thead>
						<tbody>
							@include('storewebsite::admin-urlslist')
						</tbody>
					</table>
				</div>
			</div>
		</form>
	</div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div class="common-modal modal" role="dialog">
	<div class="modal-dialog" role="document" style="width: 1000px; max-width: 1000px;">
	</div>
</div>

@include('admin-url-history')

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

	$(document).on('click','.admin-url-history',function(){
        store_website_id = $(this).data('id');
		$.ajax({
            method: "GET",
            url: `{{ route('store-website.url.histories', [""]) }}/` + store_website_id,
            dataType: "json",
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
						html += "<tr>";
						html += "<td>" + (k + 1) + "</td>";
						html += "<td>" + v.storewebsite.title + "</td>";
						html += "<td>" + v.admin_url + "</td>";
					/*	html += "<td>" + v.store_dir + "</td>";
						html += "<td>" + v.server_ip_address + "</td>";*/
						html += "<td>" + v.request_data + "</td>";
						html += "<td>" + v.response_data + "</td>";
						html += "<td class='expand-row' style='word-break: break-all'>";
						html += "<span class='td-mini-container'>" + (v.user !== undefined ? (v.user.name.length > 15 ? v.user.name.substr(0, 15) + '...' : v.user.name) : ' - ' ) + "</span>";
						html += "<span class='td-full-container hidden'>" + (v.user !== undefined ? v.user.name : ' - ' ) + "</span>";
						html += "</td>";
						html += "<td>" + v.created_at + "</td>";
						html += "</tr>";
                    });
                    $("#admin-urls-histories-list").find(".api-token-list-view").html(html);
                    $("#admin-urls-histories-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
	});

	var selectedStoreWebsiteAdminUrls = [];
	$(document).on('click', '.selectedStoreWebsiteAdminUrls', function () {
		var checked = $(this).prop('checked');
		var id = $(this).val();
		if (checked) {
			selectedStoreWebsiteAdminUrls.push(id);
		} else {
			var index = selectedStoreWebsiteAdminUrls.indexOf(id);
			selectedStoreWebsiteAdminUrls.splice(index, 1);
		}
	});

	$(document).on("click",".generate-admin-urls",function(e){
		e.preventDefault();
		if(selectedStoreWebsiteAdminUrls.length < 1) {
			toastr['error']("Select some rows first");
			return;
		}
		var x = window.confirm("Are you sure, you want to generate Admin Urls ?");
		if(!x) {
			return;
		}

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/store-website/admin-urls/bulk-generate-admin-url',
			type: "POST",
			data: {ids : selectedStoreWebsiteAdminUrls}
		}).done(function(response) {
			toastr['success'](response.message);
			window.location.reload();
		}).fail(function(errObj) {
		});
	});
</script>

@endsection