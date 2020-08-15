@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
@section('link-css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
<style type="text/css">
	.preview-category input.form-control {
	  width: auto;
	}
	.daterangepicker .ranges li.active {
		background-color : #08c !important;
	}
</style>
@endsection

<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
    </div>
    <br>
    @if(session()->has('success'))
	    <div class="col-lg-12 alert alert-success">
	        {{ session()->get('success') }}
	    </div>
	@endif
	@if(session()->has('error'))
	    <div class="col-lg-12 alert alert-danger">
	        {{ session()->get('error') }}
	    </div>
	@endif
    <div class="col-lg-12 margin-tb">
    	<div class="row">
	    	<div class="col col-md-3">
		    	<div class="row">
	    			<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-add-action">
		  				<img src="/images/add.png" style="cursor: default;">
		  			</button>
		  			<a href="/attachImages/landing-page">
			  			<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image">
			  				<img src="/images/attach.png" style="cursor: default;">
			  			</button>
			  		</a>
					<button id="update-time-btn" style="color:black;display: none;width: 10%" class="btn btn-sm" data-toggle="modal" data-target="#start-time">
						Update Time
					</button>
				 </div>
		    </div>
		    <div class="col">
		    	<div class="h">
		    		<form class="form-inline message-search-handler mb-2 fr" method="post" style="float: right;">
					  <div class="row">
				  			<div class="form-group" style="margin-left: 2px">
							    <label for="keyword">Keyword:</label>
							    <?php echo Form::text("keyword",request("keyword"),["class"=> "form-control","placeholder" => "Enter keyword"]) ?>
						  	</div>
						  	<div class="form-group" style="margin-left: 2px">
							    <label for="stock_status">Stock Status:</label>
							    <?php echo Form::select("stock_status",[0 => "Sold out", 1 => "Stock Status"],request("stock_status"),["class"=> "form-control","placeholder" => "Stock status"]) ?>
						  	</div>
						  	<div class="form-group" style="margin-left: 2px">
							    <label for="status">Status:</label>
							    <?php echo Form::select("status",[0 => "De-Active", 1 => "Active"],request("status"),["class"=> "form-control","placeholder" => "Status"]) ?>
						  	</div>
						  	<div class="form-group" style="margin-left: 2px">
							    <label for="status">Product Status:</label>
							    <?php echo Form::select("product_status",[9 => "Final Approval", 4 => "Auto Crop"],request("product_status"),["class"=> "form-control","placeholder" => "Product Status"]) ?>
						  	</div>
						  	<div class="form-group">
						  		<label for="button">&nbsp;</label>
						  		<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-search-action">
						  			<img src="/images/search.png" style="cursor: default;">
						  		</button>
						  	</div>
					  </div>	
					</form>	
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
@include("landing-page.templates.list-template")
@include("landing-page.templates.create-website-template")
@include("landing-page.templates.update-time")
<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script src="/js/jquery-ui.js"></script>
<script type="text/javascript" src="/js/common-helper.js"></script>
<script type="text/javascript" src="/js/landing-page.js"></script>

<script type="text/javascript">
	page.init({
		bodyView : $("#common-page-layout"),
		baseUrl : "<?php echo url("/"); ?>"
	});
</script>

@endsection

