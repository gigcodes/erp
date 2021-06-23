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
            <h2 class="page-heading">Purchase Products | Suppliers</h2>
        </div>
           <div class="col-10" style="padding-left:0px;">
            <div >
            <form class="form-inline" action="/purchase-product/get-suppliers" method="GET">
                
                <div class="form-group col-md-3 pd-3">
                  <input style="width:100%;" name="term" type="text" class="form-control"
                         value="{{ isset($term) ? $term : '' }}"
                         placeholder="Search">
                </div>

          

                   <div class="form-group col-md-1 pd-3">
                <button type="submit" class="btn btn-image ml-3"><img src="{{asset('images/filter.png')}}" /></button>
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
            <th width="10%">Sl no</th>
            <th width="35%">Name</th>
            <th width="20%">Product Inquiry Count</th> <!-- Purpose : Product Inquiry Count -DEVTASK-4048 -->
            <th width="20%">Communication</th> <!-- Purpose : Add communication -DEVTASK-4236 -->
            <th width="15%">Action</th>
         </tr>
        </thead>

        <tbody>
			@foreach ($suppliers as $key => $supplier)
            <tr class="">
              <td>{{ ++$key }}</td>
              <td>{{ $supplier->supplier }}</td>
              <td>{{$supplier->inquiryproductdata_count}}</td><!-- Purpose : Product Inquiry Count -DEVTASK-4048 -->
              <!-- START - purpose : Add Communication -DEVTASK-4236 -->
              <td>
              @if($supplier->phone)
              <input type="text" name="message" id="message_{{$supplier->id}}" placeholder="whatsapp message..." class="form-control send-message" data-id="{{$supplier->id}}">
              

              <a type="button" class="btn btn-xs btn-image load-communication-modal"  data-object="supplier" data-load-type="text" data-all="1" title="Load messages" data-object="supplier" data-id="{{$supplier->id}}" ><img src="/images/chat.png" alt=""></a>
              @endif
              </td>
               <!-- END - DEVTASK-4236 -->
              <td>
              <a href="#"  data-type="order" data-id="{{$supplier->id}}" class="btn btn-xs btn-secondary product-list-btn" style="color:white !important;">
                Order
              </a>
              <a href="#"  data-type="inquiry" data-id="{{$supplier->id}}" class="btn btn-xs btn-secondary product-list-btn" style="color:white !important;">
                Inquiry
              </a>
              <button title="Select all products" type="button" class="btn btn-xs btn-secondary select-all-products btn-image no-pd" data-id="{{$supplier->id}}">
                <img src="/images/completed.png" style="cursor: default;"></button>
              
              </td>
            </tr>
            <tr class="expand-row-{{$supplier->id}} hidden">
                <td colspan="5" id="product-list-data-{{$supplier->id}}">
                
                </td>
            </tr>
           @endforeach
        </tbody>
      </table>
	</div>
    </div>
    </div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
   </div>


   <div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                    <input type="text" name="search_chat_pop"  class="form-control search_chat_pop" placeholder="Search Message" style="width: 200px;">
                    <input type="hidden" id="chat_obj_type" name="chat_obj_type">
                    <input type="hidden" id="chat_obj_id" name="chat_obj_id">
                    <button type="submit" class="btn btn-default downloadChatMessages">Download</button>
                </div>
                <div class="modal-body" style="background-color: #999999;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script src="{{ asset('/js/order-awb.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
  <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
  <script src="{{asset('js/common-email-send.js')}}">//js for common mail</script> 
<script type="text/javascript">

$(document).on('click', '.product-list-btn', function(e) {
      e.preventDefault();
      let type = $(this).data('type');
      let supplier_id = $(this).data('id');
        $.ajax({
          url: '/purchase-product/get-products/'+type+'/'+supplier_id,
          type: 'GET',
          dataType: 'html',
          beforeSend: function() {
            $("#loading-image").show();
          }
        }).done( function(response) {
            $("#loading-image").hide();
            $(".expand-row-"+supplier_id).toggleClass('hidden');
            $("#product-list-data-"+supplier_id).html(response);
        }).fail(function(errObj) {
            $("#loading-image").hide();
        });
    });

    var selectAllProductBtn = $(".select-all-products");
    selectAllProductBtn.on("click", function (e) {
                    var supplier_id = $(this).data('id');
                    var $this = $(this);
                    var custCls = '.supplier-'+supplier_id;
                    if ($this.hasClass("has-all-selected") === false) {
                        $(this).find('img').attr("src", "/images/completed-green.png");
                        $(custCls).find(".select-pr-list-chk").prop("checked", true).trigger('change');
                        $this.addClass("has-all-selected");
                    }else {
                        $(this).find('img').attr("src", "/images/completed.png");
                        $(custCls).find(".select-pr-list-chk").prop("checked", false).trigger('change');
                        $this.removeClass("has-all-selected");
                    }
    })
    function unique(list) {
            var result = [];
            $.each(list, function (i, e) {
                if ($.inArray(e, result) == -1) result.push(e);
            });
            return result;
        }
    var product_ids = [];
    var order_ids = [];//Purpose : array for Order id - DEVTASK-4236
    $(document).on('click', '.btn-send', function(e) {
      e.preventDefault();
      // product_ids = [];
      let type = $(this).data('type');
      let supplier_id = $(this).data('id');

        var cus_cls = ".supplier-"+supplier_id;
            var total = $(cus_cls).find(".select-pr-list-chk").length;
            for (i = 0; i < total; i++) {
             var supplier_cls = ".supplier-"+supplier_id+" .select-pr-list-chk";
             var $input = $(supplier_cls).eq(i);
             var product_id = $input.data('id');
             var order_id = $input.data('order-id');
             if ($input.is(":checked") === true) {
                    product_ids.push(product_id);
                    product_ids = unique(product_ids);

                    //START - Purpose : Add Order id - DEVTASK-4236
                    order_ids.push(order_id);
                    order_ids = unique(order_ids);
                    //END - DEVTASK-4236
                }
            }
    if(product_ids.length == 0)
    {
        alert("Please select some products");
        return;
    }
        $.ajax({
          url: '/purchase-product/send-products/'+type+'/'+supplier_id,
          type: 'GET',
          dataType: 'html',
          data: {
              product_ids:JSON.stringify(product_ids),
              order_ids:JSON.stringify(order_ids)
          },
          beforeSend: function() {
            $("#loading-image").show();
          }
        }).done( function(response) {
            $("#loading-image").hide();
            toastr['success']("Message sent successfully!", "Success");
        }).fail(function(errObj) {
            $("#loading-image").hide();
        });
    });

    //START - purpose : Add Communication send msg -DEVTASK-4236
    $(document).on('keyup', '.send-message', function(event) {
        if (event.keyCode != 13) {
            return;
        }

        let supplierId = $(this).attr('data-id');
        let message = $(this).val();
        let self = this;

        if (message == '') {
            return;
        }

        $.ajax({
            url: "{{action('WhatsAppController@sendMessage', 'supplier')}}",
            type: 'post',
            data: {
                message: message,
                supplier_id: supplierId,
                _token: "{{csrf_token()}}",
                status: 2
            },
            success: function() {
              $("#loading-image").hide();
                $(self).removeAttr('disabled');
                $(self).val('');
                toastr['success']("Message sent successfully!", "Success");
            },
            beforeSend: function() {
                $(self).attr('disabled', true);
                $("#loading-image").show();
            },
            error: function() {
              $("#loading-image").hide();
                $(self).removeAttr('disabled');
            }
        });

    });
    //END - DEVTASK-4236
    
</script>
@endsection