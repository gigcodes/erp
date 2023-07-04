@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endsection


@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">User Access ({{$userAccessLists->total()}})</h2>
		</div>
	</div>
	@if(session('success'))
	<div class="alert alert-success">
		{{ session('success') }}
	</div>
	@endif
    <div class="mt-3 col-md-12">
		<form action="{{route('user-management.user-access-listing')}}" method="get" class="search">
            @csrf
			<div class="col-lg-2">
				<input class="form-control" type="text" id="user" placeholder="Search User" name="user" value="{{ $user ?? '' }}">
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="date" name="date">
			</div>
            <div class="col-md-2 pd-sm">
				{{ Form::select("s_ids[]", \App\UserPemfileHistory::pluck('server_name','server_name')->toArray(),request('s_ids'),["class" => "form-control globalSelect2", "multiple", "data-placeholder" => "Select Server Name"]) }}
			</div>
            <div class="col-lg-2">
				<input class="form-control" type="text" id="search_event" placeholder="Search Event" name="search_event" value="{{ $search_status ?? '' }}">
			</div>

			<div class="col-lg-2">
				<button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
				   <img src="{{ asset('images/search.png') }}" alt="Search">
			   </button>
			   <a href="{{route('user-management.user-access-listing')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>

			</div>
		</form>
	</div>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
			    <tr>
			    	<th width="3%">S.no</th>
			    	<th width="10%">User</th>
			        <th width="10%">Server</th>
			        <th width="10%">User Name</th>
			        <th width="10%">Event</th>
			        <th width="10%">Date</th>
                    <th width="10%">Action</th>
                </tr>
		    	<tbody>
                    @foreach ($userAccessLists as $key => $userAccessList)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$userAccessList->server_name}}</td>
							<td>{{$userAccessList->server_name}}</td>
							<td>{{$userAccessList->username}}</td>
                            <td>{{$userAccessList->action}}</td>
							<td>{{$userAccessList->created_at}}</td>
                            <td>
                                @if(Auth::user()->isAdmin())
                                    <button title="View Logs" type="button" class="btn btn-image view-pem-logs pd-5" data-id="{{$userAccessList->id}}" ><i class="fa fa-info-circle show-logs-icon"></i></button>
                                @endif
                            </td> 
						</tr>                        
                    @endforeach
		    	</tbody>
		    </thead>
		</table>
		{!! $userAccessLists->appends(Request::except('page'))->links() !!}
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
                                    <th width="10%">Title</th>
                                    <th width="10%">Alert Date</th>
                                    <th width="10%">Event Type</th>
                                    <th width="8%">Is Read ?</th>
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

$(document).on('click', '.view-pem-logs', function() {
        var pemfileHistoryId = $(this).data('id');
			$.ajax({
              url:  "/user-management/user-pemfile-history-logs/" + pemfileHistoryId,
				method: 'GET',
				data: {
					pemfileHistoryId: pemfileHistoryId
				},
                    success: function(data) {
                    var html = "";
                    $.each(data.data, function(index, loglist) {
                    html += "<tr>";
                    html +=  "<td>" + loglist.cmd + "</td>";
                    html += "<td>" + loglist.output + "</td>";
                    html += "<td>" + loglist.return_var + "</td>";
                    html += "</tr>";
                    });

                    var tableHeader = "<tr><th>Command</th><th>Output</th><th>Code</th></tr>";
                    $('#error_logs_modal').modal('show');
                    $('#error_logs_div thead').html(tableHeader);
                    $('#error_logs_div tbody').html(html);
                },
                error: function(xhr, status, error) {
                    alert("An error occurred. Please try again.");
                }
			});
		});
</script> 

@endsection