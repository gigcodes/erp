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

        <div class="form-group">
          <strong>Rating:</strong> {{ $customer->rating }}
        </div>

        <div class="form-group">
          <strong>Address:</strong> {{ $customer->address }}
        </div>

        <div class="form-group">
          <strong>City:</strong> {{ $customer->city }}
        </div>

        <div class="form-group">
          <strong>Country:</strong> {{ $customer->country }}
        </div>
      </div>
    </div>
  </div>

  <div class="tab-pane mt-3" id="2">
    @if (count($customer->leads) > 0)
      @foreach ($customer->leads as $key => $lead)
        <h2><a href="{{ route('leads.show', $lead->id) }}" target="_blank">Lead {{ $key + 1 }}</a></h2>
        <form action="{{ route('leads.update', $lead->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="type" value="customer">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                  <strong>Brand:</strong>
                  <select multiple="" name="multi_brand[]" class="form-control multi_brand">
                    @php $multi_brand = is_array(json_decode($lead->multi_brand,true) ) ? json_decode($lead->multi_brand,true) : []; @endphp
                      @foreach($brands as $brand_item)
                          <option value="{{$brand_item['id']}}" {{ in_array($brand_item['id'] ,$multi_brand) ? 'Selected=Selected':''}}>{{$brand_item['name']}}</option>
                      @endforeach
                  </select>

              </div>

              <div class="form-group">
                  <strong>Categories</strong>
                  @php
                  $selected_categories = is_array(json_decode( $lead->multi_category,true)) ? json_decode( $lead->multi_category ,true) : [] ;
                  $category_selection = \App\Category::attr(['name' => 'multi_category[]','class' => 'form-control multi_category'])
                                                     ->selected($selected_categories)
                                                     ->renderAsMultiple();
                  @endphp
                  {!! $category_selection  !!}
              </div>

              <div class="form-group">
                  <strong> Selected Product :</strong>

                  <select name="selected_product[]" class="select2{{ $key + 1 }} form-control" multiple="multiple"></select>

                  @if ($errors->has('selected_product'))
                      <div class="alert alert-danger">{{$errors->first('selected_product')}}</div>
                  @endif
              </div>

              <script type="text/javascript">
                $(document).ready(function() {
                  var key = {{ $key + 1 }};
                  jQuery('.select2' + key).select2({
                      ajax: {
                          url: '/productSearch/',
                          dataType: 'json',
                          delay: 750,
                          data: function (params) {
                              return {
                                  q: params.term, // search term
                              };
                          },
                          processResults: function (data,params) {

                              params.page = params.page || 1;

                              return {
                                  results: data,
                                  pagination: {
                                      more: (params.page * 30) < data.total_count
                                  }
                              };
                          },
                      },
                      placeholder: 'Search for Product by id, Name, Sku',
                      escapeMarkup: function (markup) { return markup; },
                      minimumInputLength: 5,
                      width: '100%',
                      templateResult: formatProduct,
                      templateSelection:function(product) {
                        console.log('YRA');
                        console.log(product.id);
                           return product.text || product.name;
                       },

                  });




                    @php
                    $selected_products_array = json_decode( $lead->selected_product );
                    $products_array = [];

                    if ( ! empty( $selected_products_array  ) ) {
                        foreach ($selected_products_array  as $product_id) {
                            $product = \App\Product::find($product_id);

                           $products_array[$product_id] = $product->name ? $product->name : $product->sku;
                        }
                    }
                    @endphp
                    @if(!empty($products_array ))
                      let data = [
                              @forEach($products_array as $key => $value)
                          {
                              'id': '{{ $key }}',
                              'text': '{{$value  }}',
                          },
                          @endforeach
                      ];
                  @endif

                  let productSelect = jQuery('.select2' + key);
                  // create the option and append to Select2

                  data.forEach(function (item) {

                      var option = new Option(item.text,item.id , true, true);
                      productSelect.append(option).trigger('change');

                      // manually trigger the `select2:select` event
                      productSelect.trigger({
                          type: 'select2:select',
                          params: {
                              data: item
                          }
                      });

                  });

                  function formatProduct (product) {
                      if (product.loading) {
                          return product.sku;
                      }

                      return "<p> <b>Id:</b> " +product.id  + (product.name ? " <b>Name:</b> "+product.name : "" ) +  " <b>Sku:</b> "+product.sku+" </p>";
                  }
                });
              </script>

              <div class="form-group">
                  <strong>status:</strong>
                  <Select name="status" class="form-control change_status" data-leadid="{{ $lead->id }}">
                       @foreach($status as $key => $value)
                        <option value="{{$value}}" {{$value == $lead->status ? 'Selected=Selected':''}}>{{$key}}</option>
                        @endforeach
                  </Select>
                  <span class="text-success change_status_message" style="display: none;">Successfully changed status</span>

                  <input type="hidden" class="form-control" name="userid" placeholder="status" value="{{$lead->userid}}"/>

              </div>

               <div class="form-group">
                   <strong>Created by:</strong>

                   <input type="text" class="form-control" name="" placeholder="Created by" value="{{ App\Helpers::getUserNameById($lead->userid) }}" readonly/>
               </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                  <strong>Comments:</strong>
                  <textarea  class="form-control" name="comments" placeholder="comments">{{$lead->comments}} </textarea>
              </div>

              <div class="form-group">
                <strong>Sizes:</strong>
                <input type="text" name="size" value="{{ $lead->size }}" class="form-control" placeholder="S, M, L">
              </div>

                <div class="form-group">
                    <strong>Assigned To:</strong>
                    <Select name="assigned_user" class="form-control">

                            @foreach($users as $user)
                          <option value="{{$user['id']}}" {{$user['id']== $lead->assigned_user ? 'Selected=Selected':''}}>{{$user['name']}}</option>
                          @endforeach
                    </Select>
                </div>

                <?php $images = $lead->getMedia(config('constants.media_tags')) ?>
                @foreach ($images as $key => $image)
                  <div class="old-image{{ $key }}" style="
                       @if ($errors->has('image'))
                          display: none;
                       @endif
                  ">
                    <p>
                      <img src="{{ $image->getUrl() }}" class="img-responsive" style="max-width: 200px;"  alt="">
                      <button class="btn btn-image removeOldImage" data-id="{{ $key }}" media-id="{{ $image->id }}"><img src="/images/delete.png" /></button>

                      <input type="text" hidden name="oldImage[{{ $key }}]" value="{{ $images ? '0' : '-1' }}">
                   </p>
                </div>
                @endforeach

                @if (count($images) == 0)
                  <input type="text" hidden name="oldImage[0]" value="{{ $images ? '0' : '-1' }}">
                @endif

                 <div class="form-group new-image" style="">
                     <strong>Upload Image:</strong>
                     <input  type="file" enctype="multipart/form-data" class="form-control" name="image[]" multiple />
                     @if ($errors->has('image'))
                         <div class="alert alert-danger">{{$errors->first('image')}}</div>
                     @endif
                 </div>
            </div>

              <div class="col-xs-12 text-center">
                  <div class="form-group">
                      <button type="submit" class="btn btn-secondary">Update</button>
                  </div>
              </div>

          </div>
   </form>
      @endforeach
    @else
      There are no leads for this customer
    @endif
  </div>

  <div class="tab-pane mt-3" id="3">
    @if (count($customer->orders) > 0)
      @foreach ($customer->orders as $key => $order)
        <h2><a href="{{ route('order.show', $order->id) }}" target="_blank">Order {{ $key + 1 }}</a></h2>
        <form action="{{ route('order.update',$order->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="type" value="customer">

          <div class="row">
              <div class="col-md-6 col-12">

                <div class="form-group">
                    <strong>Balance Amount:</strong>
                    <input type="text" class="form-control" name="balance_amount" placeholder="Balance Amount"
                           value="{{ old('balance_amount') ? old('balance_amount') : $order->balance_amount }}"/>
                    @if ($errors->has('balance_amount'))
                        <div class="alert alert-danger">{{$errors->first('balance_amount')}}</div>
                    @endif
                </div>

                <div class="form-group">
                    <strong> Payment Mode :</strong>
              <?php
              $paymentModes = new \App\ReadOnly\PaymentModes();

              echo Form::select('payment_mode',$paymentModes->all(), ( old('payment_mode') ? old('payment_mode') : $order->payment_mode ), ['placeholder' => 'Select a mode','class' => 'form-control']);?>

                    @if ($errors->has('payment_mode'))
                        <div class="alert alert-danger">{{$errors->first('payment_mode')}}</div>
                    @endif
                </div>

                <div class="form-group">
                    <strong>Advance Amount:</strong>
                    <input type="text" class="form-control" name="advance_detail" placeholder="Advance Detail"
                           value="{{ old('advance_detail') ? old('advance_detail') : $order->advance_detail }}"/>
                    @if ($errors->has('advance_detail'))
                        <div class="alert alert-danger">{{$errors->first('advance_detail')}}</div>
                    @endif
                </div>

                <div class="form-group">
                    <strong>Received By:</strong>
                    <input type="text" class="form-control" name="received_by" placeholder="Received By"
                           value="{{ old('received_by') ? old('received_by') : $order->received_by }}"/>
                    @if ($errors->has('received_by'))
                        <div class="alert alert-danger">{{$errors->first('received_by')}}</div>
                    @endif
                </div>

                <div class="form-group">
                    <strong>Advance Date:</strong>
                    <input type="date" class="form-control" name="advance_date" placeholder="Advance Date"
                           value="{{ old('advance_date') ? old('advance_date') : $order->advance_date }}"/>
                    @if ($errors->has('advance_date'))
                        <div class="alert alert-danger">{{$errors->first('advance_date')}}</div>
                    @endif
                </div>

              </div>
              <div class="col-md-6 col-12">

                 <div class="form-group">
                     <strong>status:</strong>
                     <Select name="status" class="form-control change_status order_status" data-orderid="{{ $order->id }}">
                          @php $order_status = (new \App\ReadOnly\OrderStatus)->all(); @endphp
                          @foreach($order_status as $key => $value)
                           <option value="{{$value}}" {{$value == $order->order_status ? 'Selected=Selected':''}}>{{$key}}</option>
                           @endforeach
                     </Select>
                     <span class="text-success change_status_message" style="display: none;">Successfully changed status</span>
                 </div>

                 <div class="form-group">
                     <strong>Estimated Delivery Date:</strong>
                     <input type="date" class="form-control" name="estimated_delivery_date" placeholder="Advance Date"
                            value="{{ old('estimated_delivery_date') ? old('estimated_delivery_date') : $order->estimated_delivery_date }}"/>
                     @if ($errors->has('estimated_delivery_date'))
                         <div class="alert alert-danger">{{$errors->first('estimated_delivery_date')}}</div>
                     @endif
                 </div>


                 <div class="form-group">
                     <strong>Note if any:</strong>
                     <input type="text" class="form-control" name="note_if_any" placeholder="Note if any"
                            value="{{ old('note_if_any') ? old('note_if_any') : $order->note_if_any }}"/>
                     @if ($errors->has('note_if_any'))
                         <div class="alert alert-danger">{{$errors->first('note_if_any')}}</div>
                     @endif
                 </div>


                <div class="form-group">
                    <strong> Name of Order Handler :</strong>
              <?php
              $sales_persons = \App\Helpers::getUsersArrayByRole( 'Sales' );
              echo Form::select('sales_person',$sales_persons, ( old('sales_person') ? old('sales_person') : $order->sales_person ), ['placeholder' => 'Select a name','class' => 'form-control']);?>
                    @if ($errors->has('sales_person'))
                        <div class="alert alert-danger">{{$errors->first('sales_person')}}</div>
                    @endif
                </div>

                 <div class="form-group">
                     <strong>Created by:</strong>
                     {{ $order->user_id != 0 ? App\Helpers::getUserNameById($order->user_id) : 'Unknown' }}
                 </div>
                <div class="form-group">
                    <strong>Remark</strong>
                    {{ $order->remark }}
                </div>




              </div>

              <div class="col-xs-12">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="text-center">
                      <h3>Product Details</h3>
                    </div>
                    <div class="form-group">
                        <table class="table table-bordered" id="products-table">
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Sku</th>
                                <th>Color</th>
                                <th>Brand</th>
                                <th>Price</th>
                                <th>Size</th>
                                <th style="width: 30px">Qty</th>
                                <th style="width: 160px">Action</th>
                            </tr>
                            @foreach($order->order_product  as $order_product)
                                <tr>
                                    @if(isset($order_product->product))
                                      <th><img width="200" src="{{ $order_product->product->getMedia(config('constants.media_tags'))->first()
                                                    ? $order_product->product->getMedia(config('constants.media_tags'))->first()->getUrl()
                                                    : '' }}" /></th>
                                      <th>{{ $order_product->product->name }}</th>
                                      <th>{{ $order_product->product->sku }}</th>
                                      <th>{{ $order_product->product->color }}</th>
                                      <th>{{ \App\Http\Controllers\BrandController::getBrandName($order_product->product->brand) }}</th>
                                    @else
                                      <th></th>
                                      <th></th>
                                      <th>{{$order_product->sku}}</th>
                                      <th></th>
                                      <th></th>
                                    @endif

                                    <th>
                                        <input class="table-input" type="text" value="{{ $order_product->product_price }}" name="order_products[{{ $order_product->id }}][product_price]">
                                    </th>
                                    <th>
                                        @if(!empty($order_product->product->size))
                                  <?php

                                  $sizes = \App\Helpers::explodeToArray($order_product->product->size);
                                  $size_name = 'order_products['.$order_product->id.'][size]';

                                  echo Form::select($size_name,$sizes,( $order_product->size ), ['placeholder' => 'Select a size'])
                                  ?>
                                        @else
                                            <select hidden class="form-control" name="order_products[{{ $order_product->id }}][size]">
                                                <option selected="selected" value=""></option>
                                            </select>
                                            nil
                                        @endif
                                    </th>
                                    <th>
                                        <input class="table-input" type="number" value="{{ $order_product->qty }}" name="order_products[{{ $order_product['id'] }}][qty]">
                                    </th>
                                    @if(isset($order_product->product))
                                        <th>
                                            <a class="btn btn-image" href="{{ route('products.show',$order_product->product->id) }}"><img src="/images/view.png" /></a>
                                            <a class="btn btn-image remove-product" href="#" data-product="{{ $order_product->id }}"><img src="/images/delete.png" /></a>
                                        </th>
                                    @else
                                        <th></th>
                                    @endif
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
                {{-- {{dd($data)}} --}}
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group btn-group">
                        <a href="{{ route('attachProducts',['order',$order->id]) }}" class="btn btn-image"><img src="/images/attach.png" /></a>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#productModal">+</button>
                    </div>
                </div>

                <div id="productModal" class="modal fade" role="dialog">
                  <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 class="modal-title">Create Product</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                      </div>
                      <div class="modal-body">
                        <div class="form-group">
                            <strong>Image:</strong>
                            <input type="file" class="form-control" name="image"
                                   value="{{ old('image') }}" id="product-image"/>
                            @if ($errors->has('image'))
                                <div class="alert alert-danger">{{$errors->first('image')}}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" class="form-control" name="name" placeholder="Name"
                                   value="{{ old('name') }}"  id="product-name"/>
                            @if ($errors->has('name'))
                                <div class="alert alert-danger">{{$errors->first('name')}}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong>SKU:</strong>
                            <input type="text" class="form-control" name="sku" placeholder="SKU"
                                   value="{{ old('sku') }}"  id="product-sku"/>
                            @if ($errors->has('sku'))
                                <div class="alert alert-danger">{{$errors->first('sku')}}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong>Color:</strong>
                            <input type="text" class="form-control" name="color" placeholder="Color"
                                   value="{{ old('color') }}"  id="product-color"/>
                            @if ($errors->has('color'))
                                <div class="alert alert-danger">{{$errors->first('color')}}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong>Brand:</strong>
                            <?php
                            $brands = \App\Brand::getAll();
                            echo Form::select('brand',$brands, ( old('brand') ? old('brand') : '' ), ['placeholder' => 'Select a brand','class' => 'form-control', 'id'  => 'product-brand']);?>
                              {{--<input type="text" class="form-control" name="brand" placeholder="Brand" value="{{ old('brand') ? old('brand') : $brand }}"/>--}}
                              @if ($errors->has('brand'))
                                  <div class="alert alert-danger">{{$errors->first('brand')}}</div>
                              @endif
                        </div>

                        <div class="form-group">
                            <strong>Price:</strong>
                            <input type="number" class="form-control" name="price" placeholder="Price"
                                   value="{{ old('price') }}" step=".01"  id="product-price"/>
                            @if ($errors->has('price'))
                                <div class="alert alert-danger">{{$errors->first('price')}}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong>Size:</strong>
                            <input type="text" class="form-control" name="size" placeholder="Size"
                                   value="{{ old('size') }}"  id="product-size"/>
                            @if ($errors->has('size'))
                                <div class="alert alert-danger">{{$errors->first('size')}}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong>Quantity:</strong>
                            <input type="number" class="form-control" name="quantity" placeholder="Quantity"
                                   value="{{ old('quantity') }}"  id="product-quantity"/>
                            @if ($errors->has('quantity'))
                                <div class="alert alert-danger">{{$errors->first('quantity')}}</div>
                            @endif
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success createProduct" data-orderid="{{ $order->id }}">Create</button>
                      </div>
                    </div>

                  </div>
                </div>
              </div>

              <div class="col-xs-12 text-center">
                <button type="submit" class="btn btn-secondary">Update</button>
              </div>
          </div>
        </form>
      @endforeach
    @else
      There are no orders for this customer
    @endif
  </div>
