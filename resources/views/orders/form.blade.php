@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ $modify ? 'Edit Order' : 'Create Create' }}</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('order.index') }}"> Back</a>
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

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Client Name:</strong>
                    <input type="text" class="form-control" name="client_name" placeholder="Client Name"
                           value="{{ old('client_name') ? old('client_name') : $client_name }}"/>
                    @if ($errors->has('client_name'))
                        <div class="alert alert-danger">{{$errors->first('client_name')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>City:</strong>
                    <input type="text" class="form-control" name="city" placeholder="City"
                           value="{{ old('city') ? old('city') : $city }}"/>
                    @if ($errors->has('city'))
                        <div class="alert alert-danger">{{$errors->first('city')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Contact Detail:</strong>
                    <input type="text" class="form-control" name="contact_detail" placeholder="Contact Detail"
                           value="{{ old('contact_detail') ? old('contact_detail') : $contact_detail }}"/>
                    @if ($errors->has('contact_detail'))
                        <div class="alert alert-danger">{{$errors->first('contact_detail')}}</div>
                    @endif
                </div>
            </div>

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

            @if($modify == 1)

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong> Products Attacted:</strong>
                        <table class="table table-bordered">
                            <tr>
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
                                        <th>{{ $order_product['product']['name'] }}</th>
                                        <th>{{ $order_product['product']['sku'] }}</th>
                                        <th>{{ $order_product['product']['color'] }}</th>
                                        <th>{{ \App\Http\Controllers\BrandController::getBrandName($order_product['product']['brand']) }}</th>
                                    @else
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
                                            <a class="btn btn-primary btn-success" href="{{ route('products.show',$order_product['product']['id']) }}">View</a>
                                            <form class="display-inline" method="post" action="{{ route('deleteOrderProduct',$order_product['id']) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-danger">Remove</button>
                                            </form>
                                        </th>
                                    @else
                                        <th></th>
                                    @endif
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <a href="{{ route('attachProducts',['order',$id]) }}"><button type="button" class="btn btn-primary">Attach From Grid</button></a>
                    </div>
                </div>
            @endif

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
                    <input type="text" class="form-control" name="office_phone_number" placeholder="Office Phone Number"
                           value="{{ old('office_phone_number') ? old('office_phone_number') : $office_phone_number }}"/>
                    @if ($errors->has('office_phone_number'))
                        <div class="alert alert-danger">{{$errors->first('office_phone_number')}}</div>
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
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>

        </div>
    </form>
@endsection