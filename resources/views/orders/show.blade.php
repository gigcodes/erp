@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">

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

                  <div class="modal-body">
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
                        <input type='text' class="form-control" name="completion_date" value="{{ date('Y-m-d H:i') }}" />

                        <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                      </div>

                      @if ($errors->has('completion_date'))
                          <div class="alert alert-danger">{{$errors->first('completion_date')}}</div>
                      @endif
                    </div>

                    <div class="form-group">
                        <select name="is_statutory" class="form-control is_statutory">
                            <option value="0">Other Task </option>
                            <option value="1">Statutory Task </option>
                        </select>
                    </div>

                    <div id="recurring-task" style="display: none;">
                        <div class="form-group">
                            <strong>Recurring Type:</strong>
                            <select name="recurring_type" class="form-control">
                                <option value="EveryDay">EveryDay</option>
                                <option value="EveryWeek">EveryWeek</option>
                                <option value="EveryMonth">EveryMonth</option>
                                <option value="EveryYear">EveryYear</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <strong>Recurring Day:</strong>
                            <div id="recurring_day"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <strong>Assigned To:</strong>
                        <select name="assign_to" class="form-control">
                          @foreach($users as $users)
                            <option value="{{$users['id']}}">{{$users['name']}}</option>
                          @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <strong>Category:</strong>
                    <?php
                    $categories = \App\Http\Controllers\TaskCategoryController::getAllTaskCategory();

                    echo Form::select('category',$categories, old('category'), ['placeholder' => 'Select a category','class' => 'form-control']);

                    ?>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Create</button>
                  </div>
                </form>
              </div>

            </div>
          </div>

            <div class="row">
                <div class="col-xs-12 col-sm-12">
                  <button type="button" class="btn btn-success" data-toggle="modal" data-target="#taskModal">Add Task</button>
                    <h3 class="text-center">Messages</h3>
                </div>
                <div class="col-xs-12 col-sm-12">
                    <div class="row">
                        <div class="col-xs-12 col-sm-8" id="message-container">
                            @foreach($messages as $message)
                                @if($message['status'] == '0')
                                    <div class="talk-bubble tri-right round left-in white">
                                        <div class="talktext">
                                          @if (strpos($message['body'], 'message-img') !== false)
                                            <p class="collapsible-message"
                                                data-messageshort="{{ strlen(substr($message['body'], 0, strpos($message['body'], '<img'))) > 110 ? (substr($message['body'], 0, 107) . '... (Has Image)') : substr($message['body'], 0, strpos($message['body'], '<img')) . ' (Has Image)' }}"
                                                data-message="{{ $message['body'] }}"
                                                data-expanded="false">
                                              {!! strlen(substr($message['body'], 0, strpos($message['body'], '<img'))) > 110 ? (substr($message['body'], 0, 107) . '... (Has Image)') : substr($message['body'], 0, strpos($message['body'], '<img')) . '(Has Image)' !!}
                                            </p>
                                          @else
                                            <p class="collapsible-message"
                                                data-messageshort="{{ strlen($message['body']) > 110 ? (substr($message['body'], 0, 107) . '...') : $message['body'] }}"
                                                data-message="{{ $message['body'] }}"
                                                data-expanded="false">
                                              {!! strlen($message['body']) > 110 ? (substr($message['body'], 0, 107) . '...') : $message['body'] !!}
                                            </p>
                                          @endif

                                            <em>Customer {{ $message['created_at'] }} </em>
                                        </div>
                                    </div>

                                  @elseif($message['status'] == '4')
                                      <div class="talk-bubble tri-right round right-in blue" data-messageid="{{$message['id']}}">
                                        <div class="talktext">
                                            {{-- <p id="message_body_{{$message['id']}}">{!! $message['body'] !!}</p> --}}
                                            @if (strpos($message['body'], 'message-img') !== false)
                                              <p class="collapsible-message"
                                                  data-messageshort="{{ strlen(substr($message['body'], 0, strpos($message['body'], '<img'))) > 110 ? (substr($message['body'], 0, 107) . '... (Has Image)') : substr($message['body'], 0, strpos($message['body'], '<img')) . ' (Has Image)' }}"
                                                  data-message="{{ $message['body'] }}"
                                                  data-expanded="false">
                                                {!! strlen(substr($message['body'], 0, strpos($message['body'], '<img'))) > 110 ? (substr($message['body'], 0, 107) . '... (Has Image)') : substr($message['body'], 0, strpos($message['body'], '<img')) . ' (Has Image)' !!}
                                              </p>
                                            @else
                                              <p class="collapsible-message"
                                                  data-messageshort="{{ strlen($message['body']) > 110 ? (substr($message['body'], 0, 107) . '...') : $message['body'] }}"
                                                  data-message="{{ $message['body'] }}"
                                                  data-expanded="false">
                                                {!! strlen($message['body']) > 110 ? (substr($message['body'], 0, 107) . '...') : $message['body'] !!}
                                              </p>
                                            @endif

                                          <em>{{ App\Helpers::getUserNameById($message['userid']) }} {{ $message['created_at'] }}  <img id="status_img_{{$message['id']}}" src="/images/1.png"> &nbsp;</em>
                                        </div>
                                   </div>
                                 @else
                                    <div class="talk-bubble tri-right round right-in green"
                                         data-messageid="{{$message['id']}}">
                                        <div class="talktext">
                                            {{-- <span id="message_body_{{$message['id']}}">{!! $message['body'] !!}</span> --}}
                                            <span id="message_body_{{$message['id']}}">
                                              @if (strpos($message['body'], 'message-img') !== false)
                                                <p class="collapsible-message"
                                                    data-messageshort="{{ strlen(substr($message['body'], 0, strpos($message['body'], '<img'))) > 110 ? (substr($message['body'], 0, 107) . '... (Has Image)') : substr($message['body'], 0, strpos($message['body'], '<img')) . ' (Has Image)' }}"
                                                    data-message="{{ $message['body'] }}"
                                                    data-expanded="false">
                                                  {!! strlen(substr($message['body'], 0, strpos($message['body'], '<img'))) > 110 ? (substr($message['body'], 0, 107) . '... (Has Image)') : substr($message['body'], 0, strpos($message['body'], '<img')) . ' (Has Image)' !!}
                                                </p>
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
                                                  @if (strpos($message['body'], 'message-img') !== false)
                                                    <button class="copy-button btn btn-primary" data-id="{{$message['id']}}" moduleid="{{$message['moduleid']}}" moduletype="leads" data-message="{{ substr($message['body'], 0, strpos($message['body'], '<img')) }}"> Copy message </button>
                                                  @else
                                                    <button class="copy-button btn btn-primary" data-id="{{$message['id']}}" moduleid="{{$message['moduleid']}}" moduletype="leads" data-message="{{ $message['body'] }}"> Copy message </button>
                                                  @endif
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
                                                  placeholder="Message here" id="message-body"></textarea>
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

                                <p class="pb-4" style="display: block;">
                                    <strong>Quick Reply</strong>
                  		          <?php
                  		          $quickReplies = (new \App\ReadOnly\QuickReplies)->all();
                  		          ?>
                                    <select name="quickComment" id="quickComment" class="form-control">
                                        <option value="">Select a reply</option>
                                        @foreach($quickReplies as $value )
                                            <option value="{{$value}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                </p>

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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

    <script type="text/javascript">
      $('#completion-datetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm'
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

              // $('input[name="completion_date"]').val("1976-01-01");
              $("#completion_form_group").hide();

              // if (!isAdmin)
              //     $('select[name="assign_to"]').html(`<option value="${current_userid}">${ current_username }</option>`);

              $('#recurring-task').show();
          }
          else {

              $("#completion_form_group").show();

              // let select_html = '';
              // for (user of users)
              //     select_html += `<option value="${user['id']}">${ user['name'] }</option>`;
              // $('select[name="assign_to"]').html(select_html);

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
        } else {
          $(this).removeClass('expanded');
          $(this).html(short_message);
          $(this).data('expanded', false);
        }

      });
    </script>

@endsection
