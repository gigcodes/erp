@extends('layouts.app')

@section('title', 'Inventory suppliers')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
<style>
.ajax-loader{
    position: fixed;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.2);
    z-index: 1060;
}
.inner_loader {
	top: 30%;
    position: absolute;
    left: 40%;
    width: 100%;
    height: 100%;
}
.pd-5 {
  padding:5px !important;
}
.pd-3 {
  padding:3px !important;
}
.status-select-cls .multiselect {
  width:100%;
}
.btn-ht {
  height:30px;
}
.status-select-cls .btn-group {
  width:100%;
  padding: 0;
}
.table.table-bordered.order-table a{
color:black!important;
}
.fa-info-circle{
    padding-left:10px;
    cursor: pointer;
}
table tr td {
  word-wrap: break-word;
}
.fa-list-ul{
    cursor: pointer;
}

.fa-upload{
    cursor: pointer;
}
.fa-refresh{
    cursor: pointer;
    color:#000;
}
</style>
@endsection

@section('large_content')
	<div class="ajax-loader" style="display: none;">
		<div class="inner_loader">
		<img src="{{ asset('/images/loading2.gif') }}">
		</div>
	</div>

    <div class="row">
        <div class="col-12" style="padding:0px;">
            <h2 class="page-heading">Purchase Product Orders List</h2>
        </div>
         <div class="col-10" style="padding-left:0px;">
            <div >
                <form class="form-inline" action="" method="GET">
                    <div class="form-group col-md-2 pd-3">
                        
                        <input style="width:100%;" name="order_id" type="text" class="form-control" value="{{ isset($_REQUEST['order_id']) ? $_REQUEST['order_id'] : '' }}" placeholder="Order id">
                    </div>
                    <div class="form-group col-md-3 pd-3">
                        <select class="form-control globalSelect2" data-ajax="{{ route('select2.suppliers') }}" style="width:100%" name="supplier_id" data-placeholder="Search Supplier By Name.." >
                        
                        @if(isset($_REQUEST['supplier_id']))
                        @if ($suppliers_all)        
                            <option value="{{ $suppliers_all->id }}" selected>{{ $suppliers_all->supplier }}</option>
                        @endif

                        @endif
                        <option ></option>
                                 
                        </select>
                    </div>
                    <div class="form-group col-md-1 pd-3">
                        <button type="submit" class="btn btn-image ml-3"><img src="{{asset('images/filter.png')}}" /></button>

                        <a href="{{ route('purchaseproductorders.list') }}" class="fa fa-refresh" aria-hidden="true"></a>
                    </div>
                </form>
            </div>
        </div>
    </div>	


    <div class="row">
        <div class="infinite-scroll" style="width:100%;">
	        <div class="table-responsive mt-2">
                <table class="table table-bordered order-table" style="border: 1px solid #ddd !important; color:black;table-layout:fixed">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="5%">Order Id</th>
                            <!-- <th width="8%">Product</th>
                            <th width="8%">SKU</th>
                            <th width="8%">Brand</th> -->
                            <th width="8%">Supplier</th>
                            <th width="5%">MRP</th>
                            <th width="5%">Discounted Price</th>
                            <th width="5%">Special Price</th>
                            <th width="7%">Invoice No</th>
                            <th width="7%">Payment Details</th>
                            <th width="5%">Cost Details</th>
                            <th width="6%">Landed cost of the product</th>
                            <th width="6%">Status</th>
                            <th width="5%">Action</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                    @foreach($purchar_product_order as $key => $value)
                        <tr class="row_{{$value->pur_pro_id}}">
                            <td>{{$key+1}}</td>
                            <td>{{$value->order_id}}</td>
                            <!-- <td>{{$value->name}}</td>
                            <td>{{$value->sku}}</td>
                            <td>{{$value->brand_name}}</td> -->
                            <td>{{$value->supplier}}</td>
                            <td>
                                <input type="text" name="product_mrp" placeholder="MRP" class="form-control mb-3 product_mrp" value="{{ $value->mrp_price ?? $value->mrp ?? '' }}">
                                <button style="display: inline;width: 5%" class="btn btn-sm btn-image add_mrp" data-id="{{$value->pur_pro_id}}"><img src="/images/filled-sent.png"></button>
                                <i class="fa fa-info-circle view_log" title="MRP Logs" aria-hidden="true" data-id="{{$value->pur_pro_id}}" data-name="MRP"></i>
                            </td>
                            <td>
                                <input type="text" name="product_discount_price" placeholder="Discounted Price" class="form-control mb-3 product_discount_price" value="{{ $value->discount_price ?? $value->price_discounted ?? '' }}">
                                <button style="display: inline;width: 5%" class="btn btn-sm btn-image add_discount_price" data-id="{{$value->pur_pro_id}}"><img src="/images/filled-sent.png"></button>
                                <i class="fa fa-info-circle view_log" title="Discounted Price Logs" aria-hidden="true" data-id="{{$value->pur_pro_id}}" data-name="Discounted Price"></i>
                            </td>
                            <td>
                            @php
                            $discount_amt = ( $value->discount_price ?? $value->price_discounted ?? 0 );
                            $special_amt = ($value->special_price ?? $value->price_special ?? 0);

                            $final_special_amt = $special_amt - $discount_amt;
                            @endphp
                                <input type="text" name="product_special_price" placeholder="Special Price" class="form-control mb-3 product_special_price" value="{{ $final_special_amt }}">
                                <button style="display: inline;width: 5%" class="btn btn-sm btn-image add_special_price" data-id="{{$value->pur_pro_id}}"><img src="/images/filled-sent.png"></button>
                                <i class="fa fa-info-circle view_log" title="Special Price Logs" aria-hidden="true" data-id="{{$value->pur_pro_id}}" data-name="Special Price"></i>
                            </td>
                            
                            <td>
                                <input type="text" name="invoice_no" placeholder="Add Invoice No." class="form-control mb-3 invoice_no" value="{{ $value->invoice ?? '' }}">
                                <button style="display: inline;width: 5%" class="btn btn-sm btn-image add_invoice" data-id="{{$value->pur_pro_id}}"><img src="/images/filled-sent.png"></button>
                                <i class="fa fa-info-circle view_log" title="Invoice Logs" aria-hidden="true" data-id="{{$value->pur_pro_id}}" data-name="Invoice"></i>
                            </td>
                            <td>
                                <input type="text" name="payment_currency" placeholder="Currency" class="form-control mb-3 payment_currency" value="{{ $value->payment_currency ?? '' }}">
                                <input type="text" name="payment_amount" placeholder="Amount" class="form-control mb-3 payment_amount" value="{{ $value->payment_amount ?? '' }}">
                                <input type="text" name="payment_mode" placeholder="Mode" class="form-control mb-3 payment_mode" value="{{ $value->payment_mode ?? '' }}">
                                <button style="display: inline;width: 5%" class="btn btn-sm btn-image add_payment_details" data-id="{{$value->pur_pro_id}}"><img src="/images/filled-sent.png"></button>
                                <i class="fa fa-info-circle view_log" title="Payment Details Logs" aria-hidden="true" data-id="{{$value->pur_pro_id}}" data-name="Payment Details"></i>
                            </td>
                            <td>
                                <input type="text" name="shipping_cost" placeholder="Shipping Costs" class="form-control mb-3 shipping_cost" value="{{ $value->shipping_cost ?? '' }}">
                                <input type="text" name="duty_cost" placeholder="Duty Costs" class="form-control mb-3 duty_cost" value="{{ $value->duty_cost ?? '' }}">
                                <button style="display: inline;width: 5%" class="btn btn-sm btn-image add_cost_details" data-id="{{$value->pur_pro_id}}"><img src="/images/filled-sent.png"></button>
                                <i class="fa fa-info-circle view_log" title="Cost Logs" aria-hidden="true" data-id="{{$value->pur_pro_id}}" data-name="Cost"></i>
                            </td>
                            <td>
                                @php
                                $purchase_price = $value->mrp_price ?? $value->mrp - $value->price_discounted / 1.22;
                                @endphp
                                {{-- ( {{$value->mrp_price ?? $value->mrp ?? 0}} - {{$value->price_discounted}} / 1.22 ) + {{$value->shipping_cost ?? 0}}  + {{$value->duty_cost ?? 0}}  --}}
                                 {{ $purchase_price + $value->shipping_cost + $value->duty_cost }} 
                            </td>
                            <td>
                                <select class="form-control change_status" name="status" id="status" data-id="{{$value->pur_pro_id}}">
                                    <option value="">Select</option>
                                    <option {{$value->status == 'in_stock' ? 'selected' : ''}} value="in_stock">In Stock</option>
                                    <option {{$value->status == 'out_stock' ? 'selected' : ''}} value="out_stock">Out Stock</option>
                                    <option {{$value->status == 'panding' ? 'selected' : ''}} value="panding">Panding</option>
                                    <option {{$value->status == 'complete' ? 'selected' : ''}} value="complete">Complete</option>
                                </select>
                                <i class="fa fa-info-circle view_log" title="Status Logs" aria-hidden="true" data-id="{{$value->pur_pro_id}}" data-name="Status"></i>
                            </td>
                            <td>
                                <i class="fa fa-list-ul view_full_order" data-id="{{$value->pur_pro_id}}" data-order-id="{{$value->order_pro_order_id}}" aria-hidden="true"></i>
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
	        </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center">
            {{ $purchar_product_order->appends($request->except('page'))->links() }}.
        </div>
    </div>

    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
   </div>

   <!-- Modal -->
    <div class="modal fade log_modal" id="log_modal" tabindex="-1" role="dialog" aria-labelledby="log_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Log Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive mt-2">
                    <table class="table table-bordered log-table" style="border: 1px solid #ddd !important; color:black;table-layout:fixed">
                        <thead>
                            <tr>
                                <th width="30%">From</th>
                                <th width="30%">To</th>
                                <th width="20%">Created By</th>
                                <th width="20%">Created By</th>
                                
                            </tr>
                        </thead>
                        
                        <tbody class="log_data" id="log_data">
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
            
            </div>
            </div>
        </div>
    </div>


    <!--Upload Data Modal -->
    <div class="modal fade" id="upload_data_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Upload</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="order_product_id" class="order_product_id" value="" />
                <input type="hidden" name="order_id" class="order_id" value="" />
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Upload :</strong>
                        <input type="file" enctype="multipart/form-data" name="file[]" class="form-control upload_file_data" name="image" multiple/>
                        
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary upload_file_btn">Save</button>
        </div>
        </div>
    </div>
    </div>

