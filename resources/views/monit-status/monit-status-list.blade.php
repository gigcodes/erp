@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection


@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">Monit Status ({{$monitStatus->total()}})</h2>
		</div>
	</div>
	<div class="mt-3 col-md-12">
		<form action="{{route('monit-status.index')}}" method="get" class="search">
			<div class="col-md-2 pd-sm">
				<input class="form-control" type="text" id="search_uptime" placeholder="Search service name" name="service_name" value="{{ (request('service_name') ?? "" )}}">
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="text" id="search_error" placeholder="Search Memory" name="search_memory" value="{{ (request('search_memory') ?? "" )}}">
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="text" id="search_type" placeholder="Search Status" name="search_status" value="{{ (request('search_status') ?? "" )}}">
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="text" id="search_uptime" placeholder="Search uptime" name="search_uptime" value="{{ (request('search_status') ?? "" )}}">
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="date" name="date" value="{{ (request('date') ?? "" )}}">
			</div>

			<div class="col-lg-2">
				<button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
				   <img src="{{ asset('images/search.png') }}" alt="Search">
			   </button>
			   <a href="{{route('monit-status.index')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
			</div>
		</form>
	</div>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
			    <tr>
			    	<th width="3%">ID</th>
			    	<th width="8%">Service Name</th>
			        <th width="30%">Memory</th>
			        <th width="10%">Status</th>
			        <th width="10%">Uptime</th>
			        <th width="10%">Date</th>
                </tr>
		    	<tbody>
                    @foreach ($monitStatus as $data)
                        <tr>
                            <td>{{$data->id}}</td>
                            <td>{{$data->service_name}}</td>
							<td class="expand-row" style="word-break: break-all">
								<span class="td-mini-container">
								   {{ strlen($data->memory) > 30 ? substr($data->memory, 0, 30).'...' :  $data->memory }}
								</span>
								<span class="td-full-container hidden">
									{{ $data->memory }}
								</span>
							</td>
							<td>{{$data->status}}</td>
							<td>{{$data->uptime}}</td>
							<td>{{$data->created_at}}</td>
						</tr>                        
                    @endforeach
		    	</tbody>
		    </thead>
		</table>
		{!! $monitStatus->appends(Request::except('page'))->links() !!}
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
		$(document).on('click', '.expand-row', function () {
			var selection = window.getSelection();
			if (selection.toString().length === 0) {
				$(this).find('.td-mini-container').toggleClass('hidden');
				$(this).find('.td-full-container').toggleClass('hidden');
			}
   	 	});
	});
</script> 
@endsection
    