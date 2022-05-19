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
        <h2 class="page-heading">{{ $title }} (<span id="count">{{ $brands->count() }}</span>)</h2>
    </div>
    <br>
    @if(session()->has('success'))
	    <div class="col-lg-12 margin-tb">
		    <div class="alert alert-success">
		        {{ session()->get('success') }}
		    </div>
		</div>    
	@endif
    <div class="col-lg-12 margin-tb">
    	<div class="row" style="margin-bottom: 10px;">
	    	<div class="col col-md-9">
		    	<div class="row">
	    			<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-add-action">
		  				<img src="/images/add.png" style="cursor: default;">
		  			</button>
		  			<form class="form-inline message-search-handler" action="?" method="get">
		  				<input type="hidden" name="push" value="1">
				  		<div class="form-group">
						    <label for="keyword">Store Wesbite:</label>
						    <?php echo Form::select("store_website_id",\App\StoreWebsite::pluck('title','id')->toArray(),request("store_website_id"),["class"=> "form-control select2","placeholder" => "Select Website"]) ?>
					  	</div>
					  	&nbsp;
						<div class="form-group">
					  		<label for="button">&nbsp;</label>
					  		<button type="submit" class="btn btn-secondary">
					  			Push Brand
					  		</button>
					  	</div>		
			  		</form>
				 </div>
		    </div>
		    <div class="col reconsile-brand-form">
		    	<div class="h" style="margin-bottom:10px;">
					<div class="row">
						<div class="form-group">
			  				<?php echo Form::select("store_website_id",\App\StoreWebsite::pluck('title','id')->toArray(),request("store_website_id"),["class"=> "form-control select2 store-website-id","placeholder" => "Select Website"]) ?>
			  			</div>
					</div>
		    	</div>
		    </div>
            <div class="col">
                <div class="h" style="margin-bottom:10px;">
                    <div class="row">
                        <div class="form-group">
                            <button class="btn btn-secondary btn-reconsile-brand">Reconsile</button>
                        </div>
                    </div>
                </div>
            </div>
	    </div>

	    <div class="row mb-3 ml-3">
		    <form class="form-inline message-search-handler handle-search" method="get">
		  		<div class="col">
		  			<div class="form-group">
					    <?php echo Form::text("keyword",request("keyword"),["class"=> "form-control","placeholder" => "Enter keyword"]) ?>
				  	</div>
					
					<div class="form-group ml-2">
						<label for="no-inventory">No Inventory</label>
						<input type="checkbox" name="no-inventory" value="1" {{ request()->has('no-inventory') ? 'checked' : '' }} />
					</div>
	
					
					<div class="form-group ml-2">
						<?php echo Form::select("category_id",$categories,request("category_id"),["class"=> "form-control select2","placeholder" => "Select Category"]) ?>
					</div>

					<div class="form-group ml-2">
						<?php echo Form::select("brd_store_website_id",$storeWebsite,request("brd_store_website_id"),["class"=> "form-control select2","placeholder" => "Select Store Website"]) ?>
					</div>

					<div class="form-group ml-2">
						<label for="no_brand">no Available brand</label>
						<input type="checkbox" name="no_brand" value="1" {{ request()->has('no_brand') ? 'checked' : '' }} />
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

		<div class="col-md-12 margin-tb" id="page-view-result">
			<div class="row">
				<table class="table table-bordered">
				<thead>
				      <tr>
				      	<th width="3%">Id</th>
				        <th width="10%">Brand</th>
				        <th width="5%">Min Price</th>
				        <th width="5%">Max Price</th>
				        <?php foreach($storeWebsite as $k => $title) { ?>
							<?php 
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
				        	<th data-id="{{$k}}" width="4%">
								<?php echo $title; ?>
								<br>
				        		<a class="brand-history text-dark"  data-id="{{$k}}" href="javascript:;" ><i class="fa fa-info-circle" aria-hidden="true"></i></a>
				        		<a class="missing-brand-history text-dark" data-id="{{$k}}" href="javascript:;" ><i class="fa fa-close" aria-hidden="true"></i></a>
				        	</th>
				        <?php } ?>	
				      </tr>
				    </thead>
				    <thead>
				      <tr>
				      	<th colspan="4"></th>
				        <?php foreach($storeWebsite as $k => $title) { ?>
						<th data-id="{{$k}}" width="4%">
							<?php if(isset($apppliedResultCount[$k])){ ?> 
							{{count($apppliedResultCount[$k])}}
							<?php } ?>
						</th>
				        <?php } ?>	
				      </tr>
				    </thead>
				    <tbody id="brand_data">
				    	@include("storewebsite::brand.partials.brand_data")
				    </tbody>
				</table>
				 <img class="infinite-scroll-products-loader center-block" src="/images/loading.gif" alt="Loading..." style="display: none" />
			</div>
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


<div id="HistoryModal" class="modal fade" role="dialog">
  	<div class="modal-dialog">
	    <!-- Modal content-->
	    <div class="modal-content">
	        <div class="modal-header">
	        	<h4 class="modal-title">History</h4>
	          	<button type="button" class="close" data-dismiss="modal">&times;</button>
	        </div>
	        <div class="modal-body"></div>
    	</div>
	</div>
</div>

<div id="brand-live-data" class="modal fade" role="dialog">
  	<div class="modal-dialog">
	    <!-- Modal content-->
	    <div class="modal-content">
	        <div class="modal-header">
	        	<h4 class="modal-title">Brand Live<span class="brand-live-data-count"></span></h4>
	          	<button type="button" class="close" data-dismiss="modal">&times;</button>
	        </div>
	        <div class="modal-body"></div>
    	</div>
	</div>
