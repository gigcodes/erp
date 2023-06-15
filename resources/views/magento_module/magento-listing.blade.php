@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', "Magento Modules")

@section('content')
<style type="text/css">
	.preview-category input.form-control {
	  width: auto;
	}
	.push-brand {
		height: 14px;
	}
	.icon-log-history {
		margin-top: -7px !important;
		display: flex;
		/*display: table-caption;*/
	}
	#page-view-result table tr th:last-child,
	#page-view-result table tr th:nth-last-child(2) {
		width: 50px !important;
		min-width: 50px !important;
		max-width: 50px !important;
	}
	
	/*Toggle Switch*/
	.mml_switch {
		position: relative;
		display: inline-block;
		width: 54px;
		height: 28px;
	}

	.mml_switch input { 
		opacity: 0;
		width: 0;
		height: 0;
	}

	.mml_switch .slider {
		position: absolute;
		cursor: pointer;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background-color: red;
		-webkit-transition: .4s;
		transition: .4s;
	}

	.mml_switch .slider:before {
		position: absolute;
		content: "";
		height: 20px;
		width: 20px;
		left: 4px;
		bottom: 4px;
		background-color: white;
		-webkit-transition: .4s;
		transition: .4s;
	}

	.mml_switch input:checked + .slider {
	background-color: green;
	}

	.mml_switch input:focus + .slider {
	box-shadow: 0 0 1px #2196F3;
	}

	.mml_switch input:checked + .slider:before {
	-webkit-transform: translateX(26px);
	-ms-transform: translateX(26px);
	transform: translateX(26px);
	}

	/* Rounded sliders */
	.mml_switch .slider.round {
	border-radius: 34px;
	}

	.mml_switch .slider.round:before {
	border-radius: 50%;
	}
</style>
<style>
	.loader-small {
		border: 2px solid #b9b7b7;
		border-radius: 50%;
		border-top: 4px dotted #4e4949;
		width: 21px;
		height: 21px;
	  	-webkit-animation: spin 2s linear infinite; /* Safari */
	  	animation: spin 2s linear infinite;
	  	float: left;
		margin: 8px;
		display: none;
	}
	
	/* Safari */
	@-webkit-keyframes spin {
	  0% { -webkit-transform: rotate(0deg); }
	  100% { -webkit-transform: rotate(360deg); }
	}
	
	@keyframes spin {
	  0% { transform: rotate(0deg); }
	  100% { transform: rotate(360deg); }
	}
</style>
<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Magento Modules ({{count($magento_modules)}})</h2>
    </div>
    <br>
    @if(session()->has('success'))
	    <div class="col-lg-12 margin-tb">
		    <div class="alert alert-success">
		        {{ session()->get('success') }}
		    </div>
		</div>    
	@endif
	<h4>Search Here</h4>
	<input id="search-input" type="text" placeholder="type to Search here...">
	<br>

    <div class="col-lg-12 margin-tb">
		<div class="col-md-12 margin-tb" id="page-view-result">
			<div class="row table-horizontal-scroll">
				<table class="table table-bordered" id="env-table">
					<thead>
				      <tr>
                        <th width="20%">Name</th>
                        <th width="20%">Description</th>
				        <?php foreach($storeWebsites as $storeWebsiteId => $storeWebsiteTitle) { ?>
							<?php 
							$title = $storeWebsiteTitle;
							$title= str_replace(' & ','&',$title);
							$title= str_replace(' - ','-',$title);
							$title= str_replace('&',' & ',$title);
							$title= str_replace('-',' - ',$title);
							$words = explode(' ', $title);
							$is_short_title=0;
							if (count($words) >= 2) {
								$title='';
								foreach($words as $word){
									$title.=strtoupper(substr($word, 0, 1));
								}
								$is_short_title=1;
							}
							?>
				        	<th data-id="{{$storeWebsiteId}}" width="10%">
								<?php echo $title; ?>
				        	</th>
				        <?php } ?>	
				      </tr>
				    </thead>
				    <tbody id="environment_data">
						<?php 
						if($magento_modules) {
							foreach($magento_modules as $key => $magento_module) {
						?>
						<tr>
							<td width="10%" class="expand-row">
								<span class="td-mini-container">
									{{ strlen($magento_module->module) > 15 ? substr($magento_module->module, 0, 15).'...' :  $magento_module->module }}
								</span>
								<span class="td-full-container hidden">
									{{$magento_module->module}}
								</span>
							</td>
								<td width="25%" class="expand-row">
									<span class="td-mini-container">
										{{ strlen( $magento_module->module_description) > 25 ? substr( $magento_module->module_description, 0, 25).'...' :  $magento_module->module_description }}
									</span>
									<span class="td-full-container hidden">
										{{ $magento_module->module_description}}
									</span>
								</td>
								<?php foreach($storeWebsites as $store_Website_id => $store_website_title) { ?>
									<td>
										<label class="mml_switch">
											<input type="checkbox" {{ $magento_module->status ? 'checked' : '' }} class="magento_module_toggle_switch" data-store_Website_id="{{$store_Website_id}}" data-magento_module_id="{{$magento_module->id}}" id="mm_status_{{$store_Website_id}}_{{$magento_module->id}}" name="mm_status_[{{$store_Website_id}}][{{$magento_module->id}}]" value="{{$magento_module->status}}">
											<span class="slider round"></span>
										</label><br>
										<button type="button" title="History" data-store_Website_id="{{$store_Website_id}}" data-magento_module_id="{{$magento_module->id}}" class="btn btn-history" style="padding: 0px 5px !important;">
											<i class="fa fa-eye" aria-hidden="true"></i>
										</button>
                                    </td>
                                  <?php } ?>
							<?php } ?>
						</tr>
						<?php } ?>
				    </tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div class="preview-history-modal modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="width: 100%;max-width: 95%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Magento Modules Logs</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Command</th>
								<th>Job Id</th>
								<th>Status</th>
								<th>Response</th>
                                <th>Updated By</th>
                                <th>Updated At</th>
                            </tr>
                        </thead>
                        <tbody id="preview-history-tbody">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script src="/js/jquery-ui.js"></script>
