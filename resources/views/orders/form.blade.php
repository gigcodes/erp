@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ $modify ? 'Edit Order' : 'Create Create' }}</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-secondary" href="{{ route('order.index') }}"> Back</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <form action="{{ $modify ? route('order.update',$id) : route('order.store')  }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($modify)
            @method('PUT')
        @endif
        <div class="row">

          <div class="col-xs-12">
             <div class="form-group">
                 <strong>Client:</strong>
                 <select class="selectpicker form-control" data-live-search="true" data-size="15" name="customer_id" title="Choose a Customer" required>
                   @foreach ($customers as $customer)
                    <option data-tokens="{{ $customer->name }} {{ $customer->email }}  {{ $customer->phone }} {{ $customer->instahandler }}" value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone }}</option>
                  @endforeach
                </select>

                 @if ($errors->has('customer_id'))
                     <div class="alert alert-danger">{{$errors->first('customer_id')}}</div>
                 @endif
             </div>
         </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong> Order Type :</strong>
			        <?php

	                $order_types = [
	                	'offline' => 'offline',
                        'online' => 'online'
                    ];

			        echo Form::select('order_type',$order_types, ( old('order_type') ? old('order_type') : $order_type ), ['class' => 'form-control']);?>
                    @if ($errors->has('order_type'))
                        <div class="alert alert-danger">{{$errors->first('order_type')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Order Date:</strong>
                    <input type="date" class="form-control" name="order_date" placeholder="Order Date"
                           value="{{ old('order_date') ? old('order_date') : $order_date }}"/>
                    @if ($errors->has('order_date'))
                        <div class="alert alert-danger">{{$errors->first('order_date')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Date of Delivery:</strong>
                    <input type="date" class="form-control" name="date_of_delivery" placeholder="Date of Delivery"
                           value="{{ old('date_of_delivery') ? old('date_of_delivery') : $date_of_delivery }}"/>
                    @if ($errors->has('date_of_delivery'))
                        <div class="alert alert-danger">{{$errors->first('date_of_delivery')}}</div>
                    @endif
                </div>
            </div>

            {{-- <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Client Name:</strong>
                    <input type="text" class="form-control" name="client_name" placeholder="Client Name"
                           value="{{ old('client_name') ? old('client_name') : $client_name }}" id="customer_suggestions" required/>
                    @if ($errors->has('client_name'))
                        <div class="alert alert-danger">{{$errors->first('client_name')}}</div>
                    @endif
                </div>
            </div> --}}

            {{-- <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>City:</strong>
                    <input type="text" class="form-control" name="city" placeholder="City"
                           value="{{ old('city') ? old('city') : $city }}"/>
                    @if ($errors->has('city'))
                        <div class="alert alert-danger">{{$errors->first('city')}}</div>
                    @endif
                </div>
            </div> --}}

            {{-- <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Contact Detail:</strong>
                    <input type="text" class="form-control" name="contact_detail" placeholder="Contact Detail"
                           value="{{ old('contact_detail') ? old('contact_detail') : $contact_detail }}"/>
                    @if ($errors->has('contact_detail'))
                        <div class="alert alert-danger">{{$errors->first('contact_detail')}}</div>
                    @endif
                    @if ($message = Session::get('phone_error'))
                        <div class="alert alert-danger">{{$message}}</div>
                    @endif
                </div>
            </div> --}}

            {{--<div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Product Price:</strong>
                    <input type="text" class="form-control" name="product_price" placeholder="Product Price"
                           value="{{ old('product_price') ? old('product_price') : $product_price }}"/>
                    @if ($errors->has('product_price'))
                        <div class="alert alert-danger">{{$errors->first('product_price')}}</div>
                    @endif
                </div>
            </div>--}}

            {{-- @if($modify == 1) --}}

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong> Products Attached:</strong>
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
                            @foreach($order_products  as $order_product)
                                <tr>
                                    @if(isset($order_product['product']))
                                        <th><img width="200" src="{{ $order_product['product']['image'] }}" /></th>
                                        <th>{{ $order_product['product']['name'] }}</th>
                                        <th>{{ $order_product['product']['sku'] }}</th>
                                        <th>{{ $order_product['product']['color'] }}</th>
                                        <th>{{ \App\Http\Controllers\BrandController::getBrandName($order_product['product']['brand']) }}</th>
                                    @else
                                        <th></th>
                                        <th></th>
                                        <th>{{$order_product['sku']}}</th>
                                        <th></th>
                                        <th></th>
                                    @endif

                                    <th>
                                        <input class="table-input" type="text" value="{{ $order_product['product_price'] }}" name="order_products[{{ $order_product['id'] }}][product_price]">
                                    </th>
                                    <th>
                                        @if(!empty($order_product['product']['size']))
					                        <?php

					                        $sizes = \App\Helpers::explodeToArray($order_product['product']['size']);
					                        $size_name = 'order_products['.$order_product['id'].'][size]';

					                        echo Form::select($size_name,$sizes,( $order_product['size'] ), ['placeholder' => 'Select a size'])
					                        ?>
                                        @else
                                            <select hidden class="form-control" name="order_products[{{ $order_product['id'] }}][size]">
                                                <option selected="selected" value=""></option>
                                            </select>
                                            nil
                                        @endif
                                    </th>
                                    <th>
                                        <input class="table-input" type="number" value="{{ $order_product['qty'] }}" name="order_products[{{ $order_product['id'] }}][qty]">
                                    </th>
                                    @if(isset($order_product['product']))
                                        <th>
                                            <a class="btn btn-image" href="{{ route('products.show',$order_product['product']['id']) }}"><img src="/images/view.png" /></a>
                                            <a class="btn btn-image remove-product" href="#" data-product="{{ $order_product['id'] }}"><img src="/images/delete.png" /></a>
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
                        <a href="{{ route('attachProducts',['order',$id]) }}" class="btn btn-image"><img src="/images/attach.png" /></a>
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
                        <button type="button" class="btn btn-success" id="createProduct">Create</button>
                      </div>
                    </div>

                  </div>
                </div>
            {{-- @endif --}}

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Advance Amount:</strong>
                    <input type="text" class="form-control" name="advance_detail" placeholder="Advance Detail"
                           value="{{ old('advance_detail') ? old('advance_detail') : $advance_detail }}"/>
                    @if ($errors->has('advance_detail'))
                        <div class="alert alert-danger">{{$errors->first('advance_detail')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Advance Date:</strong>
                    <input type="date" class="form-control" name="advance_date" placeholder="Advance Date"
                           value="{{ old('advance_date') ? old('advance_date') : $advance_date }}"/>
                    @if ($errors->has('advance_date'))
                        <div class="alert alert-danger">{{$errors->first('advance_date')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Balance Amount:</strong>
                    <input type="text" class="form-control" name="balance_amount" placeholder="Balance Amount"
                           value="{{ old('balance_amount') ? old('balance_amount') : $balance_amount }}"/>
                    @if ($errors->has('balance_amount'))
                        <div class="alert alert-danger">{{$errors->first('balance_amount')}}</div>
                    @endif
                </div>
            </div>

            {{--<div class="col-xs-12 col-sm-12 col-md-12">--}}
                {{--<div class="form-group">--}}
                    {{--<strong> Brand :</strong>--}}

			        <?php
//			        $brands = \App\Brand::getAll();
//			        echo Form::select('brand',$brands, ( old('brand') ? old('brand') : $brand ), ['placeholder' => 'Select a brand','class' => 'form-control']);?>

{{--                    @if ($errors->has('brand'))--}}
                        {{--<div class="alert alert-danger">{{$errors->first('brand')}}</div>--}}
                    {{--@endif--}}
                {{--</div>--}}
            {{--</div>--}}

           {{-- <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Product Detail:</strong>
                    <input type="text" class="form-control" name="product_detail" placeholder="Product Detail"
                           value="{{ old('product_detail') ? old('product_detail') : $product_detail }}"/>
                    @if ($errors->has('product_detail'))
                        <div class="alert alert-danger">{{$errors->first('product_detail')}}</div>
                    @endif
                </div>
            </div>--}}

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong> Name of Order Handler :</strong>
			        <?php
			        echo Form::select('sales_person',$sales_persons, ( old('sales_person') ? old('sales_person') : $sales_person ), ['placeholder' => 'Select a name','class' => 'form-control']);?>
                    @if ($errors->has('sales_person'))
                        <div class="alert alert-danger">{{$errors->first('sales_person')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Office Phone Number:</strong>
                    <Select name="whatsapp_number" class="form-control">
                              <option value>None</option>
                               <option value="919167152579" {{old('whatsapp_number') ? (old('whatsapp_number') == '919167152579' ? 'Selected=Selected':'') : ('919167152579'== $whatsapp_number ? 'Selected=Selected':'')}}>00</option>
                               <option value="918291920452" {{old('whatsapp_number') ? (old('whatsapp_number') == '918291920452' ? 'Selected=Selected':'') : ('918291920452'== $whatsapp_number ? 'Selected=Selected':'')}}>02</option>
                               <option value="918291920455" {{old('whatsapp_number') ? (old('whatsapp_number') == '918291920455' ? 'Selected=Selected':'') : ('918291920455'== $whatsapp_number ? 'Selected=Selected':'')}}>03</option>
                               <option value="919152731483" {{old('whatsapp_number') ? (old('whatsapp_number') == '919152731483' ? 'Selected=Selected':'') : ('919152731483'== $whatsapp_number ? 'Selected=Selected':'')}}>04</option>
                               <option value="919152731484" {{old('whatsapp_number') ? (old('whatsapp_number') == '919152731484' ? 'Selected=Selected':'') : ('919152731484'== $whatsapp_number ? 'Selected=Selected':'')}}>05</option>
                               <option value="971562744570" {{old('whatsapp_number') ? (old('whatsapp_number') == '971562744570' ? 'Selected=Selected':'') : ('971562744570'== $whatsapp_number ? 'Selected=Selected':'')}}>06</option>
                               <option value="918291352520" {{old('whatsapp_number') ? (old('whatsapp_number') == '918291352520' ? 'Selected=Selected':'') : ('918291352520'== $whatsapp_number ? 'Selected=Selected':'')}}>08</option>
                               <option value="919004008983" {{old('whatsapp_number') ? (old('whatsapp_number') == '919004008983' ? 'Selected=Selected':'') : ('919004008983'== $whatsapp_number ? 'Selected=Selected':'')}}>09</option>
                       </Select>
                    @if ($errors->has('whatsapp_number'))
                        <div class="alert alert-danger">{{$errors->first('whatsapp_number')}}</div>
                    @endif
                </div>
            </div>


            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong> Status :</strong>
			        <?php
			        $orderStatus = new \App\ReadOnly\OrderStatus;

			        echo Form::select('order_status',$orderStatus->all(), ( old('order_status') ? old('order_status') : $order_status ), ['placeholder' => 'Select a status','class' => 'form-control']);?>

                    @if ($errors->has('order_status'))
                        <div class="alert alert-danger">{{$errors->first('order_status')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Estimated Delivery Date:</strong>
                    <input type="date" class="form-control" name="estimated_delivery_date" placeholder="Advance Date"
                           value="{{ old('estimated_delivery_date') ? old('estimated_delivery_date') : $estimated_delivery_date }}"/>
                    @if ($errors->has('estimated_delivery_date'))
                        <div class="alert alert-danger">{{$errors->first('estimated_delivery_date')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Received By:</strong>
                    <input type="text" class="form-control" name="received_by" placeholder="Received By"
                           value="{{ old('received_by') ? old('received_by') : $received_by }}"/>
                    @if ($errors->has('received_by'))
                        <div class="alert alert-danger">{{$errors->first('received_by')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong> Payment Mode :</strong>
			        <?php
			        $paymentModes = new \App\ReadOnly\PaymentModes();

			        echo Form::select('payment_mode',$paymentModes->all(), ( old('payment_mode') ? old('payment_mode') : $payment_mode ), ['placeholder' => 'Select a mode','class' => 'form-control']);?>

                    @if ($errors->has('payment_mode'))
                        <div class="alert alert-danger">{{$errors->first('payment_mode')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Note if any:</strong>
                    <input type="text" class="form-control" name="note_if_any" placeholder="Note if any"
                           value="{{ old('note_if_any') ? old('note_if_any') : $note_if_any }}"/>
                    @if ($errors->has('note_if_any'))
                        <div class="alert alert-danger">{{$errors->first('note_if_any')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-secondary">+</button>
            </div>

        </div>
    </form>

    <form action="" method="POST" id="product-remove-form">
      @csrf
    </form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function() {
        $('#createProduct').on('click', function() {
          var token = "{{ csrf_token() }}";
          var url = "{{ route('products.store') }}";
          var order_id = {{ $id }};
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
              var brands_array = {!! json_encode(\App\Helpers::getUserArray(\App\Brand::all())) !!};
              var show_url = "{{ url('products') }}/" + response.product.id;
              var delete_url = "{{ url('deleteOrderProduct') }}/" + response.order.id;
              var product_row = '<tr><th><img width="200" src="' + response.product_image + '" /></th>';
                  product_row += '<th>' + response.product.name + '</th>';
                  product_row += '<th>' + response.product.sku + '</th>';
                  product_row += '<th>' + response.product.color + '</th>';
                  product_row += '<th>' + brands_array[response.product.brand] + '</th>';
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
                  product_row += '<th><a class="btn btn-image" href="' + show_url + '"><img src="/images/view.png" /></a>';
                  product_row += '<a class="btn btn-image remove-product" href="#" data-product="' + response.order.id + '"><img src="/images/delete.png" /></a></th>';
                  product_row += '</tr>';

              $('#products-table').append(product_row);
            }
          });
        });

        $(document).on('click', '.remove-product', function(e) {
          e.preventDefault();

          var product_id = $(this).data('product');
          var url = "{{ url('deleteOrderProduct') }}/" + product_id;
          // var token = "{{ csrf_token() }}";

          $('#product-remove-form').attr('action', url);
          $('#product-remove-form').submit();
        });

        var searchSuggestions = {!! json_encode($customer_suggestions) !!};

	      $('#customer_suggestions').autocomplete({
	        source: function(request, response) {
	          var results = $.ui.autocomplete.filter(searchSuggestions, request.term);

	          response(results.slice(0, 10));
	        }
	      });
      });
    </script>
@endsection