</div>

<div id="missing-live-data" class="modal fade" role="dialog">
  	<div class="modal-dialog">
	    <!-- Modal content-->
	    
	</div>
</div>


<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script src="/js/jquery-ui.js"></script>
<script type="text/javascript" src="/js/common-helper.js"></script>
<script type="text/javascript" src="/js/store-website-brand.js"></script>

<script type="text/javascript">
	page.init({
		bodyView : $("#common-page-layout"),
		baseUrl : "<?php echo url("/"); ?>"
	});

	function refresh() {
		$.ajax({
            url: '{{ route('store-website.refresh-min-max-price')}}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
            },beforeSend: function() {
              
            },
            success: function(response) {
                $("#loading-image").hide();
                alert(response);
            }
        });
	}
	jQuery(document).ready(function(){
		jQuery(".log_history").on("click",function(){
			$("#loading-image").show();
			var _this = jQuery(this);
			$.ajax({
	            url: jQuery(_this).data('href'),
	            type: 'GET',
	            success: function(response) {
	                jQuery("#loading-image").hide();
	                jQuery("#HistoryModal .modal-body").html(response);
	                jQuery("#HistoryModal").modal("show");
	            },
	            error: function(response) {
	            	alert("Something went wrong, please try after sometime.");
	            }
        	});
		});
	});

	$(document).on("click",".brand-history",function(e) {
		e.preventDefault();
		var $this = $(this);
		$.ajax({
            url: "/store-website/brand/live-brands",
            type: 'GET',
            data : {
            	store_website_id: $this.data("id")
            },
            beforeSend : function() {
            	$("#loading-image").show();
            },
            success: function(response) {
            	$("#loading-image").hide();
                $("#brand-live-data").find(".modal-dialog").html(response);
                $("#brand-live-data").modal("show");
            },
            error: function(response) {
                $("#loading-image").hide();
            	alert(response.responseText);
            }
    	});
	});
	
	$(document).on("click",".push-brand",function(e) {
		e.preventDefault();
		var ele = $(this);
		var brand = ele.data("brand");
        var store = ele.data("sw");
		$.ajax({
            url: "/store-website/brand/push-to-store",
            type: 'POST',
            data : {
				_token: '{{ csrf_token() }}',
            	brand: brand,
            	store: store,
				active : ele.is(":checked")
            },
            beforeSend : function() {
            	$("#loading-image").show();
            },
            success: function(response) {
				console.log(ele.attr('id'));
            	$("#loading-image").hide();
				if($('#'+ele.attr('id')).prop("checked") == true){
					$('#'+ele.attr('id')).prop('checked', false);
				}
				else if($('#'+ele.attr('id')).prop("checked") == false){
					$('#'+ele.attr('id')).prop('checked', true);
				}
				//alert(response.message);
				toastr["success"](response.message);
            },
            error: function(response) {
                $("#loading-image").hide();
				toastr["error"](response.message);
            	//alert(response.message);
            }
    	});
	});

	$(document).on("click",".missing-brand-history",function(e) {
		e.preventDefault();
		var $this = $(this);
		$.ajax({
            url: "/store-website/brand/missing-brands",
            type: 'GET',
            data : {
            	store_website_id: $this.data("id")
            },
            beforeSend: function() {
              $("#loading-image").show();
            },
            success: function(response) {
            	$("#loading-image").hide();
                $("#missing-live-data").find(".modal-dialog").html(response);
                $("#missing-live-data").modal("show");
            },
            error: function(response) {
            	$("#loading-image").hide();
                alert(response.responseText);
            }
    	});
	});

    $(document).on("click",".btn-reconsile-brand",function(e) {
        e.preventDefault();
        if(confirm("Are you sure you want to do reconsile?")) {
            var $this = $(this);
            var swi = $(".reconsile-brand-form").find(".store-website-id").val();
            $.ajax({
                url: "/store-website/brand/reconsile-brand",
                type: 'POST',
                data : {
                    store_website_id: swi,
                    _token : "{{ csrf_token() }}"
                },
                beforeSend: function() {
                  $("#loading-image").show();
                },
                success: function(response) {
                    $("#loading-image").hide();
                    if(response.code == 200) {
                        toastr["success"](response.message);
                    }else{
                        toastr["error"](response.message);
                    }
                },
                error: function(response) {
                    $("#loading-image").hide();
                    toastr["error"]("Oops, something went wrong");
                }
            });
        }
    });
//START - Load More functionality
	var isLoading = false;
	var page = 1;
	$(document).ready(function () {

		$(window).scroll(function() {
			if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
				loadMore();
			}
		});

		function loadMore() {
			if (isLoading)
				return;
			isLoading = true;
			var $loader = $('.infinite-scroll-products-loader');
			page = page + 1;
			$.ajax({
				url: "/store-website/brand?page="+page,
				type: 'GET',
				data: $('.handle-search').serialize(),
				beforeSend: function() {
					$loader.show();
				},
				success: function (data) {
					$loader.hide();					
					$('#brand_data').append(data.tbody);
					isLoading = false;
					if(data.tbody == "") {
						isLoading = true;
					} else {
						var total_count = Number($('#count').html()) + Number(data.count); 
						console.log(total_count);
						$('#count').html(total_count);
					}
				},
				error: function () {
					$loader.hide();
					isLoading = false;
				}
			});
		}
	});
	//End load more functionality
</script>

@endsection

