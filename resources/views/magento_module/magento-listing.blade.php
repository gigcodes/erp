@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', "Store Environment")

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
        <h2 class="page-heading">Store Environment({{count($magento_modules)}})</h2>
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
                        <th width="20%">Description Key</th>
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
							foreach($magento_modules as $envKey => $magento_module) {
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
								<?php foreach($storeWebsites as $storeWebsiteId => $storeWebsiteTitle) { ?>
                                    <td>
                                      <input type="checkbox" id="enable_{{$storeWebsiteId}}_{{$envKey}}" name="enable[{{$storeWebsiteId}}][]" value="{{$magento_module->module}}">
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

</script> 

@endsection

