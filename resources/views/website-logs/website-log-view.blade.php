@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection


@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">Website Logs View ({{$dataArr->total()}})</h2>
		</div>
	</div>
	<div class="mt-3 col-md-12">
		<form action="{{route('website.search.log.view')}}" method="get" class="search">
			@csrf
			<div class="col-1">
				<b>Search</b> 
			</div>
			<div class="col-md-2 pd-sm">
				{{ Form::select("website_ids[]", \App\WebsiteLog::pluck('website_id','website_id')->toArray(),request('website_ids'),["class" => "form-control globalSelect2", "multiple", "data-placeholder" => "Select Website"]) }}
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="text" id="search_error" placeholder="Search Error" name="search_error" value="{{ $search_error ?? '' }}">
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="text" id="search_type" placeholder="Search type" name="search_type" value="{{ $search_type ?? '' }}">
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="date" name="date">
			</div>

			<div class="col-lg-2">
				<button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
				   <img src="{{ asset('images/search.png') }}" alt="Search">
			   </button>
			</div>

			<div class="form-group ml-3 cls_filter_inputbox" style="margin-left: 10px;">
				<button type="submit" style="" class="btn btn-image pl-0"><img src="/images/filter.png"></button>
				<a href="{{route('website.log.view')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
				<a href="{{route('website.log.truncate')}}" class="btn btn-primary" onclick="return confirm('{{ __('Are you sure you want to Truncate a Data?Note : It will Remove All data') }}')">Truncate Data </a>		
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
							<td>
								<div>
								{{ strlen($data->error) > 10 ? substr($data->error, 0, 70).'...' : $data->error }}
								<i class="fa fa-eye show_logs show-logs-icon" data-id="{{ $data->id }}" style="color: #808080;float: right;"></i>
							    </div>
							</td>
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
<div class="modal" tabindex="-1" role="dialog" id="reply_logs_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Error details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="reply_logs_div">
                        <table class="table">
                            <thead>
                                <tr>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
		$("#tabledata").click(function(e) {
			var BASE_URL = window.location.origin+'/';
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
				url: "{{route('website.file.list.log')}}",//BASE_URL+"/scrap-logs/fetch/"+search+"/"+date,
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

		$(document).on('click', '.show-logs-icon', function() {
		var id = $(this).data('id');
			$.ajax({
				url: '{{route('website.error.show')}}',
				method: 'GET',
				data: {
					id: id
				},
				success: function(response) {
					$('#reply_logs_modal').modal('show');
					$('#reply_logs_div').html(response);
				},
				error: function(xhr, status, error) {
					alert("Error occured.please try again");
				}
			});
		});
</script> 
@endsection
    