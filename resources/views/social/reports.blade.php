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
			
			<div class="col-md-12" >
				<h2 class="text-info">Results</h2>
				<div class="content-section">
					<table class="table table-responsive table-hover table-bordered">
						<thead>
							<tr>
								<th>#</th>
								<th>Thumbnail</th>
								<th>ACC ID</th>
								<th>Ad Name</th>
								<th>Delivery</th>
								<th>Campaign</th>
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
								<td>
									<img class="img-responsive " width="80" height="80" src="{{isset($data->creative->image_url)?$data->creative->image_url:''}}" alt="Not found...">
								</td>
								<td>{{isset($data->account_id)?$data->account_id:''}}</td>
								<td>{{isset($data->name)?$data->name:''}}</td>
								<td>{{isset($data->status)?$data->status:''}}</td>
								<td>{{isset($data->campaign->name)?$data->campaign->name:''}}</td>
								<td>{{isset($data->adset->name)?$data->adset->name:''}}</td>
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

					<div class="container pull-left" style="overflow: hidden;">
						<div class="row">
							<div class="col-md-6 ml-aut mr-auto">
								<nav aria-label="Page navigation example">
									<ul class="pagination">
										<li class="page-item">
											<div class="col-md-4">
												@if(isset($resp->paging->previous))
												<form method="post" action="{{route('social.report.paginate')}}">
													@csrf
													<input type="hidden" value="{{$resp->paging->previous}}" name="previous">
													<input type="submit" value="Previous" name="submit" class="btn btn-info">
												</form>
												@endif
											</div>
										</li>
										<li class="page-item">
											<div class="col-md-4 ml-3">
												@if(isset($resp->paging->next))
												<form method="post" action="{{route('social.report.paginate')}}">
													@csrf
													<input type="hidden" value="{{$resp->paging->next}}" name="next">
													<input type="submit" value="Next" name="submit" class="btn btn-info">
												</form>
												@endif
											</div>
										</li>
										
									</ul>
								</nav>
							</div>
						</div>
					</div>
				</div>
			</div>
			
		</div>
	</div>





	@endsection