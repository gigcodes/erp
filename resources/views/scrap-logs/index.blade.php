@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection
@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">Scrap Logs</h2>
		</div>
	</div>
	<div class="mt-3 col-md-12">
		<div class="col-lg-1">
			<select name="date" id="datepicker" class="form-control">
				@for($i=0; $i<=31; $i++)
					<option value={{$i}}>{{$i}}</option>
				@endfor
			</select>
		</div>					
		<div class="col-lg-3">
			<input class="form-control" type="text" id="search" placeholder="Search name" name="search" value="{{ $name }}">
		</div>
		<div class="col-lg-4">
			<button type="button" id="tabledata" class="btn btn-image">
			<img src="/images/filter.png">
			</button>
		</div>
		<div class="col-lg-4 text-right">
			<button class ="btn-dark" type="button" onclick="window.location='{{url('development/issue/create')}}'">Create an Issue</button>
		</div>
	</div>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
			    <tr>
			    	     <th width="10%">S.No</th>
			        <th width="10%">FolderName</th>
			        <th width="30%">FileName</th>
			    </tr>
		    	<tbody>
		    	</tbody>
		    </thead>
		</table>
	</div>
@endsection

@section('scripts')
  <script>
	$(document).ready(function() 
	{
		tableData();
		$("#tabledata").click(function(e) {
			tableData();
		});
		function tableData() {
			var search = $("input[name='search'").val() != "" ? $("input[name='search'").val() : null;
			var date = $("#datepicker").val() !="" ? $("#datepicker").val() : null;
			$.ajax({
				url: "/scrap-logs/fetch/"+search+"/"+date,
				method:"get",
				headers: {
				    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				  },
				data:{},
				cache: false,
				success: function(data) {
						$("tbody").empty();
						$.each(data.file_list, function(i,row){
							var foldername = row['foldername'].replace('/','@');
							$("tbody").append("<tr><td>"+i+"</td><td>"+row['foldername']+"</td><td><a href='scrap-logs/file-view/"+foldername+ '@' +row['filename']+"' target='_blank'>"+row['filename']+"</a></td></tr>");
						});
					}
			});
		}
	});

</script> 
@endsection