@extends('layouts.app')

@section('content')
<div class="table-responsive" style="margin-top:20px;">
      <table class="table table-bordered" style="border: 1px solid #ddd;">
        <thead>
          <tr>
            <th>Date</th>
            <th>Invoice Number</th>
            <th>Client Name</th>
            <th>Country</th>
            <th>Currency</th>
            <th>Amount</th>
            <th>Site Name</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($orders as $key => $order)
            <tr>
              <td>{{ Carbon\Carbon::parse($order->order_date)->format('d-m-Y') }}</td>
              <td>{{ $order->order_id }}</td>
              <td>{{ $order->client_name }}</td>

              <td>
                @if ($order->customer)
                {{ $order->customer->country}}
                @endif
              </td>
              <td>
                {{$order->currency}}
              </td>
              <td> {{ count($order->order_product) > 0 ? $order->order_product->sum('product_price') : 0 }} </td>
              <td>
                @if ($order->customer)
                  @if ($order->customer->store_website_id)
                  {{ $order->customer->store_website->website}}
                  @endif
                @endif
              </td>
              <td>
              <a class="btn btn-image send-invoice-btn" data-id="{{ $order->id }}" href="{{ route('order.show',$order->id) }}">
                    <img title="Resend Invoice" src="/images/purchase.png" />
                </a>
                <a title="PRINT Invoice" class="btn btn-image preview-invoice-btn" href="{{ route('order.generate.invoice',$order->id) }}">
                    <i class="fa fa-print"></i>
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      {{$orders->links()}}
</div>

<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
   </div>

<script>
 $(document).on("click",".send-invoice-btn",function(e){
       e.preventDefault();
       var $this = $(this);
       $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "/order/"+$this.data("id")+"/send-invoice",
          type: "get",
          beforeSend: function() {
            $("#loading-image").show();
          }
        }).done(function(response) {
           if(response.code == 200) {
             toastr['success'](response.message);
           }else{
             toastr['error'](response.message);
           }
           $("#loading-image").hide(); 
        }).fail(function(errObj) {
           $("#loading-image").hide();
        });
    });
  </script>
@endsection