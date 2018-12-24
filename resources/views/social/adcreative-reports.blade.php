@extends('layouts.app')


@section('content')
<div class="row">
	<div class="col-lg-12 margin-tb">
		<div class="pull-left">
			<h2 class="ml-4">AdCreative Reports <h2>
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
								<th>ACC ID</th>
								<th>ACC Name</th>
								<th>Ad ID</th>
								<th>Ad Name</th>
								<th>Campaign</th>
								<th>Adset ID</th>
								<th>Adset Name</th>
								<th>CPP</th>
								<th>CPM</th>
								<th>CPC</th>
								<th>CTR</th>
								<th>Clicks</th>
								<th>Unique Clicks</th>
								<th>Spend</th>
								<th>Reach</th>
								<th>Impressions</th>
								<th>Frequency</th>
								<th>Cost P/Result</th>
								<th>Ends</th>
							</tr>
						</thead>
						<tbody>

							@if(isset($resp->data))
							@php $i=1; @endphp
							@foreach($resp->data as $data)
							
							@if(isset($data->insights->data) && !empty($data->insights->data))
							
							@foreach($data->insights->data as $insight)
							<tr>
								<td>{{$i++}}</td>
								<td>{{(isset($insight->account_id))?$insight->account_id:''}}</td>
								<td>{{(isset($insight->account_name))?$insight->account_name:''}}</td>
								<td>{{(isset($insight->ad_id))?$insight->ad_id:''}}</td>
								<td>{{(isset($insight->ad_name))?$insight->ad_name:''}}</td>
								<td>{{(isset($insight->campaign_name))?$insight->campaign_name:''}}</td>
								<td>{{(isset($insight->adset_id))?$insight->adset_id:''}}</td>
								<td>{{(isset($insight->adset_name))?$insight->adset_name:''}}</td>
								<td>{{(isset($insight->cpp))?$insight->cpp:''}}</td>
								<td>{{(isset($insight->cpm))?$insight->cpm:''}}</td>
								<td>{{(isset($insight->cpc))?$insight->cpc:''}}</td>
								<td>{{(isset($insight->ctr))?$insight->ctr:''}}</td>
								<td>{{(isset($insight->clicks))?$insight->clicks:''}}</td>
								<td>{{(isset($insight->unique_clicks))?$insight->unique_clicks:''}}</td>
								<td>{{(isset($insight->spend))?$insight->spend:''}}</td>
								<td>{{(isset($insight->reach))?$insight->reach:''}}</td>
								<td>{{(isset($insight->impressions))?$insight->impressions:''}}</td>
								<td>{{(isset($insight->frequency))?$insight->frequency:''}}</td>
								<td>{{(isset($insight->cost_per_unique_click))?$insight->cost_per_unique_click:''}}</td>
								<td>{{(isset($insight->date_stop))?$insight->date_stop:''}}</td>
								
							</tr>
							@endforeach
							@endif
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
												<form method="post" action="{{route('social.adCreative.paginate')}}">
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
												<form method="post" action="{{route('social.adCreative.paginate')}}">
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