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
                </div>
            </div>
        </div>

        <div class="row">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="direct-table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">Sr. No.</th>
                            <th style="width: 5%;">Site Name</th>
                            <th style="width: 5%;">User</th>
                            <th style="width: 10%;">Translation Language</th>
                            <th style="width: 50%;">Communication</th>
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

            function sendMessage(id){
                message = $('#message'+id).val();
                if(sendMessage){
                    $.ajax({
                        url: '{{ route('direct.send') }}',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            "_token": "{{ csrf_token() }}", 
                            "message" : message,
                            "thread_id" : id,
                       },
                    })
                    .done(function() {
                        $('#message'+id).val('');
                        console.log("success");
                    })
                    .fail(function() {
                        console.log("error");
                    })
                    .always(function() {
                        console.log("complete");
                    });
                    
                }else{
                    alert('Please Select Text')
                }
            }
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

            function getNewChats(){
                
                $.ajax({
                url: '{{ route('direct.new.chats') }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        '_token': "{{ csrf_token() }}",
                    },
                beforeSend: function() {
                       $("#loading-image").show();
                },
            
                }).done(function (data) {
                     $("#loading-image").hide();
                    console.log(data);
                    $("#direct-table tbody").empty().html(data.tbody);
                    if (data.links.length > 10) {
                        $('ul.pagination').replaceWith(data.links);
                    } else {
                        $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                    }
                    
                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });

            }
            $( document ).ready(function() {
                setInterval(function(){
                    getNewChats();
                }, 60000);
            });
            getNewChats();
    </script>

@endsection