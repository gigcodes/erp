@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Edit Leads</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('leads.index') }}"> Back</a>
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
                    <input type="text" class="form-control" name="contactno" placeholder="contactno" value="{{$leads->contactno}}"/>

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
                    <Select name="status" class="form-control">
                         @foreach($leads['statusid'] as $key => $value)
                          <option value="{{$value}}" {{$value == $leads->status ? 'Selected=Selected':''}}>{{$key}}</option>
                          @endforeach
                    </Select>

                    <input type="hidden" class="form-control" name="userid" placeholder="status" value="{{$leads->userid}}"/>

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
  <div class="col-xs-12 col-sm-12">
    <h3 style="text-center">Messages</h3>
 </div>
 <div class="col-xs-12 col-sm-12">
    <div class="row">
       <div class="col-xs-12 col-sm-8" id="message-container">
         @foreach($leads['messages'] as $message)
           @if($message['status'] == '0')
                <div class="talk-bubble tri-right round left-in white">
                      <div class="talktext">
                       <p>{!! $message['body'] !!}</p>
                        <em>Customer {{ $message['created_at'] }} </em>

                        {{-- <img id="status_img_{{$message['id']}}" src="/images/{{$message['status']}}.png"> &nbsp;
                        <a href="/message/updatestatus?status=3&id={{$message['id']}}&moduleid={{$message['moduleid']}}&moduletype=leads" style="font-size: 9px">Mark as Replied </a> --}}
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
             <div class="talk-bubble tri-right round right-in green" data-messageid="{{$message['id']}}">
               <div class="talktext">
                   {{-- <p id="message_body_{{$message['id']}}">{!! $message['body'] !!}</p> --}}
                   <p>
                     <span id="message_body_{{$message['id']}}">{!! $message['body'] !!}</span>
                     <textarea name="message_body" rows="8" class="form-control" id="edit-message-textarea{{$message['id']}}" style="display: none;">{!! $message['body'] !!}</textarea>
                   </p>

                 <em>{{ App\Helpers::getUserNameById($message['userid']) }} {{ $message['created_at'] }}  <img id="status_img_{{$message['id']}}" src="/images/{{$message['status']}}.png"> &nbsp;
                 @if($message['status'] == '2' and App\Helpers::getadminorsupervisor() == false)
                     <a href="/message/updatestatus?status=3&id={{$message['id']}}&moduleid={{$message['moduleid']}}&moduletype=leads" style="font-size: 9px">Mark as sent </a>
                 @endif

                 @if($message['status'] == '1' and App\Helpers::getadminorsupervisor() == true)
                     <a href="/message/updatestatus?status=2&id={{$message['id']}}&moduleid={{$message['moduleid']}}&moduletype=leads" style="font-size: 9px">Approve</a>

                     <a href="#" style="font-size: 9px" class="edit-message" data-messageid="{{$message['id']}}">Edit</a>
                 @endif

                 </em>
                   @if($message['status'] == '2' and App\Helpers::getadminorsupervisor() == false)
                     <button class="copy-button btn btn-primary" data-id="{{$message['id']}}" moduleid="{{$message['moduleid']}}" moduletype="orders"> Copy message </button>
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

                             <!--< @foreach($leads['messages'] as $message)
                    <div class="panel-group" id="faqAccordion">
                        <div class="panel panel-default ">
                            <div class="panel-heading accordion-toggle question-toggle collapsed" data-toggle="collapse" data-parent="#faqAccordion" data-target="#{{$message['id']}}">
                                <div class="row">
                                <div class="col-xs-12 col-sm-9">
                                 <h4 class="panel-title">

                                    <a class="ing">Lead #{{$message['id']}} -- {{$message['subject']}}
                                    @if($message['status'] =='1')
                                        @if($leads->userid == 3 or $leads->userid == 7)
                                        <span class="label label-primary" style="background: ">Approval Pending</span>
                                        @else
                                        <span class="label label-primary" style="background: red">Pending Reply</span>
                                         @endif
                                    @endif
                                    @if($message['status'] =='3')
                                        <span class="label label-primary">Approved</span>
                                    @endif
                                      @if($message['status'] =='4')
                                        <span class="label label-primary" style="background: green">Sent</span>
                                    @endif
                                    </a>
                                </h4>
                                </div>
                                <div class="col-xs-12 col-sm-3">
                                <span class="">{{ App\Helpers::timeAgo($message['created_at']) }}</span>
                                </div>
                                </div>

                            </div>
                            <div id="{{$message['id']}}" class="panel-collapse collapse" style="height: 0px;">
                                <div class="panel-body editablearea">
                                     <h5><span class="label label-primary">Message</span></h5>
                                      <form action="{{ route('message.update',$message['id']) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                         @csrf
                                         @can('supervisor-list')
                                             <div class="edit">Edit</div>
                                         @endcan
                                        <textarea name="body" class="form-control" style="display:none;">{{$message['body']}}</textarea>
                                         <p class="editable">{{$message['body']}}</p>
                                        @can('supervisor-list')
                                          <input type="hidden" name="moduleid" value="{{$message['moduleid']}}" />
                                          <input type="hidden" name="status" value="3" />
                                          @if($message['status'] < 3)
                                              <button type="submit" class="btn btn-primary ">Approve</button>
                                             <button type="submit" class="btn btn-primary save">Save</button>
                                          @endif
                                        @endcan
                                        @if($message['status'] == '3')
                                            @can('lead-create')
                                            <input type="hidden" name="moduleid" value="{{$message['moduleid']}}" />
                                            <input type="hidden" name="status" value="4" />
                                            <button type="submit" class="btn btn-primary save">Sent</button>
                                             @endcan
                                        @endif
                                    </form>
                                </div>
                            </div>
                        </div>
                      </div>
                 @endforeach  !-->


          </div>
          {{--@if(App\Helpers::getadminorsupervisor() == false)--}}
          <div class="col-xs-12 col-sm-4">
            <p><strong> Received from Customer</strong> </p>
            <form action="{{ route('message.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                        <div class="form-group">
                          <textarea  class="form-control" name="body" placeholder="Message here"></textarea>
                        </div>
                        <div class="form-group">
                          <input type="hidden" name="moduletype" value="leads" />
                          <input type="hidden" name="moduleid" value="{{$leads['id']}}" />
                          <input type="hidden" name="assigned_user" value="{{$leads['assigned_user']}}" />
                          <input type="hidden" name="status" value="0" />
                          <div class="upload-btn-wrapper">
                             <button class="btn"><img src="/images/file-upload.png" /></button>
                                    <input type="file" name="image" />
                            </div>
                          <button type="submit" class="btn btn-primary">Submit</button>
                        </div>

             </form>
             <p><strong>Send for approval</strong> </p>
             <form action="{{ route('message.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                        <div class="form-group">
                          <textarea id="message-body" class="form-control" name="body" placeholder="Message here"></textarea>
                        </div>
                        <div class="form-group">
                          <input type="hidden" name="moduletype" value="leads" />
                          <input type="hidden" name="moduleid" value="{{$leads['id']}}" />
                          <input type="hidden" name="assigned_user" value="{{$leads['assigned_user']}}" />
                          <input type="hidden" name="status" value="1" />
                          <div class="upload-btn-wrapper">
                             <button class="btn"><img src="/images/file-upload.png" /></button>
                                    <input type="file" name="image" />
                            </div>
                          <button type="submit" class="btn btn-primary">Submit</button>
                        </div>

             </form>

             @can('admin')
               <p><strong>Internal Communications</strong> </p>
               <form action="{{ route('message.store') }}" method="POST" enctype="multipart/form-data">
                  @csrf

                          <div class="form-group">
                            <textarea class="form-control" name="body" placeholder="Message here"></textarea>
                          </div>
                          <div class="form-group">
                            <input type="hidden" name="moduletype" value="leads" />
                            <input type="hidden" name="moduleid" value="{{$leads['id']}}" />
                            <input type="hidden" name="assigned_user" value="{{$leads['assigned_user']}}" />
                            <input type="hidden" name="status" value="4" />
                            <div class="upload-btn-wrapper">
                               <button class="btn"><img src="/images/file-upload.png" /></button>
                                      <input type="file" name="image" />
                              </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                          </div>

               </form>
             @endcan

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

          {{--@endif --}}

          {{--@if(App\Helpers::getadminorsupervisor() == true and !empty($message['id']))

          <div class="col-xs-12 col-sm-4" id="editmessage" style="display: none">
             <form action="{{ route('message.update',$message['id']) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <textarea name="body" class="form-control">{{$message['body']}}</textarea>
                    </div>
                     <div class="form-group">
                        <input type="hidden" name="moduleid" value="{{$message['moduleid']}}" />
                        <input type="hidden" name="messageid" value="" />
                        <input type="hidden" name="moduletype" value="leads" />
                        <div class="upload-btn-wrapper">
                             <button class="btn"><img src="/images/file-upload.png" /></button>
                                    <input type="file" name="image" />
                            </div>
                        <button type="submit" class="btn btn-primary save">update</button>
                     </div>

            </form>
          </div>

          @endif --}}

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