@endsection
@section('scripts')

<script type="text/javascript">
    $(".add_invoice").click(function (e) {
        var purchase_pro_id = $(this).data('id');
        var invoice = $(this).siblings('.invoice_no').val();

        if(invoice == ''){
            toastr['error']('Invoice No. is Required');
            return false;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('purchaseproductorders.update') }}",
            data: {
				_token: "{{ csrf_token() }}",
                from : 'invoice',
                message : invoice,
				purchase_pro_id: purchase_pro_id,
            },
            dataType : "json",
            success: function (response) {
                if(response.code == 200) {
                    toastr['success'](response.messages);
                }else{
                    toastr['error'](response.messages);
                }
            },
            error: function () {
                toastr['error']('Message not sent successfully!');
            }
        });
    });

    $(".add_payment_details").click(function (e) {
        var purchase_pro_id = $(this).data('id');
        var payment_currency = $(this).siblings('.payment_currency').val();
        var payment_amount = $(this).siblings('.payment_amount').val();
        var payment_mode = $(this).siblings('.payment_mode').val();

        if(payment_currency == ''){
            toastr['error']('Payment Currency is Required');
            return false;
        }else if(payment_amount == ''){
            toastr['error']('Payment Amount is Required');
            return false;
        }
        else if(payment_mode == ''){
            toastr['error']('Payment Mode is Required');
            return false;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('purchaseproductorders.update') }}",
            data: {
				_token: "{{ csrf_token() }}",
                from : 'payment_details',
                payment_currency : payment_currency,
                payment_amount : payment_amount,
                payment_mode : payment_mode,
				purchase_pro_id: purchase_pro_id,
            },
            dataType : "json",
            success: function (response) {
                if(response.code == 200) {
                    toastr['success'](response.messages);
                }else{
                    toastr['error'](response.messages);
                }
            },
            error: function () {
                toastr['error']('Message not sent successfully!');
            }
        });
    });

    $(".add_cost_details").click(function (e) {
        var purchase_pro_id = $(this).data('id');
        var shipping_cost = $(this).siblings('.shipping_cost').val();
        var duty_cost = $(this).siblings('.duty_cost').val();

        if(shipping_cost == ''){
            toastr['error']('Shipping Costs is Required');
            return false;
        }else if(duty_cost == ''){
            toastr['error']('Duty Costs is Required');
            return false;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('purchaseproductorders.update') }}",
            data: {
				_token: "{{ csrf_token() }}",
                from : 'costs',
                shipping_cost : shipping_cost,
                duty_cost : duty_cost,
				purchase_pro_id: purchase_pro_id,
            },
            dataType : "json",
            success: function (response) {
                if(response.code == 200) {
                    toastr['success'](response.messages);
                }else{
                    toastr['error'](response.messages);
                }
            },
            error: function () {
                toastr['error']('Message not sent successfully!');
            }
        });
    });

    $(".change_status").change(function(){
        var purchase_pro_id = $(this).data('id');
        var status = $(this).val();
        
        if(status == '')
        {
            toastr['error']('Status is Required');
            return false;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('purchaseproductorders.update') }}",
            data: {
				_token: "{{ csrf_token() }}",
                from : 'status',
                status : status,
				purchase_pro_id: purchase_pro_id,
            },
            dataType : "json",
            success: function (response) {
                if(response.code == 200) {
                    toastr['success'](response.messages);
                }else{
                    toastr['error'](response.messages);
                }
            },
            error: function () {
                toastr['error']('Message not sent successfully!');
            }
        });
    });

    $(".add_mrp").click(function (e) {
        var purchase_pro_id = $(this).data('id');
        var mrp = $(this).siblings('.product_mrp').val();

        if(mrp == ''){
            toastr['error']('MRP is Required');
            return false;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('purchaseproductorders.update') }}",
            data: {
				_token: "{{ csrf_token() }}",
                from : 'mrp',
                mrp : mrp,
				purchase_pro_id: purchase_pro_id,
            },
            dataType : "json",
            success: function (response) {
                if(response.code == 200) {
                    toastr['success'](response.messages);
                }else{
                    toastr['error'](response.messages);
                }
            },
            error: function () {
                toastr['error']('Message not sent successfully!');
            }
        });
    });

    $(".add_discount_price").click(function (e) {
        var purchase_pro_id = $(this).data('id');
        var discount_price = $(this).siblings('.product_discount_price').val();

        if(discount_price == ''){
            toastr['error']('Discount Price is Required');
            return false;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('purchaseproductorders.update') }}",
            data: {
				_token: "{{ csrf_token() }}",
                from : 'discount_price',
                discount_price : discount_price,
				purchase_pro_id: purchase_pro_id,
            },
            dataType : "json",
            success: function (response) {
                if(response.code == 200) {
                    toastr['success'](response.messages);
                }else{
                    toastr['error'](response.messages);
                }
            },
            error: function () {
                toastr['error']('Message not sent successfully!');
            }
        });
    });

    $(".add_special_price").click(function (e) {
        var purchase_pro_id = $(this).data('id');
        var special_price = $(this).siblings('.product_special_price').val();

        if(special_price == ''){
            toastr['error']('Special Price is Required');
            return false;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('purchaseproductorders.update') }}",
            data: {
				_token: "{{ csrf_token() }}",
                from : 'special_price',
                special_price : special_price,
				purchase_pro_id: purchase_pro_id,
            },
            dataType : "json",
            success: function (response) {
                if(response.code == 200) {
                    toastr['success'](response.messages);
                }else{
                    toastr['error'](response.messages);
                }
            },
            error: function () {
                toastr['error']('Message not sent successfully!');
            }
        });
    });

    $(".view_log").click(function (e) {
        var purchase_pro_id = $(this).data('id');
        var header_name = $(this).data('name');

        $.ajax({
            type: "GET",
            url: "{{ route('purchaseproductorders.logs') }}",
            data: {
				_token: "{{ csrf_token() }}",
                header_name : header_name,
				purchase_pro_id: purchase_pro_id,
            },
            dataType : "json",
            success: function (response) {

                var html_content = ''
                $.each( response.log_data, function( key, value ) {
                    html_content += '<tr>';
                    html_content += '<td>'+ value.replace_from+'</td>';
                    html_content += '<td>'+ value.replace_to+'</td>';
                    html_content += '<td>'+ value.name+'</td>';
                    html_content += '<td>'+ value.log_created_at+'</td>';
                    html_content += '</tr>';
                });

                $("#log_data").html(html_content);
                $('#log_modal').modal('show');
            },
            error: function () {
                // toastr['error']('Message not sent successfully!');
            }
        });
    });

    $(".view_full_order").click(function (e) {
        var purchase_pro_id = $(this).data('id');
        var order_id = $(this).data('order-id');

        var order_row = "row_order_data_"+purchase_pro_id;

        var row_cls = $("tr").hasClass(order_row);

        if(row_cls == true)
        {
            $("."+order_row).remove();
            return false;
        }

        
        $.ajax({
            type: "GET",
            url: "{{ route('purchaseproductorders.orderdata') }}",
            data: {
				_token: "{{ csrf_token() }}",
                order_id : order_id,
				purchase_pro_id: purchase_pro_id,
            },
            dataType : "json",
            success: function (response) {

                var html_content = '';
                html_content += '<tr class="expand-row-10 row_order_data_'+purchase_pro_id+'">';
                html_content += '<td colspan="12" id="product-list-data-10"><center><p>ORDERED PRODUCTS</p></center>';
                html_content += '<div class="table-responsive mt-2">';
                html_content += '<table class="table table-bordered order-table" style="border: 1px solid #ddd !important; color:black;table-layout:fixed">';
                html_content += '<thead>';
                html_content += '<tr>';
                html_content += '<th width="10%">#</th>';
                html_content += '<th width="30%">Name</th>';
                html_content += '<th width="20%">SKU</th>';
                html_content += '<th width="20%">Brand</th>';
                html_content += '<th width="20%">Action</th>';
                html_content += '</tr>';
                html_content += '</thead>';
                html_content += '<tbody>';

                $.each( response.order_data, function( key, value ) {
                    var index = key + 1;
                    html_content += '<tr class="supplier-10">';
                    html_content += '<td>'+index+'</td>';
                    html_content += '<td>'+value.name+'</td>';
                    html_content += '<td>'+value.sku+'</td>';
                    html_content += '<td>'+value.brands_name+'</td>';
                    html_content += '<td><i class="fa fa-upload upload_data_btn" data-id="'+value.order_products_id+'" data-order-id="'+order_id+'" aria-hidden="true"></i></td>';
                    html_content += '</tr>';

                });
                html_content += '</tbody>';
                html_content += '</table>';
                html_content += '</div>';
                html_content += '</td>';
                html_content += '</tr>';

                $(".row_"+purchase_pro_id).after(html_content);
            },
            error: function () {
                // toastr['error']('Message not sent successfully!');
            }
        });
    });

    $(document).on("click",".upload_data_btn",function(e) {

        var order_product_id = $(this).data('id');
        var order_id = $(this).data('order-id');
        $(".order_product_id").val(order_product_id);
        $(".order_id").val(order_id);
        
        $('#upload_data_modal').modal('show');
    });

    $(document).on("click",".upload_file_btn",function(e) {

        var fd = new FormData();
        var order_product_id = $(".order_product_id").val();
        var order_id = $(".order_id").val();
        var files = $('.upload_file_data')[0].files;
        var fileArray = []

        if(files.length > 0 ){

            $.each(files,function(i,e){
				fd.append('file[]',e);
			})
            fd.append('order_product_id',order_product_id);
            fd.append('order_id',order_id);
            fd.append('_token',"{{ csrf_token() }}");

            
            
			$.ajax({
                url: '{{route("purchaseproductorders.saveuploads")}}',
                type: 'post',
                data: fd,
                // async: true,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('.ajax-loader').show();
                },
                success: function(response){
                    $('.ajax-loader').hide();
                    console.log(response)
                    toastr['success'](response.msg, 'Success');
                    $('#upload_data_modal').modal('hide');
                },
                error: function () {
                    $('.ajax-loader').hide();
                    toastr['error']('Data not Uploaded successfully!');
                }
			});

        }else{
            alert("Please select a file.");
        }
    });
</script>
@endsection