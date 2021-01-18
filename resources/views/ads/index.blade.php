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
	    	<div class="col col-md-9">
		    	<div class="row">
	    			<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-add-action" data-toggle="modal" data-target="#campaningmodal">
		  				Create Campaign
		  			</button>
		  			<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-add-action">
		  				Create Ads Group
		  			</button>
		  			<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-add-action">
		  				Create Ads
		  			</button>
				 </div>
		    </div>
		    <div class="col">
		    	<div class="h" style="margin-bottom:10px;">
		    		<form class="form-inline message-search-handler" method="post">
					  <div class="row">
				  		<div class="col">
				  			<div class="form-group">
							    <label for="keyword">Keyword:</label>
							    <?php echo Form::text("keyword",request("keyword"),["class"=> "form-control","placeholder" => "Enter keyword"]) ?>
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
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div class="common-modal modal" role="dialog">
  	<div class="modal-dialog" role="document">
  	</div>	
</div>
@section('models')
<div class="modal fade" id="campaningmodal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
             <form method="POST" action="#" id="create-ad-account-form" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Create Campaign</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body create-campaning"  style="display: none;">
	                <div class="form-group row">
	                    <label for="status" class="col-sm-2 col-form-label">Select the goal</label>
	                    <div class="col-sm-10">
	                        <select class="browser-default custom-select" id="goal" name="goal" style="height: auto">
	                            <option value="" selected>-----Select goal-----</option>
	                            <option value="Sales" >Sales</option>
	                            <option value="Leads">Leads</option>
	                            <option value="Web traffic">Web traffic</option>
	                            <option value="Product and brand consideration">Product and brand consideration</option>
	                            <option value="Brand awareness and reach">Brand awareness and reach</option>
	                            <option value="App promotion">App promotion</option>
	                            <option value="Local store visits and promotions">Local store visits and promotions</option>
	                            <option value="Create a campaign without a goal's guidance">Create a campaign without a goal's guidance</option>
	                        </select>
	                    </div>
	                </div>
                </div>
                <div class="modal-body create-campaning-phase-2">
                	<div class="row">
                		<div class="col-md-12">
                			<div class="form-group">
							    <label class="col-md-12 control-label" for="rolename">Languages</label>
							    <div class="col-md-12">
							        <select id="dates-field2" class="multiselect-ui form-control" multiple="multiple" >
		                				<option value="English">English</option>
		                				<option value="Hindi">Hindi</option>
							        </select>
							    </div>
							</div>
                		</div>
                	</div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="continue-phase-1" class="btn btn-primary">Continue</button>
                    <button type="submit" id="create-camp-btn" style="display: none;" class="btn btn-primary">Create</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@include("ads.templates.list-template")
@include("ads.templates.create-website-template")
<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script src="/js/jquery-ui.js"></script>
<script type="text/javascript" src="/js/common-helper.js"></script>
<script type="text/javascript" src="/js/ads.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.16/js/bootstrap-multiselect.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.16/css/bootstrap-multiselect.min.css" />
<script type="text/javascript">
	page.init({
		bodyView : $("#common-page-layout"),
		baseUrl : "<?php echo url("/"); ?>"
	});
	$(document).ready(function(){
		$('#dates-field2').multiselect({
	        includeSelectAllOption: true,
	        selectAllText: 'All Languages',
	    });
	});
</script>

@endsection

