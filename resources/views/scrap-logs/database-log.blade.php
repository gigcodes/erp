@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection
@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">Databse Logs</h2>
		</div>
	</div>
	<div class="mt-3 col-md-12">
		<div class="col-lg-2">
			<input class="form-control" type="text" id="search" placeholder="Search name" name="search" value="">
		</div>
		<div class="col-lg-2">
			<button type="button" id="tabledata" class="btn btn-image">
			<img src="/images/filter.png">
			</button>
		</div>
	</div>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
				<td>Scraper ID</td>
				<td>File Name</td>
				<td>Log Messages</td>
			</thead>
			<tbody id="log_popup_body">
				@php $count = 1;  @endphp
				@foreach($lines as $key => $line)
					<tr>
	    				<td>{{$key+1}}</td>
	    				<td>{{$filename}}</td>
	    				<td>{{$line}}</td>
	    			</tr>
				@endforeach
			</tbody>
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
	$(document).ready(function(){

	});
</script> 
@endsection