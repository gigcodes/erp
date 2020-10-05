@extends('layouts.app')

@section('title', 'Shipment List')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
@if(session()->has('success'))
    <div class="col-lg-12 alert alert-success">
        {{ session()->get('success') }}
    </div>
@endif
@if(session()->has('errors'))
    @if(is_array(session()->get('errors'))) 
        @foreach(session()->get('errors') as $err)
            <div class="col-lg-12 alert alert-danger">
                {{ $err }}
            </div>
        @endforeach
    @else
        <div class="col-lg-12 alert alert-danger">
            {{ session()->get('errors') }}
        </div>
    @endif
@endif
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Shipment List</h2>
    </div>
</div>
<div class="infinite-scroll">
    <div class="row col-md-12">
        <div class="col-md-4">
            <button class="btn btn-secondary add-shipment" data-target="#addShipment" data-toggle="modal">+</button>
        </div>
    </div>
    <form method="get" action="">
        <div class="row col-md-12">
            <div class="col-md-3">
                <input type="text" placeholder="AWB" name="awb" value="{{ @$_REQUEST['awb'] }}"/>
            </div>
            <div class="col-md-3">
                <input type="text" placeholder="Destination" name="destination" value="{{ @$_REQUEST['destination'] }}"/>
            </div>
            <div class="col-md-3">
                <input type="text" placeholder="Consignee" name="consignee" value="{{ @$_REQUEST['consignee'] }}"/>
            </div>
            <div class="col-md-3">
                <button class="btn btn-image">
                    <img src="https://erp.amourint.com/images/search.png" alt="Search" style="cursor: default;">
                </button>
            </div>
        </div>
    </form>

	<div class="table-responsive mt-3">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>AWB number</th>
            <th>Customer name</th>
            <th>Destination</th>
            <th>Shipped Date</th>
            <th>Current Status</th>
            <th>Weight of Shipment</th>
            <th>Dimensions</th>
            <th>Volume Weight</th>
            <th>Cost of Shipment</th>
            <th>Duty Cost</th>
            <th>Location</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
            @forelse ($waybills_array as $key => $item)
                <tr>
                    <td>{{ @$item->awb }}</td>
                    <td>{{ @$item->order->customer->name ?? @$item->customer->name}}</td>
                    <td>{{ @$item->order->customer->address ?? @$item->customer->address }}</td>
                    <td>{{ ($item->created_at) ? date('d-m-Y', strtotime($item->created_at)) : '' }}</td>
                    <td>{{ @$item->order->order_status }}</td>
                    <td>{{ @$item->actual_weight }}</td>
                    <td>{{ @$item->dimension }}</td>
                    <td>{{ @$item->volume_weight??'N/A' }}</td>
                    <td>{{ @$item->cost_of_shipment?? 'N/A' }}</td>
                    <td>{{ @$item->duty_cost?? 'N/A' }}</td>
                    <td>{{ (@$item->waybill_track_histories->count() > 0)? @$item->waybill_track_histories->last()->location : "" }}</td>
                    <td>
                        <button type="button" class="btn btn-image" id="send_email_btn" data-order-id="{{ $item->order_id }}" title="Send Email"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
                        <a class="btn" href="javascript:void(0);" id="view_mail_btn" title="View communication sent" data-order-id="{{ $item->order_id }}">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </a>
                        <a class="btn" href="javascript:void(0);" id="waybill_track_history_btn" title="Way Bill Track History" data-waybill-id="{{ $item->id }}">
                            <i class="fa fa-list" aria-hidden="true"></i>
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
@include('shipment.partial.generate-shipping')

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

$(document).on('click', '#waybill_track_history_btn', function() {
    var waybillId = $(this).data('waybill-id');
    $.ajax({
        url: "{{ route('shipment/view/sent/email') }}",
        type: 'GET',
        data: {'waybill_id': waybillId},
        success: function(data) {
            $("#view_track_body").html(data);
            $('#view_waybill_track_histories').modal('show');
        }
    });
});

$(document).on('click', '#send_email_btn', function() {
    var orderId = $(this).data('order-id');
    $("#order_id").val(orderId);
    $('#send_email_modal').modal('show');
});

$(document).on("change","#customer_name",function() {
    var cus_id = $(this).val();
    if(cus_id == ''){
        $('.input_customer_city').val('');
        $('.input_customer_phone').val('');
        $('.input_customer_address1').val('');
        $('.input_customer_pincode').val('');
    }
    $.ajax({
        url: "{{ url('shipment/customer-details') }}"+'/'+cus_id,
        type: "GET"
    }).done( function(response) {
        if(response.status == 1)
        {
            $('.input_customer_city').val(response.data.city);
            let countryField = $('.input_customer_country');
            let countryOptionsField = countryField.find('option')
            if (countryOptionsField && countryOptionsField.length){
                for (let i in countryOptionsField){
                    if (countryOptionsField[i].innerText && countryOptionsField[i].innerText.toLowerCase() === response.data.country.toLowerCase()){
                        countryField.val(countryOptionsField[i].value)
                    }
                }
            }
            $('.input_customer_phone').val(response.data.phone);
            $('.input_customer_address1').val(response.data.address);
            $('.input_customer_pincode').val(response.data.pincode);
        }
    })
});

$(document).on("change", '#email_name', function(){
   var template_name = $(this).val();
    $.ajax({
        url: "{{ url('shipment/get-templates-by-name/') }}"+'/'+template_name,
        type: "GET"
    }).done( function(response) {
        if(response.status == 1)
        {
            $('#templates').empty();
            for(var i = 0; i <response.data.length; i++){
                $('#templates').append('<option value="'+response.data[i]['id']+'">'+response.data[i]['mail_tpl']+'</option>');
            }
        }
    })
});
$(document).on("click", '.add-shipment', function(){
    $('#generate-shipment-form .form-error').html(''),
    $('.any-message').html('');
});

</script>
@endsection