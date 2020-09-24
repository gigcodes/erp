@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@endsection

@section('content')
	<?php $base_url = URL::to('/');?>
	<div class = "row">
		<div class="col-lg-6 margin-tb">
			<h2 class="page-heading">Custom Charity Order List</h2>
			@if(Session::has('flash_type'))
				<p class="alert alert-{{Session::get('flash_type')}}">{{ Session::get('message') }}</p>
			@endif
        </div>
	</div>
	
   
    <div class="row">
        <div class="col-lg-6 margin-tb">
			<div class="panel-group" style="margin-bottom: 5px;">
                <div class="panel mt-3 panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Charity Order</h4>
                    </div>
					<div class="panel-body">
						<table class="table table-bordered table-striped">
							<tr>
								<th>Order ID</th>
								<th>Customer</th>
								<th>Email</th>
								<th>Amount</th>
								<!--th>Action</th-->
							</tr>
							@foreach ($charityOrder as $data )
								<tr>
									<td>{{$data['orderData']['amount']}}</td>
									<td>{{$data['userData']['name']}}</td>
									<td>{{$data['userData']['email']}}</td>
									<td>{{$data['orderData']['amount']}}</td>
									<!--td>
										<button type="button" class="btn btn-default edit_charity" data-toggle="modal" data-id="{{$data['orderData']['id']}}" data-target="#updateCharityModal">Update</button>
									</td-->
								</tr>
							@endforeach
						</table>
						
						{{ $charityoOrderPagination->links() }}
                    </div>
                </div>
            </div>
		</div>
	</div>
	
	<div id="createCharityModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Create Charity</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="{{url('/charity/store')}}" method="POST">
					@csrf
					
                    <div class="modal-body">
                        <div class="form-group">
							<strong>Charity Name</strong>
							<input type='text' class="form-control" name="name" id="charity_name" required/>
                        </div>
						<div class="form-group">
							<strong>Contact No</strong>
							<input type='text' class="form-control" name="contact_no" id="contact_no" required/>
                        </div>
						<div class="form-group">
							<strong>Email</strong>
							<input type='text' class="form-control" name="email" id="email" required/>
						</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Save</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
	
@endsection