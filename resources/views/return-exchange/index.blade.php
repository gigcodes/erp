@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'List | Return Exchange')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
@endsection

@section('content')

<div class="row" id="return-exchange-page">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Return Exchange <span id="total-counter"></span></h2>
    </div>
    <br>
    <div class="col-lg-12 margin-tb">
    	<div class="row" style="margin-bottom:20px;">
	    	<div class="col">
		    	<div class="h" style="margin-bottom:10px;">
		    		<form class="form-inline return-exchange-handler" method="post">
					  <div class="row">
				  		<div class="col">
				  			<div class="form-group">
							    <label for="from">Customer Name:</label>
							    <?php echo Form::text("customer_name",request("customer_name"),["class"=> "form-control","placeholder" => "Enter Customer Name"]) ?>
						  	</div>
						    <div class="form-group">
							    <label for="from">Order number:</label>
							    <?php echo Form::text("order_number",request("order_number"),["class"=> "form-control","placeholder" => "Enter Order Number"]) ?>
						  	</div>
						  	<div class="form-group">
							    <label for="from">Product:</label>
							    <?php echo Form::text("product",request("product"),["class"=> "form-control","placeholder" => "Enter product sku/id/name"]) ?>
						  	</div>
				  			<div class="form-group">
							    <label for="action">Status:</label>
							    <?php echo Form::select("status",\App\ReturnExchange::STATUS,request("limti"),[
							    	"class" => "form-control select2",
							    	"placeholder" => "-- None --"
							    ]) ?>
						  	</div>
						  	<div class="form-group">
							    <label for="action">Type:</label>
							    <?php echo Form::select("type",["refund" => "Refund", "exchange" => "Exchange"],request("limti"),[
							    	"class" => "form-control select2",
							    	"placeholder" => "-- None --"
							    ]) ?>
						  	</div>
						  	<div class="form-group">
							    <label for="action">Number of records:</label>
							    <?php echo Form::select("limit",[10 => "10", 20 => "20", 30 => "30" , 50 => "50", 100 => "100" , 500 => "500" , 1000 => "1000"],request("limti"),["class" => "form-control select2","placeholder" => "Page limit"]) ?>
						  	</div>
						  	<div class="form-group">
						  		<label for="button">&nbsp;</label>
						  		<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-search-action">
						  			<img src="/images/search.png" style="cursor: default;">
						  		</button>
						  	</div>		
				  		</div>
					  </div>	
					</form>	
		    	</div>
		    </div>
	    </div>	
		<div class="col-md-12 margin-tb infinite-scroll" id="page-view-result">

		</div>
	</div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div class="common-modal modal" role="dialog">
  	<div class="modal-dialog modal-lg" role="document">
  	</div>	
</div>

@include("return-exchange.templates.list-template")
@endsection

@section('scripts')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
	<script type="text/javascript" src="/js/jsrender.min.js"></script>
	<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
	<script src="/js/jquery-ui.js"></script>
	<script type="text/javascript" src="/js/common-helper.js"></script>
	<script type="text/javascript" src="/js/return-exchange.js"></script>
	<script type="text/javascript">
		msQueue.init({
			bodyView : $("#return-exchange-page"),
			baseUrl : "<?php echo url("/"); ?>"
		});
	</script>
@endsection


