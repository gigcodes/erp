@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2> View Order</h2>
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

    <!-- New order Layout -->

    <!-- New Order Layout -->
    <div class="row">
        <div class="col-md-6 col-12">
            <div class="form-group">
                <strong>Order Type:</strong> {{ $order_type }}
            </div>

            <div class="form-group">
                <strong>Sale Order No. </strong> {{ $order_id }}
            </div>
            <div class="form-group">
                <strong>Date of Order : </strong> {{ $order_date }}
            </div>

            <div class="form-group">
                <strong> Price :</strong>
                {{ $total_price }}
            </div>
            <div class="form-group">
                <strong> Date of delivery :</strong>
                {{ $date_of_delivery }}
            </div>


            <div class="form-group">
                <strong> Office Phone Number :</strong>
                {{ $office_phone_number }}
            </div>


            <div class="form-group">
                <strong> Status :</strong>
				<?php
				$orderStatus = ( new \App\ReadOnly\OrderStatus )->getNameById( $order_status );
				echo $orderStatus;
				?>
            </div>

            <div class="form-group">
                <strong> Estimated Delivery Date:</strong>
                {{ $estimated_delivery_date }}
            </div>


            <div class="form-group">
                <strong> Note if any:</strong>
                {{ $note_if_any }}
            </div>


            <div class="form-group">
                <strong> Name of Order Handler :</strong>
                {{ !empty($sales_person) ? $sales_persons[$sales_person] : 'nil' }}
            </div>

            <div class="form-group">
                <strong>Remark</strong>
                {{ $remark }}
            </div>

        </div>
        <div class="col-md-6 col-12">
            <div class="form-group">
                <strong>Client Name:</strong> {{ $client_name }}
            </div>
            <div class="form-group">
                <strong>Client City:</strong> {{ $city }}
            </div>
            <div class="form-group">
                <strong>Contact Details:</strong> {{ $contact_detail }}
            </div>
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
                        <th>{{$order_product['sku']}}</th>
                        <th></th>
                        <th></th>
                    @endif

                    <th>{{ $order_product['product_price']}}</th>
                    <th>{{$order_product['size']}}</th>
                    <th>{{ $order_product['qty'] }}</th>

                    @if(isset($order_product['product']))
                        <th>
                            <a class="btn btn-primary btn-success"
                               href="{{ route('products.show',$order_product['product']['id']) }}">View</a>
                        </th>
                    @else
                        <th></th>
                    @endif
                </tr>
            @endforeach
        </table>
    </div>

    <div class="row">
        <div class="col-md-6 col-12">
            <h3>Payment Details</h3>

            <div class="form-group">
                <strong> Balance Amount :</strong>
                {{ $balance_amount }}
            </div>

            <div class="form-group">
                <strong> Payment Mode :</strong>
                {{ $payment_mode }}
            </div>

            <div class="form-group">
                <strong> Advance Detail :</strong>
                {{ $advance_detail }}
            </div>

            <div class="form-group">
                <strong> Received By :</strong>
                {{ $received_by }}
            </div>

            <div class="form-group">
                <strong> Advance Date :</strong>
                {{ $advance_date }}
            </div>

        </div>
        <div class="col-md-6 col-12">
            <div class="row">
                <div class="col-xs-12 col-sm-12">
                    <h3 class="text-center">Messages</h3>
                </div>
                <div class="col-xs-12 col-sm-12">
                    <div class="row">
                        <div class="col-xs-12 col-sm-8" id="message-container">
                            @foreach($messages as $message)
                                @if($message['status'] == '0')
                                    <div class="talk-bubble tri-right round left-in white">
                                        <div class="talktext">
                                            <p>{!! $message['body'] !!}</p>
                                            <em>Customer {{ $message['created_at'] }} </em>
                                        </div>
                                    </div>

                                  @elseif($message['status'] == '4')
                                      <div class="talk-bubble tri-right round right-in blue" data-messageid="{{$message['id']}}">
                                        <div class="talktext">
                                            <p id="message_body_{{$message['id']}}">{!! $message['body'] !!}</p>

                                          <em>{{ App\Helpers::getUserNameById($message['userid']) }} {{ $message['created_at'] }}  <img id="status_img_{{$message['id']}}" src="/images/1.png"> &nbsp;</em>
                                        </div>
                                   </div>
                                 @else
                                    <div class="talk-bubble tri-right round right-in green"
                                         data-messageid="{{$message['id']}}">
                                        <div class="talktext">
                                          <p>
                                            <span id="message_body_{{$message['id']}}">{!! $message['body'] !!}</span>
                                            <textarea name="message_body" rows="8" class="form-control" id="edit-message-textarea{{$message['id']}}" style="display: none;">{!! $message['body'] !!}</textarea>
                                          </p>

                                            <em>{{ App\Helpers::getUserNameById($message['userid']) }} {{ $message['created_at'] }} <img
                                                        src="/images/{{$message['status']}}.png"> &nbsp;
                                                @if($message['status'] == '2' and App\Helpers::getadminorsupervisor() == false)
                                                    <a href="/message/updatestatus?status=3&id={{$message['id']}}&moduleid={{$message['moduleid']}}&moduletype=order"
                                                       style="font-size: 9px">Mark as sent </a>
                                                @endif

                                                @if($message['status'] == '1' and App\Helpers::getadminorsupervisor() == true)
                                                    <a href="/message/updatestatus?status=2&id={{$message['id']}}&moduleid={{$message['moduleid']}}&moduletype=order"
                                                       style="font-size: 9px">Approve</a>

                                                    <a href="#" style="font-size: 9px" class="edit-message" data-messageid="{{$message['id']}}">Edit</a>
                                                @endif

                                                @if($message['status'] == '2' and App\Helpers::getadminorsupervisor() == false)
                                                  <button class="copy-button btn btn-primary" data-id="{{$message['id']}}" moduleid="{{$message['moduleid']}}" moduletype="leads"> Copy message </button>
                                                @endif

                                            </em>
                                        </div>
                                    </div>

                                @endif
                            @endforeach
                            @if(!empty($message['id']))
                                <div class="show_more_main" id="show_more_main{{$message['id']}}">
                        <span id="{{$message['id']}}" class="show_more" title="Load more posts"
                              data-moduleid={{$message['moduleid']}} data-moduletype="order">Show more</span>
                                    <span class="loding" style="display: none;"><span
                                                class="loding_txt">Loading...</span></span>
                                </div>
                            @endif


                        </div>
{{--                        @if(App\Helpers::getadminorsupervisor() == false)--}}
                            <div class="col-xs-12 col-sm-4">
                                <p><strong> Received from Customer</strong></p>
                                <form action="{{ route('message.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="form-group">
                                        <textarea class="form-control" name="body"
                                                  placeholder="Message here"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" name="moduletype" value="order"/>
                                        <input type="hidden" name="moduleid" value="{{$id}}"/>
                                        <input type="hidden" name="assigned_user" value="{{$sales_person}}" />
                                        <input type="hidden" name="status" value="0"/>
                                        <div class="upload-btn-wrapper">
                                            <button class="btn"><img src="/images/file-upload.png"/></button>
                                            <input type="file" name="image"/>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>

                                </form>
                                <p><strong>Send for approval</strong></p>
                                <form action="{{ route('message.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf


                                    <div class="form-group">
                                        <textarea class="form-control" name="body"
                                                  placeholder="Message here"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" name="moduletype" value="order"/>
                                        <input type="hidden" name="moduleid" value="{{$id}}"/>
                                        <input type="hidden" name="status" value="1"/>
                                        <input type="hidden" name="assigned_user" value="{{$sales_person}}" />
                                        <div class="upload-btn-wrapper">
                                            <button class="btn"><img src="/images/file-upload.png"/></button>
                                            <input type="file" name="image"/>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>

                                </form>

                                <p><strong>Internal Communications</strong></p>
                                <form action="{{ route('message.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf


                                    <div class="form-group">
                                        <textarea class="form-control" name="body"
                                                  placeholder="Message here"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" name="moduletype" value="order"/>
                                        <input type="hidden" name="moduleid" value="{{$id}}"/>
                                        <input type="hidden" name="status" value="4"/>
                                        <input type="hidden" name="assigned_user" value="{{$sales_person}}" />
                                        <div class="upload-btn-wrapper">
                                            <button class="btn"><img src="/images/file-upload.png"/></button>
                                            <input type="file" name="image"/>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>

                                </form>

                            </div>

                        {{--@endif--}}

                        {{--@if(App\Helpers::getadminorsupervisor() == true and !empty($message['id']))

                            <div class="col-xs-12 col-sm-4" id="editmessage" style="display: none">
                                <form action="{{ route('message.update',$message['id']) }}" method="POST"
                                      enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <textarea name="body" class="form-control">{{$message['body']}}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" name="moduleid" value="{{$message['moduleid']}}"/>
                                        <input type="hidden" name="messageid" value=""/>
                                        <input type="hidden" name="moduletype" value="order"/>
                                        <input type="hidden" name="assigned_user" value="{{$sales_person}}" />
                                        <div class="upload-btn-wrapper">
                                            <button class="btn"><img src="/images/file-upload.png"/></button>
                                            <input type="file" name="image"/>
                                        </div>
                                        <button type="submit" class="btn btn-primary save">update</button>
                                    </div>

                                </form>
                            </div>

                        @endif--}}
                    </div>


                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
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
    </script>

@endsection
