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

	<div class="col-lg-12 pl-5 pr-5">
		<form action="/store-website/generate-api-token" method="post">
			<?php echo csrf_field(); ?>
			
			<div class="col-md-12">
				<div class="table-responsive mt-3">
					<table class="table table-bordered overlay admin-password-table" id="tblAdminPassword">
						<thead>
						<tr>
							<th>Id</th>
							<th width="30%">Website</th>
							<th width="30%">Admin Url</th>
							<th width="30%">Store Directory</th>	
							<th width="30%">Server Ip</th>
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
						html += "<td>" + v.created_at + "</td>";
						html += "<td>" + v.admin_url + "</td>";
						html += "<td>" + v.store_dir + "</td>";
						html += "<td>" + v.server_ip_address + "</td>";
						html += "<td class='expand-row' style='word-break: break-all'>";
						html += "<span class='td-mini-container'>" + (v.user !== undefined ? (v.user.name.length > 15 ? v.user.name.substr(0, 15) + '...' : v.user.name) : ' - ' ) + "</span>";
						html += "<span class='td-full-container hidden'>" + (v.user !== undefined ? v.user.name : ' - ' ) + "</span>";
						html += "</td>";
						html += "<td>" + v.created_at + "</td>";
						html += "</tr>";
                    });
                    $("#api-token-histories-list").find(".api-token-list-view").html(html);
                    $("#api-token-histories-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
	});
</script>

@endsection