@extends('layouts.app')

@section('content')

@section('styles')
    <!-- START - Purpose : Add CSS - DEVTASK-4416 -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style type="text/css">
        table tr td {
            overflow-wrap: break-word;
        }

        .page-note {
            font-size: 14px;
        }

        .flex {
            display: flex;
        }
        .btn-secondary1{
            background: #fff !important;
            border: 1px solid #ddd !important;
            color: #757575 !important;
            padding: 8px 5px 8px 10px;
        }
        .space-right{
        padding-right:10px;
        padding-left: 10px;
    }
   
    </style>
    <!-- END - DEVTASK-4416 -->
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
@section('content')

    <div class="row" style="margin:0%">
        <div class="col-md-12 margin-tb p-0">
            <h2 class="page-heading"> ListingApproved - SOP

                <div class="pull-right">
                    <button type="button" class="btn btn-secondary1 mr-2" data-toggle="modal" data-target="#exampleModal">Add Notes</button>
                </div>

            </h2>
        </div>

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">
                <div class="form-group" style="margin-bottom: 0px;">
                    <div class="row">
                        <form method="get" action="{{ route('sop.index') }}">
                            <div class="flex">
                                <div class="col" id="search-bar">

                                    <input type="text" value="{{ request('search') ?? '' }}" name="search" class="form-control"
                                        placeholder="Search Here.." style="margin-left: -5px;">
                                    {{-- <input type="text" name="search" id="search" class="form-control search-input" placeholder="Search Here Text.." autocomplete="off"> --}}
                                </div>

                                <button type="submit" style="display: inline-block;width: 10%"
                                    class="btn btn-sm btn-image search-button">
                                    <img src="/images/search.png" style="cursor: default;">
                                </button>

                                <a href="{{ route('sop.store') }}" type="button" class="btn btn-image" id=""><img
                                        src="/images/resend2.png"></a>

                            </div>
                        </form>
                    </div>

                </div>
            </div>

            
        </div>
    </div>

    <!-- Button trigger modal -->

    <!--------------------------------------------------- Add Data Modal ------------------------------------------------------->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="FormModal">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required />
                        </div>

                        <div class="form-group">
                            <label for="content">Content</label>
                            <input type="text" class="form-control" id="content" required />
                        </div>


                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary btnsave" id="btnsave">Submit</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <!--------------------------------------------------- end Add Data Modal ------------------------------------------------------->
    
        <div class="space-right">

            <div class="table-responsive">
                <table class="table table-bordered page-notes" style="font-size:13.8px;border:0px !important;" id="NameTable">
                    <thead>
                        <tr>
                            <th width="3%">ID</th>
                            
                            <th width="10%">Name</th>
                            <th width="50%">Content</th>
                            <th width="18%">Communication</th>
                            <th width="8%">Created at</th>
                            <th width="12%">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($usersop as $key => $value)
                            <tr id="sid{{ $value->id }}" class="parent_tr" data-id="{{ $value->id }}">
                                <td class="sop_table_id">{{ $value->id }}</td>
                                <td class="sop_table_name">{{ $value->name }}</td>

                                
                                 <td class='{{ strlen($value->content) > 270 ? "expand-row" : "" }}' data-subject="{!! $value->content ? ($value->content) : '' !!}" data-details="{{$value->value}}" data-switch="0" style="word-break: break-all;">
                                    <span class="td-mini-container">
                                    {{ $value->content ? substr(strip_tags($value->content), 0, 270) . (strlen(($value->content)) > 270 ? '......' : '') : '' }}
                                    </span>
                                    <span class="td-full-container hidden">{!! $value->content !!}</span>
                                 </td> 
                                                        
                                {{-- <td> {!! $value->content !!}</td> --}}

                                <td class="table-hover-cell pr-0">
                                    <div class=" d-flex flex-row w-100 justify-content-between">
                                        <div style="flex-grow: 1">
                                            <textarea  style="height:37px;" class="form-control" id="messageid_{{ $value->user_id }}" name="message" placeholder="Message"></textarea>
                                        </div>
                                        <div style="width: min-content">
                            
                                            <button class="btn btn-xs btn-image send-message-open" style="margin-left:6px;"
                                                    data-user_id="{{ $value->user_id }}">
                                                <img src="/images/filled-sent.png" style="color: #757575"/>
                                            </button>
                            
                                            
                                             <button type="button" 
                                                    style="margin-left:6px;"
                                                    class="btn btn-xs btn-image load-communication-modal" 
                                                    data-id="{{$value->user_id}}" title="Load messages"
                                                    data-object="SOP">
                                                    <i class="fa fa-comments-o"></i>
                                            </button>
                            
                                        </div>
                                   </div>
                                </td>    
                                

                                <td>{{ date('yy-m-d', strtotime($value->created_at)) }}</td>

                                <td>                                    

                                    <a href="javascript:;" data-id="{{ $value->id }}"
                                        class="editor_edit btn-xs btn btn-image p-2" style="font-size:10px;">
                                        {{-- <img src="/images/edit.png"></a> --}}<i class="fa fa-edit"></i>
                                    {{-- <a onclick="editname({{$value->id}})" class="btn btn-image"> <img src="/images/edit.png"></a> --}}

                                    <a class="btn btn-image deleteRecord" data-id="{{ $value->id }}" style="font-size:15px; margin-left:-5px;">
                                        {{-- <img src="/images/delete.png" /></a> --}}
                                        <i class="fa fa-trash" style="color: #757575;" aria-hidden="true"></i>

                                        
                                    <a class="fa fa-info-circle view_log" style="font-size:15px; margin-left:-3px; color: #757575;"
                                        title="status-log"
                                        data-name="{{ $value->purchaseProductOrderLogs ? $value->purchaseProductOrderLogs->header_name : '' }}"
                                        data-id="{{ $value->id }}" data-toggle="modal" data-target="#ViewlogModal"></a>

                                     
                                        <a title="Download Invoice" class="btn btn-image" href="{{ route('sop.download',$value->id) }}">
                                            <i class="fa fa-download downloadpdf" style="font-size:15px; margin-left:-7px;"></i>
                                            </a>

                                            <button type="button" class="btn send-email-common-btn" data-toemail="@if ($value->user) {{$value->user->email}} @endif" data-object="Sop" data-id="{{$value->user_id}}" style="font-size:15px; margin-left:-19px;"><i class="fa fa-envelope-square"></i></button>
                                       
                                </td>
                        @endforeach

                    </tbody>
                </table>
                {{ $usersop->appends(request()->input())->links() }}
            </div>

        </div>
  

    {{-- ------------------ View Log ----------------------- --}}

    <div class="modal fade log_modal" id="ViewlogModal" tabindex="-1" role="dialog" aria-labelledby="log_modal"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">History Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive mt-2">
                        <table class="table table-bordered log-table"
                            style="border: 1px solid #ddd !important; color:black;table-layout:fixed">
                            <thead>
                                <tr>
                                    <th width="30%">Created By</th>
                                    <th width="30%">Updated By</th>
                                   
                                    <th width="40%">Updated At</th>

                                </tr>
                            </thead>

                            <tbody class="log_data" id="log_data">

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>

    {{-- -------------------------- end view log --------------------------- --}}


    {{-- --------------------------------------------- Update Data start----------------------------------------- --}}

    <div id="sopupdate" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Data</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form action="<?php echo route('updateName'); ?>" id="sop_edit_form">
                        <input type="text" hidden name="id" id="sop_edit_id">
                        @csrf
                        <div class="form-group">
                            <label for="name">Notes:</label>
                            <input type="hidden" class="form-control sop_old_name" name="sop_old_name" id="sop_old_name"
                                value="">
                            <input type="text" class="form-control sopname" name="name" id="sop_edit_name">
                        </div>

                        <div class="form-group">
                            <label for="content">Content</label>
                            <textarea class="form-control sop_edit_class" name="content" id="sop_edit_content"></textarea>
                        </div>

                        <button type="submit" class="btn btn-secondary ml-3 updatesopnotes">Update</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- -------------------------- end Update Data start-------------------------- --}}


    <div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                    <input type="text" name="search_chat_pop"  class="form-control search_chat_pop" placeholder="Search Message" style="width: 200px;">
                    <input type="hidden" id="chat_obj_type" name="chat_obj_type">
                    <input type="hidden" id="chat_obj_id" name="chat_obj_id">
                    <button type="submit" class="btn btn-default downloadChatMessages">Download</button>
                </div>
                <div class="modal-body" style="background-color: #999999;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
{{-- Content POP-up --}}
<div id="logMessageModel" class="modal fade" role="dialog">
     <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                  <h4 class="modal-title">Content description</h4>
            </div>
            <div class="modal-body">
               <p style="word-break: break-word;" ></p>
            </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
             </div>
         </div>
    </div>
