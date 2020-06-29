@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Site Development')

@section('styles')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<style type="text/css">
	.preview-category input.form-control {
	  width: auto;
	}

	#loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
</style>
@endsection

@section('content')

<div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
</div>
<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Site Development <span class="count-text"></span></h2>
    </div>
    <br>
    <div class="col-lg-12 margin-tb">
    	<div class="row">
	    	<div class="col col-md-9">
		    	<div class="row">
	    			
				 </div> 		
		    </div>
		    <div class="col">
		    	<div class="h" style="margin-bottom:10px;">
		    		<form class="form-inline message-search-handler" onsubmit="event.preventDefault(); saveCategory();">
					  <div class="row">
				  		<div class="col">
				  			<div class="form-group">
							    <label for="keyword">Add Category:</label>
							    <?php echo Form::text("keyword",request("keyword"),["class"=> "form-control","placeholder" => "Enter Category","id" => "add-category"]) ?>
						  	</div>
						  	<div class="form-group">
						  		<label for="button">&nbsp;</label>
						  		<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-search-action">
						  			<img src="/images/send.png" style="cursor: default;" >
						  		</button>
						  	</div>		
				  		</div>
					  </div>	
					</form>	
					<form class="form-inline handle-search">
					  <div class="row">
				  		<div class="col">
				  			<div class="form-group">
							    <label for="keyword">Search keyword:</label>
							    <?php echo Form::text("k",request("k"),["class"=> "form-control","placeholder" => "Enter keyword","id" => "enter-keyword"]) ?>
							    <label for="status">Status:</label>
							    <?php echo Form::select("status",["" => "All","ignored" => "Ignored"],request("status"),["class"=> "form-control","id" => "enter-status"]) ?>
						  	</div>
						  	<div class="form-group">
						  		<label for="button">&nbsp;</label>
						  		<button style="display: inline-block;width: 10%" type="submit" class="btn btn-sm btn-image btn-search-keyword">
						  			<img src="/images/send.png" style="cursor: default;" >
						  		</button>
						  	</div>		
				  		</div>
					  </div>	
					</form>	
		    	</div>
		    </div>
	    </div>
	    <div class="row">
	    	<a href="{{ route('site-development-status.index') }}" target="__blank">
		    	<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image">
		  			+ Add Status
		  		</button>
		  	</a>
	    </div>
		<div class="col-md-12 margin-tb infinite-scroll">
			<div class="row">
				<table class="table table-bordered" id="documents-table">
					<thead>
						<tr>
						<th width="10%"></th>
						<th width="15%">Title</th>
						<th width="25%">Description</th>
						<th width="15%">Action</th>
						<th width="25%">Communication</th>
						<th width="5%">Created</th>
					</tr>
					</thead>
					<tbody>
					@include("storewebsite::site-development.partials.data")
					</tbody>
				</table>
				{{ $categories->appends(request()->capture()->except('page','pagination') + ['pagination' => true])->render() }}	
			</div>
		</div>
	</div>
