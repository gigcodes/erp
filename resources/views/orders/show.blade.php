@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2> View Order</h2>
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

    <div id="exTab2" class="container">
           <ul class="nav nav-tabs">
              <li class="active">
                 <a  href="#1" data-toggle="tab">Order Info</a>
              </li>
              {{-- <li><a href="#2" data-toggle="tab">WhatsApp Conversation</a>
              </li> --}}
              <li><a href="#3" data-toggle="tab">Call Recordings</a>
              </li>
           </ul>
        </div>

    <!-- New order Layout -->
    <div class="tab-content ">
            <!-- Pending task div start -->
            <div class="tab-pane active" id="1">

              <form action="{{ route('order.update',$id) }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  @method('PUT')

                <div class="row">
                    <div class="col-md-6 col-12">
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

                        <div class="form-group">
                            <strong>Sale Order No. </strong> {{ $order_id }}
                        </div>

                        <div class="form-group">
                            <strong>Order Date:</strong>
                            <input type="date" class="form-control" name="order_date" placeholder="Order Date"
                                   value="{{ old('order_date') ? old('order_date') : $order_date }}"/>
                            @if ($errors->has('order_date'))
                                <div class="alert alert-danger">{{$errors->first('order_date')}}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong> Price :</strong>
                            {{ $total_price }}
                        </div>

                        <div class="form-group">
                            <strong>Date of Delivery:</strong>
                            <input type="date" class="form-control" name="date_of_delivery" placeholder="Date of Delivery"
                                   value="{{ old('date_of_delivery') ? old('date_of_delivery') : $date_of_delivery }}"/>
                            @if ($errors->has('date_of_delivery'))
                                <div class="alert alert-danger">{{$errors->first('date_of_delivery')}}</div>
                            @endif
                        </div>


                        <div class="form-group">
                            <strong>Office Phone Number:</strong>
                            <input type="text" class="form-control" name="office_phone_number" placeholder="Office Phone Number"
                                   value="{{ old('office_phone_number') ? old('office_phone_number') : $office_phone_number }}"/>
                            @if ($errors->has('office_phone_number'))
                                <div class="alert alert-danger">{{$errors->first('office_phone_number')}}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong>Solo Phone:</strong>
                            <Select name="whatsapp_number" class="form-control">
                                      <option value>None</option>
                                       <option value="919167152579" {{'919167152579' == $whatsapp_number ? 'Selected=Selected':''}}>00</option>
                                       <option value="918291920452" {{'918291920452'== $whatsapp_number ? 'Selected=Selected':''}}>02</option>
                                       <option value="918291920455" {{'918291920455'== $whatsapp_number ? 'Selected=Selected':''}}>03</option>
                                       <option value="919152731483" {{'919152731483'== $whatsapp_number ? 'Selected=Selected':''}}>04</option>
                                       <option value="919152731484" {{'919152731484'== $whatsapp_number ? 'Selected=Selected':''}}>05</option>
                                       <option value="919152731486" {{'919152731486'== $whatsapp_number ? 'Selected=Selected':''}}>06</option>
                                       <option value="918291352520" {{'918291352520'== $whatsapp_number ? 'Selected=Selected':''}}>08</option>
                                       <option value="919004008983" {{'919004008983'== $whatsapp_number ? 'Selected=Selected':''}}>09</option>
                               </Select>
                            @if ($errors->has('whatsapp_number'))
                                <div class="alert alert-danger">{{$errors->first('whatsapp_number')}}</div>
                            @endif
                        </div>




                        @php $status = ( new \App\ReadOnly\OrderStatus )->getNameById( $order_status ); @endphp

                         <div class="form-group">
                             <strong>status:</strong>
                             <Select name="status" class="form-control" id="change_status">
                                  @foreach($order_statuses as $key => $value)
                                   <option value="{{$value}}" {{$value == $status ? 'Selected=Selected':''}}>{{$key}}</option>
                                   @endforeach
                             </Select>
                             <span id="change_status_message" class="text-success" style="display: none;">Successfully changed status</span>
                         </div>

                         <div class="form-group">
                             <strong>Estimated Delivery Date:</strong>
                             <input type="date" class="form-control" name="estimated_delivery_date" placeholder="Advance Date"
                                    value="{{ old('estimated_delivery_date') ? old('estimated_delivery_date') : $estimated_delivery_date }}"/>
                             @if ($errors->has('estimated_delivery_date'))
                                 <div class="alert alert-danger">{{$errors->first('estimated_delivery_date')}}</div>
                             @endif
                         </div>


                         <div class="form-group">
                             <strong>Note if any:</strong>
                             <input type="text" class="form-control" name="note_if_any" placeholder="Note if any"
                                    value="{{ old('note_if_any') ? old('note_if_any') : $note_if_any }}"/>
                             @if ($errors->has('note_if_any'))
                                 <div class="alert alert-danger">{{$errors->first('note_if_any')}}</div>
                             @endif
                         </div>


                        <div class="form-group">
                            <strong> Name of Order Handler :</strong>
        			        <?php
        			        echo Form::select('sales_person',$sales_persons, ( old('sales_person') ? old('sales_person') : $sales_person ), ['placeholder' => 'Select a name','class' => 'form-control']);?>
                            @if ($errors->has('sales_person'))
                                <div class="alert alert-danger">{{$errors->first('sales_person')}}</div>
                            @endif
                        </div>

                         <div class="form-group">
                             <strong>Created by:</strong>
                             {{ $user_id != 0 ? App\Helpers::getUserNameById($user_id) : 'Unknown' }}
                         </div>
                        <div class="form-group">
                            <strong>Remark</strong>
                            {{ $remark }}
                        </div>

                    </div>
                    <div class="col-md-6 col-12">
                      <div class="form-group">
                          <strong>Client Name:</strong>
                          <input type="text" class="form-control" name="client_name" placeholder="Client Name"
                                 value="{{ old('client_name') ? old('client_name') : $client_name }}"/>
                          @if ($errors->has('client_name'))
                              <div class="alert alert-danger">{{$errors->first('client_name')}}</div>
                          @endif
                      </div>

                      <div class="form-group">
                          <strong>City:</strong>
                          <input type="text" class="form-control" name="city" placeholder="City"
                                 value="{{ old('city') ? old('city') : $city }}"/>
                          @if ($errors->has('city'))
                              <div class="alert alert-danger">{{$errors->first('city')}}</div>
                          @endif
                      </div>

                      <div class="form-group">
                          <strong>Contact Detail:</strong>
                          <input type="text" class="form-control" name="contact_detail" placeholder="Contact Detail"
                                 value="{{ old('contact_detail') ? old('contact_detail') : $contact_detail }}"/>
                          @if ($errors->has('contact_detail'))
                              <div class="alert alert-danger">{{$errors->first('contact_detail')}}</div>
                          @endif
                      </div>

                        <h3>Payment Details</h3>

                        <div class="form-group">
                            <strong>Balance Amount:</strong>
                            <input type="text" class="form-control" name="balance_amount" placeholder="Balance Amount"
                                   value="{{ old('balance_amount') ? old('balance_amount') : $balance_amount }}"/>
                            @if ($errors->has('balance_amount'))
                                <div class="alert alert-danger">{{$errors->first('balance_amount')}}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong> Payment Mode :</strong>
        			        <?php
        			        $paymentModes = new \App\ReadOnly\PaymentModes();

        			        echo Form::select('payment_mode',$paymentModes->all(), ( old('payment_mode') ? old('payment_mode') : $payment_mode ), ['placeholder' => 'Select a mode','class' => 'form-control']);?>

                            @if ($errors->has('payment_mode'))
                                <div class="alert alert-danger">{{$errors->first('payment_mode')}}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong>Advance Amount:</strong>
                            <input type="text" class="form-control" name="advance_detail" placeholder="Advance Detail"
                                   value="{{ old('advance_detail') ? old('advance_detail') : $advance_detail }}"/>
                            @if ($errors->has('advance_detail'))
                                <div class="alert alert-danger">{{$errors->first('advance_detail')}}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong>Received By:</strong>
                            <input type="text" class="form-control" name="received_by" placeholder="Received By"
                                   value="{{ old('received_by') ? old('received_by') : $received_by }}"/>
                            @if ($errors->has('received_by'))
                                <div class="alert alert-danger">{{$errors->first('received_by')}}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong>Advance Date:</strong>
                            <input type="date" class="form-control" name="advance_date" placeholder="Advance Date"
                                   value="{{ old('advance_date') ? old('advance_date') : $advance_date }}"/>
                            @if ($errors->has('advance_date'))
                                <div class="alert alert-danger">{{$errors->first('advance_date')}}</div>
                            @endif
                        </div>


                    </div>

                    <div class="col-xs-12">
                      <div class="col-xs-12 col-sm-12 col-md-12">
                          <div class="form-group">
                              <strong> Products Attacted:</strong>
                              <table class="table table-bordered" id="products-table">
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
                    </div>

                    <div class="col-xs-12 text-center">
                      <button type="submit" class="btn btn-secondary">Update</button>
                    </div>
                </div>
              </form>

              <div id="statusModal" class="modal fade" role="dialog">
                <div class="modal-dialog">

                  <!-- Modal content-->
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title">Create Action</h4>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <form action="{{ route('status.store') }}" method="POST" enctype="multipart/form-data">
                      @csrf

                      <div class="modal-body">
                        <div class="form-group">
                            <strong>Order Status:</strong>
                             <input type="text" class="form-control" name="status" placeholder="Order Status" id="status" required />
                             @if ($errors->has('status'))
                                 <div class="alert alert-danger">{{$errors->first('status')}}</div>
                             @endif
                        </div>

                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Create</button>
                      </div>
                    </form>
                  </div>

                </div>
              </div>

              <div class="row">
                <div class="col-md-6 col-12">
                  <form action="{{ route('status.report.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="order_id" value="{{ $id }}">

                    <div class="form-group">
                      <strong>Next action due</strong>
                      <a href="#" data-toggle="modal" data-target="#statusModal" class="btn-link">Add Action</a>

                      <select class="form-control" name="status_id" required>
                        <option value="">Select action</option>
                        @foreach ($order_status_report as $status)
                          <option value="{{ $status->id }}">{{ $status->status }}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="form-group" id="completion_form_group">
                      <strong>Completion Date:</strong>
                      <div class='input-group date' id='completion-datetime'>
                        <input type='text' class="form-control" name="completion_date" value="{{ date('Y-m-d H:i') }}" />

                        <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                      </div>

                      @if ($errors->has('completion_date'))
                          <div class="alert alert-danger">{{$errors->first('completion_date')}}</div>
                      @endif
                    </div>

                    <button type="submit" class="btn btn-secondary">Add Report</button>
                  </form>

                  @if (count($order_reports) > 0)
                    <h4>Order Reports</h4>

                    <table class="table table-bordered mt-4">
                      <thead>
                        <tr>
                          <th>Status</th>
                          <th>Created at</th>
                          <th>Creator</th>
                          <th>Due date</th>
                        </tr>
                      </thead>

                      <tbody>
                        @php $users_array = \App\Helpers::getUserArray(\App\User::all()); @endphp
                        @foreach ($order_reports as $report)
                          <tr>
                            <td>{{ $report->status }}</td>
                            <td>{{ Carbon\Carbon::parse($report->created_at)->format('d-m H:i') }}</td>
                            <td>{{ $users_array[$report->user_id] }}</td>
                            <td>{{ Carbon\Carbon::parse($report->completion_date)->format('d-m H:i') }}</td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  @endif
                </div>
              </div>


                <div class="form-group">
                    <div class="text-center">
                        <h3> Products Details:</h3>
                    </div>
                    <table class="table table-bordered mt-4">
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Sku</th>
                            <th>Color</th>
                            <th>Brand</th>
                            <th>Price</th>
                            <th>Size</th>
                            <th>Qty</th>
                            <th style="width: 100px">Action</th>
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

                                <th>{{ $order_product['product_price']}}</th>
                                <th>{{$order_product['size']}}</th>
                                <th>{{ $order_product['qty'] }}</th>

                                @if(isset($order_product['product']))
                                    <th>
                                        <a class="btn btn-image"
                                           href="{{ route('products.show',$order_product['product']['id']) }}"><img src="/images/view.png" /></a>
                                    </th>
                                @else
                                    <th></th>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                </div>

                {{-- <div class="row">
                    <div class="col-md-6 col-12"> --}}


                        </div>
                    {{-- </div>
                </div> --}}
        {{-- <div class="tab-pane" id="2">
            <div class="chat-frame">
                <div class="col-xs-12 col-sm-12">
                    <h3 style="text-center">WhatsApp Messages</h3>
                 </div>
                <div class="col-xs-12 col-sm-12">
                    <div class="row">
                       <div class="col-md-12" id="waMessages">
                       </div>
                    </div>
                </div>
            </div>

            <div class="row">
              <div class="col-xs-10">
                      <textarea id="waNewMessage" class="form-control" placeholder="Type new message.."></textarea>
                      <br/>
                      <label>Attach Media</label>
                      <input id="waMessageMedia" type="file" name="media" />
              </div>
              <div class="col-xs-2">
                  <button id="waMessageSend" class="btn btn-image"><img src="/images/filled-sent.png" /></button>
              </div>
            </div>


        </div> --}}

        <div class="tab-pane" id="3">
          <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3 style="text-center">Call Recordings</h3>
             </div>

            <div class="col-xs-12 col-sm-12">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <td>Recording</td>
                                <td>Created At</td>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach ($order_recordings as $recording)
                                <tr>
                                    <td><a href="{{$recording['recording_url']}}" target="_blank">{{$recording['recording_url']}}</a></td>
                                    <td>{{$recording['created_at']}}</td>
                                </tr>
                            @endforeach --}}
                        </tbody>
                    </table>
                </div>
            </div>
          </div>

        </div>

      </div>

      <div id="taskModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Create Task</h4>
            </div>

            <form action="{{ route('task.store') }}" method="POST" enctype="multipart/form-data">
              @csrf

              <input type="hidden" name="task_type" value="quick_task">
              <input type="hidden" name="model_type" value="order">
              <input type="hidden" name="model_id" value="{{$id}}">

              <div class="modal-body">
                <div class="form-group">
                    <strong>Task Subject:</strong>
                     <input type="text" class="form-control" name="task_subject" placeholder="Task Subject" id="task_subject" required />
                     @if ($errors->has('task_subject'))
                         <div class="alert alert-danger">{{$errors->first('task_subject')}}</div>
                     @endif
                </div>
                <div class="form-group">
                    <strong>Task Details:</strong>
                     <textarea class="form-control" name="task_details" placeholder="Task Details" required></textarea>
                     @if ($errors->has('task_details'))
                         <div class="alert alert-danger">{{$errors->first('task_details')}}</div>
                     @endif
                </div>

                <div class="form-group" id="completion_form_group">
                  <strong>Completion Date:</strong>
                  <div class='input-group date' id='completion-datetime'>
                    <input type='text' class="form-control" name="completion_date" value="{{ date('Y-m-d H:i') }}" required/>

                    <span class="input-group-addon">
                      <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                  </div>

                  @if ($errors->has('completion_date'))
                      <div class="alert alert-danger">{{$errors->first('completion_date')}}</div>
                  @endif
                </div>

                <div class="form-group">
                    <strong>Assigned To:</strong>
                    <select name="assign_to[]" class="form-control" multiple required>
                      @foreach($users as $user)
                        <option value="{{$user['id']}}">{{$user['name']}}</option>
                      @endforeach
                    </select>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-secondary">Create</button>
              </div>
            </form>
          </div>

        </div>
      </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12 mb-3">
              <button type="button" class="btn btn-secondary mb-3" data-toggle="modal" data-target="#taskModal" id="addTaskButton">Add Task</button>

              @if (count($tasks) > 0)
                <table class="table">
                    <thead>
                      <tr>
                          <th>Sr No</th>
                          <th>Date</th>
                          <th class="category">Category</th>
                          <th>Task Subject</th>
                          <th>Est Completion Date</th>
                          <th>Assigned From</th>
                          <th>&nbsp;</th>
                          {{-- <th>Remarks</th> --}}
                          <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; $users_array = \App\Helpers::getUserArray(\App\User::all()); ?>
                      @foreach($tasks as $task)
                    <tr class="{{ \App\Http\Controllers\TaskModuleController::getClasses($task) }}" id="task_{{ $task['id'] }}">
                        <td>{{$i++}}</td>
                        <td>{{ Carbon\Carbon::parse($task['created_at'])->format('d-m H:i') }}</td>
                        <td> {{ isset( $categories[$task['category']] ) ? $categories[$task['category']] : '' }}</td>
                        <td class="task-subject" data-subject="{{$task['task_subject'] ? $task['task_subject'] : 'Task Details'}}" data-details="{{$task['task_details']}}" data-switch="0">{{ $task['task_subject'] ? $task['task_subject'] : 'Task Details' }}</td>
                        <td> {{ Carbon\Carbon::parse($task['completion_date'])->format('d-m H:i')  }}</td>
                        <td>{{ $users_array[$task['assign_from']] }}</td>
                        @if( $task['assign_to'] == Auth::user()->id )
                            <td><a href="/task/complete/{{$task['id']}}">Complete</a></td>
                        @else
                            <td>Assign to  {{ $task['assign_to'] ? $users_array[$task['assign_to']] : 'Nil'}}</td>
                        @endif
                        {{-- <td> --}}
                          <!-- @include('task-module.partials.remark',$task)  -->
                        {{-- </td> --}}
                        <td>
                            <a href id="add-new-remark-btn" class="add-task" data-toggle="modal" data-target="#add-new-remark_{{$task['id']}}" data-id="{{$task['id']}}">Add</a>
                            <span> | </span>
                            <a href id="view-remark-list-btn" class="view-remark" data-toggle="modal" data-target="#view-remark-list" data-id="{{$task['id']}}">View</a>
                          <!--<button class="delete-task" data-id="{{$task['id']}}">Delete</button>-->
                        </td>
                    </tr>

                    <!-- Modal -->
                    <div id="add-new-remark_{{$task['id']}}" class="modal fade" role="dialog">
                      <div class="modal-dialog">

                        <!-- Modal content-->
                        <div class="modal-content">
                          <div class="modal-header">
                            <h4 class="modal-title">Add New Remark</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>

                          </div>
                          <div class="modal-body">
                            <form id="add-remark">
                              <input type="hidden" name="id" value="">
                              <textarea id="remark-text_{{$task['id']}}" rows="1" name="remark" class="form-control"></textarea>
                              <button type="button" class="mt-2 " onclick="addNewRemark({{$task['id']}})">Add Remark</button>
                          </form>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                          </div>
                        </div>

                      </div>
                    </div>

                    <!-- Modal -->
                    <div id="view-remark-list" class="modal fade" role="dialog">
                      <div class="modal-dialog">

                        <!-- Modal content-->
                        <div class="modal-content">
                          <div class="modal-header">
                            <h4 class="modal-title">View Remark</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>

                          </div>
                          <div class="modal-body">
                            <div id="remark-list">

                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                          </div>
                        </div>

                      </div>
                    </div>
                   @endforeach
                    </tbody>
                  </table>
                @endif
            </div>
            <div class="col-xs-12">
              <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <form action="{{ route('message.store') }}" method="POST" enctype="multipart/form-data" class="d-flex">
                        @csrf

                        {{-- <div class="row"> --}}
                          <div class="form-group">
                            <div class="upload-btn-wrapper btn-group">
                              <button class="btn btn-image px-1"><img src="/images/upload.png"/></button>
                              <input type="file" name="image"/>
                              <button type="submit" class="btn btn-image px-1 send-communication"><img src="/images/filled-sent.png" /></button>
                            </div>
                          </div>

                          {{-- <div class="col-xs-6"> --}}
                            <div class="form-group flex-fill">
                                <textarea class="form-control" name="body"
                                          placeholder="Received from Customer"></textarea>

                                <input type="hidden" name="moduletype" value="order"/>
                                <input type="hidden" name="moduleid" value="{{$id}}"/>
                                <input type="hidden" name="assigned_user" value="{{$sales_person}}" />
                                <input type="hidden" name="status" value="0"/>
                            </div>
                          {{-- </div>

                        </div> --}}

                    </form>
                  </div>

                  <div class="col-xs-12 col-sm-6">
                    <form action="{{ route('message.store') }}" method="POST" enctype="multipart/form-data" class="d-flex">
                        @csrf

                        {{-- <div class="row"> --}}
                          <div class="form-group">
                            <div class="upload-btn-wrapper btn-group">
                              <button class="btn btn-image px-1"><img src="/images/upload.png"/></button>
                              <input type="file" name="image"/>
                              <button type="submit" class="btn btn-image px-1 send-communication"><img src="/images/filled-sent.png"/></button>
                            </div>
                          </div>

                          {{-- <div class="col-xs-6"> --}}
                            <div class="form-group flex-fill">
                                <textarea class="form-control mb-3" name="body"
                                          placeholder="Send for Approval" id="message-body"></textarea>

                                <input type="hidden" name="moduletype" value="order"/>
                                <input type="hidden" name="moduleid" value="{{$id}}"/>
                                <input type="hidden" name="status" value="1"/>
                                <input type="hidden" name="assigned_user" value="{{$sales_person}}" />

                                <p class="pb-4" style="display: block;">
                                    {{-- <strong>Quick Reply</strong> --}}

                                    <select name="quickComment" id="quickComment" class="form-control">
                                        <option value="">Quick Reply</option>
                                        @foreach($approval_replies as $reply )
                                            <option value="{{$reply->reply}}">{{$reply->reply}}</option>
                                        @endforeach
                                    </select>
                                </p>
                            </div>
                          {{-- </div>
                        </div> --}}
                    </form>
                  </div>
                  <div class="col-xs-12 col-sm-6">
                    <form action="{{ route('message.store') }}" method="POST" enctype="multipart/form-data" class="d-flex">
                        @csrf

                        {{-- <div class="row"> --}}
                          <div class="form-group">
                            <div class="upload-btn-wrapper btn-group">
                              <button class="btn btn-image px-1"><img src="/images/upload.png"/></button>
                              <input type="file" name="image"/>
                              <button type="submit" class="btn btn-image px-1 send-communication"><img src="/images/filled-sent.png"/></button>
                            </div>
                          </div>
                          {{-- <div class="col-xs-6"> --}}
                            <div class="form-group flex-fill">
                                <textarea class="form-control mb-3" name="body"
                                          placeholder="Internal Communications" id="internal-message-body"></textarea>

                                <input type="hidden" name="moduletype" value="order"/>
                                <input type="hidden" name="moduleid" value="{{$id}}"/>
                                <input type="hidden" name="status" value="4"/>
                                {{-- <input type="hidden" name="assigned_user" value="{{$sales_person}}" /> --}}

                                <strong>Assign to</strong>
                                <select name="assigned_user" class="form-control mb-3" required>
                                  <option value="">Select User</option>
                                  @if (isset($sales_person))
                                    <option value="{{ $sales_person }}">Order Handler</option>
                                  @endif
                                  @foreach($users as $user)
                                    <option value="{{$user['id']}}">{{$user['name']}}</option>
                                  @endforeach
                                </select>

                                <p class="pb-4" style="display: block;">
                                    {{-- <strong>Quick Reply</strong> --}}

                                    <select name="quickCommentInternal" id="quickCommentInternal" class="form-control">
                                        <option value="">Quick Reply</option>
                                        @foreach($internal_replies as $reply )
                                            <option value="{{$reply->reply}}">{{$reply->reply}}</option>
                                        @endforeach
                                    </select>
                                </p>
                            </div>
                          {{-- </div>
                        </div> --}}
                    </form>
                  </div>

                  <div class="col-xs-12 col-sm-6">
                    <div class="d-flex">
                      <div class="form-group">
                        <a href="/leads?type=multiple" class="btn btn-xs btn-secondary">Send Multiple</a>
                        <button id="waMessageSend" class="btn btn-sm btn-image"><img src="/images/filled-sent.png" /></button>
                      </div>

                      <div class="form-group flex-fill">
                        <textarea id="waNewMessage" class="form-control" placeholder="Whatsapp message"></textarea>
                      </div>
                    </div>

                    <label>Attach Media</label>
                    <input id="waMessageMedia" type="file" name="media" />
                  </div>

                  {{-- <div class="col-xs-12 col-sm-6">
                  </div> --}}
              </div>
            </div>



            </div>

            <div class="row">
              <h3>Messages</h3>
              <div class="col-xs-12" id="message-container">
                  {{-- <div class="row">
                      <div class="col-xs-12 col-sm-8" id="message-container"> --}}
                          {{-- @foreach($messages as $message)
                              @if($message['status'] == '0' || $message['status'] == '5' || $message['status'] == '6')
                                  <div class="talk-bubble round grey">
                                      <div class="talktext">
                                        @if (strpos($message['body'], 'message-img') !== false)
                                          @if (strpos($message['body'], '<br>') !== false)
                                            @php $exploded = explode('<br>', $message['body']) @endphp

                                            <p class="collapsible-message"
                                                data-messageshort="{{ strlen($exploded[0]) > 150 ? (substr($exploded[0], 0, 147) . '...<br>' . $exploded[1]) : $message['body'] }}"
                                                data-message="{{ $message['body'] }}"
                                                data-expanded="false"
                                                data-messageid="{{ $message['id'] }}">
                                              {!! strlen($exploded[0]) > 150 ? (substr($exploded[0], 0, 147) . '...<br>' . $exploded[1]) : $message['body'] !!}
                                            </p>
                                          @else
                                            @php
                                              preg_match_all('/<img src="(.*?)" class="message-img" \/>/', $message['body'], $match);
                                              $images = '<br>';
                                              foreach ($match[0] as $key => $image) {
                                                $images .= str_replace('message-img', 'message-img thumbnail-200', $image);
                                              }
                                            @endphp

                                            <p class="collapsible-message"
                                                data-messageshort="{{ strlen(substr($message['body'], 0, strpos($message['body'], '<img'))) > 150 ? (substr($message['body'], 0, 147) . '...' . $images) : substr($message['body'], 0, strpos($message['body'], '<img')) . $images }}"
                                                data-message="{{ $message['body'] }}"
                                                data-expanded="false"
                                                data-messageid="{{ $message['id'] }}">
                                              {!! strlen(substr($message['body'], 0, strpos($message['body'], '<img'))) > 150 ? (substr($message['body'], 0, 147) . '...' . $images) : substr($message['body'], 0, strpos($message['body'], '<img')) . $images !!}
                                            </p>
                                          @endif
                                        @else
                                          <p class="collapsible-message"
                                              data-messageshort="{{ strlen($message['body']) > 110 ? (substr($message['body'], 0, 107) . '...') : $message['body'] }}"
                                              data-message="{{ $message['body'] }}"
                                              data-expanded="false">
                                            {!! strlen($message['body']) > 110 ? (substr($message['body'], 0, 107) . '...') : $message['body'] !!}
                                          </p>
                                        @endif

                                          <em>Customer {{ Carbon\Carbon::parse($message['created_at'])->format('d-m H:i') }} </em>

                                          @if ($message['status'] == '0')
                                            <a href data-url="/message/updatestatus?status=5&id={{$message['id']}}&moduleid={{$message['moduleid']}}&moduletype=order" style="font-size: 9px" class="change_message_status">Mark as Read </a>
                                          @endif
                                          @if ($message['status'] == '0') | @endif
                                          @if ($message['status'] == '0' || $message['status'] == '5')
                                            <a href data-url="/message/updatestatus?status=6&id={{$message['id']}}&moduleid={{$message['moduleid']}}&moduletype=order" style="font-size: 9px" class="change_message_status">Mark as Replied </a>
                                          @endif
                                      </div>
                                  </div>

                                @elseif($message['status'] == '4')
                                    <div class="talk-bubble round dashed-border" data-messageid="{{$message['id']}}">
                                      <div class="talktext">
                                          {{-- <p id="message_body_{{$message['id']}}">{!! $message['body'] !!}</p>
                                          @if (strpos($message['body'], 'message-img') !== false)
                                            @if (strpos($message['body'], '<br>') !== false)
                                              @php $exploded = explode('<br>', $message['body']) @endphp

                                              <p class="collapsible-message"
                                                  data-messageshort="{{ strlen($exploded[0]) > 150 ? (substr($exploded[0], 0, 147) . '...<br>' . $exploded[1]) : $message['body'] }}"
                                                  data-message="{{ $message['body'] }}"
                                                  data-expanded="false"
                                                  data-messageid="{{ $message['id'] }}">
                                                {!! strlen($exploded[0]) > 150 ? (substr($exploded[0], 0, 147) . '...<br>' . $exploded[1]) : $message['body'] !!}
                                              </p>
                                            @else
                                              @php
                                                preg_match_all('/<img src="(.*?)" class="message-img" \/>/', $message['body'], $match);
                                                $images = '<br>';
                                                foreach ($match[0] as $key => $image) {
                                                  $images .= str_replace('message-img', 'message-img thumbnail-200', $image);
                                                }
                                              @endphp

                                              <p class="collapsible-message"
                                                  data-messageshort="{{ strlen(substr($message['body'], 0, strpos($message['body'], '<img'))) > 150 ? (substr($message['body'], 0, 147) . '...' . $images) : substr($message['body'], 0, strpos($message['body'], '<img')) . $images }}"
                                                  data-message="{{ $message['body'] }}"
                                                  data-expanded="false"
                                                  data-messageid="{{ $message['id'] }}">
                                                {!! strlen(substr($message['body'], 0, strpos($message['body'], '<img'))) > 150 ? (substr($message['body'], 0, 147) . '...' . $images) : substr($message['body'], 0, strpos($message['body'], '<img')) . $images !!}
                                              </p>
                                            @endif
                                          @else
                                            <p class="collapsible-message"
                                                data-messageshort="{{ strlen($message['body']) > 110 ? (substr($message['body'], 0, 107) . '...') : $message['body'] }}"
                                                data-message="{{ $message['body'] }}"
                                                data-expanded="false">
                                              {!! strlen($message['body']) > 110 ? (substr($message['body'], 0, 107) . '...') : $message['body'] !!}
                                            </p>
                                          @endif

                                        <em>{{ App\Helpers::getUserNameById($message['userid']) }} {{ ($message['assigned_to'] != 0 && $message['assigned_to'] != $sales_person && $message['userid'] != $message['assigned_to']) ? ' - ' . App\Helpers::getUserNameById($message['assigned_to']) : '' }} {{ Carbon\Carbon::parse($message['created_at'])->format('d-m H:i') }}  <img id="status_img_{{$message['id']}}" src="/images/1.png"> &nbsp;</em>
                                      </div>
                                 </div>
                               @else
                                  <div class="talk-bubble round"
                                       data-messageid="{{$message['id']}}">
                                      <div class="talktext">
                                          {{-- <span id="message_body_{{$message['id']}}">{!! $message['body'] !!}</span>
                                          <span id="message_body_{{$message['id']}}">
                                            @if (strpos($message['body'], 'message-img') !== false)
                                              @if (strpos($message['body'], '<br>') !== false)
                                                @php $exploded = explode('<br>', $message['body']) @endphp

                                                <p class="collapsible-message"
                                                    data-messageshort="{{ strlen($exploded[0]) > 150 ? (substr($exploded[0], 0, 147) . '...<br>' . $exploded[1]) : $message['body'] }}"
                                                    data-message="{{ $message['body'] }}"
                                                    data-expanded="false"
                                                    data-messageid="{{ $message['id'] }}">
                                                  {!! strlen($exploded[0]) > 150 ? (substr($exploded[0], 0, 147) . '...<br>' . $exploded[1]) : $message['body'] !!}
                                                </p>
                                              @else
                                                @php
                                                  preg_match_all('/<img src="(.*?)" class="message-img" \/>/', $message['body'], $match);
                                                  $images = '<br>';
                                                  foreach ($match[0] as $key => $image) {
                                                    $images .= str_replace('message-img', 'message-img thumbnail-200', $image);
                                                  }
                                                @endphp

                                                <p class="collapsible-message"
                                                    data-messageshort="{{ strlen(substr($message['body'], 0, strpos($message['body'], '<img'))) > 150 ? (substr($message['body'], 0, 147) . '...' . $images) : substr($message['body'], 0, strpos($message['body'], '<img')) . $images }}"
                                                    data-message="{{ $message['body'] }}"
                                                    data-expanded="false"
                                                    data-messageid="{{ $message['id'] }}">
                                                  {!! strlen(substr($message['body'], 0, strpos($message['body'], '<img'))) > 150 ? (substr($message['body'], 0, 147) . '...' . $images) : substr($message['body'], 0, strpos($message['body'], '<img')) . $images !!}
                                                </p>
                                              @endif
                                            @else
                                              <p class="collapsible-message"
                                                  data-messageshort="{{ strlen($message['body']) > 110 ? (substr($message['body'], 0, 107) . '...') : $message['body'] }}"
                                                  data-message="{{ $message['body'] }}"
                                                  data-expanded="false">
                                                {!! strlen($message['body']) > 110 ? (substr($message['body'], 0, 107) . '...') : $message['body'] !!}
                                              </p>
                                            @endif
                                          </span>
                                          <textarea name="message_body" rows="8" class="form-control" id="edit-message-textarea{{$message['id']}}" style="display: none;">{!! $message['body'] !!}</textarea>

                                          <em>{{ App\Helpers::getUserNameById($message['userid']) }} {{ Carbon\Carbon::parse($message['created_at'])->format('d-m H:i') }} <img
                                                      src="/images/{{$message['status']}}.png"> &nbsp;
                                              @if($message['status'] == '2' and App\Helpers::getadminorsupervisor() == false)
                                                  <a href data-url="/message/updatestatus?status=3&id={{$message['id']}}&moduleid={{$message['moduleid']}}&moduletype=order"
                                                     style="font-size: 9px" class="change_message_status">Mark as sent </a>
                                              @endif

                                              @if($message['status'] == '1' and App\Helpers::getadminorsupervisor() == true)
                                                  <a href data-url="/message/updatestatus?status=2&id={{$message['id']}}&moduleid={{$message['moduleid']}}&moduletype=order"
                                                     style="font-size: 9px" class="change_message_status wa_send_message" data-messageid="{{ $message['id'] }}">Approve</a>

                                                  <a href="#" style="font-size: 9px" class="edit-message" data-messageid="{{$message['id']}}">Edit</a>
                                              @endif

                                              @if($message['status'] == '2' and App\Helpers::getadminorsupervisor() == false)
                                                @if (strpos($message['body'], 'message-img') !== false)
                                                  <button class="copy-button btn btn-secondary" data-id="{{$message['id']}}" moduleid="{{$message['moduleid']}}" moduletype="leads" data-message="{{ substr($message['body'], 0, strpos($message['body'], '<img')) }}"> Copy message </button>
                                                @else
                                                  <button class="copy-button btn btn-secondary" data-id="{{$message['id']}}" moduleid="{{$message['moduleid']}}" moduletype="leads" data-message="{{ $message['body'] }}"> Copy message </button>
                                                @endif
                                              @endif

                                          </em>
                                      </div>
                                  </div>

                              @endif
                          @endforeach --}}
                          {{-- @if(!empty($message['id']))
                              <div class="show_more_main" id="show_more_main{{$message['id']}}">
                      <span id="{{$message['id']}}" class="show_more" title="Load more posts"
                            data-moduleid={{$message['moduleid']}} data-moduletype="order">Show more</span>
                                  <span class="loding" style="display: none;"><span
                                              class="loding_txt">Loading...</span></span>
                              </div>
                          @endif --}}
                      {{-- </div>

                  </div> --}}


              </div>
            </div>

            <div class="col-xs-12 text-center">
              <button type="button" id="load-more-messages" data-nextpage="2" class="btn btn-secondary">Load More</button>
            </div>

    {{-- </div> --}}

    <form action="" method="POST" id="product-remove-form">
      @csrf
    </form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

    <script type="text/javascript">
      $('#completion-datetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm'
      });

      $(document).on('click', '.remove-product', function(e) {
        e.preventDefault();

        var product_id = $(this).data('product');
        var url = "{{ url('deleteOrderProduct') }}/" + product_id;
        // var token = "{{ csrf_token() }}";

        $('#product-remove-form').attr('action', url);
        $('#product-remove-form').submit();
      });

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
            var show_url = "{{ url('products') }}/" + response.order.id;
            var delete_url = "{{ url('deleteOrderProduct') }}/" + response.order.id;
            var product_row = '<tr><th>' + response.product.name + '</th>';
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

      $('.edit-message').on('click', function(e) {
        e.preventDefault();
        var message_id = $(this).data('messageid');

        $('#message_body_' + message_id).css({'display': 'none'});
        $('#edit-message-textarea' + message_id).css({'display': 'block'});

        $('#edit-message-textarea' + message_id).keypress(function(e) {
          var key = e.which;

          if (key == 13) {
            e.preventDefault();
            var token = "{{ csrf_token() }}";
            var url = "{{ url('message') }}/" + message_id;
            var message = $('#edit-message-textarea' + message_id).val();

            $.ajax({
              type: 'POST',
              url: url,
              data: {
                _token: token,
                body: message
              },
              success: function(data) {
                $('#edit-message-textarea' + message_id).css({'display': 'none'});
                $('#message_body_' + message_id).text(message);
                $('#message_body_' + message_id).css({'display': 'block'});
              }
            });
          }
        });
      });

      $(document).on('change', '.is_statutory', function () {
          if ($(".is_statutory").val() == 1) {
              $("#completion_form_group").hide();
              $('#recurring-task').show();
          }
          else {
              $("#completion_form_group").show();
              $('#recurring-task').hide();

          }

      });

      $(document).on('click', ".collapsible-message", function() {
        var short_message = $(this).data('messageshort');
        var message = $(this).data('message');
        var status = $(this).data('expanded');

        if (status == false) {
          $(this).addClass('expanded');
          $(this).html(message);
          $(this).data('expanded', true);
          $(this).siblings('.thumbnail-wrapper').remove();
          $(this).parent().find('.message-img').removeClass('thumbnail-200');
          $(this).parent().find('.message-img').parent().css('width', 'auto');
        } else {
          $(this).removeClass('expanded');
          $(this).html(short_message);
          $(this).data('expanded', false);
          $(this).parent().find('.message-img').addClass('thumbnail-200');
          $(this).parent().find('.message-img').parent().css('width', '200px');
        }

      });

      $('#addTaskButton').on('click', function () {
        var client_name = "{{ $client_name }} ";

        $('#task_subject').val(client_name);
      });

      $('#change_status').on('change', function() {
        var token = "{{ csrf_token() }}";
        var status = $(this).val();
        var id = {{ $id }};

        $.ajax({
          url: '/order/' + id + '/changestatus',
          type: 'POST',
          data: {
            _token: token,
            status: status
          }
        }).done( function(response) {
          $('#change_status_message').fadeIn(400);
          setTimeout(function () {
            $('#change_status_message').fadeOut(400);
          }, 2000);
        }).fail(function(errObj) {
          alert("Could not change status");
        });
      });

      $(document).on('click', '.change_message_status', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        var thiss = $(this);

        if ($(this).hasClass('wa_send_message')) {
          var message_id = $(this).data('messageid');
          var message = $('#message_body_' + message_id).text().trim();

          $('#waNewMessage').val(message);
          $('#waMessageSend').click();
        }

        $.ajax({
          url: url,
          type: 'GET',
          beforeSend: function() {
            $(thiss).text('Loading');
          }
        }).done( function(response) {
          $(thiss).remove();
        }).fail(function(errObj) {
          alert("Could not change status");
        });
      });

      $(document).on('click', '.task-subject', function() {
        if ($(this).data('switch') == 0) {
          $(this).text($(this).data('details'));
          $(this).data('switch', 1);
        } else {
          $(this).text($(this).data('subject'));
          $(this).data('switch', 0);
        }
      });

      function addNewRemark(id){

        var formData = $("#add-new-remark").find('#add-remark').serialize();
        var remark = $('#remark-text_'+id).val();
        $.ajax({
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: '{{ route('task.addRemark') }}',
            data: {id:id,remark:remark},
        }).done(response => {
            alert('Remark Added Success!')
            window.location.reload();
        });
      }

      $(".view-remark").click(function () {

        var taskId = $(this).attr('data-id');

          $.ajax({
              type: 'GET',
              headers: {
                  'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              url: '{{ route('task.gettaskremark') }}',
              data: {id:taskId},
          }).done(response => {
              console.log(response);

              var html='';

              $.each(response, function( index, value ) {

                html+=' <p> '+value.remark+' <br> <small>By ' + value.user_name + ' updated on '+ moment(value.created_at).format('DD-M H:mm') +' </small></p>';
                html+"<hr>";
              });
              $("#view-remark-list").find('#remark-list').html(html);
              // getActivity();
              //
              // $('#loading_activty').hide();
          });
      });

      $(document).on('click', '.thumbnail-delete', function() {
        var thiss = $(this);
        var image = $(this).data('image');
        var message_id = $(this).closest('.talk-bubble').find('.collapsible-message').data('messageid');
        var message = $(this).closest('.talk-bubble').find('.collapsible-message').data('message');
        var token = "{{ csrf_token() }}";
        var url = "{{ url('message') }}/" + message_id;

        var image_container = '<div class="thumbnail-wrapper"><img src="' + image + '" class="message-img thumbnail-200" /><span class="thumbnail-delete" data-image="' + image + '">x</span></div>';
        var new_message = message.replace(image_container, '');

        if (new_message.indexOf('message-img') != -1) {
          var short_new_message = new_message.substr(0, new_message.indexOf('<div class="thumbnail-wrapper">')).length > 150 ? (new_message.substr(0, 147)) : new_message;
        } else {
          var short_new_message = new_message.length > 150 ? new_message.substr(0, 147) + '...' : new_message;
        }

        $.ajax({
          type: 'POST',
          url: url,
          data: {
            _token: token,
            body: new_message
          },
          success: function(data) {
            $(thiss).parent().remove();
            $('#message_body_' + message_id).children('.collapsible-message').data('messageshort', short_new_message);
            $('#message_body_' + message_id).children('.collapsible-message').data('message', new_message);
          }
        });
      });

      $(document).ready(function() {
        var container = $("div#message-container");
		var sendBtn = $("#waMessageSend");
		var orderId = "{{$id}}";

        var addElapse = false;
        function errorHandler(error) {
            console.error("error occured: " , error);
        }
        function approveMessage(element, message) {
            $.post( "/whatsapp/approve/orders", { messageId: message.id })
              .done(function( data ) {
                element.remove();
              }).fail(function(response) {
                console.log(response);
                alert( "Technical error. could not approve message");
              });
        }
        function createMessageArgs() {
             var data = new FormData();
            var text = $("#waNewMessage").val();
            var files = $("#waMessageMedia").prop("files");
            var text = $("#waNewMessage").val();

            data.append("order_id", orderId);
            if (files && files.length>0){
                for ( var i = 0; i != files.length; i ++ ) {
                  data.append("media[]", files[ i ]);
                }
                return data;
            }
            if (text !== "") {
                data.append("message", text);
                return data;
            }

            alert("please enter a message or attach media");
          }

		function renderMessage(message, ontop = null, checking = null) {
				var domId = "waMessage_" + message.id;
				var current = $("#" + domId);
        var is_admin = "{{ Auth::user()->hasRole('Admin') }}";
				if ( current.get( 0 ) ) {
					return false;
				}

        if (message.body) {
          var users_array = {!! json_encode($users_array) !!};
          var orders_assigned_user = "{{ $sales_person }}";

          var text = $("<div class='talktext'></div>");
          var p = $("<p class='collapsible-message'></p>");

          if ((message.body).indexOf('<br>') !== -1) {
            var splitted = message.body.split('<br>');
            var short_message = splitted[0].length > 150 ? (splitted[0].substring(0, 147) + '...<br>' + splitted[1]) : message.body;
            var long_message = message.body;
          } else {
            var short_message = message.body.length > 150 ? (message.body.substring(0, 147) + '...') : message.body;
            var long_message = message.body;
          }

          p.attr("data-messageshort", short_message);
          p.attr("data-message", long_message);
          p.attr("data-expanded", "false");
          p.attr("data-messageid", message.id);
          p.html(short_message);

          if (message.status == 0 || message.status == 5 || message.status == 6) {
            var row = $("<div class='talk-bubble round grey'></div>");

            var meta = $("<em>Customer " + moment(message.created_at).format('DD-MM H:m') + " </em>");
            var mark_read = $("<a href data-url='/message/updatestatus?status=5&id=" + message.id + "&moduleid=" + message.moduleid + "&moduletype=order' style='font-size: 9px' class='change_message_status'>Mark as Read </a><span> | </span>");
            var mark_replied = $('<a href data-url="/message/updatestatus?status=6&id=' + message.id + '&moduleid=' + message.moduleid + '&moduletype=order" style="font-size: 9px" class="change_message_status">Mark as Replied </a>');

            row.attr("id", domId);

            p.appendTo(text);
            meta.appendTo(text);

            if (message.status == 0) {
              mark_read.appendTo(text);
            }
            if (message.status == 0 || message.status == 5) {
              mark_replied.appendTo(text);
            }

            text.appendTo(row);

            if (ontop) {
              row.prependTo(container);
            } else {
              row.appendTo(container);
            }

          } else if (message.status == 4) {
            var row = $("<div class='talk-bubble round dashed-border' data-messageid='" + message.id + "'></div>");
            var chat_friend =  (message.assigned_to != 0 && message.assigned_to != orders_assigned_user && message.userid != message.assigned_to) ? ' - ' + users_array[message.assigned_to] : '';
            var meta = $("<em>" + users_array[message.userid] + " " + chat_friend + " " + moment(message.created_at).format('DD-MM H:m') + " <img id='status_img_" + message.id + "' src='/images/1.png' /> &nbsp;</em>");

            row.attr("id", domId);

            p.appendTo(text);
            meta.appendTo(text);

            text.appendTo(row);
            if (ontop) {
              row.prependTo(container);
            } else {
              row.appendTo(container);
            }
          } else {
            var row = $("<div class='talk-bubble round' data-messageid='" + message.id + "'></div>");
            var body = $("<span id='message_body_" + message.id + "'></span>");
            var edit_field = $('<textarea name="message_body" rows="8" class="form-control" id="edit-message-textarea' + message.id + '" style="display: none;">' + message.body + '</textarea>');
            var meta = "<em>" + users_array[message.userid] + " " + moment(message.created_at).format('DD-MM H:m') + " <img id='status_img_" + message.id + "' src='/images/" + message.status + ".png' /> &nbsp;";

            if (message.status == 2 && is_admin == false) {
              meta += '<a href data-url="/message/updatestatus?status=3&id=' + message.id + '&moduleid=' + message.moduleid + '&moduletype=order" style="font-size: 9px" class="change_message_status">Mark as sent </a>';
            }

            if (message.status == 1 && is_admin == true) {
              meta += '<a href data-url="/message/updatestatus?status=2&id=' + message.id + '&moduleid=' + message.moduleid + '&moduletype=order" style="font-size: 9px" class="change_message_status wa_send_message" data-messageid="' + message.id + '">Approve</a>';
              meta += ' <a href="#" style="font-size: 9px" class="edit-message" data-messageid="' + message.id + '">Edit</a>';
            }

            meta += "</em>";
            var meta_content = $(meta);



            row.attr("id", domId);

            p.appendTo(body);
            body.appendTo(text);
            edit_field.appendTo(text);
            meta_content.appendTo(text);

            if (message.status == 2 && is_admin == false) {
              var copy_button = $('<button class="copy-button btn btn-secondary" data-id="' + message.id + '" moduleid="' + message.moduleid + '" moduletype="orders" data-message="' + message.body + '"> Copy message </button>');
              copy_button.appendTo(text);
            }



            text.appendTo(row);
            if (ontop) {
              row.prependTo(container);
            } else {
              row.appendTo(container);
            }
          }
        } else {
          var row = $("<div class='talk-bubble round'></div>");
          var text = $("<div class='talktext'></div>");
          var p = $("<p class='collapsible-message'></p>");
          var meta = $("<em>Customer " + moment(message.created_at).format('DD-MM H:m') + " </em>");

          row.attr("id", domId);

          p.attr("data-messageshort", message.message);
          p.attr("data-message", message.message);
          p.attr("data-expanded", "true");
          console.log("renderMessage message is ", message);
          if ( message.message ) {
              p.html( message.message );
          } else if ( message.media_url ) {
              var splitted = message.content_type.split("/");
              if (splitted[0]==="image") {
                  var a = $("<a></a>");
                  a.attr("target", "_blank");
                  a.attr("href", message.media_url);
                  var img = $("<img></img>");
                  img.attr("src", message.media_url);
                  img.attr("width", "100");
                  img.attr("height", "100");
                  img.appendTo( a );
                  a.appendTo( p );
                  console.log("rendered image message ", a);
              } else if (splitted[0]==="video") {
                  $("<a target='_blank' href='" + message.media_url+"'>"+ message.media_url + "</a>").appendTo(p);
              }
          }

          p.appendTo( text );
          meta.appendTo(text);
          if (!message.received) {
            if (!message.approved) {
                var approveBtn = $("<button class='btn btn-xs btn-secondary btn-approve ml-3'>Approve</button>");
                approveBtn.click(function() {
                    approveMessage( this, message );
                } );

                if (is_admin) {
                  approveBtn.appendTo( text );
                }
            }
          } else {
            var moduleid = "{{ $id }}";
            var mark_read = $("<a href data-url='/whatsapp/updatestatus?status=5&id=" + message.id + "&moduleid=" + moduleid+ "&moduletype=order' style='font-size: 9px' class='change_message_status'>Mark as Read </a><span> | </span>");
            var mark_replied = $('<a href data-url="/whatsapp/updatestatus?status=6&id=' + message.id + '&moduleid=' + moduleid + '&moduletype=order" style="font-size: 9px" class="change_message_status">Mark as Replied </a>');

            if (message.status == 0) {
              mark_read.appendTo(text);
            }
            if (message.status == 0 || message.status == 5) {
              mark_replied.appendTo(text);
            }
          }

          text.appendTo( row );
          if (ontop) {
            row.prependTo(container);
          } else {
            if (checking) {
              row.prependTo(container);
            } else {
              row.appendTo(container);
            }

          }
        }
                return true;
		}
		function pollMessages(page = null, ontop = null, addElapse = null, checking = null) {
            var qs = "";
            qs += "/orders?orderId=" + orderId;
            if (page) {
              qs += "&page=" + page;
            }
            if (addElapse) {
                qs += "&elapse=3600";
            }
            var anyNewMessages = false;
            return new Promise(function(resolve, reject) {
                $.getJSON("/whatsapp/pollMessages" + qs, function( data ) {
                  $('#load-more-messages').data('nextpage', data.current_page + 1);
                  $('#load-more-messages').text('Loading...');

                    data.data.forEach(function( message ) {
                        var rendered = renderMessage( message, ontop, checking );
                        if ( !anyNewMessages && rendered ) {
                            anyNewMessages = true;
                        }
                    } );
                    $('#load-more-messages').text('Load More');
                    if ( anyNewMessages ) {
                        scrollChatTop();
                        anyNewMessages = false;
                    }
                    if (!addElapse) {
                        addElapse = true; // load less messages now
                    }
                    resolve();
                });
            });
		}
        function scrollChatTop() {
            console.log("scrollChatTop called");
            // var el = $(".chat-frame");
            // el.scrollTop(el[0].scrollHeight - el[0].clientHeight);
        }
		function startPolling(checking = null) {
			setTimeout( function() {
                pollMessages(null, null, addElapse, checking).then(function() {
                    startPolling(true);
                }, errorHandler);
            }, 1000);
		}
		function sendWAMessage() {
			var data = createMessageArgs();
            //var data = new FormData();
            //data.append("message", $("#waNewMessage").val());
            //data.append("order_id", orderId );
			$.ajax({
				url: '/whatsapp/sendMessage/orders',
				type: 'POST',
                "dataType"    : 'text',           // what to expect back from the PHP script, if anything
                "cache"       : false,
                "contentType" : false,
                "processData" : false,
                "data": data
			}).done( function(response) {
        $('#waNewMessage').val('');
        pollMessages(null, true);
				console.log("message was sent");
			}).fail(function(errObj) {
				alert("Could not send message");
			});
		}

		sendBtn.click(function() {
			sendWAMessage();
		} );
		startPolling();

    $(document).on('click', '.send-communication', function(e) {
      e.preventDefault();

      var thiss = $(this);
      var url = $(this).closest('form').attr('action');
      var token = "{{ csrf_token() }}";
      var file = $($(this).closest('form').find('input[type="file"]'))[0].files[0];
      var status = $(this).closest('form').find('input[name="status"]').val();
      var formData = new FormData();

      formData.append("_token", token);
      formData.append("image", file);
      formData.append("body", $(this).closest('form').find('textarea').val());
      formData.append("moduletype", $(this).closest('form').find('input[name="moduletype"]').val());
      formData.append("moduleid", $(this).closest('form').find('input[name="moduleid"]').val());
      formData.append("assigned_user", $(this).closest('form').find('input[name="assigned_user"]').val());
      formData.append("status", status);

      if (status == 4) {
        formData.append("assigned_user", $(this).closest('form').find('select[name="assigned_user"]').val());
      }

      if ($(this).closest('form')[0].checkValidity()) {
        $.ajax({
          type: 'POST',
          url: url,
          data: formData,
          processData: false,
          contentType: false
        }).done(function() {
          pollMessages(null, true);
          $(thiss).closest('form').find('textarea').val('');
        }).fail(function() {
          alert('Error sending a message');
        });
      } else {
        $(this).closest('form')[0].reportValidity();
      }

    });

    $(document).on('click', '#load-more-messages', function() {
      var next_page = $(this).data('nextpage');
      pollMessages(next_page);
    });

	});

    </script>

@endsection