</div>

<form action="" method="POST" id="product-remove-form">
  @csrf
</form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

    <script type="text/javascript">
      $('#completion-datetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm'
      });

          jQuery(document).ready(function() {

              jQuery('.multi_brand').select2({
                  placeholder: 'Brand',
                  width: '100%'
              });


              jQuery('.multi_category').select2({
                  placeholder: 'Categories',
                  width: '100%'
              });


          });

          $('.change_status').on('change', function() {
            var thiss = $(this);
            var token = "{{ csrf_token() }}";
            var status = $(this).val();


            if ($(this).hasClass('order_status')) {
              var id = $(this).data('orderid');
              var url = '/order/' + id + '/changestatus';
            } else {
              var id = $(this).data('leadid');
              var url = '/leads/' + id + '/changestatus';
            }

            $.ajax({
              url: url,
              type: 'POST',
              data: {
                _token: token,
                status: status
              }
            }).done( function(response) {
              $(thiss).siblings('.change_status_message').fadeIn(400);
              setTimeout(function () {
                $(thiss).siblings('.change_status_message').fadeOut(400);
              }, 2000);
            }).fail(function(errObj) {
              alert("Could not change status");
            });
          });

          $('.createProduct').on('click', function() {
            var token = "{{ csrf_token() }}";
            var url = "{{ route('products.store') }}";
            var order_id = $(this).data('orderid');
            var image = $('#product-image').prop('files')[0];
            var name = $('#product-name').val();
            var sku = $('#product-sku').val();
            var color = $('#product-color').val();
            var brand = $('#product-brand').val();
            var price = $('#product-price').val();
            var size = $('#product-size').val();
            var quantity = $('#product-quantity').val();

            var form_data = new FormData();
            form_data.append('_token', token);
            form_data.append('order_id', order_id);
            form_data.append('image', image);
            form_data.append('name', name);
            form_data.append('sku', sku);
            form_data.append('color', color);
            form_data.append('brand', brand);
            form_data.append('price', price);
            form_data.append('size', size);
            form_data.append('quantity', quantity);

            $.ajax({
              type: 'POST',
              url: url,
              processData: false,
              contentType: false,
              enctype: 'multipart/form-data',
              data: form_data,
              success: function(response) {
                var show_url = "{{ url('products') }}/" + response.order.id;
                var delete_url = "{{ url('deleteOrderProduct') }}/" + response.order.id;
                var product_row = '<tr><th></th>';
                    product_row += '<th>' + response.product.name + '</th>';
                    product_row += '<th>' + response.product.sku + '</th>';
                    product_row += '<th>' + response.product.color + '</th>';
                    product_row += '<th>' + response.product.brand + '</th>';
                    product_row += '<th><input class="table-input" type="text" value="' + response.product.price + '" name="order_products[' + response.order.id + '][product_price]"></th>';
                    // product_row += '<th>' + response.product.size + '</th>';

                    if (response.product.size != null) {
                      var exploded = response.product.size.split(',');

                      product_row += '<th><select class="form-control" name="order_products[' + response.order.id + '][size]">';
                      product_row += '<option selected="selected" value="">Select</option>';

                      $(exploded).each(function(index, value) {
                        product_row += '<option value="' + value + '">' + value + '</option>';
                      });

                      product_row += '</select></th>';

                    } else {
                        product_row += '<th><select hidden class="form-control" name="order_products[' + response.order.id + '][size]"><option selected="selected" value=""></option></select>nil</th>';
                    }

                    product_row += '<th><input class="table-input" type="number" value="' + response.order.qty + '" name="order_products[' + response.order.id + '][qty]"></th>';
                    product_row += '<th><a class="btn btn-secondary" href="' + show_url + '">View</a>';
                    product_row += '<form class="display-inline" method="post" action="' + delete_url + '">@csrf<button type="submit" class="btn btn-secondary">Remove</button></form></th>';
                    product_row += '</tr>';

                $('#products-table').append(product_row);
              }
            });
          });

          $(document).on('click', '.remove-product', function(e) {
            e.preventDefault();

            var product_id = $(this).data('product');
            var url = "{{ url('deleteOrderProduct') }}/" + product_id;

            $('#product-remove-form').attr('action', url);
            $('#product-remove-form').submit();
          });


    </script>

    @endsection
