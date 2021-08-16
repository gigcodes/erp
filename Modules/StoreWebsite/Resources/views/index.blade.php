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
	    			<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-add-action">
		  				<img src="/images/add.png" style="cursor: default;">
		  			</button>
                    <button style="display: inline-block;" class="btn btn-sm ml-5 btn-secondary open-store-magento-user-lising">
                            User Listing
                        </button> &nbsp; 
                    <button class="btn btn-secondary" data-toggle="modal" data-target="#store-generate-pem-file"> Store Generate Reindex</button>

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
						  		<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-secondary btn-search-action">
						  			<img src="/images/search.png" style="cursor: default;">
						  		</button>
						  	</div>		
				  		</div>
					  </div>	
					</form>	
		    	</div>
		    </div>
	    </div>	
		<div class="margin-tb" id="page-view-result">

		</div>
	</div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div class="common-modal modal" role="dialog">
  	<div class="modal-dialog" role="document" style="width: 1000px; max-width: 1000px;">
  	</div>	
</div>

@include("storewebsite::templates.list-template")
@include("storewebsite::templates.create-website-template")

<div id="userPasswordHistory" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Password History</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
      <div class="modal-body">
        <div class="table-responsive mt-3">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>User Name</th>
                <th>Old Password</th>
                <th>New Password</th>
              </tr>
            </thead>
            <tbody>

            </tbody>
          </table>
        </div>
      </div>    
			</div>
		</div>
	</div>
</div>

    <div class="modal fade" id="store-generate-pem-file" role="dialog">
		<div class="modal-dialog">
		 <div class="modal-content">
			<div class="modal-header">
			  <h5 class="modal-title">Store Generate Reindex</h5>
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12">
						<form action="/store-website/generate-reindex" method="post">
							<?php echo csrf_field(); ?>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="meta_title">Server List</label>
										<select class="form-control select2" name="for_server">
											<option value="Erp-Server">Erp-Server</option>
											<option value="s01">Scrap-Server-s01</option>
											<option value="s02">Scrap-Server-s02</option>
											<option value="s03">Scrap-Server-s03</option>
											<option value="s04">Scrap-Server-s04</option>
											<option value="s05">Scrap-Server-s05</option>
											<option value="s06">Scrap-Server-s06</option>
											<option value="s07">Scrap-Server-s07</option>
											<option value="s08">Scrap-Server-s08</option>
											<option value="s09">Scrap-Server-s09</option>
											<option value="s10">Scrap-Server-s10</option>
											<option value="s11">Scrap-Server-s11</option>
											<option value="s12">Scrap-Server-s12</option>
											<option value="s13">Scrap-Server-s13</option>
											<option value="s14">Scrap-Server-s14</option>
											<option value="s15">Scrap-Server-s15</option>
											<option value="Cropper-Server">Cropper-Server</option>
											<option value="BRANDS">BRANDS</option>
											<option value="AVOIRCHIC">AVOIRCHIC</option>
											<option value="OLABELS">OLABELS</option>
											<option value="SOLOLUXURY">SOLOLUXURY</option>
											<option value="SUVANDNAT">SUVANDNAT</option>
											<option value="THEFITEDIT">THEFITEDIT</option>
											<option value="THESHADESSHOP">THESHADESSHOP</option>
											<option value="UPEAU">UPEAU</option>
											<option value="VERALUSSO">VERALUSSO</option>
										</select>
									 </div>
								</div> 
								<div class="col-md-12">
									<div class="form-group">
										<button type="submit" class="btn btn-secondary submit-generete-file-btn">Generate</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			</div>
		</div>
	</div>
<script type="text/javascript" src="{{ asset('/js/jsrender.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('/js/jquery-ui.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/store-website.js') }}"></script>

<script type="text/javascript">
	page.init({
		bodyView : $("#common-page-layout"),
		baseUrl : "<?php echo url("/"); ?>"
	});
</script>

@endsection

