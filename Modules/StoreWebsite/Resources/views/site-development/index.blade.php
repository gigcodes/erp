@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Site Development')

@section('styles')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
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

		.dis-none {
            display: none;
        }
		.pd-5 {
			padding: 5px;
		}
		.toggle.btn {
			min-height:25px;
		}
		.toggle-group .btn {
			padding: 2px 12px;
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
	    	<div class="col col-md-12">
		    	<div class="row">
					<div class="col-md-4">
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
					</div>
					<div class="col-md-8">
						<form class="form-inline handle-search">
								<div class="form-group" style="margin-right:10px;">
									<label for="keyword">Search keyword:</label>
									<?php echo Form::text("k",request("k"),["class"=> "form-control","placeholder" => "Enter keyword","id" => "enter-keyword"]) ?>
								</div>
								<div class="form-group">
									<label for="status">Status:</label>
									<?php echo Form::select("status",["" => "All","ignored" => "Ignored"],request("status"),["class"=> "form-control","id" => "enter-status"]) ?>
								</div>
								<div class="form-group">
										<label for="button">&nbsp;</label>
										<button style="display: inline-block;width: 10%" type="submit" class="btn btn-sm btn-image btn-search-keyword">
											<img src="/images/send.png" style="cursor: default;" >
										</button>
								</div>
						</form>
					</div>
				 </div>
		    </div>
	    </div>
		<br>
	    <div class="row" style="margin-bottom: 10px;">
	    	<a href="{{ route('site-development-status.index') }}" target="__blank">
		    	<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image">
		  			+ Add Status
		  		</button>
		  	</a>
			<button style="display: inline-block;width: 10%;margin-right:5px;" class="btn btn-sm btn-secondary latest-remarks-btn">
		  			Remarks
		  	</button>
		  	<a class="btn btn-secondary" data-toggle="collapse" href="#statusFilterCount" role="button" aria-expanded="false" aria-controls="statusFilterCount">
		  		Status Count
            </a>
	    </div>

	    <div class="row">
		    <div class="col-md-12">
		        <div class="collapse" id="statusFilterCount">
		            <div class="card card-body">
		              <?php if(!empty($statusCount)) { ?>
		                <div class="row col-md-12">
		                    <?php foreach($statusCount as $sC) { ?>
		                      <div class="col-md-2">
		                            <div class="card">
		                              <div class="card-header">
		                                <?php echo $sC->name; ?>
		                              </div>
		                              <div class="card-body">
		                                  <?php echo $sC->total; ?>
		                              </div>
		                          </div>
		                       </div> 
		                  <?php } ?>
		                </div>
		              <?php } else  { 
		                echo "Sorry , No data available";
		              } ?>
		            </div>
		        </div>
		    </div>    
		</div>

		<div class="col-md-12 margin-tb infinite-scroll">
			<div class="row">
				<table class="table table-bordered" id="documents-table">
					<thead>
						<tr>
						<th width="4%">Sl no</th>
						<th width="10%"></th>
						<th width="18%">Title</th>
						<th width="18%">Description</th>
						<th width="35%">Communication</th>
						<th width="15%">Action</th>
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
<div id="file-upload-area-section" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
           <form action="{{ route("site-development.upload-documents") }}" method="POST" enctype="multipart/form-data">
	            <input type="hidden" name="store_website_id" id="hidden-store-website-id" value="">
	            <input type="hidden" name="id" id="hidden-site-id" value="">
	            <input type="hidden" name="site_development_category_id" id="hidden-site-category-id" value="">
	            <div class="modal-header">
	                <h4 class="modal-title">Upload File(s)</h4>
	            </div>
	            <div class="modal-body" style="background-color: #999999;">
				    	@csrf
					    <div class="form-group">
					        <label for="document">Documents</label>
					        <div class="needsclick dropzone" id="document-dropzone">

					        </div>
					    </div>

	            </div>
	            <div class="modal-footer">
	                <button type="button" class="btn btn-default btn-save-documents">Save</button>
	                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	            </div>
			</form>
        </div>
    </div>
</div>
<div id="file-upload-area-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        	<div class="modal-body">
        		<table class="table table-bordered">
				    <thead>
				      <tr>
				        <th width="5%">No</th>
				        <th width="45%">Link</th>
				        <th width="25%">Send To</th>
				        <th width="25%">Action</th>
				      </tr>
				    </thead>
				    <tbody class="display-document-list">
				    </tbody>
				</table>
			</div>
           <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="remark-area-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        	<div class="modal-body">
    			<div class="col-md-12">
	    			<div class="col-md-8" style="padding-bottom: 10px;">
	    				<textarea class="form-control" col="5" name="remarks" data-id="" id="remark-field"></textarea>
	    			</div>
	    			<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-remark-field">
						<img src="/images/send.png" style="cursor: default;" >
					</button>
				</div>
    			<div class="col-md-12">
	        		<table class="table table-bordered">
					    <thead>
					      <tr>
					        <th width="5%">No</th>
					        <th width="45%">Remark</th>
					        <th width="25%">BY</th>
					        <th width="25%">Date</th>
					      </tr>
					    </thead>
					    <tbody class="remark-action-list-view">
					    </tbody>
					</table>
				</div>
			</div>
           <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="preview-website-image" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        	<div class="modal-body">
    			<div class="col-md-12">
	        		<table class="table table-bordered">
					    <thead>
					      <tr>
					        <th>Sl no</th>
					        <th>Image</th>
					      </tr>
					    </thead>
					    <tbody class="website-image-list-view">
					    </tbody>
					</table>
				</div>
			</div>
           <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="latest-remarks-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        	<div class="modal-body">
    			<div class="col-md-12">
	        		<table class="table table-bordered">
					    <thead>
					      <tr>
					        <th>Sl no</th>
					        <th>Category</th>
					        <th>Remarks</th>
					      </tr>
					    </thead>
					    <tbody class="latest-remarks-list-view">
					    </tbody>
					</table>
				</div>
			</div>
           <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="artwork-history-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        	<div class="modal-body">
    			<div class="col-md-12">
	        		<table class="table table-bordered">
					    <thead>
					      <tr>
					        <th>Sl no</th>
					        <th>Date</th>
					        <th>Status</th>
					        <th>Username</th>
					      </tr>
					    </thead>
					    <tbody class="artwork-history-list-view">
					    </tbody>
					</table>
				</div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
<script type="text/javascript">

// $('.infinite-scroll').jscroll({
//         autoTrigger: true,
//         loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
//         padding: 20,
//         nextSelector: '.pagination li.active + li a',
//         contentSelector: 'div.infinite-scroll',
//         callback: function () {
//             $('ul.pagination').first().remove();
//         }
//     });

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

		$(document).on("click" , ".save-artwork-status" , function() {
			websiteId = $('#website_id').val()
			category = $(this).data("category")
			type = $(this).data("type")
			site = $(this).data("site")
			var text = $(this).val();
		    $.ajax({
				url: '{{ route("site-development.save") }}',
				type: 'POST',
				dataType: 'json',
				data: {websiteId: websiteId , "_token": "{{ csrf_token() }}" , category : category , type : type , text : text,site:site},
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


		$(document).on("click",".btn-remark-field",function() {
			var id  = $("#remark-field").data("id");
			var val = $("#remark-field").val();
			$.ajax({
				url: '/site-development/'+id+'/remarks',
				type: 'POST',
				headers: {
		      		'X-CSRF-TOKEN': "{{ csrf_token() }}"
		    	},
		    	data : {remark : val},
				beforeSend: function() {
					$("#loading-image").show();
	           	}
			}).done(function (response) {
				$("#loading-image").hide();
				$("#remark-field").val("");
				toastr["success"]("Remarks fetched successfully");
				var html = "";
				$.each(response.data,function(k,v){
					html += "<tr>";
						html += "<td>"+v.id+"</td>";
						html += "<td>"+v.remarks+"</td>";
						html += "<td>"+v.created_by+"</td>";
						html += "<td>"+v.created_at+"</td>";
					html += "</tr>";
				});
				$("#remark-area-list").find(".remark-action-list-view").html(html);
				//$("#remark-area-list").modal("show");
				//$this.closest("tr").remove();
			}).fail(function (jqXHR, ajaxOptions, thrownError) {
				toastr["error"]("Oops,something went wrong");
				$("#loading-image").hide();
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
		var $this = $(this);
		site = $(this).data("id");
		category = $(this).data("category");
		message = $('#message-'+site).val();
		userId = $('#user-'+site+' option:selected').val();

		var users = [];
		
		var hidden_row_class = 'hidden_row_'+category;
		
		if($this.closest("tr").find("input[name='developer']:checked").length > 0){
			var value = $('.hidden_row_'+category).find("select[name='developer_id']").val();
			users.push(value);
		}
		if($this.closest("tr").find("input[name='designer']:checked").length > 0){
			var value = $('.hidden_row_'+category).find("select[name='designer_id']").val();
			users.push(value);
		}
		if($this.closest("tr").find("input[name='html']:checked").length > 0){
			var value = $('.hidden_row_'+category).find("select[name='html_designer']").val();
			users.push(value);
		}
		if(users.length <= 0){
			alert('Please Select User');
		}else if(site){
			$.ajax({
				url: '/whatsapp/sendMessage/site_development',
				dataType: "json",
				type: 'POST',
				data: { 'site_development_id' : site , 'message' : message , 'users' : users , "_token": "{{ csrf_token() }}" , 'status' : 2},
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

    $(document).on("click",".fa-ignore-category",function() {
		var $this = $(this);
    	var msg = "disallow";
		var status = $this.data("status");
    	if(status) {
    	   msg = "allow";
    	}
		if(confirm("Are you sure want to "+msg+" this category?")) {
			var store_website_id = $this.data("site-id");
			var category = $this.data("category-id");
			$.ajax({
				url: '/site-development/disallow-category',
				dataType: "json",
				type: 'POST',
				data: { 'store_website_id' : store_website_id , 'category' : category, "_token": "{{ csrf_token() }}" ,status : status},
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

	$(document).on("click",".btn-file-upload",function() {
		var $this = $(this);
		$("#file-upload-area-section").modal("show");
		$("#hidden-store-website-id").val($this.data("store-website-id"));
		$("#hidden-site-id").val($this.data("site-id"));
		$("#hidden-site-category-id").val($this.data("site-category-id"));
	});

	$(document).on("click",".btn-file-list",function(e) {
		e.preventDefault();
		var $this = $(this);
		var id = $(this).data("site-id");
		$.ajax({
			url: '/site-development/'+id+'/list-documents',
			type: 'GET',
			headers: {
	      		'X-CSRF-TOKEN': "{{ csrf_token() }}"
	    	},
	    	dataType:"json",
			beforeSend: function() {
				$("#loading-image").show();
           	}
		}).done(function (response) {
			$("#loading-image").hide();
			var html = "";
			$.each(response.data,function(k,v){
				html += "<tr>";
					html += "<td>"+v.id+"</td>";
					html += "<td>"+v.url+"</td>";
					html += "<td><div class='form-row'>"+v.user_list+"</div></td>";
					html += '<td><a class="btn-secondary" href="'+v.url+'" data-site-id="'+v.site_id+'" target="__blank"><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;<a class="btn-secondary link-delete-document" data-site-id="'+v.site_id+'" data-id='+v.id+' href="_blank"><i class="fa fa-trash" aria-hidden="true"></i></a>&nbsp;<a class="btn-secondary link-send-document" data-site-id="'+v.site_id+'" data-id='+v.id+' href="_blank"><i class="fa fa-comment" aria-hidden="true"></i></a></td>';
				html += "</tr>";
			});
			$(".display-document-list").html(html);
			$("#file-upload-area-list").modal("show");
		}).fail(function (jqXHR, ajaxOptions, thrownError) {
			toastr["error"]("Oops,something went wrong");
			$("#loading-image").hide();
		});
	});

	$(document).on("click",".btn-save-documents",function(e){
		e.preventDefault();
		var $this = $(this);
		var formData = new FormData($this.closest("form")[0]);
		$.ajax({
			url: '/site-development/save-documents',
			type: 'POST',
			headers: {
	      		'X-CSRF-TOKEN': "{{ csrf_token() }}"
	    	},
	    	dataType:"json",
			data: $this.closest("form").serialize(),
			beforeSend: function() {
				$("#loading-image").show();
           	}
		}).done(function (data) {
			$("#loading-image").hide();
			toastr["success"]("Document uploaded successfully");
			location.reload();
		}).fail(function (jqXHR, ajaxOptions, thrownError) {
			toastr["error"](jqXHR.responseJSON.message);
			$("#loading-image").hide();
		});
	});


	$(document).on("click",".link-send-document",function(e) {
		e.preventDefault();
		var id = $(this).data("id");
		var site_id = $(this).data("site-id");
		var user_id = $(this).closest("tr").find(".send-message-to-id").val();
		$.ajax({
			url: '/site-development/send-document',
			type: 'POST',
			headers: {
	      		'X-CSRF-TOKEN': "{{ csrf_token() }}"
	    	},
	    	dataType:"json",
			data: { id : id , site_id : site_id, user_id: user_id},
			beforeSend: function() {
				$("#loading-image").show();
           	}
		}).done(function (data) {
			$("#loading-image").hide();
			toastr["success"]("Document sent successfully");
		}).fail(function (jqXHR, ajaxOptions, thrownError) {
			toastr["error"]("Oops,something went wrong");
			$("#loading-image").hide();
		});

	});

	$(document).on("click",".link-delete-document",function(e) {
		e.preventDefault();
		var id = $(this).data("id");
		var $this = $(this);
		if(confirm("Are you sure you want to delete records ?")) {
			$.ajax({
				url: '/site-development/delete-document',
				type: 'POST',
				headers: {
		      		'X-CSRF-TOKEN': "{{ csrf_token() }}"
		    	},
		    	dataType:"json",
				data: { id : id},
				beforeSend: function() {
					$("#loading-image").show();
	           	}
			}).done(function (data) {
				$("#loading-image").hide();
				toastr["success"]("Document deleted successfully");
				$this.closest("tr").remove();
			}).fail(function (jqXHR, ajaxOptions, thrownError) {
				toastr["error"]("Oops,something went wrong");
				$("#loading-image").hide();
			});
		}
	});

	$(document).on("click",".btn-store-development-remark",function(e) {
		var id = $(this).data("site-id");
		$.ajax({
				url: '/site-development/'+id+'/remarks',
				type: 'GET',
				headers: {
		      		'X-CSRF-TOKEN': "{{ csrf_token() }}"
		    	},
				beforeSend: function() {
					$("#loading-image").show();
	           	}
			}).done(function (response) {
				$("#loading-image").hide();
				toastr["success"]("Remarks fetched successfully");

				var html = "";

				$.each(response.data,function(k,v){
					html += "<tr>";
						html += "<td>"+v.id+"</td>";
						html += "<td>"+v.remarks+"</td>";
						html += "<td>"+v.created_by+"</td>";
						html += "<td>"+v.created_at+"</td>";
					html += "</tr>";
				});

				$("#remark-area-list").find("#remark-field").attr("data-id",id);
				$("#remark-area-list").find(".remark-action-list-view").html(html);
				$("#remark-area-list").modal("show");
				//$this.closest("tr").remove();
			}).fail(function (jqXHR, ajaxOptions, thrownError) {
				toastr["error"]("Oops,something went wrong");
				$("#loading-image").hide();
			});
	});

	var uploadedDocumentMap = {}
  	Dropzone.options.documentDropzone = {
    	url: '{{ route("site-development.upload-documents") }}',
    	maxFilesize: 20, // MB
    	addRemoveLinks: true,
    	headers: {
      		'X-CSRF-TOKEN': "{{ csrf_token() }}"
    	},
    	success: function (file, response) {
      		$('form').append('<input type="hidden" name="document[]" value="' + response.name + '">')
      		uploadedDocumentMap[file.name] = response.name
    	},
    	removedfile: function (file) {
      		file.previewElement.remove()
      		var name = ''
      		if (typeof file.file_name !== 'undefined') {
        		name = file.file_name
      		} else {
        		name = uploadedDocumentMap[file.name]
      		}
      		$('form').find('input[name="document[]"][value="' + name + '"]').remove()
    	},
    	init: function () {

    	}
  }

  $(document).on('click', '.preview-img-btn', function (e) {
            e.preventDefault();
			id = $(this).data('id');
			if(!id) {
				alert("No data found");
				return;
			}
            $.ajax({
                url: "/site-development/preview-img/"+id,
				type: 'GET',
				beforeSend: function() {
					$("#loading-image").show();
	           	},
                success: function (response) {
					var tr = '';
					for(var i=1;i<=response.data.length;i++) {
						tr = tr + '<tr><td>'+ i +'</td><td><img style="height:100px;" src="'+response.data[i-1].url+'"></td></tr>';
					}
					$("#preview-website-image").modal("show");
					$(".website-image-list-view").html(tr);
					$("#loading-image").hide();
                },
                error: function () {
					$("#loading-image").hide();
                }
            });
		});

		$(document).on('click', '.latest-remarks-btn', function (e) {
			websiteId = $('#website_id').val();
			websiteId = $.trim(websiteId);
            $.ajax({
                url: "/site-development/latest-reamrks/"+websiteId,
				type: 'GET',
				beforeSend: function() {
					$("#loading-image").show();
	           	},
                success: function (response) {
					console.log(response);
					var tr = '';
					for(var i=1;i<=response.data.length;i++) {
						tr = tr + '<tr><td>'+ i +'</td><td>'+response.data[i-1].title+'</td><td>'+response.data[i-1].remarks+'</td></tr>';
					}
					$("#latest-remarks-modal").modal("show");
					$(".latest-remarks-list-view").html(tr);
					$("#loading-image").hide();
                },
                error: function () {
					$("#loading-image").hide();
                }
            });
		});

		$(document).on('click', '.artwork-history-btn', function (e) {
			id = $(this).data('id');
			if(!id) return;
            $.ajax({
                url: "/site-development/artwork-history/"+id,
				type: 'GET',
				beforeSend: function() {
					$("#loading-image").show();
	           	},
                success: function (response) {
					console.log(response);
					var tr = '';
					for(var i=1;i<=response.data.length;i++) {
						tr = tr + '<tr><td>'+ i +'</td><td>'+response.data[i-1].date+'</td><td> Status changed from '+response.data[i-1].from_status+ ' to '+response.data[i-1].to_status+'</td><td>'+response.data[i-1].username+'</td></tr>';
					}
					$("#artwork-history-modal").modal("show");
					$(".artwork-history-list-view").html(tr);
					$("#loading-image").hide();
                },
                error: function () {
					$("#loading-image").hide();
                }
            });
		});
		
		$(document).on("click", ".toggle-class", function () {
            $(".hidden_row_" + $(this).data("id")).toggleClass("dis-none");
		});

</script>
<script type="text/javascript">
  $(document).ready(function(){
    $( ".developer" ).change(function() {
      $(this).closest("tr").find("input[name='developer']").prop('checked', true)
    });

    $( ".designer" ).change(function() {
      $(this).closest("tr").find("input[name='designer']").prop('checked', true)
    });

    $( ".html" ).change(function() {
      $(this).closest("tr").find("input[name='html']").prop('checked', true)
    });
  });
  </script>
@endsection