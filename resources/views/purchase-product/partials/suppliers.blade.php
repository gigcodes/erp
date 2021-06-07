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
            <th width="75%">Name</th>
            <th width="75%">Product Inquiry Count</th> <!-- Purpose : Product Inquiry Count -DEVTASK-4048 -->
            <th width="15%">Action</th>
         </tr>
        </thead>

        <tbody>
			@foreach ($suppliers as $key => $supplier)
            <tr class="">
              <td>{{ ++$key }}</td>
              <td>{{ $supplier->supplier }}</td>
              <td>{{$supplier->inquiryproductdata_count}}</td><!-- Purpose : Product Inquiry Count -DEVTASK-4048 -->
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
                <td colspan="4" id="product-list-data-{{$supplier->id}}">
                
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
             if ($input.is(":checked") === true) {
                    product_ids.push(product_id);
                    product_ids = unique(product_ids);
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
              product_ids:JSON.stringify(product_ids)
          },
          beforeSend: function() {
            $("#loading-image").show();
          }
        }).done( function(response) {
            $("#loading-image").hide();
        }).fail(function(errObj) {
            $("#loading-image").hide();
        });
    });
    
</script>
@endsection