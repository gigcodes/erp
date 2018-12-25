@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">

<div class="row">
  <div class="col-lg-12 margin-tb">
    <div class="pull-left">
      <h2>Customer Page</h2>
    </div>
    <div class="pull-right">
      <a class="btn btn-secondary" href="{{ route('customer.index') }}">Back</a>
    </div>
  </div>
</div>


@if ($message = Session::get('success'))
<div class="alert alert-success">
  <p>{{ $message }}</p>
</div>
@endif

<div class="row">
  <div class="col-md-6 col-12">
    <div class="form-group">
      <strong>Name:</strong> {{ $customer->name }}
    </div>

    <div class="form-group">
      <strong>Email:</strong> {{ $customer->email }}
    </div>

    <div class="form-group">
      <strong>Phone:</strong> {{ $customer->phone }}
    </div>

  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

<script type="text/javascript">
  $('#completion-datetime').datetimepicker({
    format: 'YYYY-MM-DD HH:mm'
  });
</script>

@endsection
