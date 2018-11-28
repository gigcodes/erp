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

    {{-- <div class="row">
        <div class="col-md-6 col-12"> --}}
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
                        <strong>Assigned To:</strong>
                        <select name="assign_to" class="form-control">
                          @foreach($users as $user)
                            <option value="{{$user['id']}}">{{$user['name']}}</option>
                          @endforeach
                        </select>
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
                <div class="col-xs-12 col-sm-12 mb-3">
                  <button type="button" class="btn btn-secondary mb-3" data-toggle="modal" data-target="#taskModal" id="addTaskButton">Add Task</button>

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
                </div>

                <div class="col-xs-12">
                  <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <form action="{{ route('message.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                              <div class="col-xs-9">
                                <div class="form-group">
                                    <textarea class="form-control" name="body"
                                              placeholder="Received from Customer"></textarea>

                                    <input type="hidden" name="moduletype" value="order"/>
                                    <input type="hidden" name="moduleid" value="{{$id}}"/>
                                    <input type="hidden" name="assigned_user" value="{{$sales_person}}" />
                                    <input type="hidden" name="status" value="0"/>
                                </div>
                              </div>

                              <div class="col-xs-1">
                                <div class="upload-btn-wrapper">
                                  <button class="btn"><img src="/images/file-upload.png"/></button>
                                  <input type="file" name="image"/>
                                </div>
                              </div>

                              <div class="col-xs-2">
                                <button type="submit" class="btn btn-secondary">Submit</button>
                              </div>
                            </div>

                        </form>
                      </div>

                      <div class="col-xs-12 col-sm-6">
                        <form action="{{ route('message.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                              <div class="col-xs-9">
                                <div class="form-group">
                                    <textarea class="form-control" name="body"
                                              placeholder="Send for Approval" id="message-body"></textarea>

                                    <input type="hidden" name="moduletype" value="order"/>
                                    <input type="hidden" name="moduleid" value="{{$id}}"/>
                                    <input type="hidden" name="status" value="1"/>
                                    <input type="hidden" name="assigned_user" value="{{$sales_person}}" />
                                </div>
                              </div>

                              <div class="col-xs-1">
                                <div class="upload-btn-wrapper">
                                  <button class="btn"><img src="/images/file-upload.png"/></button>
                                  <input type="file" name="image"/>
                                </div>
                              </div>

                              <div class="col-xs-2">
                                <button type="submit" class="btn btn-secondary">Submit</button>
                              </div>
                            </div>

                        </form>
                      </div>

                      <div class="col-xs-12 col-sm-6">
                        <form action="{{ route('message.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                              <div class="col-xs-9">
                                <div class="form-group">
                                    <textarea class="form-control" name="body"
                                              placeholder="Internal Communications" id="internal-message-body"></textarea>

                                    <input type="hidden" name="moduletype" value="order"/>
                                    <input type="hidden" name="moduleid" value="{{$id}}"/>
                                    <input type="hidden" name="status" value="4"/>
                                    {{-- <input type="hidden" name="assigned_user" value="{{$sales_person}}" /> --}}

                                    <strong>Assign to</strong>
                                    <select name="assigned_user" class="form-control">
                                      <option value="{{$sales_person}}">Order Handler</option>
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
                              </div>

                              <div class="col-xs-1">
                                <div class="upload-btn-wrapper">
                                  <button class="btn"><img src="/images/file-upload.png"/></button>
                                  <input type="file" name="image"/>
                                </div>
                              </div>

                              <div class="col-xs-2">
                                <button type="submit" class="btn btn-secondary">Submit</button>
                              </div>
                            </div>



                        </form>
                      </div>

                      <div class="col-xs-12 col-sm-6">
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
                  </div>
                </div>



                </div>

                <div class="row">
                  <div class="col-xs-12" id="message-container">
                    <h3>Messages</h3>
                      {{-- <div class="row">
                          <div class="col-xs-12 col-sm-8" id="message-container"> --}}
                              @foreach($messages as $message)
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

                                              <em>Customer {{ $message['created_at'] }} </em>

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
                                              {{-- <p id="message_body_{{$message['id']}}">{!! $message['body'] !!}</p> --}}
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

                                            <em>{{ App\Helpers::getUserNameById($message['userid']) }} {{ $message['created_at'] }}  <img id="status_img_{{$message['id']}}" src="/images/1.png"> &nbsp;</em>
                                          </div>
                                     </div>
                                   @else
                                      <div class="talk-bubble round"
                                           data-messageid="{{$message['id']}}">
                                          <div class="talktext">
                                              {{-- <span id="message_body_{{$message['id']}}">{!! $message['body'] !!}</span> --}}
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

                                              <em>{{ App\Helpers::getUserNameById($message['userid']) }} {{ $message['created_at'] }} <img
                                                          src="/images/{{$message['status']}}.png"> &nbsp;
                                                  @if($message['status'] == '2' and App\Helpers::getadminorsupervisor() == false)
                                                      <a href data-url="/message/updatestatus?status=3&id={{$message['id']}}&moduleid={{$message['moduleid']}}&moduletype=order"
                                                         style="font-size: 9px" class="change_message_status">Mark as sent </a>
                                                  @endif

                                                  @if($message['status'] == '1' and App\Helpers::getadminorsupervisor() == true)
                                                      <a href data-url="/message/updatestatus?status=2&id={{$message['id']}}&moduleid={{$message['moduleid']}}&moduletype=order"
                                                         style="font-size: 9px" class="change_message_status">Approve</a>

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


                          {{-- </div>

                      </div> --}}


                  </div>
                </div>

            </div>
        {{-- </div>
    </div> --}}

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
        // console.log(id);
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
            // $('#add-new-remark').modal('hide');
            // $("#add-new-remark").hide();
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
    </script>

@endsection
