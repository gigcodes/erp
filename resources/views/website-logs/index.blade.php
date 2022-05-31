@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
{{-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> --}}

@endsection


@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">Website Logs</h2>
		</div>
	</div>
	{{-- <div class="mt-3 col-md-12">
		<div class="col-lg-1">
			<select name="date" id="datepicker" class="form-control">
				@for($i=0; $i<=31; $i++)
					<option value="{{$i}}" @if((date("d") - 1) == $i) selected @endif>{{$i}}</option>
				@endfor
			</select>
		</div>
		<div class="col-lg-1">
			<select name="month" id="monthpicker" class="form-control">
				@foreach(["","Jan","Feb","Mar","Apr","May","Jun","July","Aug","Sep","Oct","Nov","Dec"] as $mon)
					<option value="{{$mon}}" @if((request("month") - 1) == $mon) selected @endif>{{$mon}}</option>
				@endforeach
			</select>
		</div>
		<div class="col-lg-1">
			<select name="year" id="yearpicker" class="form-control">
				@foreach(["","19","20","21","22","23","24","25"] as $year)
					<option value="{{$year}}" @if((request("year") - 1) == $year) selected @endif>{{$year}}</option>
				@endforeach
			</select>
		</div>
		<div class="col-lg-2">
			<select name="date" id="datepicker" class="form-control server_id-value">
				<option value="">Select Server</option>
					@foreach($servers as $server)
						<option value="{{ $server['server_id'] }}")>{{$server['server_id'] }}</option>
					@endforeach
			</select>
		</div>					
		<div class="col-lg-2">
			<input class="form-control" type="text" id="search" placeholder="Search name" name="search" value="{{ $name }}">
		</div>
        <div class="col-lg-1">
            <select class="form-control" name="download_option">
                <option value="no">No</option>
                <option value="yes">Yes</option>
            </select>
        </div>
        <div class="col-lg-1">
			<button type="button" id="tabledata" class="btn btn-image">
			<img src="/images/filter.png"style="margin-left:17px;">
			</button>
		</div>
		<div class="creat-stutush">
		<div class="text-rights">
			<button class ="btn-dark" type="button" onclick="window.location='{{url('development/issue/create')}}'">Create an Issue</button>
		</div>
		<div class="text-rights">
			<button class ="btn-dark" type="button" data-toggle="modal" data-target="#status-create">Create Status</button>
		</div>
		<div class="text-rights">
			<button class ="btn-dark" type="button" data-toggle="modal" id ="logdatahistory" data-target="#logdatacounter">Log History</button>
		</div>

		<div class="col-lg-2 text-rights">
			<button class ="btn-dark" type="button" data-toggle="modal" data-target="#logdatastatus">Map Log Status</button>
		</div> 
        --}}


	</div>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
			    <tr>
			    	<th width="10%">S.No</th>
			        <th width="10%">File Name</th>
			        <th width="10%">Website</th>
			        <th width="30%">Folder Path</th>
			        
			    </tr>
		    	<tbody>
                    @foreach ($dataArr as $data)
                        <tr>
                            <td>{{$data['S_No']}}</td>
                            <td><a href="{{route('website.log.file.view')}}?path={{$data['File_Path']}}">{{$data['File_name']}}</a></td>
                            <td>{{$data['Website']}}</td>
                            <td>{{$data['File_Path']}}</td>
							<td><a style="padding:1px;" class="btn d-inline btn-image execute-task" href="#" data-id="4" title="execute Task"><img src="/images/send.png" style="cursor: pointer; width: 0px;"></a></td>
						</tr>                        
                    @endforeach
		    	</tbody>
		    </thead>
		</table>
	</div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
    50% 50% no-repeat;display:none;">
</div>
@endsection

@section('scripts')
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
	/*
    $(document).ready(function() 
	{
		tableData(BASE_URL);
		$("#tabledata").click(function(e) {
			tableData(BASE_URL);
		});
		function tableData(BASE_URL) {
			
           // var search = $("input[name='search'").val() != "" ? $("input[name='search'").val() : null;
			//var date = $("#datepicker").val() !="" ? $("#datepicker").val() : null;
            //var download = "?download=" + $("select[name='download_option'").val();
			//var server_id = $('.server_id-value').val();
            

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
                    /*'server_id':server_id, month : $("#monthpicker").val(),"year" : $("#yearpicker").val()* /
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
	*/
	$(document).on("click",".execute-task",function(e) {
        thiss = $(this);
        thiss.html(`<img src="/images/loading_new.gif" style="cursor: pointer; width: 0px;">`);
        $.ajax({
            type: "GET",
            url: "/website/command/log", 
            dataType : "json",
            success: function (response) {
                toastr['success']('Task executed successfully!');
                thiss.html(`<img src="/images/send.png" style="cursor: pointer; width: 0px;">`);
            },
            error: function (response) {
                if(response.status == 200){
                    toastr['success']('Task executed successfully!');
                }else{
                    toastr['error'](response);
                }
                thiss.html(`<img src="/images/send.png" style="cursor: pointer; width: 0px;">`);
            }
        });
    }); 
</script> 
@endsection
    