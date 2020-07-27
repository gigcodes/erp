@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Social strategy')

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
</style>
@endsection

@section('content')

<div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
</div>
<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Social strategy <span class="count-text"></span></h2>
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
		    		<form class="form-inline message-search-handler" onsubmit="event.preventDefault(); saveSubject();">
					  <div class="row">
				  		<div class="col">
				  			<div class="form-group">
							    <label for="title">Add Subject:</label>
							    <?php echo Form::text("title",request("title"),["class"=> "form-control","placeholder" => "Enter Subject","id" => "add-subject"]) ?>
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
		    </div>
	    </div>
		<div class="col-md-12 margin-tb infinite-scroll">
			<div class="row">
				<table class="table table-bordered" id="documents-table">
					<thead>
						<tr>
						<th width="10%"></th>
						<th width="25%">Description</th>
						<th width="15%">Action</th>
						<th width="25%">Communication</th>
						<th width="5%">Created</th>
					</tr>
					</thead>
					<tbody>
					@include("storewebsite::social-strategy.partials.data")
					</tbody>
				</table>
				{{ $subjects->appends(request()->capture()->except('page','pagination') + ['pagination' => true])->render() }}	
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

function refreshPage(){
		$.ajax({
		    url: '/store-website/'+{{$website->id}}+'/social-strategy',
		    dataType: "json",
		    data: {},
		}).done(function (data) {
		    $("#documents-table tbody").empty().html(data.tbody);
		}).fail(function (jqXHR, ajaxOptions, thrownError) {
		    alert('No response from server');
		});
	}
	
	function saveSubject() {
		var text = $('#add-subject').val()
		var website_id = $('#website_id').val()
		if(text === ''){
			alert('Please Enter Text');
		}else{
			$.ajax({
				url: '/store-website/'+{{$website->id}}+'/social-strategy/add-subject',
				type: 'POST',
				dataType: 'json',
				data: {text: text , "_token": "{{ csrf_token() }}"},
				beforeSend: function () {
                    $("#loading-image").show();
                },
			})
			.done(function(data) {
				$('#add-subject').val('')
				refreshPage()
				$("#loading-image").hide();
				console.log("success");
			})
			.fail(function(data) {
				$('#add-subject').val('')
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

</script>

@endsection