</div>
{{-- End Content POP-up --}}


 <!-- Send Email Modal-->
<div id="commonEmailModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

       
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Send Email</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="" method="POST" enctype="multipart/form-data" id="resetdata">
                <input type="hidden" name="id" id="id">
                <input type="hidden" name="object" id="object">
                <input type="hidden" name="action" class="action" value="{{route('common.getmailtemplate')}}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Send To</strong>
                        <input type="text" name="sendto" class="form-control sendto" id="sendto">
                    </div>

                    <div class="form-group">
                        <strong>From Mail</strong>
                        <select class="form-control" name="from_mail" id="from_mail">
                          <?php $emailAddressArr = \App\EmailAddress::all(); ?>
                          @foreach ($emailAddressArr as $emailAddress)
                            <option value="{{ $emailAddress->from_address }}">{{ $emailAddress->from_name }} - {{ $emailAddress->from_address }} </option>
                          @endforeach
                        </select>
                    </div>

                    <div class="form-group text-right">
                        <a class="add-cc mr-3" href="#">Cc</a>
                        <a class="add-bcc" href="#">Bcc</a>
                    </div>

                    <div id="cc-label" class="form-group" style="display:none;">
                        <strong class="mr-3">Cc</strong>
                        <a href="#" class="add-cc">+</a>
                    </div>

                    <div id="cc-list" class="form-group">

                    </div>

                    <div id="bcc-label" class="form-group" style="display:none;">
                        <strong class="mr-3">Bcc</strong>
                        <a href="#" class="add-bcc">+</a>
                    </div>

                    <div id="bcc-list" class="form-group">

                    </div>
                   
                    <div class="form-group">
                        <strong>Subject *</strong>
                        <input type="text" class="form-control subject" name="subject" id="subject" required>
                    </div>

                    <div class="form-group">
                        <strong>Message *</strong>
                        <textarea name="message" class="form-control mail_message" id="message" rows="8" cols="80" required></textarea>
                    </div>

                    <div class="form-group">
                        <strong>Files</strong>
                        <input type="file" name="file[]" value="" multiple>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary sop-mail-send">Send</button>
                </div>
            </form>
        </div>

    </div>
