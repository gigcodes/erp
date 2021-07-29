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
	    	<div class="col col-md-6">
		    	<div class="row">

	    			<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-add-action" data-toggle="modal" data-target="#colorCreateModal">
		  				<img src="/images/add.png" style="cursor: default;">
		  			</button>
				 </div>
		    </div>
		    <div class="col">
		    	<div class="h" style="margin-bottom:10px;">
					<div class="row">
		    			<form class="form-inline message-search-handler" method="get">
					  		<div class="col">
					  			<div class="form-group">
								    <label for="keyword">Website Name:</label>
								    <?php echo Form::select("website_store",["" => "-- select website --"] + $websites,request('website_store'), ["class" => "form-control"]); ?>
							  	</div>
					  			<div class="form-group">
								    <label for="keyword">Website Store:</label>
								    <?php echo Form::select("website_store_id",["" => "-- select website --"] + $websiteStores,request('website_store_id'), ["class" => "form-control"]); ?>
							  	</div>
					  			<div class="form-group">
								    <label for="keyword">Keyword:</label>
								    <?php echo Form::text("keyword",request("keyword"),["class"=> "form-control","placeholder" => "Enter keyword"]) ?>
							  	</div>
							  	<div class="form-group">
							  		<label for="button">&nbsp;</label>
							  		<button type="submit" style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-search-action">
							  			<img src="/images/search.png" style="cursor: default;">
							  		</button>
							  	</div>
					  		</div>
				  		</form>
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


@include("storewebsite::website-store-view.templates.list-template")
@include("storewebsite::website-store-view.templates.create-website-template")
@include("storewebsite::website-store-view.templates.create-group-template")

<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script src="/js/jquery-ui.js"></script>
<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/website-store-view.js') }}"></script>

<script type="text/javascript">
	page.init({
		bodyView : $("#common-page-layout"),
		baseUrl : "<?php echo url("/"); ?>"
	});
	var agents = [];
	$.ajax({
		type: "GET",
		url: "/store-website/website-store-views/agents", 
	}).done(function (response) {
		agents = response;
	}); 
	var groups = [];
	$.ajax({
		type: "GET",
		url: "/store-website/website-store-views/groups", 
	}).done(function (response) {
		groups = response;
	}); 
 
	$(document).on("click",".btn-create-group",function(e) {
 
		let code = $(this).closest('tr').children('.code_div').text() == '1' ? '1' : $(this).closest('tr').children('.code_div').text().split('-')[1];
		$('.modal-body #name').val($(this).closest('tr').children('.name_div').text() + '_' + code);
		let html_groups = `<div class="form-group col-md-12 group"><select name="group" class="form-control select-2"><option value="">Choose Theme</option>`;
		for(let i=0; i<groups.responseData.length; i++){
			html_groups += `<option value="${groups.responseData[i].id}">${groups.responseData[i].name}</option>`;
		}
		html_groups += '</select></div>'; 

		$('.modal-body .name_div').after(html_groups);
		let options = `<select name="agents[]" class="form-control select-2"> `;
			for(let i=0; i<agents.responseData.length; i++){
				options += `<option value="${agents.responseData[i].id}">${agents.responseData[i].id}</option>`;
			}
			options += '</select>';
			html_agents = `
				<div class="abc">
					<div class="form-group col-md-7 agents">
						${options}
					</div> 
					<div class="form-group col-md-4 priorities">
						<select name="priorites[]" class="form-control select-2"> 
							<option value="first">first</option> 
							<option value="normal" selected>normal</option> 
							<option value="last">last</option> 
							<option value="supervisor">supervisor</option> 
						</select>
					</div>
					<div class="form-group col-md-1">
						<button type="button" title="Remove" data-id="" class="btn btn-remove-priority">
							<i class="fa fa-close" aria-hidden="true"></i>
						</button>
					</div>
				</div>
			`;
			$('.modal-body').append(html_agents);
	});
	$(document).on('click', '.btn-remove-priority', function(){
		$(this).closest('.abc').remove();
	}); 
	$(document).on('click', '.btn-add-priority', function(){
		$.ajax({
			type: "GET",
			url: "/store-website/website-store-views/agents", 
		}).done(function (response) {
			let options = `<select name="agents[]" class="form-control select-2"> `;
			for(let i=0; i<response.responseData.length; i++){
				options += `<option value="${response.responseData[i].id}">${response.responseData[i].id}</option>`;
			}
			options += '</select>';
			var html = `
				<div class="abc">
					<div class="form-group col-md-7 agents">
						${options}
					</div> 
					<div class="form-group col-md-4 priorities">
						<select name="priorites[]" class="form-control select-2"> 
							<option value="first">first</option> 
							<option value="normal" selected>normal</option> 
							<option value="last">last</option> 
							<option value="supervisor">supervisor</option> 
						</select>
					</div>
					<div class="form-group col-md-1">
						<button type="button" title="Remove" data-id="" class="btn btn-remove-priority">
							<i class="fa fa-close" aria-hidden="true"></i>
						</button>
					</div>
				</div>
			`;
			$('.modal-body').append(html);
		}).fail(function (response) {
			
		});
	});
</script>
@endsection