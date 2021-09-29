@extends('layouts.app')
@section('large_content')

<?php 
$chatIds = \App\CustomerLiveChat::latest()->orderBy('seen','asc')->orderBy('status','desc')->get();
$newMessageCount = \App\CustomerLiveChat::where('seen',0)->count();
?>
@section('link-css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style type="text/css">
        .chat-righbox a{
            color: #555 !important;
            font-size: 18px;
        }
        .type_msg.message_textarea {
            width: 90%;
            height: 60px;
        }
        .cls_remove_rightpadding{
            padding-right: 0px !important;
        }
        .cls_remove_leftpadding{
            padding-left: 0px !important;
        }
        .cls_remove_padding{
            padding-right: 0px !important;
            padding-left: 0px !important;
        }
        .cls_quick_commentadd_box{
            padding-left: 5px !important;   
            margin-top: 3px;
        }
        .cls_quick_commentadd_box button{
            font-size: 12px;
            padding: 5px 9px;
            margin-left: -8px;
            background: none;
        }
        .send_btn {
            margin-left: -5px; 
        }
        .cls_message_textarea{
            height: 35px !important;
            width: 100% !important;
        }
        .cls_quick_reply_box{
            margin-top: 5px;
        }
        .cls_addition_info {
            padding: 0px 0px;
            margin-top: -8px;
        }
        .table-responsive{
            margin-left: 10px;
            margin-right: 10px;
        }
        .chat-righbox{
            border: none;
            background: transparent;
            padding: 0;
        }
        .typing-indicator{
            height: auto;
            padding: 0;
        }
        textarea{
            border: 1px solid #ddd !important;
        }
        .send_btn{
            background-color: transparent !important;

        }
        .send_btn i{
            color: #808080;
        }
    </style>
@endsection
        <div class="row">
            <div class="col-lg-12 margin-tb p-0">
                <h2 class="page-heading">Twilio Chat</h2>
                <div class="pull-right">
                    <div style="text-align: right; margin-bottom: 10px;">
                        <!-- <button type="button" class="btn btn-primary" onclick="createCoupon()">New Coupon</button> -->
                        <span>&nbsp;</span>                        
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="keywordassign_table">
                    <thead>
                        <tr>
                            <th style="width: 2%;">Sr. No.</th>
                            <th style="width: 2%;">Sender Number</th>
                            <th style="width: 2%;">Receiver Number</th>
                            <th style="width: 15%;">Message</th>
                            <!-- <th style="width: 10%;">Actions</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $srno=1;
                        ?>
                         @if(isset($chat_message) && !empty($chat_message))
                            @foreach ($chat_message as $chatId)
                               {{-- @php
                                $customer = \App\Customer::where('id',$chatId->customer_id)->first();
                                $customerInital = substr($customer->name, 0, 1);
                                @endphp --}}
                                   <tr>
                                    <td><?php echo $srno;?></td>
                                    <td><?php echo $chatId->send_by;?></td>
                                    <td><?php echo $chatId->number;?></td>
                                    <td><?php echo $chatId->message;?></td>
                                   </tr>
                                <?php $srno++;?>
                            @endforeach
                        @endif   
                    </tbody>
                </table>
            </div>
        </div>
    
        <!-- ADD COUPON MODAL -->
        <div class="modal fade" id="couponModal" tabindex="-1" role="dialog" aria-labelledby="couponModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <!-- <form id="coupon-form" method="POST" onsubmit="return executeCouponOperation();"> -->
                <form id="coupon-form" method="POST" >
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="couponModalLabel">New Coupon</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @csrf
                            <!-- Accordian form start -->
                            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab" id="headingOne">
                                        <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Coupon Information 
                                        </a>
                                        </h4>
                                    </div>
                                    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                                        <div class="panel-body">
                                            <input type="hidden" name="active" id="is_active" value="1" />
                                            <input type="hidden" name="uses_per_coupon" id="use_per_coupon" value="1" class="form-control" />
                                            <input type="hidden" name="priority" value="Default"  />
                                            <input type="hidden" class="form-control" name="store_labels[0]" value="Quck Created Coupon" />
                                            
                                            <input type="hidden" name="uses_per_coupon" value="1" />
                                            <input type="hidden" name="priority" value="1" />
                                            <input type="hidden" name="coupon_qty" value="1" />
                                            <input type="hidden" name="code_length" value="1" />
                                            <input type="hidden" name="format" value="1oed4" />
                                            <input type="hidden" name="prefix" value="sdfe2" />
                                            <input type="hidden" name="suffix" value="ldoec2" />
                                            <input type="hidden" name="dash" value="1" />
                                            <input type="hidden" name="discount_qty" value="1" />
                                            <input type="hidden" name="discount_step" value="1" />
                                            <input type="hidden" name="apply_to_shipping" value="true" />
                                            <input type="hidden" name="stop_rules_processing" value="true" />                                            
                                            
                                            <div class="form-group row">
                                                <label for="code" class="col-sm-3 col-form-label required">Rule Name</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control required" name="name" placeholder="Name" value="{{old('name')}}" id="rule_name" />
                                                    @if ($errors->has('name'))
                                                    <div class="alert alert-danger">{{$errors->first('name')}}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="description" class="col-sm-3 col-form-label">Description</label>
                                                <div class="col-sm-8">
                                                    <textarea type="text" class="form-control" name="description" placeholder="Description" id="description">{{old('description')}}</textarea>
                                                    @if ($errors->has('description'))
                                                    <div class="alert alert-danger">{{$errors->first('description')}}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="store_website_id" class="col-sm-3 col-form-label required">Store Websites</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control select select2" name="store_website_id" onchange="getWebsitesByStoreId(this);">
                                                        <option value="">Please select</option>
                                                        @foreach($store_websites as $ws)
                                                            <option value="{{ $ws->id }}">{{ $ws->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="website_ids" class="col-sm-3 col-form-label required">Websites</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control select select2 required websites" name="website_ids" multiple="true" id="website_ids">
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="customer_groups" class="col-sm-3 col-form-label required">Customer Groups</label>
                                                <div class="col-sm-8">
                                                        <select class="form-control select select2 required customers" name="customer_groups" multiple="true" id="customer_groups">
                                                            <option data-title="NOT LOGGED IN" value="0" selected>NOT LOGGED IN</option>
                                                            <option data-title="General" value="1">General</option>
                                                            <option data-title="Wholesale" value="2">Wholesale</option>
                                                            <option data-title="Retailer" value="3">Retailer</option>
                                                        </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="coupon_type" class="col-sm-3 col-form-label required">Coupon</label>
                                                <div class="col-sm-8">
                                                        <select class="form-control select select2 required" name="coupon_type" id="coupon_type" >
                                                            <option  value="NO_COUPON">No Coupon</option>
                                                            <option  value="SPECIFIC_COUPON">Specific Coupon</option>
                                                        </select>
                                                </div>
                                            </div>
                                            <div class="form-group row hide_div">
                                                <label for="coupon_code" class="col-sm-3 col-form-label">Coupon Code</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="code" placeholder="Code" id="coupon_code" />
                                                    @if ($errors->has('code'))
                                                    <div class="alert alert-danger">{{$errors->first('code')}}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="start" class="col-sm-3 col-form-label">Uses per Coustomer</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="uses_per_coustomer" placeholder="" id="use_per_coustomer" />
                                                    <div class="">Usage limit enforced for logged in customers only.</div>
                                                    @if ($errors->has('uses_per_coustomer'))
                                                    <div class="alert alert-danger">{{$errors->first('uses_per_coustomer')}}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="start" class="col-sm-3 col-form-label">Start</label>
                                                <div class="col-sm-8">
                                                    <div class='input-group date' id='start'>
                                                        <input type='text' class="form-control" name="start" value="{{old('start')}}" id="start_input" />
                                                        <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                    @if ($errors->has('start'))
                                                    <div class="alert alert-danger">{{$errors->first('start')}}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="expiration" class="col-sm-3 col-form-label">Expiration</label>
                                                <div class="col-sm-8">
                                                    <div class='input-group date' id='expiration'>
                                                        <input type='text' class="form-control" name="expiration" value="{{old('expiration')}}" id="to_input" />
                                                        <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                    @if ($errors->has('expiration'))
                                                    <div class="alert alert-danger">{{$errors->first('expiration')}}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="start" class="col-sm-3 col-form-label ">Apply</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control select select2 " name="simple_action" id="simple_action">
                                                        <option data-title="Percent of product price discount" value="by_percent">Percent of product price discount</option>
                                                        <option data-title="Fixed amount discount" value="by_fixed">Fixed amount discount</option>
                                                        <option data-title="Fixed amount discount for whole cart" value="cart_fixed">Fixed amount discount for whole cart</option>
                                                        <option data-title="Buy X get Y free (discount amount is Y)" value="buy_x_get_y">Buy X get Y free (discount amount is Y)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="start" class="col-sm-3 col-form-label required">Discount Amount</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control required" name="discount_amount" placeholder="Discount amount" id="discount_amount" />
                                                    @if ($errors->has('discount_amount'))
                                                    <div class="alert alert-danger">{{$errors->first('discount_amount')}}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Accordian form end here -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <!-- <button type="submit" class="btn btn-primary">Save</button> -->
                            <button type="button" class="btn btn-primary save-button">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
    <!-- New Coupon -->
    <script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).ready(function() {
        $('#start').datetimepicker({
            format: 'YYYY-MM-DD HH:mm',            
        });
        $('#expiration').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });
    });
    function createCoupon() {
        /* beautify preserve:start */
        $('#coupon-form').attr('action', '{{ route('quick.couponcode.store') }}')
        /* beautify preserve:end */
        //$('#coupon-form input').not('input[name="_token"]').val('');
        //$('#coupon-form textarea').val('');
        $('#couponModal').modal('show');
    }
    function getWebsitesByStoreId(ele){
        let store_id = $(ele).val();

        $.ajax({
            url : "{{ route('getWebsiteByStore') }}",
            type : "POST",
            data : {
                store_id : store_id,
            },
            beforeSend: function () {
              $("#loading-image-preview").show();
            },
            success : function (response){
              $("#loading-image-preview").hide();  
                if(response.type == "success"){
                    $('.websites').html("");
                    $('.websites').append(response.data);

                    $('.websites_edit').html("");
                    $('.websites_edit').append(response.data);
                }
            },
            error : function (xhr, status, error){
              $("#loading-image-preview").hide();
              var err = eval("(" + xhr.responseText + ")");
              toastr['error'](err, 'error'); 
            }
        });
    }
    $('.save-button').on('click',function(){
        
        if($('#coupon-form').valid()){
            let formData = $('#coupon-form').serializeArray();

            var indexed_array = {};
            $.map(formData, function(n, i){
                if(n['name'] == "website_ids"){
                    indexed_array[n['name']] = $('.websites').val();
                }else if(n['name'] == "customer_groups"){
                    indexed_array[n['name']] = $('.customers').val(); 
                }else{
                     if(n['value'] != "") {
                        indexed_array[n['name']] = n['value'];
                     }
                }
            });

            if($("#disable_coupon_code").is(":checked")) {
                indexed_array["auto_generate"] = true;
            }
            
            $.ajax({
                url : "{{ route('quick.couponcode.store') }}",
                type : "POST",
                data : indexed_array,
                beforeSend: function () {
                  $("#loading-image-preview").show();
                },
                success : function (response){
                    $("#loading-image-preview").hide();
                    if(response.type == "error"){
                        toastr['error'](response.message, 'error'); 
                        return false;
                    }else if(response.type == "success"){
                      toastr['success'](response.message, 'success'); 
                      location.reload();
                    }
                },
                error : function (xhr, status, error){
                  $("#loading-image-preview").hide();
                  var err = eval("(" + xhr.responseText + ")");
                  toastr['error'](err, 'error'); 
                }
            });
        }
        
    });
    </script>
    <script>
        var openChatWindow = "<?php echo request('open_chat',false); ?>";
        if(openChatWindow == "true") {
            $("#quick-chatbox-window-modal").modal("show");
               chatBoxOpen = true;
               openChatBox(true);
        }

        $(document).on("click",".send_btn",function(){
            var $this = $(this);
            var customerID = $this.data("id");
            var message = $this.closest("td").find(".message_textarea");
            $.ajax({
                url: "{{ route('livechat.send.message') }}",
                type: 'POST',
                dataType: 'json',
                data: { 
                    id : customerID ,
                    message : message.val(),
                   _token: "{{ csrf_token() }}" 
                }
            }).done(function(data) {
                message.val('');
            }).fail(function() {
                alert('Chat Not Active');
            });
        });

        function openPopupGeneralInfo(id)
        {
            $('#GeneralInfo'+id).modal('show');
        }
        function openPopupVisitedPages(id)
        {
            $('#VisitedPages'+id).modal('show');   
        }
        function openPopupAdditionalinfo(id)
        {
            $('#AdditionalInfo'+id).modal('show');   
        }
        function openPopupTechnology(id)
        {
            $('#Technology'+id).modal('show');   
        }
        function getLiveChats(id){
            // Close the connection, if open.
            if (websocket.readyState === WebSocket.OPEN) {
                clearInterval(pingTimerObj);
                websocket.close();
            }

            $('#liveChatCustomerInfo').html('Fetching Details...');
            $('#liveChatVisitedPages').html('Fetching Details...');
            $('#liveChatAdditionalInfo').html('Fetching Details...');
            $('#liveChatTechnology').html('Fetching Details...');
            $.ajax({
                        url: "{{ route('livechat.get.message') }}",
                        type: 'POST',
                        dataType: 'json',
                        data: { id : id ,   _token: "{{ csrf_token() }}" },
                    })
                    .done(function(data) {
                        console.log(data);
                        //if(typeof data.data.message != "undefined" && data.length > 0 && data.data.length > 0) {
                        $('#live-message-recieve').empty().html(data.data.message);
                        $('#message-id').val(data.data.id);
                        $('#new_message_count').text(data.data.count);
                        $('#user_name').text(data.data.name);
                        $("li.active").removeClass("active");
                        $("#user"+data.data.id).addClass("active");
                        $('#user_inital').text(data.data.customerInital);
                        $('#selected_customer_store').val(data.data.store_website_id);
                        var customerInfo = data.data.customerInfo;
                        if(customerInfo!=''){
                            customerInfoSetter(customerInfo);
                        }
                        else{
                            $('#liveChatCustomerInfo').html('');
                            $('#liveChatVisitedPages').html('');
                            $('#liveChatAdditionalInfo').html('');
                            $('#liveChatTechnology').html('');
                        }

                        currentChatId = data.data.threadId;

                        //open socket
                        runWebSocket(data.data.threadId);

                        //}
                        console.log("success");
                    })
                    .fail(function() {
                        console.log("error");
                        $('#chatCustomerInfo').html('');
                        $('#chatVisitedPages').html('');
                        $('#chatAdditionalInfo').html('');
                        $('#chatTechnology').html('');
                    });
        }

            $(document).on('click', '.expand-row', function () {
                var selection = window.getSelection();
                if (selection.toString().length === 0) {
                    $(this).find('.td-mini-container').toggleClass('hidden');
                    $(this).find('.td-full-container').toggleClass('hidden');
                }
            });
            $(document).on('change', '#categories', function () {
                if ($(this).val() != "") {
                    var category_id = $(this).val();

                    var store_website_id = $('#selected_customer_store').val();
                  /*  if(store_website_id == ''){
                        store_website_id = 0;
                    }*/
                    $.ajax({
                        url: "{{ url('get-store-wise-replies') }}"+'/'+category_id+'/'+store_website_id,
                        type: 'GET',
                        dataType: 'json'
                    }).done(function(data){
                        console.log(data);
                        if(data.status == 1){
                            $('#quick_replies').empty().append('<option value="">Quick Reply</option>');
                            var replies = data.data;
                            replies.forEach(function (reply) {
                                $('#quick_replies').append($('<option>', {
                                    value: reply.reply,
                                    text: reply.reply,
                                    'data-id': reply.id
                                }));
                            });
                        }
                    });

                }
            });

            $('.quick_comment_add').on("click", function () {
                var textBox = $(".quick_comment").val();
                var quickCategory = $('#categories').val();

                if (textBox == "") {
                    alert("Please Enter New Quick Comment!!");
                    return false;
                }

                if (quickCategory == "") {
                    alert("Please Select Category!!");
                    return false;
                }
                console.log("yes");

                $.ajax({
                    type: 'POST',
                    url: "{{ route('save-store-wise-reply') }}",
                    dataType: 'json',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'category_id' : quickCategory,
                        'reply' : textBox,
                        'store_website_id' : $('#selected_customer_store').val()
                    }
                }).done(function (data) {
                    console.log(data);
                    $(".quick_comment").val('');
                    $('#quick_replies').append($('<option>', {
                        value: data.data,
                        text: data.data
                    }));
                })
            });

            $('#quick_replies').on("change", function(){
                $('.message_textarea').text($(this).val());
            });
    </script>
@endsection