@extends('layouts.app')

@section('content')

    <style>
        td audio {
            height: 30px;
        }

        td {
            padding: 5px 8px 0 !important;

        }

    </style>

<h2 class="page-heading flex" style="padding: 8px 5px 8px 10px;border-bottom: 1px solid #ddd;line-height: 32px;">
    Missed Call
    <div class="margin-tb" style="flex-grow: 1;">
        <div class="pull-right ">


            <div class="d-flex justify-content-between  mx-3">

                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#add-status">
                    Add Status
                </button>
            </div>
        </div>
    </div>
</h2>


<form method="get" action="{{route('order.missed-calls')}}">

    <div class="form-group">
        <div class="row ml-2">
    
   
            <div class="col-md-2">

                <select name="filterWebsite" class="set-call-status select2" >
                    <option value="{{ null }}">Select website</option>
                    @foreach ($storeWebsite as $key=> $website)
                        <option value="{{ $key }}"  {{ $key == $selectedWebsite ? 'selected': '' }}>{{ $website }}</option>
                    @endforeach
                </select>

        </div>


            <div class="col-md-2">

                    <select name="filterStatus" class="set-call-status select2" >
                        <option value="{{ null }}">Select status</option>
                        @foreach ($allStatuses as $status)
                            <option value="{{ $status->id }}" {{ $selectedStatus == $status->id ? 'selected' :'' }} >{{ $status->name }}</option>
                        @endforeach
                    </select>

            </div>

           
            <div class="col-md-1 d-flex justify-content-between">
                <button type="submit" class="btn btn-image" ><img src="/images/filter.png"></button>
            </div>
        </div>
    </div>
</form>


    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

      
     


    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th style="width: 12%">Mobile Number</th>
                    <th style="width: 12%">Message</th>
                    <th style="width: 15%">Website name</th>
                    <th style="width: 20%">Call Recording</th>
                    <th style="width: 15%">Call Time</th>
                    <th style="width: 11%">Status</th>
                    <th  style="width:15%">Action</th>
                </tr>


                @foreach ($callBusyMessages['data'] as $key => $callBusyMessage)
                    <tr class="">
                        <td>
                            @if (isset($callBusyMessage['customer_name']))
                                {{ $callBusyMessage['customer_name'] }}
                            @else
                                {{ $callBusyMessage['twilio_call_sid'] }}
                            @endif
                        </td>
                        <td>{{ $callBusyMessage['message'] }}</td>
                        <td>{{ !empty($callBusyMessage['store_website_name']) ? $callBusyMessage['store_website_name'] : ' ' }}
                        </td>
                        <td>
                            <audio src="{{ $callBusyMessage['recording_url'] }}" controls preload="metadata">
                                <p>Alas, your browser doesn't support html5 audio.</p>
                            </audio>
                        </td>
                        <td>{{ $callBusyMessage['created_at'] }}</td>

                        <td>

                            <select name="status" class="set-call-status select2" data-call-id = {{ $callBusyMessage['id']  }}>
                                <option value="{{ null }}">Select status</option>
                                @foreach ($allStatuses as $status)
                                    <option value="{{ $status->id }}"  {{ $status->id == $callBusyMessage['call_busy_message_statuses_id'] ? 'selected':''  }}>{{ $status->name }}</option>
                                @endforeach
                            </select>

                        </td>

                        <td>
                            <i class="fa fa-info-circle show-histories" type="button" data-product-id="1709"
                            title="Status Logs" aria-hidden="true" data-id="107" data-name="Status"
                            style="cursor: pointer;" data-call-message-id={{ $callBusyMessage['id'] }}></i>
                         
                         
                            <i class="fa fa-envelope-o send-mail-or-whatsup-message-button pl-1" type="button" data-product-id="1709"
                            title="Send Message" aria-hidden="true" data-id="107" data-name="Status"
                            style="cursor: pointer;" data-call-message-id={{ $callBusyMessage['id']}}   data-number="{{ $callBusyMessage['twilio_call_sid'] }}" data-fullname="{{ isset($callBusyMessage['customer_name']) ? $callBusyMessage['customer_name'] : null }}" data-customer-id="{{ isset($callBusyMessage['customerid']) ? $callBusyMessage['customerid'] : null }}"></i>

                            
                            <a type="button" class="btn btn-xs btn-image load-communication-modal pl-1" data-object="customer" data-id="{{isset($callBusyMessage['customerid']) ? $callBusyMessage['customerid'] : null}}" data-load-type="text" data-all="1" title="Load messages" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}"><img src="/images/chat.png" alt=""></a>

                            @if (isset($callBusyMessage['customerid']))
                                <a class="btn btn-image p-0"
                                    href="{{ route('customer.show', $callBusyMessage['customerid']) }}"><img
                                        src="/images/view.png" /></a>
                            @endif

                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="order-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Orders</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="show-ordres-table" class="table table-bordered table-hover" style="table-layout:fixed;">
                        <thead>
                            <th>Order id</th>
                            <th>Order type</th>
                            <th>Order status</th>
                            <th>Payment mode</th>
                            <th>Price</th>
                            <th>Currency</th>
                            <th>Order date </th>
                            {{-- <th>Created At</th> --}}
                        </thead>
                        <tbody class="show-ordres-body">

                        </tbody>
                    </table>
                </div>
              
            </div>
        </div>
    </div>

    
     <!-- Add status  Modal -->
     <div class="modal fade" id="add-status" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="exampleModalLabel">Add status</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body add-status-body">
              
                <form action="{{ route('order.store.add-status') }}" method="POST" id="add-status-form">
                    @csrf
                    <div class="form-group">
                        <input type="text" class="form-control" id="exampleInputEmail1" name="name" class="form-text text-muted" placeholder="Add status" required>
                    </div>
    
                    <div class="float-right">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      <button  class="btn btn-secondary">Add status</button>
                    </div>
                </form>
    
            </div>
          </div>
        </div>
      </div>
    
     <!-- Send whats-up /email message  -->
     <div class="modal fade" id="send-mail-or-whatsup-message" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="exampleModalLabel">Send message</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body add-status-body">
              
                <form action="{{ route('order.send-message.whatsapp-or-email') }}" method="POST" id="send-message-whatsup-or-email">
                    @csrf
                    

                    <div class="form-group">
                        <label for="formGroupExampleInput">Message</label>
                        <textarea type="text" class="form-control" name="message" id="formGroupExampleInput" placeholder="Enter message" required></textarea>
                      </div>

                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="whatsapp-checked" value="whatsapp" name="whatsapp" checked>
                            <label class="form-check-label" for="whatsapp-checked">Whatsapp</label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="email-checked" value="email" name="email" checked>
                            <label class="form-check-label" for="email-checked">Email</label>
                          </div>
                      </div>

                      <div class="float-right">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button  class="btn btn-secondary">Send</button>
                      </div>

                </form>
    
            </div>
          </div>
        </div>
      </div>
    
        {{-- chat history modal  --}}
        <div id="chat-list-history" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Communication</h4>&nbsp;&nbsp;
                        <input type="text" name="search_chat_pop"  class="form-control search_chat_pop" placeholder="Search Message" style="width: 50%;">&nbsp;&nbsp;
                        <!-- <input type="text" name="search_chat_pop_time"  class="form-control search_chat_pop_time" placeholder="Search Time" style="width: 200px;"> -->
            <input style="min-width: 30px;" placeholder="Search by date" value="" type="text" class="form-control search_chat_pop_time" name="search_chat_pop_time">
                        
                    </div>
                    <div class="modal-body" style="background-color: #999999;">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>



    <script type="text/javascript">
