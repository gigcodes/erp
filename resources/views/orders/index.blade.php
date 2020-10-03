@extends('layouts.app')

@section('title', 'Orders List')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
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
            <h2 class="page-heading">Orders List ({{$totalOrders}})</h2>
        </div>
           <div class="col-10" style="padding-left:0px;">
            <div >
            <form class="form-inline" action="{{ route('order.index') }}" method="GET">
                
                <div class="form-group col-md-3 pd-3">
                  <input style="width:100%;" name="term" type="text" class="form-control"
                         value="{{ isset($term) ? $term : '' }}"
                         placeholder="Search">
                </div>

                 <div class="form-group col-md-2 pd-3 status-select-cls">
                  <select class="form-control select-multiple" name="status[]" multiple>
                    <option value="">Select a Status</option>


                     @foreach ($order_status_list as $id => $order_st)
                      <option value="{{ $id }}" {{ isset($order_status) && in_array($id, $order_status) ? 'selected' : '' }}>{{ $order_st }}</option>
                    @endforeach
                  </select>
                </div>


                 <!-- <div class="form-group col-md-2 pd-3">
                  <?php echo Form::select("brand_id[]",["" => "-- Select Brands --"]+$brandList,request('brand_id',[]),["class" => "form-control select2"]); ?>
                </div> -->


                 <div class="form-group col-md-2 pd-3">
                  <div class='input-group date' id='order-datetime'>
                    <input type='text' class="form-control" name="date" value="{{ isset($date) ? $date : '' }}" />


                     <span class="input-group-addon">
                      <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                  </div>
                </div>

                   <div class="form-group col-md-2 pd-3">
                  <div class="form-group ml-3">	
                      <select class="form-control select2" name="store_website_id">
                      <option value="">Select Site Name</option>
                      @forelse ($registerSiteList as $key => $item)
                          <option value="{{ $key }}" {{ isset($store_site) && $store_site == $key ? 'selected' : '' }}>{{ $item }}</option>
                      @empty
                      @endforelse
                      </select>
                  </div>
                  </div>

                   <div class="form-group col-md-1 pd-3">
                <button type="submit" class="btn btn-image ml-3"><img src="{{asset('images/filter.png')}}" /></button>
                  </div>
              </form>
               
            </div>
             </div>
          <div class="col-md-2" style="padding:0px;">
                  <a class="btn btn-xs btn-secondary" href="{{ route('order.create') }}">+</a>
                  <a href="{{ action('OrderController@downloadOrderInPdf', Request::all()) }}" class="btn btn-success btn-xs">Download</a>
              </div>
        </div>	
<div class="row">
@include('partials.flash_messages')
    <?php if(!empty($statusFilterList)) { ?>
      <div class="row col-md-12">
          <?php foreach($statusFilterList as $listFilter) { ?>
            <div class="card">
                <div class="card-header">
                <?php echo ucwords($listFilter["order_status"]); ?> (<?php echo $listFilter["total"]; ?>)
                </div>
                <!-- <div class="card-body">
                    <?php echo $listFilter["total"]; ?>
                </div> -->
            </div>
        <?php } ?>
      </div>
    <?php } ?>  
</div>


<div class="row">
        <div class="col-md-12" style="padding:0px;">
            <div class="pull-right">
              <a href="#" class="btn btn-xs btn-secondary delete-orders">
                            Archive
              </a>
              <a href="#" class="btn btn-xs update-customer btn-secondary">
                            Update
              </a>
            </div>
        </div>
    </div>
