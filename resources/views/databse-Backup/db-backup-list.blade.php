@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection


@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">Database Backup Monitoring ({{$dbLists->total()}})</h2>
		</div>
	</div>
	@if(session('success'))
	<div class="alert alert-success">
		{{ session('success') }}
	</div>
	@endif
    <div class="mt-3 col-md-12">
		<form action="{{route('get.backup.monitor.lists')}}" method="get" class="search">
            @csrf
            <div class="col-md-2 pd-sm">
				{{ Form::select("s_ids[]", \App\Models\DatabaseBackupMonitoring::pluck('server_name','server_name')->toArray(),request('s_ids'),["class" => "form-control globalSelect2", "multiple", "data-placeholder" => "Select Server Name"]) }}
			</div>
            <div class="col-md-2 pd-sm">
				{{ Form::select("db_ids[]", \App\Models\DatabaseBackupMonitoring::pluck('database_name','database_name')->toArray(),request('db_ids'),["class" => "form-control globalSelect2", "multiple", "data-placeholder" => "Select DataBase"]) }}
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="text" id="search_instance" placeholder="Search Instance" name="search_instance" value="{{ $search_instance ?? '' }}">
			</div>
            <div class="col-lg-2">
				<input class="form-control" type="text" id="search_error" placeholder="Search Error" name="search_error" value="{{ $search_error ?? '' }}">
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="date" name="date">
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="text" id="search_status" placeholder="Search Status" name="search_status" value="{{ $search_status ?? '' }}">
			</div>

			<div class="col-lg-2">
				<button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
				   <img src="{{ asset('images/search.png') }}" alt="Search">
			   </button>
			   <a href="{{route('get.backup.monitor.lists')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>

			</div>
		</form>
	</div>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
			    <tr>
			    	<th width="3%">S.no</th>
			    	<th width="10%">Server Name</th>
			        <th width="10%">Instance</th>
			        <th width="10%">Database Name</th>
			        <th width="30%">Error</th>
					<th width="5%">Is Resolved</th>
			        <th width="10%">date</th>
                    <th width="10%">Status</th>
                </tr>
		    	<tbody>
                    @foreach ($dbLists as $key => $dbList)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$dbList->server_name}}</td>
							<td>{{$dbList->instance}}</td>
							<td>{{$dbList->database_name}}</td>
							<td>
								<div>
								{{ strlen($dbList->error) > 10 ? substr($dbList->error, 0, 70).'...' : $dbList->error }}
								<i class="fa fa-eye show_logs show-logs-icon" data-id="{{ $dbList->id }}" style="color: #808080;float: right;"></i>
							    </div>
							</td>
							<td>
								<input type="checkbox" name="is_resolved" value="1" data-id="{{ $dbList->id }}" onchange="updateIsResolved(this)">
							</td>
							<td>{{$dbList->date}}</td>
                            <td>{{$dbList->status}}</td>   
						</tr>                        
                    @endforeach
		    	</tbody>
		    </thead>
		</table>
		{!! $dbLists->appends(Request::except('page'))->links() !!}
	</div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
    50% 50% no-repeat;display:none;">
</div>

<div class="modal" tabindex="-1" role="dialog" id="error_logs_modal">
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
                    <div class="col-md-12" id="error_logs_div">
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

		$(document).on('click', '.show-logs-icon', function() {
		var id = $(this).data('id');
			$.ajax({
				url: '{{route('db.error.show')}}',
				method: 'GET',
				data: {
					id: id
				},
				success: function(response) {
					$('#error_logs_modal').modal('show');
					$('#error_logs_div').html(response);
				},
				error: function(xhr, status, error) {
					alert("Error occured.please try again");
				}
			});
		});
</script> 
@endsection