let userData  = null

        $('.set-call-status').select2()

        jQuery(document).ready(function($) {
            $('audio').on("play", function(me) {
                $('audio').each(function(i, e) {
                    if (e !== me.currentTarget) {
                        this.pause();
                    }
                });
            });
        })



        $(document).on('click', '.show-histories', function() {

         const customer_id = $(this).data("call-message-id")

            $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/order/missed-calls/orders/" +customer_id,
                   
                })
                .done(function(response) {
                    let html = null
                    if (response.length) {

                        response.forEach((element) => {
                            console.log(element)
                            const final_html = `
                                  <tr>
                                    <td style="word-break: break-word;">${element.order_id  ?? '-'}</td>
                                    <td style="word-break: break-word;">${element.order_type ?? '-'}</td>
                                    <td style="word-break: break-word;">${element.order_status ?? '-'}</td>
                                    <td style="word-break: break-word;">${element.payment_mode ?? '-'}</td>
                                    <td style="word-break: break-word;">${element.price ?? '-'}</td>
                                    <td style="word-break: break-word;">${element.currency ?? '-'}</td>
                                    <td style="word-break: break-word;">${element.order_date ?? '-'}</td>
                                </tr>
                                `
                            html += final_html

                        })

                        $('.show-ordres-body').html(html)
                    }

                });
            $('#order-details').modal('show')
        })


    $(document).on('submit','#add-status-form',function(e){
                e.preventDefault()
                $.ajax({
                    method:'POST',
                    url: "/order/calls/add-status",
                    data:$(this).serialize()
                   
                })
                .done(function(response) {
                    toastr['success'](response.message, 'success');

                        const html = ` <option value=${response.data.id}>${response.data.name}</option> `
                        $('.set-call-status').append(html);
                        $('#add-status-form')[0].reset()
 
                    $('#add-status').modal('hide')
                   
                });

    })

    $(document).on('change','.set-call-status',function(e){
                e.preventDefault()
        let select_id = $(this).val();
        let select_call_id = $(this).data('call-id')
        let fullname = $(this).data('fullname')
        let data_number = $(this).data('number')


                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method:'POST',
                    url: "/order/calls/store-status/"+select_call_id,
                    data:{
                        select_id,
                        select_call_id,
                        fullname,
                        data_number

                    }
                   
                })
                .done(function(response) {
                    toastr['success'](response.message, 'success');
                   
                });

    })


    $(document).on('click','.send-mail-or-whatsup-message-button',function(e){
        const fullName = $(this).data('fullname')
        const fullNumber = $(this).data('number')
        const fullcustomerId = $(this).data('customer-id')

        userData = {fullName,fullNumber,fullcustomerId}
        e.preventDefault()
        $('#send-mail-or-whatsup-message').modal('show')

    })



    $(document).on('submit','#send-message-whatsup-or-email',function(e){

        e.preventDefault()
const formData = $(this).serialize()
// const formData = new FormData(document.getElementById("send-message-whatsup-or-email"))
// console.log(formData)
        
            $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () {
                    $("#loading-image-preview").show();
                },
                    method:'POST',
                    url: "/order/calls/send-message",
                    data: {
                        formData,
                        fullName : userData.fullName,
                        fullNumber: userData.fullNumber,
                        customerId:userData.fullcustomerId
                    }
                   
                })
                .done(function(response) {
                    $("#loading-image-preview").hide();

                    if(response.error){
                        toastr['error'](response.error, 'error');
                        return
                    }
                    if(response.message){
                        $('#send-message-whatsup-or-email')[0].reset()
                        toastr['success'](response.message, 'success');
                        $('#send-mail-or-whatsup-message').modal('hide')
                    }

                }).fail(function (response) {
                $("#loading-image-preview").hide();
                console.log("Sorry, something went wrong");
            });;

    })







    </script>

@endsection
