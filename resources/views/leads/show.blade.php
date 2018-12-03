@extends('layouts.app')


@section('content')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Edit Leads</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-secondary" href="{{ route('leads.index') }}"> Back</a>
            </div>
        </div>
    </div>

@if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div id="exTab2" class="container">
           <ul class="nav nav-tabs">
              <li class="active">
                 <a  href="#1" data-toggle="tab">Lead Info</a>
              </li>
              <li><a href="#2" data-toggle="tab">WhatsApp Conversation</a>
              </li>
              <li><a href="#3" data-toggle="tab">Call Recordings</a>
              </li>
           </ul>
        </div>
        <div class="tab-content ">
            <!-- Pending task div start -->
            <div class="tab-pane active" id="1">
                <div class="row">
                     <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                        <div class="form-group">
                            <strong>Client Name:</strong>
                            <input type="text" class="form-control" name="client_name" placeholder="client_name" value="{{$leads->client_name}}"/>

                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                        <div class="form-group">
                            <strong>Address:</strong>
                            <input type="text" class="form-control" name="address" placeholder="address" value="{{$leads->address}}"/>
                        </div>
                    </div>

                     <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                        <div class="form-group">
                            <strong>Contact No:</strong>
                            <input type="text" class="form-control" name="contactno" placeholder="contactno" data-twilio-call value="{{$leads->contactno}}"/>

                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                        <div class="form-group">
                            <strong>Email:</strong>
                            <input type="text" class="form-control" name="email" placeholder="email" value="{{$leads->email}}"/>

                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12">
                                    <strong>Source:</strong><br>
                                </div>
                            </div>
                            <div class="row">
                                 <div class="col-sm-6 ol-xs-12">

                                 <Select name="source" class="form-control" id="leadsource">
                                    <option value="database" {{'database' == $leads->source ? 'Selected=Selected':''}}>Database</option>
                                    <option value="instagram" {{'instagram' == $leads->source ? 'Selected=Selected':''}}>Instagram</option>
                                    <option value="facebook" {{'facebook' == $leads->source ? 'Selected=Selected':''}}>Facebook</option>
                                    <option value="new" {{'new' == $leads->source ? 'Selected=Selected':''}}>New Lead</option>
                                    </Select>
                                 </div>
                                 <div class="col-sm-6 ol-xs-12">
                                     <input type="text" class="form-control" id="leadsourcetxt" name="source" placeholder="Comments" value="{{$leads->leadsourcetxt}}"/>
                                </div>
                            </div>

                        </div>
                    </div>



                      <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                        <div class="form-group">
                            <strong>City:</strong>
                            <input type="text" class="form-control" name="city" placeholder="city" value="{{$leads->city}}"/>

                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                        <div class="form-group">
                            <strong>Solo Phone:</strong>
                         <Select name="solophone" class="form-control">
                                   <option value>None</option>
                                    <option value="01" {{'01' == $leads->solophone ? 'Selected=Selected':''}}>01</option>
                                    <option value="02" {{'02'== $leads->solophone ? 'Selected=Selected':''}}>02</option>
                                    <option value="03" {{'03'== $leads->solophone ? 'Selected=Selected':''}}>03</option>
                                    <option value="04" {{'04'== $leads->solophone ? 'Selected=Selected':''}}>04</option>
                                    <option value="05" {{'05'== $leads->solophone ? 'Selected=Selected':''}}>05</option>
                                    <option value="06" {{'06'== $leads->solophone ? 'Selected=Selected':''}}>06</option>
                                    <option value="07" {{'07'== $leads->solophone ? 'Selected=Selected':''}}>07</option>
                                    <option value="08" {{'08'== $leads->solophone ? 'Selected=Selected':''}}>08</option>
                                    <option value="09" {{'09'== $leads->solophone ? 'Selected=Selected':''}}>09</option>
                            </Select>

                        </div>
                    </div>



                      <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                        <div class="form-group">
                            <strong>Rating:</strong>
                            <Select name="rating" class="form-control">
                                    <option value="1" {{1== $leads->rating ? 'Selected=Selected':''}}>1</option>
                                    <option value="2" {{2== $leads->rating ? 'Selected=Selected':''}}>2</option>
                                    <option value="3" {{3== $leads->rating ? 'Selected=Selected':''}}>3</option>
                                    <option value="4" {{4== $leads->rating ? 'Selected=Selected':''}}>4</option>
                                    <option value="5" {{5== $leads->rating ? 'Selected=Selected':''}}>5</option>
                                    <option value="6" {{6== $leads->rating ? 'Selected=Selected':''}}>6</option>
                                    <option value="7" {{7== $leads->rating ? 'Selected=Selected':''}}>7</option>
                                    <option value="8" {{8== $leads->rating ? 'Selected=Selected':''}}>8</option>
                                    <option value="9" {{9== $leads->rating ? 'Selected=Selected':''}}>9</option>
                                    <option value="10" {{10== $leads->rating ? 'Selected=Selected':''}}>10</option>
                            </Select>


                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                      <div class="form-group">
                        @foreach ($leads->getMedia(config('constants.media_tags')) as $image)
                          <img src="{{ $image->getUrl() }}" class="img-responsive mb-3" alt="">
                        @endforeach
                      </div>
                    </div>

                    <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                        <div class="form-group">
                            <strong>Brand:</strong>
                            <select disabled id="multi_brand" multiple="" name="multi_brand[]" class="form-control">
                                @foreach($leads['brands'] as $brand_item)
                                    <option value="{{$brand_item['id']}}" {{ in_array($brand_item['id'] ,$leads['multi_brand']) ? 'Selected=Selected':''}}>{{$brand_item['name']}}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                        <div class="form-group">
                            <strong>Categories</strong>
                            {!! $data['category_select']  !!}
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                        <div class="form-group">
                            <strong>Comments:</strong>
                            <textarea  class="form-control" name="comments" placeholder="comments">{{$leads->comments}} </textarea>


                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                        <div class="form-group">
                            <strong> Selected Product :</strong>
                            {{--<input type="text" class="form-control" name="selected_product" placeholder="Selected Product" value="{{ old('selected_product') ? old('selected_product') : $selected_product }}"/>--}}
                            <?php
                            //                  echo Form::select('allocated_to',$products_array, ( old('selected_products_array') ? old('selected_products_array') : $selected_products_array ), ['multiple'=>'multiple','name'=>'selected_product[]','class' => 'form-control select2']);?>

                            <select name="selected_product[]" class="select2 form-control" multiple="multiple" id="select2"></select>

                            @if ($errors->has('selected_product'))
                                <div class="alert alert-danger">{{$errors->first('selected_product')}}</div>
                            @endif
                        </div>

                        <script type="text/javascript">
                            jQuery(document).ready(function() {

                                jQuery('#multi_brand').select2({
                                    placeholder: 'Brand',
                                });


                                jQuery('#multi_category').select2({
                                    placeholder: 'Categories',
                                });


                                jQuery('#select2').select2({
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
                                    templateResult: formatProduct,
                                    templateSelection:function(product) {
                                         return product.text || product.name;
                                     },

                                });



                                @if(!empty($data['products_array'] ))
                                    let data = [
                                            @forEach($data['products_array'] as $key => $value)
                                        {
                                            'id': '{{ $key }}',
                                            'text': '{{$value  }}',
                                        },
                                        @endforeach
                                    ];
                                @endif

                                let productSelect = jQuery('#select2');
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

                                /*function boilerPlateCode() {
                                    //boilerplate
                                    jQuery('ul.select2-selection__rendered li').each(function (item) {
                                        $( this ).append($( this ).attr('title'));
                                    });
                                }
                                boilerPlateCode();*/

                            });


                        </script>
                    </div>

                    <div class="col-xs-12 col-sm-8 col-sm-offset-2">
        							<div class="form-group">
        								<strong>Sizes:</strong>
        								<input type="text" name="size" value="{{ $leads->size }}" class="form-control" placeholder="S, M, L">
        							</div>
        						</div>

                    <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                        <div class="form-group">
                            <strong>Assigned To:</strong>
                            <Select name="assigned_user" class="form-control">

                                    @foreach($leads['users'] as $users)
                                  <option value="{{$users['id']}}" {{$users['id']== $leads->assigned_user ? 'Selected=Selected':''}}>{{$users['name']}}</option>
                                  @endforeach
                            </Select>


                        </div>
                    </div>


                     <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                        <div class="form-group">
                            <strong>status:</strong>
                            <Select name="status" class="form-control" id="change_status">
                                 @foreach($leads['statusid'] as $key => $value)
                                  <option value="{{$value}}" {{$value == $leads->status ? 'Selected=Selected':''}}>{{$key}}</option>
                                  @endforeach
                            </Select>
                            <span id="change_status_message" class="text-success" style="display: none;">Successfully changed status</span>

                            <input type="hidden" class="form-control" name="userid" placeholder="status" value="{{$leads->userid}}"/>

                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                       <div class="form-group">
                           <strong>Created by:</strong>

                           <input type="text" class="form-control" name="userid" placeholder="Created by" value="{{ App\Helpers::getUserNameById($leads->userid) }}"/>
                       </div>
                   </div>

                    <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                        <div class="form-group">
                            <strong>Remark:</strong>
                            {{ $leads['remark'] }}
                        </div>
                    </div>
                </div>
         </form>
         <div class="col-xs-12 col-sm-12">
            <hr>
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
                 <input type="hidden" name="model_type" value="leads">
                 <input type="hidden" name="model_id" value="{{ $leads['id'] }}">

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
                         @foreach($leads['users'] as $user)
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
        </div>
        <div class="tab-pane" id="2">
            <div class="col-xs-12 col-sm-12">
                <h3 style="text-center">WhatsApp Messages</h3>
             </div>
            <div class="col-xs-12 col-sm-12">
                <div class="row">
                   <div class="col-md-12" id="waMessages">
                   </div>
                </div>
            </div>
            <div class="col-md-10">
                    <textarea id="waNewMessage" class="form-control" placeholder="Type new message.."></textarea>
            </div>
            <div class="col-md-2">
                <button id="waMessageSend" class="btn btn-success">Send</button>
            </div>

        </div>
        <div class="tab-pane" id="3">
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
                            {{-- @foreach ($leads['recordings'] as $recording)
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
       {{-- <p><strong></strong></p> --}}
       <form action="{{ route('message.store') }}" method="POST" enctype="multipart/form-data" class="d-flex">
           @csrf

           {{-- <div class="row"> --}}
             <div class="form-group">
               <div class="upload-btn-wrapper btn-group">
                 <button class="btn btn-image px-1"><img src="/images/upload.png" /></button>
                 <input type="file" name="image" />
                 <button type="submit" class="btn btn-image px-1"><img src="/images/filled-sent.png" /></button>
               </div>
             </div>

             {{-- <div class="col-xs-6"> --}}
               <div class="form-group flex-fill">
                 <textarea  class="form-control" name="body" placeholder="Received from Customer"></textarea>

                 <input type="hidden" name="moduletype" value="leads" />
                 <input type="hidden" name="moduleid" value="{{$leads['id']}}" />
                 <input type="hidden" name="assigned_user" value="{{$leads['assigned_user']}}" />
                 <input type="hidden" name="status" value="0" />
               </div>
             {{-- </div> --}}

           {{-- </div> --}}

        </form>
      </div>

      <div class="col-xs-12 col-sm-6">
        {{-- <p><strong></strong></p> --}}
        <form action="{{ route('message.store') }}" method="POST" enctype="multipart/form-data" class="d-flex">
           @csrf

           {{-- <div class="row"> --}}
             <div class="form-group">
               <div class="upload-btn-wrapper btn-group pr-0 d-flex">
                 <button class="btn btn-image px-1"><img src="/images/upload.png" /></button>
                 <input type="file" name="image" />
                 <a href="{{ route('attachImages', ['leads', $leads['id'], 1, $leads['assigned_user']]) }}" class="btn btn-image px-1"><img src="/images/attach.png" /></a>
                 <button type="submit" class="btn btn-image px-1"><img src="/images/filled-sent.png" /></button>
               </div>
             </div>

             {{-- <div class="col-xs-6"> --}}
               <div class="form-group flex-fill">
                 <textarea id="message-body" class="form-control mb-3" name="body" placeholder="Send for approval"></textarea>

                 <input type="hidden" name="moduletype" value="leads" />
                 <input type="hidden" name="moduleid" value="{{$leads['id']}}" />
                 <input type="hidden" name="assigned_user" value="{{$leads['assigned_user']}}" />
                 <input type="hidden" name="status" value="1" />

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
             {{-- </div> --}}

           {{-- </div> --}}

        </form>
      </div>

      <div class="col-xs-12 col-sm-6">
          {{-- <p><strong></strong></p> --}}
          <form action="{{ route('message.store') }}" method="POST" enctype="multipart/form-data" class="d-flex">
             @csrf

             {{-- <div class="row"> --}}
               <div class="form-group">
                 <div class="upload-btn-wrapper btn-group">
                    <button class="btn btn-image px-1"><img src="/images/upload.png" /></button>
                     <input type="file" name="image" />
                     <button type="submit" class="btn btn-image px-1"><img src="/images/filled-sent.png" /></button>
                   </div>
               </div>

               {{-- <div class="col-xs-6"> --}}
                 <div class="form-group flex-fill">
                   <textarea class="form-control mb-3" name="body" placeholder="Internal Communications" id="internal-message-body"></textarea>

                   <input type="hidden" name="moduletype" value="leads" />
                   <input type="hidden" name="moduleid" value="{{$leads['id']}}" />
                   {{-- <input type="hidden" name="assigned_user" value="{{$leads['assigned_user']}}" /> --}}
                   <input type="hidden" name="status" value="4" />

                   <strong>Assign to</strong>
                   <select name="assigned_user" class="form-control mb-3">
                     <option value="{{$leads['assigned_user']}}">Assigned User</option>
                     @foreach($leads['users'] as $user)
                       <option value="{{$user['id']}}">{{$user['name']}}</option>
                     @endforeach
                   </select>

                   <p class="pb-4" style="display: block;">
                       {{-- <strong>Quick Reply</strong> --}}

                       <select name="quickCommentInternal" id="quickCommentInternal" class="form-control">
                           <option value="">Quick Reply</option>
                           @foreach($internal_replies as $reply)
                               <option value="{{$reply->reply}}">{{$reply->reply}}</option>
                           @endforeach
                       </select>
                   </p>
                 </div>
               {{-- </div> --}}


             {{-- </div> --}}


          </form>
        </div>

        {{-- <div class="col-xs-12 col-sm-6">

     </div> --}}
   </div>
 </div>

 <div class="col-xs-12 col-sm-12" id="message-container">
   <h3>Messages</h3>
    {{-- <div class="row">
       <div class="col-xs-12 col-sm-8" id="message-container"> --}}
         @foreach($leads['messages'] as $message)
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
                              data-messageshort="{{ strlen($message['body']) > 150 ? (substr($message['body'], 0, 147) . '...') : $message['body'] }}"
                              data-message="{{ $message['body'] }}"
                              data-expanded="false">
                            {!! strlen($message['body']) > 150 ? (substr($message['body'], 0, 147) . '...') : $message['body'] !!}
                          </p>
                        @endif


                        <em>Customer {{ Carbon\Carbon::parse($message['created_at'])->format('d-m H:i') }} </em>

                        {{-- <img id="status_img_{{$message['id']}}" src="/images/{{$message['status']}}.png"> &nbsp; --}}
                        @if ($message['status'] == '0')
                          <a href data-url="/message/updatestatus?status=5&id={{$message['id']}}&moduleid={{$message['moduleid']}}&moduletype=leads" style="font-size: 9px" class="change_message_status">Mark as Read </a>
                        @endif
                        @if ($message['status'] == '0') | @endif
                        @if ($message['status'] == '0' || $message['status'] == '5')
                          <a href data-url="/message/updatestatus?status=6&id={{$message['id']}}&moduleid={{$message['moduleid']}}&moduletype=leads" style="font-size: 9px" class="change_message_status">Mark as Replied </a>
                        @endif
                      </div>
                </div>

            @elseif($message['status'] == '4')
                <div class="talk-bubble round dashed-border" data-messageid="{{$message['id']}}">
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
                          data-messageshort="{{ strlen($message['body']) > 150 ? (substr($message['body'], 0, 147) . '...') : $message['body'] }}"
                          data-message="{{ $message['body'] }}"
                          data-expanded="false">
                        {!! strlen($message['body']) > 150 ? (substr($message['body'], 0, 147) . '...') : $message['body'] !!}
                      </p>
                    @endif

                    <em>{{ App\Helpers::getUserNameById($message['userid']) }} {{ ($message['assigned_to'] != 0 && $message['assigned_to'] != $leads['assigned_user'] && $message['userid'] != $message['assigned_to']) ? ' - ' . App\Helpers::getUserNameById($message['assigned_to']) : '' }} {{ Carbon\Carbon::parse($message['created_at'])->format('d-m H:i') }}  <img id="status_img_{{$message['id']}}" src="/images/1.png"> &nbsp;</em>
                  </div>
             </div>
           @else
             <div class="talk-bubble round" data-messageid="{{$message['id']}}">
               <div class="talktext">
                   {{-- <p id="message_body_{{$message['id']}}">{!! $message['body'] !!}</p> --}}
                     <span id="message_body_{{$message['id']}}">
                       @if (strpos($message['body'], 'message-img') !== false)
                         {{-- @php
                           preg_match_all('/<img src="(.*?)" class="message-img" \/>/', $message['body'], $match);
                           $message_body = $message['body'];
                           $message_id = $message['id'];
                           $images = '<br>';
                           foreach ($match[0] as $key => $image) {
                             $images .= '<div class="thumbnail-wrapper">' . str_replace('message-img', 'message-img thumbnail-200', $image) . "<span class='thumbnail-delete' data-image='$image' data-messageid='$message_id'>x</span></div>";
                           }
                         @endphp --}}
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


                         <form action="{{ route('download.images') }}" method="POST">
                           @csrf

                           <input type="hidden" name="images" value="{{ $message['body'] }}">

                           <button type="submit" class="btn-link">Download ZIP</button>
                         </form>
                       @else
                         <p class="collapsible-message"
                             data-messageshort="{{ strlen($message['body']) > 150 ? (substr($message['body'], 0, 147) . '...') : $message['body'] }}"
                             data-message="{{ $message['body'] }}"
                             data-expanded="false">
                           {!! strlen($message['body']) > 150 ? (substr($message['body'], 0, 147) . '...') : $message['body'] !!}
                         </p>
                       @endif
                     </span>

                     @if (strpos($message['body'], 'message-img') !== false)
                       <textarea name="message_body" rows="8" class="form-control" id="edit-message-textarea{{$message['id']}}" style="display: none;">{!! substr($message['body'], 0, strpos($message['body'], '<img')) !!}</textarea>
                     @else
                       <textarea name="message_body" rows="8" class="form-control" id="edit-message-textarea{{$message['id']}}" style="display: none;">{!! $message['body'] !!}</textarea>
                     @endif

                 <em>{{ App\Helpers::getUserNameById($message['userid']) }} {{ Carbon\Carbon::parse($message['created_at'])->format('d-m H:i') }}  <img id="status_img_{{$message['id']}}" src="/images/{{$message['status']}}.png"> &nbsp;
                 @if($message['status'] == '2' and App\Helpers::getadminorsupervisor() == false)
                     <a href data-url="/message/updatestatus?status=3&id={{$message['id']}}&moduleid={{$message['moduleid']}}&moduletype=leads" style="font-size: 9px" class="change_message_status">Mark as sent </a>
                 @endif

                 @if($message['status'] == '1' and App\Helpers::getadminorsupervisor() == true)
                     <a href data-url="/message/updatestatus?status=2&id={{$message['id']}}&moduleid={{$message['moduleid']}}&moduletype=leads" style="font-size: 9px" class="change_message_status wa_send_message" data-messageid="{{ $message['id'] }}">Approve</a>

                     <a href="#" style="font-size: 9px" class="edit-message" data-messageid="{{$message['id']}}">Edit</a>
                 @endif

                 </em>
                   @if($message['status'] == '2' and App\Helpers::getadminorsupervisor() == false)
                     @if (strpos($message['body'], 'message-img') !== false)
                       <button class="copy-button btn btn-secondary" data-id="{{$message['id']}}" moduleid="{{$message['moduleid']}}" moduletype="orders" data-message="{{ substr($message['body'], 0, strpos($message['body'], '<img')) }}"> Copy message </button>
                     @else
                       <button class="copy-button btn btn-secondary" data-id="{{$message['id']}}" moduleid="{{$message['moduleid']}}" moduletype="orders" data-message="{{ $message['body'] }}"> Copy message </button>
                     @endif
                   @endif
               </div>
          </div>
            @endif
         @endforeach
         @if(!empty($message['id']))
         <div class="show_more_main" id="show_more_main{{$message['id']}}" >
             <span id="{{$message['id']}}" class="show_more" title="Load more posts" data-moduleid={{$message['moduleid']}} data-moduletype="leads">Show more</span>
             <span class="loding" style="display: none;"><span class="loding_txt">Loading...</span></span>
         </div>
          @endif

          {{-- </div> --}}

     {{-- </div> --}}

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
         var message_html = '<p class="collapsible-message" data-messageshort="" data-message="" data-expanded="false">' + message + '</p>';

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

   $(document).ready(function() {
		var container = $("div#waMessages");
		var sendBtn = $("#waMessageSend");
		var leadId = "{{$leads->id}}";
		function renderMessage(message) {
				var domId = "waMessage_" + message.id;
				var current = $("#" + domId);
				if ( current.get( 0 ) ) {
					return;
				}
				var domId = "waMessage_" + message.id;
                if (message.received) {
				    var row = $("<div class='talk-bubble tri-right round right-in blue'></div>");
                } else {
				    var row = $("<div class='talk-bubble tri-right round left-in white'></div>");
                }
                var text = $("<div class='talktext'></div>");
                var p = $("<p class='collapsible-message'></p>");

                row.attr("id", domId);

                p.attr("data-messageshort", message.message);
                p.attr("data-message", message.message);
                p.attr("data-expanded", "true");
                p.html( message.message );
                p.appendTo( text );
                text.appendTo( row );
				row.appendTo( container );
		}
		function pollMessages() {
            var qs = "";
            qs += "/leads?leadId=" + leadId;
            qs += "&elapse=3600";
			var url = $.getJSON("/whatsapp/pollMessages" + qs, function( data ) {
				data.forEach(function( message ) {
					renderMessage( message );
				} );
			});
		}
		function startPolling() {
			setInterval( pollMessages, 1000);
		}
		function sendWAMessage() {
			var text = $("#waNewMessage").val();
			var data = { "lead_id": leadId, "message": text };
			$.ajax({
				url: '/whatsapp/sendMessage/leads',
				type: 'POST',
				contentType: 'application/json; charset=UTF-8',
				data: JSON.stringify( data )
			}).done( function(response) {
				console.log("message was sent");
			}).fail(function(errObj) {
				alert("Could not send message");
			});
		}

		sendBtn.click(function() {
			sendWAMessage();
		} );
		startPolling();
	});

  $('#addTaskButton').on('click', function () {
    var client_name = "{{ $leads->client_name }} ";

    $('#task_subject').val(client_name);
  });

  $('#change_status').on('change', function() {
    var token = "{{ csrf_token() }}";
    var status = $(this).val();
    var id = {{ $leads['id'] }};

    $.ajax({
      url: '/leads/' + id + '/changestatus',
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
      // alert(message);
      // console.log(message);
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