</div>
<div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                    <input type="text" name="search_chat_pop"  class="form-control search_chat_pop" placeholder="Search Message" style="width: 200px;">
                </div>
                <div class="modal-body" style="background-color: #999999;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script type="text/javascript">

	$('.infinite-scroll').jscroll({
        autoTrigger: true,
        loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
        padding: 20,
        nextSelector: '.pagination li.active + li a',
        contentSelector: 'div.infinite-scroll',
        callback: function () {
            $('ul.pagination').first().remove();
        }
    });
	
	function saveCategory() {
		var text = $('#add-category').val()
		if(text === ''){
			alert('Please Enter Text');
		}else{
			$.ajax({
				url: '{{ route("site-development.category.save") }}',
				type: 'POST',
				dataType: 'json',
				data: {text: text , "_token": "{{ csrf_token() }}"},
				beforeSend: function () {
                    $("#loading-image").show();
                },
			})
			.done(function(data) {
				$('#add-category').val('')
				refreshPage()
				$("#loading-image").hide();
				console.log(data)
				console.log("success");
			})
			.fail(function(data) {
				$('#add-category').val('')
				console.log(data)
				console.log("error");
			});
			
		}
	}

	$(function(){
		$(document).on("focusout" , ".save-item" , function() {
			websiteId = $('#website_id').val()
			category = $(this).data("category")
			type = $(this).data("type")
			site = $(this).data("site")
			var text = $(this).val();
		    $.ajax({
				url: '{{ route("site-development.save") }}',
				type: 'POST',
				dataType: 'json',
				data: {websiteId: websiteId , "_token": "{{ csrf_token() }}" , category : category , type : type , text : text ,site : site},
				beforeSend: function () {
                    $("#loading-image").show();
                },
			})
			.done(function(data) {
				console.log(data)
				$("#loading-image").hide();
				console.log("success");
			})
			.fail(function(data) {
				console.log(data)
				$("#loading-image").hide();
				console.log("error");
			});
		});

		$(document).on("change" , ".save-item-select" , function() {
			websiteId = $('#website_id').val()
			category = $(this).data("category")
			type = $(this).data("type")
			site = $(this).data("site")
			var text = $(this).val();
		    $.ajax({
				url: '{{ route("site-development.save") }}',
				type: 'POST',
				dataType: 'json',
				data: {websiteId: websiteId , "_token": "{{ csrf_token() }}" , category : category , type : type , text : text , site : site},
			})
			.done(function(data) {
				console.log(data)
				console.log("success");
			})
			.fail(function(data) {
				console.log(data)
				console.log("error");
			});
		});
	});


	function editCategory(id){
		$('#editCategory'+id).modal('show');
	}

	function submitCategoryChange(id){
		category = $('#category-name'+id).val()
		categoryId = id
		$.ajax({
			url: '{{ route("site-development.category.edit") }}',
			type: 'POST',
			dataType: 'json',
			data: {category: category , "_token": "{{ csrf_token() }}" , categoryId : categoryId},
			beforeSend: function () {
                    $("#loading-image").show();
                },
		})
		.done(function(data) {
			console.log(data)
			refreshPage()
			$("#loading-image").hide();
			$('#editCategory'+id).modal('hide');
			console.log("success");
		})
		.fail(function(data) {
			console.log(data)
			refreshPage()
			console.log("error");
		});
	}


	function refreshPage(){
		$.ajax({
		    url: '{{ route("site-development.index" , $website->id)}}',
		    dataType: "json",
		    data: {},
		}).done(function (data) {
		    $("#documents-table tbody").empty().html(data.tbody);
		    if (data.links.length > 10) {
		        $('ul.pagination').replaceWith(data.links);
		    } else {
		        $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
		    }
		    
		}).fail(function (jqXHR, ajaxOptions, thrownError) {
		    alert('No response from server');
		});
	}

	
	$(document).on('click', '.send-message-site', function() {
		site = $(this).data("id")
		message = $('#message-'+site).val();
		userId = $('#user-'+site+' option:selected').val();
		if(userId == 'Select Developer'){
			alert('Please Select User');
		}else if(site){
			$.ajax({
				url: '/whatsapp/sendMessage/site_development',
				dataType: "json",
				type: 'POST',
				data: { 'site_development_id' : site , 'message' : message , 'user_id' : userId , "_token": "{{ csrf_token() }}" , 'status' : 2},
				beforeSend: function() {
					$('#message-'+site).attr('disabled', true);
               	}
			}).done(function (data) {
				$('#message-'+site).attr('disabled', false);
				$('#message-'+site).val('');
			}).fail(function (jqXHR, ajaxOptions, thrownError) {
			    alert('No response from server');
			});
		}else{
			alert('Site is not saved please enter value or select User');
		} 
    });

    $(document).on("change",".fa-ignore-category",function() {
		var $this = $(this);
    	var msg = "allow";

    	if($this.prop('checked')) {
    	   msg = "disallow";
    	}

		if(confirm("Are you sure want to "+msg+" this category?")) {
			var store_website_id = $this.data("site-id");
			var category = $this.data("category-id");
			$.ajax({
				url: '/site-development/disallow-category',
				dataType: "json",
				type: 'POST',
				data: { 'store_website_id' : store_website_id , 'category' : category, "_token": "{{ csrf_token() }}" ,status : $this.prop('checked')},
				beforeSend: function() {
					$("#loading-image").show();
               	}
			}).done(function (data) {
				$("#loading-image").hide();
				toastr["success"]("Category removed successfully");
				$this.closest("tr").remove();
			}).fail(function (jqXHR, ajaxOptions, thrownError) {
				toastr["error"]("Oops,something went wrong");
				$("#loading-image").hide();
			});
		}
	});

</script>

@endsection