<script type="text/javascript" src="/js/common-helper.js"></script>
<script type="text/javascript" src="/js/store-website-brand.js"></script>
<script>
	
	$("#search-input").on("keyup", function() {
		var value = $(this).val().toLowerCase();
		$("#env-table tr").filter(function() {
		$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
		});

		var hasVisibleRows = $("#env-table tr:visible").length > 0;
		$("#env-table thead").toggle(hasVisibleRows);
	});

	$( document ).ready(function() {
		$(document).on('click', '.expand-row', function () {
			var selection = window.getSelection();
			if (selection.toString().length === 0) {
				$(this).find('.td-mini-container').toggleClass('hidden');
				$(this).find('.td-full-container').toggleClass('hidden');
			}
		});
		$(document).on("click",".magento_module_toggle_switch",function(event){
			//event.preventDefault();
			var var_return=false;
			var ele=$(this);
			var status=0;
			var temp_m="Are you sure you want to disable the Magento Module?"
			if ($(this).is(':checked')) {
				var status=1;
				var temp_m="Are you sure you want to enable the Magento Module?"
			}
			
			if(confirm(temp_m)){
				var store_website_id=$(this).attr("data-store_website_id");
				var magento_module_id=$(this).attr("data-magento_module_id");
				let formData = new FormData();

				formData.append('_token', "{{ csrf_token() }}");
				formData.append('store_website_id', store_website_id);
				formData.append('magento_module_id', magento_module_id);
				formData.append('status', status);

				$.ajax({
					url:"{{route('magentoModuleUpdateStatus')}}",
					method: 'post',
					data: formData,
					processData: false,
					contentType: false,
					//enctype: 'multipart/form-data',
					dataType: 'json',
					beforeSend: function () {
						$("#loading-image").show();
					}
				}).done(function (response) {
					$("#loading-image").hide();
					
					if (response.code == 200) {
						toastr['success'](response.message);
						location.reload();
					} else {
						toastr['error'](response.message);
						
						
					}
					
				}).fail(function () {
					console.log("error");
					$("#loading-image").hide();
					//event.preventDefault();
					
				});
			}
			return var_return;
			
		});
		$(document).on("click", ".btn-history", function (e) {
			e.preventDefault();
			let page = $(this).data("id");
			
			var store_website_id=$(this).attr("data-store_website_id");
			var magento_module_id=$(this).attr("data-magento_module_id");
			let formData = new FormData();

			formData.append('_token', "{{ csrf_token() }}");
			formData.append('store_website_id', store_website_id);
			formData.append('magento_module_id', magento_module_id);
			

			$.ajax({
				url: "/magento_modules/update-status/logs",
				method: 'post',
				data: formData,
				processData: false,
				contentType: false,
				//enctype: 'multipart/form-data',
				dataType: 'json',
				beforeSend: function () {
					$("#loading-image").show();
				}
			}).done(function (response) {
				$("#loading-image").hide();
				
				var html = '';
				if (response.code == 200) {
					$.each(response.data, function (k, v) {
						var user = v.user ? v.user.name : "";
						html +=
						`<tr>
									<td>` +
						v.id +
						`</td>
									<td>` +
						v.command +
						`</td>
									<td>` +
						v.job_id +
						`</td>
									<td>` +
						v.status +
						`</td>
									<td>` +
						v.response +
						`</td>
									<td>` +
						v.userName +
						`</td>
									<td>` +
						v.created_at +
						`</td>
								</tr>`;
					});
					$("#preview-history-tbody").html(html);
					$("#loading-image").hide();
					$(".preview-history-modal").modal("show");
				}
				
			}).fail(function () {
				console.log("error");
				$("#loading-image").hide();
				
			});
			
		});
	});

</script> 

@endsection

