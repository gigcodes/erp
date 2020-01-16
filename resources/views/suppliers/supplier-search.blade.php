@extends('layouts.app')

@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
			<h2 class="page-heading">Suppliers Search</h2>
		</div>
	</div>
	<form action="{{url('/supplier-search')}}" method="GET">
		<div class="col-lg-3">
			<input class="form-control" type="text" name="supplier" placeholder="search names">
		</div>
		<div class="col-lg-3">
			<select name="brand" class="form-control">
				<option selected disabled>Select Brands</option>
				@foreach($brands as $brand)
					<option value="{{$brand->id}}">{{$brand->name}}</option>
				@endforeach
			</select>
		</div>
		<div class="col-lg-4">
			<button type="submit" class="btn btn-image">
				<img src="/images/filter.png">
			</button>
		</div>
	</form>
		<div class="col-lg-12" style="margin-top: 20px" >
		<h4>Search results for supplier-<b>{{app('request')->input('supplier')}}</b> brand-<b>{{$requestBrand}}</b> </h4>
	</div>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<th width="5%">S.no</th>
					<th width="30%">Suppliers</th>
					<th width="30%">Brands</th>
				</tr>
					@foreach($supplier as $key=>$value)
					<tr>
						<td>{{$key+1}}</td>
						<td>{{$value->supplier}}</td>
						<td>{{$value->brands}}</td>
					</tr>
					@endforeach
			</thead>
		</table>
		
	</div>
@endsection