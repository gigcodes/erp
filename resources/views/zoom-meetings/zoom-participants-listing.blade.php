@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection


@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">Zoom Participants ({{$zoomParticipants->total()}})</h2>
		</div>
	</div>
	<div class="col-md-12">
		<form action="{{route('list.all-participants')}}" method="get" class="search">
			<div class="col-md-2">
                <h5> <b>Search Name </b></h5>
                <?php 
                    if(request('name')){   $search_name = request('name'); }
                    else{ $search_name = []; }
                ?>
                <select name="name[]" id="name" class="form-control select2" multiple style="max-width: 100%;">
                    <option value="" @if($search_name=='') selected @endif>-- Select a name--</option>
                    @forelse($allNames as $swId => $name)
                    <option value="{{ $name }}" @if(in_array($name, $search_name)) selected @endif>{!! $name !!}</option>
                    @empty
                    @endforelse
                </select>
            </div>
            <div class="col-md-2">
                <h5> <b> Search Email</b> </h5>
                <?php 
                    if(request('email')){   $search_email = request('email'); }
                    else{ $search_email = []; }
                ?>
                <select name="email[]" id="email" class="form-control select2" multiple style="max-width: 100%;">
                    <option value="" @if($search_email=='') selected @endif>-- Select a Email--</option>
                    @forelse($allEmails as $swId => $email)
                    <option value="{{ $email }}" @if(in_array($email, $search_email)) selected @endif>{!! $email !!}</option>
                    @empty
                    @endforelse
                </select>
            </div>            
			<div class="col-lg-2">
                <h5><b> Search Reason</b></h5>
				<input class="form-control" type="text" id="search_reason" placeholder="Search Reason" name="search_reason" value="{{ (request('search_reason') ?? "" )}}">
			</div>
            <div class="col-lg-2">
                <h5><b> Search Duration</b></h5>
				<input class="form-control" type="text" id="duration" placeholder="Search Duration" name="duration" value="{{ (request('duration') ?? "" )}}">
			</div>
            <div class="col-lg-2">
                <h5> <b> Search Join Date </b></h5>
				<input class="form-control" type="date" name="join_time" value="{{ (request('join_time') ?? "" )}}">
			</div>
            <div class="col-lg-2">
                <h5> <b> Search Leave Date </b></h5>
				<input class="form-control" type="date" name="leave_time" value="{{ (request('leave_time') ?? "" )}}">
			</div>
			<div class="col-lg-2"><br>
                <h5> <b> Search Created At </b></h5>
				<input class="form-control" type="date" name="date" value="{{ (request('date') ?? "" )}}">
			</div>

			<div class="col-lg-2"><br><br><br>
				<button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
				   <img src="{{ asset('images/search.png') }}" alt="Search">
			   </button>
			   <a href="{{route('list.all-participants')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
			</div>
		</form>
	</div>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
			    <tr>
			    	<th width="3%">ID</th>
			    	<th width="10%">Name</th>
			        <th width="20%">Email</th>
                    <th width="10%">Join Time</th>
                    <th width="10%">Leave Time</th>
			        <th width="20%">Leave Reason</th>
					<th width="5%">Durartion</th>
                    <th width="5%">Created At</th>
                </tr>
		    	<tbody>
                    @foreach ($zoomParticipants as $key=>$data)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$data->name}}</td>
                            <td>{{$data->email}}</td>
                            <td>{{$data->join_time}}</td>
                            <td>{{$data->leave_time}}</td>
                            <td class="expand-row" style="word-break: break-all">
                                <span class="td-mini-container">
                                   {{ strlen($data->leave_reason) > 30 ? substr($data->leave_reason, 0, 60).'...' :  $data->leave_reason }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ $data->leave_reason }}
                                </span>
                            </td>
							<td>{{$data->duration}}</td>
                            <td>{{ $data->created_at?->format('Y-m-d') }}</td>
						</tr>                        
                    @endforeach
		    	</tbody>
		    </thead>
		</table>
		{!! $zoomParticipants->appends(Request::except('page'))->links() !!}
	</div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
    50% 50% no-repeat;display:none;">
</div>
<div class="modal" tabindex="-1" role="dialog" id="show_full_log_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Full Log</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="show_full_log_modal_content">
                        
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

@section("styles")
<style>
    /* CSS to make specific modal body scrollable */
    #show_full_log_modal .modal-body {
      max-height: 400px; /* Maximum height for the scrollable area */
      overflow-y: auto; /* Enable vertical scrolling when content exceeds the height */
    }
</style>

@endsection

@section('scripts')
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>

    $('.select2').select2();

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
    