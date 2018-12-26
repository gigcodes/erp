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

<div id="exTab2" class="container">
  <ul class="nav nav-tabs">
    <li class="active">
      <a href="#1" data-toggle="tab">Customer Info</a>
    </li>
    <li>
      <a href="#2" data-toggle="tab">Leads</a>
    </li>
    <li><a href="#3" data-toggle="tab">Orders</a>
    </li>
  </ul>
</div>

<div class="tab-content ">
  <div class="tab-pane active mt-3" id="1">
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

        <div class="form-group">
          <strong>Instagram Handle:</strong> {{ $customer->instahandler }}
        </div>
      </div>
    </div>
  </div>

  <div class="tab-pane mt-3" id="2">
    @if (count($customer->leads) > 0)
      <ul>
        @foreach ($customer->leads as $lead)
        <li><a href="{{ route('leads.show', $lead->id) }}" target="_blank">{{ $lead->id }}</a></li>
        @endforeach
      </ul>
    @else
      There are no leads for this customer
    @endif
  </div>

  <div class="tab-pane mt-3" id="3">
    @if (count($customer->orders) > 0)
      <ul>
        @foreach ($customer->orders as $order)
        <li><a href="{{ route('order.show', $order->id) }}" target="_blank">{{ $order->id }}</a></li>
        @endforeach
      </ul>
    @else
      There are no orders for this customer
    @endif
  </div>
</div>



    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

    <script type="text/javascript">
      $('#completion-datetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm'
      });
    </script>

    @endsection
