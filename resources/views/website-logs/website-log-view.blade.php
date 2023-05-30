@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection


@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">Website Logs View</h2>
		</div>
	</div>
	<div class="mt-3 col-md-12">
		<form action="{{route('website.search.log.view')}}" method="get">
			@csrf
			<div class="col-1">
				<b>Search</b> 
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="text" id="search_error" placeholder="Search Error" name="search_error" value="{{ $search_error ?? '' }}">
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="text" id="search_type" placeholder="Search type" name="search_type" value="{{ $search_type ?? '' }}">
			</div>
			<div class="form-group ml-3 cls_filter_inputbox" style="margin-left: 10px;">
				<button type="submit" style="" class="btn btn-image pl-0"><img src="/images/filter.png"></button>
				<a href="{{route('website.log.view')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
				
			</div>
		</form>
	</div>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
			    <tr>
			    	<th width="3%">ID</th>
			    	<th width="3%">Website</th>
			        <th width="30%">Error</th>
			        <th width="10%">Type</th>
			        <th width="10%">File Path</th>
			        <th width="10%">Date</th>
                </tr>
		    	<tbody>
                    @foreach ($dataArr as $data)
                        <tr>
                            <td>{{$data->id}}</td>
                            <td>{{$data->website_id}}</td>
							<td>{{$data->error}}</td>
							<td>{{$data->type}}</td>
							<td>{{$data->file_path}}</td>
							<td>{{$data->created_at}}</td>
						</tr>                        
                    @endforeach
		    	</tbody>
		    </thead>
		</table>
		{!! $dataArr->appends(Request::except('page'))->links() !!}
	</div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
    50% 50% no-repeat;display:none;">
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
			/*
            var search = $("input[name='search'").val() != "" ? $("input[name='search'").val() : null;
			var date = $("#datepicker").val() !="" ? $("#datepicker").val() : null;
            var download = "?download=" + $("select[name='download_option'").val();
			var server_id = $('.server_id-value').val();
            */

            if($("select[name='download_option'").val() == "yes") {
                window.location.href = BASE_URL+"/scrap-logs/fetch/"+search+"/"+date+"/"+download;
            }

			$.ajax({
				url: "{{route('website.file.list.log')}}"//BASE_URL+"/scrap-logs/fetch/"+search+"/"+date,
				method:"get",
				headers: {
				    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				  },
				data:{ 
                    /*'server_id':server_id, month : $("#monthpicker").val(),"year" : $("#yearpicker").val()*/
                },
				cache: false,
				success: function(data) {
                    console.log(data)
                    $("#log-table tbody").empty();

                    $.each(data.file_list, function(i,row){
                        $("#log-table tbody").append("<tr><td>"+(i+1)+"</td><td>"+row['foldername']+"</td><td><a href='scrap-logs/file-view/"+row['filename']+ '/' +row['foldername']+"' target='_blank'>"+row['filename']+"</a>&nbsp;<a href='javascript:;' onclick='openLasttenlogs(\""+row['scraper_id']+"\")'><i class='fa fa-weixin' aria-hidden='true'></i></a></td><td>"+row['log_msg']+"</td><td>"+row['status']+"</td><td><button style='padding:3px;' type='button' class='btn btn-image make-remark d-inline' data-toggle='modal' data-target='#makeRemarkModal' data-name='"+row['scraper_id']+"'><img width='2px;' src='/images/remark.png'/></button><button style='padding:3px;' type='button' class='btn btn-image log-history d-inline' data-toggle='modal' data-target='#loghistory' data-filename='"+row['filename']+"' data-name='"+row['scraper_id']+"'><i class='fa fa-sticky-note'></i></button><button style='padding:3px;' type='button' class='btn btn-image history d-inline' data-toggle='modal' data-target='#history' data-filename='"+row['filename']+"' data-name='"+row['scraper_id']+"'><i class='fa fa-history'></i></button></td></tr>");

                    });
                }
			});
		}
	});
</script> 
@endsection
    