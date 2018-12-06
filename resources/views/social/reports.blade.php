@extends('layouts.app')


@section('content')
<div class="row">
	<div class="col-lg-12 margin-tb">
		<div class="pull-left">
			<h2 class="ml-4">Ad Reports <h2>
			</div>
		</div>
	</div>


	@if ($message = Session::get('message'))
	<div class="alert alert-success">
		<p>{{ $message }}</p>
	</div>
	@endif

	<div class="container-fluid mt-3">
		<div class="row">
			
			<div class="col-md-12" style="overflow-x:auto;">
				<h2 class="text-info">Results</h2>
				<div class="content-section">
					<table class="table table-responsive table-hover table-bordered">
						<thead>
							<tr>
								<th>#</th>
								<th>ACC ID</th>
								<th>Campaign Name</th>
								<th>Delivery</th>
								<th>Adset</th>
								<th>Type</th>
								<th>Reach</th>
								<th>Impressions</th>
								<th>Amount</th>
								<th>Cost P/Result</th>
								<th>Ends</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>

							@if(isset($resp->data))
							@php $i=1; @endphp
							@foreach($resp->data as $data)
							<tr>
								<td>{{$i++}}</td>
								<td>{{isset($data->account_id)?$data->account_id:''}}</td>
								<td>{{isset($data->name)?$data->name:''}}</td>
								<td>{{isset($data->status)?$data->status:''}}</td>
								<td>{{isset($data->adsets->data[0]->name)?$data->adsets->data[0]->name:''}}</td>
								<td>{{isset($data->insights->data[0]->actions[0]->action_type)?$data->insights->data[0]->actions[0]->action_type:''}}</td>
								<td>
									{{isset($data->insights->data[0]->reach)?number_format($data->insights->data[0]->reach, 2):''}}

								</td>
								<td>
									{{isset($data->insights->data[0]->impressions)?number_format($data->insights->data[0]->impressions, 2):''}}
								</td>
								<td>
									{{isset($data->insights->data[0]->spend)?number_format($data->insights->data[0]->spend, 2):''}}
								</td>
								<td>{{isset($data->insights->data[0]->cost_per_unique_click)?number_format($data->insights->data[0]->cost_per_unique_click,2, '.', ''):''}}</td>
								<td>{{isset($data->insights->data[0]->date_stop)?$data->insights->data[0]->date_stop:''}}</td>

								<!-- Changing status of ad's -->

								<td>
									<div class="dropdown show">
										<a class="btn btn-info btn-sm dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											Status....
										</a>
										<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
											<a class="dropdown-item" href="{{route('social.report.ad.status',['ad_id'=>$data->id,'status'=>'ACTIVE'])}}">
												ACTIVE
											</a>
											<a class="dropdown-item" href="{{route('social.report.ad.status',['ad_id'=>$data->id,'status'=>'PAUSED'])}}">
												PAUSED
											</a>
											<a class="dropdown-item" href="{{route('social.report.ad.status',['ad_id'=>$data->id,'status'=>'ARCHIVED'])}}">
												ARCHIVED
											</a>
											<a class="dropdown-item" href="{{route('social.report.ad.status',['ad_id'=>$data->id,'status'=>'DELETED'])}}">
												DELETED
											</a>
										</div>
									</div>
								</td>
								<!-- End of changing status of ad's -->

							</tr>
							@endforeach
							@endif

						</tbody>
					</table>
				</div>
			</div>
			
		</div>
	</div>





	@endsection