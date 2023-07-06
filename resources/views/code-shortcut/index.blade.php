@extends('layouts.app')
@section('title', 'Code Shortcut')
@section('content')
<script>

    function confirmDelete(code, url) {
        let result = confirm("Are you sure you want to delete the code " + code + "?");
        if (result) {
            window.location.href = url;
        }
    }
</script>
<style type="text/css">
	#loading-image {
		position: fixed;
		top: 50%;
		left: 50%;
		margin: -50px 0px 0px -50px;
		z-index: 60;
	}
</style>
<div id="myDiv">
	<img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
</div>
<div class="row" id="product-template-page">
	<div class="col-lg-12 margin-tb">
		<h2 class="page-heading">Code Shortcut (<span id="user_count">{{ count($codeshortcut) }}</span>)</h2>
		<div class="pull-left">
			<div class="form-group">
				<div class="row">
					<div class="col-md-3">
						<select class="form-control select-multiple" id="supplier-select">
							<option value="">Select Supplier</option>
							@foreach($suppliers as $supplier)
							<option value="{{ $supplier->id }}">{{ $supplier->supplier }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-3">
						<input name="term" type="text" class="form-control" value="{{ isset($term) ? $term : '' }}" placeholder="Name of Code" id="term">
					</div>
					<br><br>
					<div class="col-md-3">
						<input name="title" type="text" class="form-control" value="{{ isset($title) ? $title : '' }}" placeholder="Name of title" id="code_title">
					</div>
					<div class="col-md-3">
						<select class="form-control select-multiple" id="createdAt-select">
							<option value="">Select SortBy CreatedAt</option>						
							<option value="asc">Asc</option>
							<option value="desc">Desc</option>
						</select>
					</div>
					<h5>Search Platform	</h5>		
					<div class="col-md-3">	
					<select class="form-control globalSelect2" multiple="true" id="platform-select" name="platforms" place-holder="Select Platform">
						<option value="">Select Platform</option>
						@foreach($platforms as $platform)
						<option value="{{ $platform->id }}">{{ $platform->name }}</option>
						@endforeach
					</select>
					</div>
					<div class="col-md-2">
						<button type="button" class="btn btn-image" onclick="submitSearch()"><img src="/images/filter.png" /></button>
					</div>
					<div class="col-md-2">
						<button type="button" class="btn btn-image" id="resetFilter" onclick="resetSearch()"><img src="/images/resend2.png" /></button>
					</div>
				</div>
			</div>
		</div>
		<div class="pull-right pr-4">
			<button type="button" class="btn btn-secondary create-platform-btn" data-toggle="modal" data-target="#code-shortcut-platform">+ Add Platform</button>
			<button type="button" class="btn btn-secondary create-product-template-btn" data-toggle="modal" data-target="#create_code_shortcut">+ Add Code</button>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		@if(session()->has('success'))
		<div class="alert alert-success" role="alert">{{session()->get('success')}}</div>
		@endif

	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="pull-right pr-4">
			<input type="text" id="search_input" placeholder="Search By Type.....">
		</div>
		<br><br>
		<table class="table table-striped table-bordered" id="code_table">
			<thead>
				<tr>
					<th>ID</th>
					<th>Platform name</th>
					<th>Title</th>
					<th>Code</th>
					<th>Description</th>
					<th>Solution</th>
					<th>User Name</th>
					<th>Supplier Name</th>
					<th>Created At</th>
					<th>Image</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				@include('code-shortcut.partials.list-code')
			</tbody>


		</table>
	</div>
</div>

<!-- Modal -->
     <!-- Platform Modal content-->
	


<div class="modal fade" id="edit_code_shortcut" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLongTitle">Edit Code Shortcut</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" enctype="multipart/form-data" id="edit_code_shortcut_from">
				@csrf
				@method('put')

				<div class="modal-body">

					<div class="col-sm-12">
						<div class="form-group">
							<label>Supplier</label>
							<select name="supplier" id="supplier" class="form-control code">
								<option value="0">Selet Supplier</option>
								@foreach($suppliers as $supplier)
								<option value="{{$supplier->id}}">{{$supplier->supplier}}</option>
								@endforeach
							</select>

						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<label>Platform</label>
							<select name="platform_id" class="form-control code" id="platform_id">
								<option value="0">Selet Platform</option>
								@foreach($platforms as $platform)
								<option value="{{$platform->id}}">{{$platform->name}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
						<label>filename</label>
						<input type="file" name="notesfile" id="shortnotefileInput">
						<img src="" alt="Existing Image" height='50' width="50" id="filename">	
					</div>		
				</div>			
					<div class="col-sm-12">
						<div class="form-group">
							<label>Code</label>
							<?php echo Form::text('code', null, ['id' => 'code', 'class' => 'form-control code', 'required' => 'true', 'value' => "{{old('code')}}"]); ?>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<label>Title</label>
							<?php echo Form::text('title', null, ['id' => 'codetitle','class' => 'form-control title', 'required' => 'true', 'value' => "{{old('title')}}"]); ?>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<label>Solution</label>
							<?php echo Form::text('solution', null, ['id' => 'solution', 'class' => 'form-control solution', 'required' => 'true', 'value' => "{{old('solution')}}"]); ?>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<label>Description</label>
							<?php echo Form::text('description', null, ['id' => 'description', 'class' => 'form-control description', 'required' => 'true', 'value' => "{{old('description')}}"]); ?>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	function submitSearch() {
		src = '{{route("code-shortcuts")}}'
		term = $('#term').val()
		id = $('#supplier-select').val()
		var platformIds = $('#platform-select').val();
		createdAt = $('#createdAt-select').val()
	
		codeTitle = $('#code_title').val()
		$.ajax({
			url: src,
			dataType: "json",
			data: {
				term: term,
				id: id,
				platformIds: platformIds,
				codeTitle:codeTitle,
				createdAt:createdAt

			},
			beforeSend: function() {
				$("#loading-image").show();
			},

		}).done(function(data) {
			$("#loading-image").hide();
			$("#code_table tbody").empty().html(data.tbody);

		}).fail(function(jqXHR, ajaxOptions, thrownError) {
			alert('No response from server');
		});

	}

	function resetSearch() {
		src = '{{route("code-shortcuts")}}'
		blank = ''
		$.ajax({
			url: src,
			dataType: "json",
			data: {

				blank: blank,

			},
			beforeSend: function() {
				$("#loading-image").show();
			},

		}).done(function(data) {
			$("#loading-image").hide();
			$('#term').val('')
			$('#supplier-select').val('')
			$("#code_table tbody").empty().html(data.tbody);

		}).fail(function(jqXHR, ajaxOptions, thrownError) {
			alert('No response from server');
		});
	}

	$(document).on("keydown", "#search_input", function() {
		var query = $(this).val().toLowerCase();
			$("#code_table tr").filter(function() {
			$(this).toggle($(this).text().toLowerCase().indexOf(query) > -1)
		});
	});

</script>

<script>
	$(document).ready(function() {
		$('.edit_modal').on('click', function() {
			var id = $(this).attr("data-id")
			var url = '{{route("code-shortcuts.update",0)}}'
			url = url.replace("/0/", "/" + id + "/")
			$("#edit_code_shortcut_from").attr('action', url)
			$('#code').val($(this).attr("data-code"));
			$('#description').val($(this).attr("data-des"));
			$('#supplier').val($(this).attr("data-supplier"));
			$('#codetitle').val($(this).attr("data-title"));
			$('#solution').val($(this).attr("data-solution"));
			$('#platform_id').val($(this).attr("data-platformId"));
			var imageUrl = $(this).attr("data-shortcutfilename"); 
			var image = "./codeshortcut-image/" + imageUrl; 
			$('#filename').attr('src', image);
			$('#edit_code_shortcut').modal('show');
		})
	});
</script>

@endsection