<div class="row">
    <div class="infinite-scroll" style="width:100%;">
	<div class="table-responsive mt-2">
      <table class="table table-bordered order-table" style="border: 1px solid #ddd !important;">
        <thead>
        <tr>
            <th width="14%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=id{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">ID</a></th>
            <th width="6%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=date{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">Date</a></th>
            <th width="10%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=client_name{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">Client</a></th>
            <th width="10%">Site Name</th>
            <th width="10%">Products</th>
            <th>Brands</th>
            <th width="14%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=status{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">Order Status</a></th>
            <th width="8%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=advance{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">Advance</a></th>
            <th width="8%"><a href="/or    der{{ isset($term) ? '?term='.$term.'&' : '?' }}{{ isset($order_status) ? implode('&', array_map(function($item) {return 'status[]='. $item;}, $order_status)) . '&' : '&' }}sortby=balance{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">Balance</a></th>
            {{-- <th style="width: 5%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=action{{ ($orderby == 'asc') ? '&orderby=desc' : '' }}">Action Status</a></th>
            <th style="width: 8%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=due{{ ($orderby == 'asc') ? '&orderby=desc' : '' }}">Due</a></th> --}}
            {{-- <th style="width: 8%">Message Status</th> --}}
            {{-- <th style="width: 20%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=communication{{ ($orderby == 'asc') ? '&orderby=desc' : '' }}">Communication</a></th> --}}
            <th width="10%">Action</th>
         </tr>
        </thead>

        <tbody>
			@foreach ($orders_array as $key => $order)
            <tr class="{{ \App\Helpers::statusClass($order->assign_status ) }}">
              <td class="table-hover-cell">
                <div class="form-inline">
                  @if ($order->is_priority == 1)
                    <strong class="text-danger mr-1">!!!</strong>
                  @endif
                  <span class="td-mini-container">
                  <input type="checkbox" class="selectedOrder" name="selectedOrder" value="{{$order->id}}"><span style="font-size:14px;">{{ $order->order_id }}</span>
                  </span>
                </div>
              </td>
              <td>{{ Carbon\Carbon::parse($order->order_date)->format('d-m') }}</td>
              <td class="expand-row table-hover-cell">
                @if ($order->customer)
                  <span class="td-mini-container">
                    <a href="{{ route('customer.show', $order->customer->id) }}">{{ strlen($order->customer->name) > 15 ? substr($order->customer->name, 0, 13) . '...' : $order->customer->name }}</a>
                  </span>

                  <span class="td-full-container hidden">
                    <a href="{{ route('customer.show', $order->customer->id) }}">{{ $order->customer->name }}</a>
                  </span>
                @endif
              </td>
              <td class="expand-row table-hover-cell">
                @if ($order->storeWebsiteOrder)
                  @if ($order->storeWebsiteOrder->storeWebsite)
                    @php
                      $storeWebsite = $order->storeWebsiteOrder->storeWebsite;
                    @endphp
                    <span class="td-mini-container">
                        <a href="{{$storeWebsite->website}}" target="_blank">{{ strlen($storeWebsite->website) > 15 ? substr($storeWebsite->website, 0, 13) . '...' : $storeWebsite->website }}</a>
                    </span>
                    <span class="td-full-container hidden">
                        <a href="{{$storeWebsite->website}}" target="_blank">{{ $storeWebsite->website }}</a>
                    </span>
                  @endif
                @endif
              </td>
              <td class="expand-row table-hover-cell">	              
                @php $count = 0; @endphp	               
                <div class="d-flex">	               
                  <div class="">	                 
                    @foreach ($order->order_product as $order_product)	                        
                      @if ($order_product->product)	                      
                        @if ($order_product->product->hasMedia(config('constants.media_tags')))	                       
                          <span class="td-mini-container">	                         
                            @if ($count == 0)	                          
                              <a href="{{ route('products.show', $order_product->product->id) }}" target="_blank"><img src="{{ $order_product->product->getMedia(config('constants.media_tags'))->first()->getUrl() }}" class="img-responsive thumbnail-200 mb-1"></a>	                     
                              @php ++$count; @endphp	                        
                            @endif	                     
                          </span>	                        
                          <span class="td-full-container hidden">	                        
                            @if ($count >= 1)	       
                              <a href="{{ route('products.show', $order_product->product->id) }}" target="_blank"><img src="{{ $order_product->product->getMedia(config('constants.media_tags'))->first()->getUrl() }}" class="img-responsive thumbnail-200 mb-1"></a>
                              @php $count++; @endphp	      
                            @endif	                     
                          </span>	                 
                        @endif	                 
                      @endif	             
                    @endforeach	            
                  </div>	    
                  @if (($count - 1) > 1)	           
                    <span class="ml-1">	         
                      ({{ ($count - 1) }})	       
                    </span>	           
                  @endif	        
                </div>	        
              </td>
              <td>
                <?php 
                   $totalBrands = explode(",",$order->brand_name_list);
                    if(count($totalBrands) > 1) {
                      $str = 'Multi';
                    }
                    else {
                      $str = $order->brand_name_list;
                    }
                ?>
                <span style="font-size:14px;">{{$str}}</span>
              </td>
              <td class="expand-row table-hover-cell">
                <div class="form-group" style="margin-bottom:0px;">
                  <select data-placeholder="Order Status" class="form-control order-status-select" id="supplier" data-id={{$order->id}} >
                            <optgroup label="Order Status">
                              <option value="">Select Order Status</option>
                                @foreach ($order_status_list as $id => $status)
                                    <option value="{{ $id }}" {{ $order->order_status_id == $id ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </optgroup>
                    </select>
                </div>
              </td>
              <td>{{ $order->advance_detail }}</td>
              <td>{{ $order->balance_amount }}</td>
              {{-- <td></td>
              <td></td> --}}
              {{-- <td>{{ $order->action->status }}</td>
              <td>{{ $order->action->completion_date ? Carbon\Carbon::parse($order->action->completion_date)->format('d-m') : '' }}</td> --}}
              <td>
                <div class="d-flex">
                  <a class="btn btn-image pd-5 btn-ht" href="{{route('purchase.grid')}}?order_id={{$order->id}}">
                    <img title="Purchase Grid" style="display: inline; width: 15px;" src="{{ asset('images/customer-order.png') }}" alt="">
                  </a>
                  <a class="btn btn-image pd-5 btn-ht" href="{{ route('order.show',$order->id) }}"><img title="View order" src="{{asset('images/view.png')}}" /></a>
                  <a class="btn btn-image send-invoice-btn pd-5 btn-ht" data-id="{{ $order->id }}" href="{{ route('order.show',$order->id) }}">
                    <img title="Send Invoice" src="{{asset('images/purchase.png')}}" />
                  </a>
                  <a title="Preview Order" class="btn btn-image preview-invoice-btn pd-5 btn-ht" href="{{ route('order.perview.invoice',$order->id) }}">
                    <i class="fa fa-hourglass"></i>
                  </a>
                  @if ($order->waybill)
                    <a title="Download Package Slip pd-5 btn-ht" href="{{ route('order.download.package-slip', $order->waybill->id) }}" class="btn btn-image" href="javascript:;">
                        <i class="fa fa-download" aria-hidden="true"></i>
                    </a>
                    <a title="Track Package Slip pd-5 btn-ht" href="javascript:;" data-id="{{ $order->waybill->id }}" data-awb="{{ $order->waybill->awb }}" class="btn btn-image track-package-slip">
                        <i class="fa fa fa-globe" aria-hidden="true"></i>
                    </a>
                  @else
                    <a title="Generate AWB" data-customer='<?php echo ($order->customer) ? json_encode($order->customer) : json_encode([]); ?>' class="btn btn-image generate-awb pd-5 btn-ht" href="javascript:;">
                      <i class="fa fa-truck" aria-hidden="true"></i>
                    </a>
                  @endif
                  {{-- @can('order-edit')
                  <a class="btn btn-image pd-5 btn-ht" href="{{ route('order.edit',$order['id']) }}"><img src="{{asset('images/edit.png')}}" /></a>
                  @endcan --}}

                  {!! Form::open(['method' => 'DELETE','route' => ['order.destroy', $order->id],'style'=>'display:inline;margin-bottom:0px;height:30px;']) !!}
                  <button type="submit" class="btn btn-image pd-5 btn-ht"><img title="Archive Order" src="{{asset('images/archive.png')}}" /></button>
                  {!! Form::close() !!}
                  <?php
                  if($order->auto_emailed)
                  {
                    $title_msg = "Resend Email";
                  }
                  else
                  {
                    $title_msg = "Send Email"; 
                  }
                  ?>
                  <a title="<?php echo $title_msg;?>" class="btn btn-image send-order-email-btn pd-5 btn-ht" data-id="{{ $order->id }}" href="javascript:;">
                      <i class="fa fa-paper-plane" aria-hidden="true"></i>
                  </a>
                  @if(auth()->user()->checkPermission('order-delete'))
                    {!! Form::open(['method' => 'DELETE','route' => ['order.permanentDelete', $order->id],'style'=>'display:inline;margin-bottom:0px;height:30px;']) !!}
                    <button type="submit" class="btn btn-image pd-5 btn-ht"><img title="Delete Order" src="{{asset('images/delete.png')}}" /></button>
                    {!! Form::close() !!}
                  @endif
                  @if(!$order->invoice_id)
                <a title="Add invoice" class="btn btn-image add-invoice-btn pd-5 btn-ht" data-id='{{$order->id}}'>
                     +
                </a>
                @endif
                <a title="Return / Exchange" data-id="{{$order->id}}" class="btn btn-image quick_return_exchange pd-5 btn-ht">
                     <i class="fa fa-product-hunt" aria-hidden="true"></i>
                </a>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

	{!! $orders_array->appends(Request::except('page'))->links() !!}
	</div>
    </div>
    </div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
   </div>

   <div id="updateCustomer" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content ">
      <div class="modal-header">
                    <h4 class="modal-title">Update Customers</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="" id="customerUpdateForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-2">
                                    <strong>Status:</strong>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                      <select data-placeholder="Order Status" name="order_status" class="form-control select2" >
                                              <optgroup label="Order Status">
                                                <option value="">Select Order Status</option>
                                                  @foreach ($order_status_list as $id => $status)
                                                      <option value="{{ $id }}" {{ $order->order_status_id == $id ? 'selected' : '' }}>{{ $status }}</option>
                                                  @endforeach
                                              </optgroup>
                                      </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-2">
                                    <strong>Message:</strong>
                                </div>
                                <div class="col-md-8">
                                <div class="form-group">
                                  <textarea cols="45" class="form-control" name="customer_message"></textarea>
                                </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-2">
                                    <strong>Update type:</strong>
                                </div>
                                <div class="col-md-8">
                                <div class="form-group">
                                  <select name="update_type" class="form-control">
                                    <option value="1">Only send message</option>
                                    <option value="2">Send message and update status</option>
                                  </select>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Update</button>
                    </div>
                </form>
      </div>
    </div>
</div>
@endsection

@include("partials.modals.tracking-event-modal")
@include("partials.modals.generate-awb-modal")
@include("partials.modals.add-invoice-modal")
@include('partials.modals.return-exchange-modal')
@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script src="{{ asset('/js/order-awb.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $('#order-datetime').datetimepicker({
        format: 'YYYY-MM-DD'
      });

      $(document).on("click",".generate-awb",function() {
          var customer = $(this).data("customer");
            if(typeof customer != "undefined" || customer != "") {
               $(".input_customer_name").val(customer.name);
               $(".input_customer_phone").val(customer.phone);
               $(".input_customer_address1").val(customer.address);
               $(".input_customer_address2").val(customer.city);
               $(".input_customer_city").val(customer.city);
               $(".input_customer_pincode").val(customer.pincode);
            }
            $("#generateAWBMODAL").modal("show");
      });

      $(document).on("change",".order-status-select",function() {
        $.ajax({
          url: "/erp/order/change-status",
          type: "GET",
          async : false,
          data : {
            id : $(this).data("id"),
            status : $(this).val()
          }
        }).done( function(response) {
         
        }).fail(function(errObj) {
          alert("Could not change status");
        });
      });

      $(".select2").select2({tags:true});

      $(".select-multiple").multiselect({
        // buttonWidth: '100%',
        // includeSelectAllOption: true
      });
	  
	  
	  $('ul.pagination').hide();
		$('.infinite-scroll').jscroll({
			autoTrigger: true,
			// debug: true,
			loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
			padding: 0,
			nextSelector: '.pagination li.active + li a',
			contentSelector: 'div.infinite-scroll',
			callback: function () {
				$('ul.pagination').first().remove();
				$('ul.pagination').hide();
			}
		});
    });

    $(document).on('click', '.change_message_status', function(e) {
      e.preventDefault();
      var url = $(this).data('url');
      var thiss = $(this);
      var type = 'GET';

      if ($(this).hasClass('approve-whatsapp')) {
        type = 'POST';
      }

        $.ajax({
          url: url,
          type: type,
          beforeSend: function() {
            $(thiss).text('Loading');
          }
        }).done( function(response) {
          $(thiss).closest('tr').removeClass('row-highlight');
          $(thiss).prev('span').text('Approved');
          $(thiss).remove();
        }).fail(function(errObj) {
          alert("Could not change status");
        });
    });

    $(document).on('click', '.expand-row', function() {
      var selection = window.getSelection();
      if (selection.toString().length === 0) {
        $(this).find('.td-mini-container').toggleClass('hidden');
        $(this).find('.td-full-container').toggleClass('hidden');
      }
    });

    $(document).on('click', '.expand-row', function() {
      var selection = window.getSelection();
      if (selection.toString().length === 0) {
        $(this).find('.td-mini-container').toggleClass('hidden');
        $(this).find('.td-full-container').toggleClass('hidden');
      }
    });
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

    $(document).on("click",".send-order-email-btn",function(e){
       e.preventDefault();
       var $this = $(this);
       $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "/order/"+$this.data("id")+"/send-order-email",
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

    $(document).on("click",".add-invoice-btn",function(e){
       e.preventDefault();
       var $this = $(this);
       $.ajax({
          url: "/order/"+$this.data("id")+"/add-invoice",
          type: "get"
        }).done(function(response) {
          $('#addInvoice').modal('show');
           $("#add-invoice-content").html(response); 
        }).fail(function(errObj) {
           $("#addInvoice").hide();
        });
    });

	
	


    var selected_orders = [];
         $(document).on('click', '.selectedOrder', function () {
            var checked = $(this).prop('checked');
            var id = $(this).val();
             if (checked) {
              selected_orders.push(id);
            } else {
                var index = selected_orders.indexOf(id);
                 selected_orders.splice(index, 1);
            }
        });
        $(document).on("click",".delete-orders",function(e){
          e.preventDefault();
          if(selected_orders.length < 1) {
            toastr['error']("Select some orders first");
            return;
          }
          var x = window.confirm("Are you sure, you want to delete ?");
          if(!x) {
            return;
          }
          $.ajax({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: "/order/deleteBulkOrders",
              type: "post",
              data: {ids : selected_orders}
            }).done(function(response) {
              toastr['success'](response.message);
              window.location.reload();
            }).fail(function(errObj) {
            });
        });
        // $(document).on("click",".view-product",function(e){
        //   e.preventDefault();
        //   var id = $(this).data('id');
        //   $.ajax({
        //       headers: {
        //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //       },
        //       url: "/order/"+id+"/view-products",
        //       type: "GET"
        //     }).done(function(response) {
        //       $('#view-products').modal('show');
        //       $("#view-products-content").html(response); 
        //     }).fail(function(errObj) {
        //     });
        // });
         $(document).on("click",".update-customer",function(e){
          e.preventDefault();
          if(selected_orders.length < 1) {
            toastr['error']("Select some orders first");
            return;
          }
          $('#updateCustomer').modal('show');
        });
        
        $(document).on('submit', '#customerUpdateForm', function (e) {
                e.preventDefault();
                var data = $(this).serializeArray();
                data.push({name: 'selected_orders', value: selected_orders});
                $.ajax({
                    url: "{{route('order.update.customer')}}",
                    type: 'POST',
                    data: data,
                    success: function (response) {
                        toastr['success']('Successful', 'success');
                        $('#updateCustomer').modal('hide');
                        $("#customerUpdateForm").trigger("reset");
                        $(".order-table tr").find('.selectedOrder').each(function () {
                          if ($(this).prop("checked") == true) {
                            $(this).prop("checked", false);
                          }
                        });
                        selected_orders = [];
                    },
                    error: function () {
                        alert('There was error loading priority task list data');
                    }
                });
            });
             $(document).on('click', '.quick_return_exchange', function (e) {
            let $this       = $(this),
                $modelData  = $(document).find(".return-exchange-model-data");
             $('#return-exchange-modal').modal('show');
             $.ajax({
                type: "GET",
                url: "/return-exchange/getProducts/" + $this.data("id"),
            }).done(function (response) {
              $modelData.html(response.html);
            }).fail(function (response) {});
        });

        $(document).on("click","#return-exchange-form input[name='type']",function() {
            if($(this).val() == "refund") {
                $("#return-exchange-form").find(".refund-section").show();
            }else{
                $("#return-exchange-form").find(".refund-section").hide();
            }
        });
         $(document).on("click","#btn-return-exchage-request",function(e) {
            e.preventDefault();
            var form = $("#return-exchange-form");
            $.ajax({
                type: form.attr("method"),
                url: form.attr("action"),
                data: form.serialize(),
                dataType:"json"
            }).done(function (response) {
                toastr[(response.code == 200) ?'success' : 'error'](response.message);
                $('#return-exchange-modal').modal('hide');
                document.getElementById("return-exchange-form").reset();
            }).fail(function (response) {
                console.log(response);
            });
        });
  </script>
@endsection
