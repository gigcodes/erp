@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
	<div class="row" id="common-page-layout">
		<div class="col-lg-12 margin-tb">
			<h2 class="page-heading">{{$title}} <span>{{$records_count}}</span></h2>
		</div>
		<br>
		<div class="col-lg-12 margin-tb">
			<div class="row">
				<div class="col col-md-4">
					<div class="row">
						<button style="display: inline-block;width: 10%;margin-left:10px;" class="btn btn-sm btn-image btn-add-action"
								data-toggle="modal" data-target="#scriptdocumentsCreateModal">
							<img src="/images/add.png" style="cursor: default;">
						</button>

						<form class="form-inline message-search-handler" method="get">
							<div class="form-group" style="width: 300px;margin-bottom: 0px;">
								<input name="keyword" type="text" class="form-control" placeholder="Search Keywords" id="keyword" data-allow-clear="true" style=" width: 100%;"/>
							</div>

							<div class="form-group" >
								<button type="submit" style="display: inline-block;width: 10%;margin-top: 1px;" class="btn btn-sm btn-image btn-search-action">
									<img src="/images/search.png" style="cursor: default;">
								</button>
								<a href="/script-documents" class="btn btn-image" style="margin-top: 1px;">
									<img src="/images/resend2.png" style="cursor: nwse-resize;">
								</a>
							</div>
						</form>
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

	@include("script-documents.templates.list-template")
    @include("script-documents.create")
    @include("script-documents.edit")
    @include('script-documents.history')
    @include('script-documents.comment')
    @include('script-documents.last-output')
	
	<div id="uploadeScriptDocumentsScreencastModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
	
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Upload Screencast/File to Google Drive</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
	
				<form action="{{ route('script-documents.upload-file') }}" method="POST" enctype="multipart/form-data">
					@csrf
					<input type="hidden" name="script_document_id" id="script_document_id">
					<div class="modal-body">						
						<div class="form-group">
							<strong>Upload File</strong>
							<input type="file" name="images[]" id="fileInput" class="form-control input-sm" placeholder="Upload File" style="height: fit-content;" multiple="multiple" required>
							@if ($errors->has('images'))
								<div class="alert alert-danger">{{$errors->first('images')}}</div>
							@endif
						</div>
						<div class="form-group">
							<strong>File Creation Date:</strong>
							<input type="date" name="file_creation_date" value="{{ old('file_creation_date') }}" class="form-control input-sm" placeholder="Drive Date" required>
						</div>
						{{-- @if(auth()->user()->isAdmin())
							<div class="form-group custom-select2 read_user">
								<label>Read Permission for Users
								</label>
								<select class="w-100 js-example-basic-multiple js-states" id="id_label_multiple_user_read" multiple="multiple" name="file_read[]">
									@foreach($permission_users as $val)
									<option value="{{$val->gmail}}" class="form-control">{{$val->name}}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group custom-select2 write_user">
								<label>Write Permission for Users
								</label>
								<select class="w-100 js-example-basic-multiple js-states" id="id_label_multiple_user_write" multiple="multiple" name="file_write[]">
									@foreach($permission_users as $val)
									<option value="{{$val->gmail}}" class="form-control">{{$val->name}}</option>
									@endforeach
								</select>
						</div>
						@endif --}}
						<div class="form-group">
								<label>Remarks:</label>
								<textarea id="remarks" name="remarks" rows="4" cols="64" value="{{ old('remarks') }}" placeholder="Remarks" required class="form-control"></textarea>
	
								@if ($errors->has('remarks'))
									<div class="alert alert-danger">{{$errors->first('remarks')}}</div>
								@endif
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-secondary">Upload</button>
					</div>
				</form>
			</div>
	
		</div>
	</div>

	<div id="displayScriptDocumentsUpload" class="modal fade" role="dialog">
		<div class="modal-dialog modal-xl">
	
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Google Drive Script Document files</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
	
				<div class="modal-body">
					<div class="table-responsive mt-3">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>Filename</th>
									<th>File Creation Date</th>
									<th>URL</th>
									<th>Remarks</th>
								</tr>
							</thead>
							<tbody id="googleDriveScriptDocumentData">
								
							</tbody>
						</table>
					</div>
				 </div>
			</div>
		</div>
	</div>

	<div id="script_documentShowFullTextModel" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content ">
				<div id="add-mail-content">
					<div class="modal-content">
						<div class="modal-header">
							<h3 class="modal-title">Full text view</h3>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body script_documentmanShowFullTextBody">

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
		
		
	<script>
		@if($errors->any())
			@php
				$error = $errors->all()
			@endphp
			toastr["error"]("{{$error[0] ?? 'Something went wrong.'}}");
		@endif
			
		@if ($message = Session::get('success'))
			toastr["success"]("{{$message}}");
		@endif
		@if ($message = Session::get('error'))
			toastr["error"]("{{$message}}");
		@endif
	
		var page_script_document = 0; 
		var total_limit_script_document = 19;
		var action_script_document = 'inactive';

	</script>
	<script type="text/javascript" src="{{ asset('/js/jsrender.min.js')}}"></script>
	<script type="text/javascript" src="{{ asset('/js/jquery.validate.min.js')}}"></script>
	<script src="{{ asset('/js/jquery-ui.js')}}"></script>
	<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/script-documents.js') }}"></script>
	
	<script type="text/javascript">
		page.init({
			bodyView: $("#common-page-layout"),
			baseUrl: "<?php echo url("/"); ?>"
		});
	    $(document).ready(function () {
	        $('.js-example-basic-multiple').select2();
	  	})
	</script>
	<script type="text/javascript">	
		$(document).ready(function() {
			$("body").tooltip({ selector: '[data-toggle=tooltip]' });
		});

		$('.change_assign_to_top').select2({
				width: "150px",
				placeholder: 'Select Assign To'
		});
		
		$(document).on('click', '.expand-row-msg-chat', function() {
			var id = $(this).data('id');
			var full = '.expand-row-msg-chat .td-full-container-' + id;
			var mini = '.expand-row-msg-chat .td-mini-container-' + id;
			$(full).toggleClass('hidden');
			$(mini).toggleClass('hidden');
		});

		$( document ).ready(function() {
			$(document).on('click', '.expand-row,.expand-row-msg', function () {
				var selection = window.getSelection();
				if (selection.toString().length === 0) {
					$(this).find('.td-mini-container').toggleClass('hidden');
					$(this).find('.td-full-container').toggleClass('hidden');
				}
			});
		});
	
	
		// Script Document  ajax starts
		function GetParameterValues(param) {  
			var url = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');  
			for (var i = 0; i < url.length; i++) {  
				var urlparam = url[i].split('=');  
				if (urlparam[0] == param) {  
					return urlparam[1];  
				}  
			}  
		}
		
		$(window).scroll(function(){
			let urlString_script_document = window.location.href;
			let paramString_script_document = urlString_script_document.split('?')[1];
			let queryString_script_document = new URLSearchParams(paramString_script_document);
			var arr = 0;
			for(let pair of queryString_script_document.entries()) {
				console.log("Key is:" + pair[0]);
				console.log("Value is:" + pair[1]);
				arr = 1;
			}
			
			if(arr==0) {
				if($(window).scrollTop() + $(window).height() > $("#page-view-result").height() && action_script_document == 'inactive'){
					
					action_script_document = 'active';
					page_script_document++;				   
					setTimeout(function(){
						console.log("coming");						
						load_more(page_script_document);
					}, 1000);
					console.log("act="+action_script_document);
				}
			}
		});
		
		function load_more(page_script_document){	
			$.ajax({
				url: "/script-documents/record-script-document-ajax?page="+page_script_document+"&"+$(".message-search-handler").serialize(),
				type: "get",
				datatype: "html",			  
				beforeSend: function()
				{
					$('#loading-image-preview').css("display","block");
				}
			})
			.done(function(data)
			{
				$('#loading-image-preview').css("display","none");
				
				if(data.length == 0){
					console.log("len="+data.length);
					//notify user if nothing to load
					action_script_document = "inactive";
					//$('.ajax-loading').html("No more records!");
					page_script_document = 0;
					console.log("if="+action_script_document);
					return;
				}
				$('.loading-image-preview').hide(); //hide loading animation once data is received
				$('#loading-image-preview').css("display","none");			  
				$('#script_document_maintable > tbody:last').append(data); 
				action_script_document = "inactive";
				console.log("in success="+action_script_document);			   
		   })
		   .fail(function(jqXHR, ajaxOptions, thrownError)
		   {
			  alert('No response from server');
		   });
		}

		$(window).on('load', function() {
			$( "th" ).resizable();
		});
		
		var uriv = window.location.href.toString();
		if (uriv.indexOf("?") > 0) {
			var clean_uri = uriv.substring(0, uriv.indexOf("?"));
			$('#script_document-id-search').val("");
			window.history.replaceState({}, document.title, clean_uri);
		}

		$(document).ready(function () {
			$(document).on("click", ".upload-script_documents-files-button", function (e) {
				e.preventDefault();
				let script_document_id = $(this).data("script_document_id");
				$("#uploadeScriptDocumentsScreencastModal #script_document_id").val(script_document_id || 0);
				$("#uploadeScriptDocumentsScreencastModal").modal("show")
			});

			$(document).on("click", ".view-script_documents-files-button", function (e) {
				e.preventDefault();
				let script_document_id = $(this).data("script_document_id");
				$.ajax({
					type: "get",
					url: "{{route('script-documents.files.record')}}",
					data: {
						script_document_id
					},
					success: function (response) {
						$("#googleDriveScriptDocumentData").html(response.data);
						$("#displayScriptDocumentsUpload").modal("show")
					},
					error: function (response) {

					}
				});
			});
		});

		$(document).on('click','.script-document-history',function(){
	        id = $(this).data('id');
			$.ajax({
	            method: "GET",
	            url: `{{ route('script-documents.histories', [""]) }}/` + id,
	            dataType: "json",
	            success: function(response) {
                    var html = "";
                    $.each(response.data, function(k, v) {
						html += "<tr>";
					
						if(v.description!=null){
							html += "<td>" + v.description + "</td>";
						} else {
							html += "<td></td>";
						}

						if(v.run_time!=null){
							html += "<td>" + v.run_time + "</td>";
						} else {
							html += "<td></td>";
						}

						if(v.last_output_text!=null){
							html += "<td>" + v.last_output_text + "</td>";
						} else {
							html += "<td></td>";
						}

						if(v.run_status!=null){
							html += "<td>" + v.run_status + "</td>";
						} else {
							html += "<td></td>";
						}

						if(v.created_at!=null){
							html += "<td>" + v.created_at + "</td>";
						} else {
							html += "<td></td>";
						}

						html += "</tr>";
                    });

                    $("#script-document-histories-list").find(".script-document-list-view").html(html);
                    $("#script-document-histories-list").modal("show");	                
	            }
	        });
		});

		$(document).on('click','.script-document-comment-view',function(){
	        id = $(this).data('id');
			$.ajax({
	            method: "GET",
	            url: `{{ route('script-documents.comment', [""]) }}/` + id,
	            dataType: "json",
	            success: function(response) {
	               
                    $("#script-document-comment-list").find(".script-document-comment-view").html(response.data.comments);
                    $("#script-document-comment-list").modal("show");
	         
	            }
	        });
		});

		$(document).on('click','.script-document-last_output-view',function(){
	        id = $(this).data('id');
			$.ajax({
	            method: "GET",
	            url: `{{ route('script-documents.comment', [""]) }}/` + id,
	            dataType: "json",
	            success: function(response) {
	               
                    $("#script-document-last-output-list").find(".script-document-last-output-view").html(response.data.last_output);
                    $("#script-document-last-output-list").modal("show");
	         
	            }
	        });
		});

		$(document).on('click', '.expand-row-msg', function () {
			var name = $(this).data('name');
			var id = $(this).data('id');
			var full = '.expand-row-msg .show-short-'+name+'-'+id;
			var mini ='.expand-row-msg .show-full-'+name+'-'+id;
			$(full).toggleClass('hidden');
			$(mini).toggleClass('hidden');
		});
	</script>
@endsection