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
		<div class="col-lg-2">
			<select name="date" id="datepicker" class="form-control">
				@for($i=0; $i<=31; $i++)
					<option value="{{$i}}" @if((date("d") - 1) == $i) selected @endif>{{$i}}</option>
				@endfor
			</select>
		</div>
		<div class="col-lg-2">
			<select name="date" id="datepicker" class="form-control">
				<option value="">Select Server</option>
					@foreach($servers as $server)
						<option value="{{ $server['server_id'] }}")>{{$server['server_id'] }}</option>
					@endforeach
			</select>
		</div>					
		<div class="col-lg-2">
			<input class="form-control" type="text" id="search" placeholder="Search name" name="search" value="{{ $name }}">
		</div>
        <div class="col-lg-2">
            <select class="form-control" name="download_option">
                <option value="no">No</option>
                <option value="yes">Yes</option>
            </select>
        </div>
		<div class="col-lg-2">
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
			        <th width="30%">Log Message</th>
			    </tr>
		    	<tbody>
		    	</tbody>
		    </thead>
		</table>
	</div>

	<div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Log Messages</h4>
                </div>
                <div class="modal-body">
                	<div class="cls_log_popup">
                		<table class="table">
                			<thead>
                				<td>Scraper ID</td>
                				<td>File Name</td>
                				<td>Log Messages</td>
                			</thead>
                			<tbody id="log_popup_body">
                				
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
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script>
	$(document).ready(function() 
	{
		tableData(BASE_URL);
		$("#tabledata").click(function(e) {
			tableData(BASE_URL);
		});
		function tableData(BASE_URL) {
			var search = $("input[name='search'").val() != "" ? $("input[name='search'").val() : null;
			var date = $("#datepicker").val() !="" ? $("#datepicker").val() : null;
            var download = "?download=" + $("select[name='download_option'").val();

            if($("select[name='download_option'").val() == "yes") {
                window.location.href = BASE_URL+"/scrap-logs/fetch/"+search+"/"+date+"/"+download;
            }

			$.ajax({
				url: BASE_URL+"/scrap-logs/fetch/"+search+"/"+date,
				method:"get",
				headers: {
				    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				  },
				data:{},
				cache: false,
				success: function(data) {
						console.log(data)
						$("tbody").empty();
						$.each(data.file_list, function(i,row){
							$("tbody").append("<tr><td>"+(i+1)+"</td><td>"+row['foldername']+"</td><td><a href='scrap-logs/file-view/"+row['filename']+ '/' +row['foldername']+"' target='_blank'>"+row['filename']+"</a>&nbsp;<a href='javascript:;' onclick='openLasttenlogs(\""+row['scraper_id']+"\")'><i class='fa fa-weixin' aria-hidden='true'></i></a></td><td>"+row['log_msg']+"</td></tr>");
						});
						
					}
			});
		}
	});
	function openLasttenlogs(scraper_id){
		$.ajax({
			url: BASE_URL+"/fetchlog",
			method:"get",
			headers: {
			    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			  },
			data:{},
			cache: false,
			success: function(data) {
				$("#log_popup_body").empty();
				$.each(data.file_list, function(i,row){
					$("#log_popup_body").append("<tr><td>"+row['scraper_id']+"</td><td>"+row['filename']+"</td><td>"+row['log_msg']+"</td></tr>");
				});
			}
		});
		$('#chat-list-history').modal("show");
	}
</script> 
@endsection