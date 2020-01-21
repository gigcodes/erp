@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'List | Message Queue')

@section('content')

<div class="row" id="message-queue-page">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Message Queue</h2>
    </div>
    <br>
    <div class="col-lg-12 margin-tb">
    	<div class="row">
	    	<div class="col col-md-5">
		    	<div class="h" style="margin-bottom:10px;">
		    		<form class="form-inline message-queue-handler" method="post">
					  <div class="row">
				  		<div class="col">
				  			<div class="form-group">
							    <label for="action">Action:</label>
							    <select class="form-control" id="action-to-run">
							    	<option value="">-- Select --</option>
							    	<option value="change_to_broadcast">Change to Broadcast</option>
							    	<option value="delete_records">Delete Records</option>
							    	<option value="delete_all">Delete All Records</option>	
							    </select>
						  	</div>
						  	<div class="form-group">
						  		<label for="button">&nbsp;</label>
						  		<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-send-action">
						  			<img src="/images/filled-sent.png" style="cursor: default;">
						  		</button>
						  	</div>		
				  		</div>
					  </div>	
					</form>	
		    	</div>
		    </div>
		    <div class="col">
		    	<div class="h" style="margin-bottom:10px;">
		    		<form class="form-inline message-search-handler" method="post">
					  <div class="row">
				  		<div class="col">
				  			<div class="form-group">
							    <label for="from">From:</label>
							    <?php echo Form::text("from",request("from"),["class"=> "form-control","placeholder" => "Enter Number from"]) ?>
						  	</div>
						  	<div class="form-group">
							    <label for="to">To:</label>
							    <?php echo Form::text("to",request("to"),["class"=> "form-control","placeholder" => "Enter Number to"]) ?>
						  	</div>
						  	<div class="form-group">
							    <label for="action">Number of records:</label>
							    <?php echo Form::select("limit",[10 => "10", 20 => "20", 30 => "30" , 50 => "50"],request("limti"),["class" => "form-control","placeholder" => "Page limit"]) ?>
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
		<div class="col-md-12 margin-tb" id="page-view-result">

		</div>
	</div>
</div>

@include("messagequeue::templates.list-template")

<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script src="/js/jquery-ui.js"></script>
<script type="text/javascript" src="/js/common-helper.js"></script>
<script type="text/javascript" src="/js/message-queue.js"></script>

<script type="text/javascript">
	msQueue.init({
		bodyView : $("#message-queue-page"),
		baseUrl : "<?php echo url("/"); ?>"
	});
</script>

@endsection