</div>
 <!-- End Send Email Modal-->

@endsection
{{-- @include('common.commonEmailModal') --}}
@section('scripts')
{{-- <script src="{{asset('js/common-email-send.js')}}">//js for common mail</script>  --}}
    <script src="https://cdn.ckeditor.com/4.11.4/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('content');
        CKEDITOR.replace('sop_edit_content');
    </script>
<script>
   
    $(document).on('click','.send-email-common-btn',function(e){
        e.preventDefault();
     
        var ele = $(this).parentsUntil('form').parent();
        var mailtype = $(this).data('object');
        var id = $(this).data('id');
        var content =$(this).data('content');
        var toemail = $(this).data('toemail');
        $('#commonEmailModal').find('form').find('input[name="id"]').val(id);
        $('#commonEmailModal').find('form').find('input[name="sendto"]').val(toemail);
        $('#commonEmailModal').find('form').find('input[name="object"]').val(mailtype);
        $('#commonEmailModal').modal("show");
    });

    $(document).on('click','.sop-mail-send',function(e){
        e.preventDefault();
        
            let id = $("#id").val();
            let sendto = $("#sendto").val();
            let from_mail = $("#from_mail").val();
            let object = $("#object").val();
            let subject = $("#subject").val();
            let message = $(".mail_message").val();

            $.ajax({
                url: "{{ route('common.send.email') }}",
                type: 'POST',
                data: {
                   "id": id,
                    "sendto": sendto,
                   "from_mail": from_mail,
                    "object": object,
                    "subject": subject,
                    "message": message,
                    "from": 'sop',
                    "_token": "{{csrf_token()}}",
                                   
                },
                dataType: "json",
                success: function (response) {
                    $("#resetdata")[0].reset();
                    $('#commonEmailModal').modal('hide');
                   
                   toastr["success"]("Your Mail sent successfully!", "Message");
                 
                                              
                },
                error: function (response) {
                    toastr["error"]("There was an error sending the Mail...", "Message");
                   
                }
            });
       
        });
    
</script>

<script>

$(document).on('click', '.send-message-open', function (event) {

            var thiss = $(this);
            var $this = $(this);
            var data = new FormData();
            var sop_user_id = $(this).data('user_id');
            var message = $(this).parents('td').find("#messageid_"+sop_user_id).val();
           
            if (message.length > 0) {

            //  let self = textBox;

            $.ajax({
                url: "{{action('WhatsAppController@sendMessage', 'SOP-Data')}}",
                type: 'POST',
                data: {
                    "sop_user_id": sop_user_id,
                    "message": message,
                    "_token": "{{csrf_token()}}",
                   "status": 2,
                  
                },
                dataType: "json",
                success: function (response) {
                    $this.parents('td').find("#messageid_"+sop_user_id).val('');
                   toastr["success"]("Message sent successfully!", "Message");
                    $('#message_list_' + sop_user_id).append('<li>' + response.message.created_at + " : " + response.message.message + '</li>');
                   
                   
                },
               
                error: function (response) {
                    toastr["error"]("There was an error sending the message...", "Message");
                   
                }
            });
        } else {
                alert('Please enter a message first');
            }
        });
    </script>

    <script>
 $(document).on('click', '.expand-row', function () {
    $('#logMessageModel .modal-body p').html($(this).attr('data-subject'));
    $('#logMessageModel').modal("show");
    var selection = window.getSelection();
    // if (selection.toString().length === 0) {
       
    //     $(this).find('.td-mini-container').toggleClass('hidden');
    //     $(this).find('.td-full-container').toggleClass('hidden');
    // }
});
</script>
    <script>
        $(document).on("click", ".view_log", function(e) {

            var id = $(this).data('id');

            var purchase_order_products_id = $(this).data('data-id');
            var header_name = $(this).attr('data-name');

            $.ajax({
                type: "GET",
                url: "{{ route('sopname.logs') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    purchase_order_products_id: purchase_order_products_id,
                    header_name: header_name,
                },
                dataType: "json",
                success: function(response) {

                    var html_content = ''
                    $.each(response.log_data, function(key, value) { 
                        html_content += '<tr>'; 
                        html_content += '<td>' + value.sop.user.name + '</td>';
                        html_content += '<td>' + value.updated_by.name + '</td>';
                        html_content += '<td>' + value.created_at + '</td>';
                        html_content += '</tr>';
                        console.log(html_content, 132);
                    });

                    $("#log_data").html(html_content);
                    $('#log_modal').modal('show');
                },
                error: function() {
                    toastr['error']('Message not sent successfully!');
                }
            });
        });
    </script>

    <script>
        $('#FormModal').submit(function(e) {
            e.preventDefault();
            let name = $("#name").val();
            let content = CKEDITOR.instances['content'].getData(); //$('#cke_content').html();//$("#content").val();

            let _token = $("input[name=_token]").val();

            $.ajax({
                url: "{{ route('sop.store') }}",
                type: "POST",
                data: {
                    name: name,
                    content: content,

                    _token: _token
                },
                success: function(response) {
                
                    if (response) {

                        var content_class = response.sop.content.length < 270 ? '' : 'expand-row';
                        var content = response.sop.content.length < 270 ? response.sop.content : response.sop.content.substr(0, 270) + '.....';
                        $("#NameTable tbody").prepend('<tr id="sid' + response.sop.id +'" data-id="' + response.sop.id +'" class="parent_tr"><td>' + response.sop.id +
                            '</td><td> ' + response.sop.name + ' </td><td class="'+content_class+'" data-subject="'+response.sop.content+'"> ' + content  + ' </td><td class="table-hover-cell pr-0"> <div style="display:flex;" class=" d-flex flex-row w-100 justify-content-between"> <div style="flex-grow: 1"> <textarea  style="height:37px;" class="form-control" id="messageid_'+ response.sop.user_id +'" name="message" placeholder="Message"></textarea> </div>  <div style="width: min-content"><button class="btn btn-xs btn-image send-message-open" style="margin-left:6px;" data-user_id="'+ response.sop.user_id +'"> <img src="/images/filled-sent.png"/> </button> <button type="button" style="margin-left:6px;" class="btn btn-xs btn-image load-communication-modal" data-id="'+ response.sop.user_id +'" title="Load messages"data-object="SOP"> <i class="fa fa-comments-o"></i></button></div></div></td><td> ' + response.only_date + ' </td><td>' +
                                        

                            ' <a href="javascript:;" data-id="' + response.sop.id +'" class="editor_edit btn-xs btn btn-image p-2"><i class="fa fa-edit"></i></a>' +
                            '<a class="btn btn-image deleteRecord" style="font-size:15px; margin-left:-6px;" data-id="'+response.sop.id+'" ><i class="fa fa-trash" style="color: #757575;" aria-hidden="true"></i>' +

                            ' <a class="fa fa-info-circle view_log" style="font-size:15px; margin-left:-2px; color: #757575; " title="status-log" data-id="' +
                            response.sop.id +
                            '" data-toggle="modal" data-target="#ViewlogModal" data-name="' +
                            response.params.header_name + '"></a>' +

                            '<a title="Download Invoice" class="btn btn-image" href="'+ 'sop/DownloadData/' + response.sop.id +'"><i class="fa fa-download downloadpdf" style="font-size:15px; margin-left:-1px "></i></a>' +

                          ' <button type="button" class="btn send-email-common-btn" data-toemail="' + response.user_email[0].email + '" data-object="Sop" data-id='+ response.sop.user_id +' style="font-size:15px; margin-left:-20px;"><i class="fa fa-envelope-square"></i></button>' +

                            '</td></tr>');

                        $("#FormModal")[0].reset();
                        $('.cke_editable p').text(' ')
                        CKEDITOR.instances['content'].setData('')
                        $("#exampleModal").modal('hide');
                        toastr["success"]("Data Inserted Successfully!", "Message")
                    }
                }

            });
        });
    </script>


    <script>
        $(document).on('click', '.deleteRecord', function() {

            let $this = $(this)
            console.log($this)
            var result = window.confirm('Are You Sure Want To Delete This Records?');
            if (result == true) {
                // alert('Are You Sure Want To Delete This Records?');
                var id = $(this).data("id");
                var token = $("meta[name='csrf-token']").attr("content");

                $.ajax({
                    url: "/sop/" + id,
                    type: 'DELETE',
                    data: {
                        "id": id,
                        "_token": token,
                    },
                    success: function(response) {

                        $this.closest('.parent_tr').remove()
                        toastr["success"](response.message, "Message")

                    }
                });
            }
        });
    </script>

    <script>
        $(document).on('click', '.editor_edit', function() {

            var $this = $(this);

            $.ajax({
                type: "GET",
                data: {
                    id: $this.data("id")

                },
                url: "{{ route('editName') }}"
            }).done(function(data) {

                console.log(data.sopedit);

                $('#sop_edit_id').val(data.sopedit.id)
                $('#sop_edit_name').val(data.sopedit.name)
                $('#sop_old_name').val(data.sopedit.name)

                CKEDITOR.instances['sop_edit_content'].setData(data.sopedit.content)

                $("#sopupdate #sop_edit_form").attr('data-id', $($this).attr('data-id'));
                $("#sopupdate").modal("show");

            }).fail(function(data) {
                console.log(data);
            });
        });
    </script>

    <script>
        $(document).on('submit', '#sop_edit_form', function(e) {
            e.preventDefault();
            const $this = $(this)
            $(this).attr('data-id', );

            $.ajax({
                type: "POST",
                data: $(this).serialize(),
                url: "{{ route('updateName') }}",
                datatype: "json"
            }).done(function(data) {
                          
                
                    var content = data.sopedit.content.length < 270 ? data.sopedit.content : data.sopedit.content.substr(0, 270) + '.....';
    
                    let id = $($this).attr('data-id');
                   
                    $('#sid' + id + ' td:nth-child(1)').html(data.sopedit.id);
                    $('#sid' + id + ' td:nth-child(2)').html(data.sopedit.name);
                    $('#sid' + id + ' td:nth-child(3)').attr('data-subject',data.sopedit.content).html(content);
                    if(data.sopedit.content.length < 270){
                        $('#sid' + id + ' td:nth-child(3)').html(content).removeClass('expand-row');
                    }else{
                        $('#sid' + id + ' td:nth-child(3)').html(content).addClass('expand-row');
                    }
                    $("#sopupdate").modal("hide");
                    toastr["success"]("Data Updated Successfully!", "Message")
                // }
            }).fail(function(data) {
                console.log(data);
            });
        });
    </script>

@endsection
