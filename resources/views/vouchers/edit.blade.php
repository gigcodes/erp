@extends('layouts.app')

@section('styles')
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection

@section('content')

<div class="row">
	<div class="col-lg-12 margin-tb">
		<h2 class="page-heading">Edit Cash Voucher</h2>
		<div class="pull-right">
			<a class="btn btn-secondary" href="{{ route('voucher.index') }}">Back</a>
		</div>
	</div>
</div>

@if ($message = Session::get('success'))
	<div class="alert alert-success">
		<p>{{ $message }}</p>
	</div>
@endif

@if ($errors->any())
	<div class="alert alert-danger">
		<strong>Whoops!</strong> There were some problems with your input.<br><br>
		<ul>
			@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
@endif

<div class="row">
	<div class="col-xs-12 col-md-8 col-md-offset-2">
		<form action="{{ route('voucher.update', $voucher->id) }}" method="POST">
			@csrf
			@method('PUT')

			<div class="form-group">
				<strong>Description:</strong>
				<textarea class="form-control" name="description" placeholder="Descripton" required>{{ $voucher->description }}</textarea>
				@if ($errors->has('description'))
					<div class="alert alert-danger">{{$errors->first('description')}}</div>
				@endif
			</div>

			<div class="form-group">
				<strong>Amount:</strong>
				<input type="number" class="form-control" name="amount" placeholder="10000" value="{{ $voucher->amount }}" required />
				@if ($errors->has('amount'))
					<div class="alert alert-danger">{{$errors->first('amount')}}</div>
				@endif
			</div>

			<div class="form-group">
				<strong>Date:</strong>
				<div class='input-group date' id='datetime'>
					<input type='text' class="form-control" name="date" value="{{ $voucher->date }}" required />

					<span class="input-group-addon">
						<span class="glyphicon glyphicon-calendar"></span>
					</span>
				</div>

				@if ($errors->has('date'))
					<div class="alert alert-danger">{{$errors->first('date')}}</div>
				@endif
			</div>

			<div class="form-group text-center">
				<button type="submit" class="btn btn-secondary">Update</button>
			</div>
		</form>
	</div>
</div>

@endsection

@section('scripts')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
	<script type="text/javascript">
		$('#datetime').datetimepicker({
			format: 'YYYY-MM-DD'
		});
	</script>
@endsection
