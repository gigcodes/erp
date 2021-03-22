@extends('layouts.app')
@section('large_content')

<?php 
$chatIds = \App\CustomerLiveChat::orderBy('seen','asc')->orderBy('status','desc')->get();
$newMessageCount = \App\CustomerLiveChat::where('seen',0)->count();
?>
    <style type="text/css">
        .chat-righbox a{
            color: #555 !important;
            font-size: 18px;
        }
        .type_msg.message_textarea {
            width: 100% !important;
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
        #circle {
        background: green;
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
    </style>
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <h2 class="page-heading">Direct Message</h2>
                <div class="pull-right">
                    <div class="pb-2">
                        <button type="button" class="btn btn-xs btn-secondary" id="fetchNewMessage">Fetch new message</button>
                    </div>
                </div>
            </div>
        </div>
        @include('partials.flash_messages')
        <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
              @if (Auth::user()->hasRole('Admin'))
                <form action="" class="form-inline" method="GET">
                    <div class="form-group mr-3">
                            <input type="text" name="keyword" class="form-control" placeholder="Search keywords" value="{{ request('keyword') }}">
                    </div>

                    <div class="form-group ml-3">
                        <select class="form-control from_account_list" name="form_account">
                            <option value="">Select option</option>
                            @foreach ($accounts as $item)
                                <option value="{{ $item->id }}"> {{ $item->last_name }} </option>
                            @endforeach
                        </select>
                    </div>
                  <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
                </form>
              @endif
            </div>
        </div>
    </div>

        <div class="row">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="direct-table">
                    <thead>
                        <tr>
                            <th style="width: 1%;">Sr. No.</th>
                            <th style="width: 5%;">Site Name</th>
                            <th style="width: 10%;">User</th>
                            <th style="width: 10%;">Translation Language</th>
                            <th style="width: 30%;">Communication</th>
                            <th style="width: 10%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                       @include('instagram.direct.data') 
                    </tbody>
                </table>
            </div>
        </div>
     <div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog" style="width: 1000px; max-width: 1000px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                </div>
                <div class="modal-body" style="background-color: #999999;" id="direct-modal-chat">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@include('instagram.partials.customer-form-modals')
@include('instagram.direct.history')
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

    <script>
        
        $(document).on('click', '.instagramHandle', function () {
            var Values = $(this).attr("id");
            
            $('#customerCreate #handled_values').val(Values);

        });
        $(document).on('click', '.load-direct-chat-model', function () {

            $.ajax({
                url: '{{ route('direct.messages') }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        '_token': "{{ csrf_token() }}",
                         id : $(this).data("id")
                    },
                beforeSend: function() {
                       
                },
            
                }).done(function (data) {
                    console.log(data.messages)
                     $('#direct-modal-chat').empty().append(data.messages);
                     $('#chat-list-history').modal('show');
                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });

        });

        $(document).on("click",".task-history",function(e) {
            e.preventDefault();
            var btn = $(this);
            var id = $(this).data("id");
            $.ajax({
                url: '{{ route('direct.history') }}',
                type: 'POST',
                data : { _token: "{{ csrf_token() }}", id : id },
                dataType: 'json',
                beforeSend: function () {
                    btn.prop('disabled',true);
                },
                success: function(result){
                    if(result.code == 200) {
                        var t = '';
                        $.each(result.data,function(k,v) {
                            t += `<tr><td>`+v.title+`</td>`;
                            t += `<td>`+v.description+`</td>`;
                            t += `<td>`+v.created_at+`</td></tr>`;
                        });
                        if( t == '' ){
                            t = '<tr><td colspan="5" class="text-center">No data found</td></tr>';
                        }
                    }
                    $("#category-history-modal").find(".show-list-records").html(t);
                    $("#category-history-modal").modal("show");
                    btn.prop('disabled',false);
                },
                error: function (){
                    btn.prop('disabled',false);
                }
            });
        });

        $('.attach-media-btn').on('click', function () {
            event.preventDefault();
            var form_account = $('#from_account_id'+$(this).data('target')).val();
            window.location.href = $(this).attr('href')+'&from_account='+form_account
            return false;
        });

        $(document).on('click', '.expand-row', function () {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });
        
        $(document).on('change', '.categories_load', function () {
            var thread_id = $(this).data('id');
            if ($(this).val() != "") {
                var category_id = $(this).val();

                var store_website_id = $('#selected_customer_store').val();
                 if(!store_website_id){
                    var store_website_id = '0';
                }
                $.ajax({
                    url: "{{ url('get-store-wise-replies') }}"+'/'+category_id+'/'+store_website_id,
                    type: 'GET',
                    dataType: 'json'
                }).done(function(data){
                    console.log(data);
                    if(data.status == 1){
                        $('#quick_replies'+thread_id).empty().append('<option value="">Quick Reply</option>');
                        var replies = data.data;
                        replies.forEach(function (reply) {
                            $('#quick_replies'+thread_id).append($('<option>', {
                                value: reply.reply,
                                text: reply.reply,
                                'data-id': reply.id
                            }));
                        });
                        toastr['success']('Success', 'success');
                    }
                });

            }
        });

            function sendMessage(id){
                
                message = $('#message'+id).val();
                if(message == ''){
                    toastr['error']('Message field required', 'success');
                    return false;
                }
                var from_account = $('#from_account_id'+id).val();
                if(sendMessage){
                    $.ajax({
                        url: '{{ route('direct.send') }}',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            "_token": "{{ csrf_token() }}", 
                            "message" : message,
                            "thread_id" : id,
                            "from_account_id" : from_account,
                       },
                    }).beforeSend( function() {
                        
                    })
                    .done(function() {
                        $('#message'+id).val('');
                        toastr['success']('Message sent', 'success');
                    })
                    .fail(function() {
                        toastr['success']('Server error', 'success');
                    })
                    .always(function() {
                        
                    });
                    
                }else{
                    alert('Please Select Text')
                }
            }
            $('.quick_comment_add').on("click", function () {
                var thread_id = $(this).data('id');
                var textBox = $(".quick_comment"+thread_id).val();
                var quickCategory = $('#categories'+thread_id).val();

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
                    $(".quick_comment"+thread_id).val('');
                    $('#quick_replies'+thread_id).append($('<option>', {
                        value: data.data,
                        text: data.data
                    }));
                    toastr['success']('Success', 'success');
                })
            });

            $('.quick_replies').on("change", function(){
                $('#message'+$(this).data('id')).text( $(this).val() );
            });

            $('#fetchNewMessage').on('click', function () {
                var button = $(this);
                $.ajax({
                    url: '{{ route('direct.new.chats') }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        '_token': "{{ csrf_token() }}",
                    },
                beforeSend: function() {
                    button.prop('disabled', true);
                },
            
                }).done(function (data) {
                    $("#loading-image").hide();
                    $("#direct-table tbody").empty().html(data.tbody);
                    if (data.links.length > 10) {
                        $('ul.pagination').replaceWith(data.links);
                    } else {
                        $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                    }
                    toastr['success']('Success!', 'success');
                    button.prop('disabled', false);
                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    button.prop('disabled', false);
                    toastr['success']('No response from server', 'success');
                });
            });
    </script>

@endsection