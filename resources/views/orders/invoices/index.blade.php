@extends('layouts.app')

@section('content')



<div class="table-responsive" style="margin-top:20px;">
      <table class="table table-bordered" style="border: 1px solid #ddd;">
        <thead>
          <tr>
            <th>Date</th>
            <th>Invoice Number</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($invoices as $key => $invoice)
            <tr>
              <td>{{ $invoice->invoice_date }}</td>
              <td>{{ $invoice->invoice_number }}</td>
              <td>
              <a class="btn btn-image send-invoice-btn" data-id="{{ $invoice->id }}">
                    <img title="Resend Invoice" src="/images/purchase.png" />
              </a>
                <a title="Edit Invoice" data-toggle="modal" data-target="#editInvoice" class="btn btn-image edit-invoice-btn" href="{{ route('order.edit.invoice',$invoice->id) }}">
                    <i class="fa fa-edit"></i>
                </a> 
                <a title="View Invoice" class="btn btn-image" href="{{ route('order.view.invoice',$invoice->id) }}">
                    <!-- <i class="fa fa-edit"></i> -->
                    <img title="View Invoice" src="/images/view.png" />
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      {{$invoices->links()}}
</div>
@include("partials.modals.edit-invoice-modal")

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
          url: "/order/"+$this.data("id")+"/mail-invoice",
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


    $(document).on("click",".edit-invoice-btn",function(e){
       e.preventDefault();
       var $this = $(this);
       $.ajax({
          url: "/order/"+$this.data("id")+"/edit-invoice",
          type: "get"
        }).done(function(response) {
           $("#edit-invoice-content").html(response); 
        }).fail(function(errObj) {
           $("#editInvoice").hide();
        });
    });
  </script>
@endsection