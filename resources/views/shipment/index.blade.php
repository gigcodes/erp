@extends('layouts.app')

@section('title', 'Shipment List')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Shipment List</h2>
    </div>
</div>
<div class="infinite-scroll">
	<div class="table-responsive mt-3">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>AWB number</th>
            <th>Customer name</th>
            <th>Shipped Date</th>
            <th>Current Status</th>
            <th> Weight of Shipment</th>
            <th>Dimensions</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
            @forelse ($waybills_array as $key => $item)
                <tr>
                    <td>{{ $item->awb }}</td>
                    <td>{{ $item->order->customer->name }}</td>
                    <td>{{ ($item->created_at) ? date('d-m-Y', strtotime($item->created_at)) : '' }}</td>
                    <td>{{ $item->order->order_status }}</td>
                    <td>{{ $item->actual_weight }}</td>
                    <td>{{ $item->dimension }}</td>
                    <td>
                        <button type="button" class="btn btn-image" id="send_email_btn" data-order-id="{{ $item->order_id }}" title="Send Email"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
                        <a class="btn" href="javascript:void(0);" id="view_mail_btn" title="View communication sent" data-order-id="{{ $item->order_id }}">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr></tr>
            @endforelse
        </tbody>
      </table>

	{!! $waybills_array->appends(Request::except('page'))->links() !!}
	</div>
</div>

@include('shipment.partial.modal')

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script type="text/javascript">
$(".to-email, .cc-email, .bcc-email").select2({
    tags: true,
    tokenSeparators: [',', ' '],
    placeholder: {
        id: '-1', // the value of the option
        text: 'Type Email'
    },
    allowClear: true,
    "language": {
       "noResults": function(){
           return "Type email";
       }
    },
    escapeMarkup: function (markup) {
        return markup;
    }
});

$(document).on('click', '#view_mail_btn', function() {
    var orderId = $(this).data('order-id');
    $.ajax({
        url: "{{ route('shipment/view/sent/email') }}",
        type: 'GET',
        data: {'order_id': orderId},
        success: function(data) {
            $("#view_email_body").html(data);
            $('#view_sent_email_modal').modal('show');
        }
    });
});

$(document).on('click', '#send_email_btn', function() {
    var orderId = $(this).data('order-id');
    $("#order_id").val(orderId);
    $('#send_email_modal').modal('show');
});
</script>
@endsection