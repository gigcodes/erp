@extends('layouts.app')

@section('title', 'SKU log')

@section("styles")
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 48;
        }

        input {
            width: 100px;
        }
        .log-text-style{
            word-wrap: break-word;
            max-width: 600px;
        }
        .load-more svg {
            -webkit-animation: spin 3s linear infinite;
            animation: spin 3s linear infinite;
            /*transform: rotate(180deg);*/
        }
        .load-more {
            position: relative;
            width: 100%;
            text-align: center;
            padding: 40px 0;
        }

    </style>
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading"> Whatsapp Logs (<span id="count">{{ count($array) }}</span>)</h2>
            <div class="pull-right">
                <button type="button" class="btn btn-secondary" onclick="sendMulti()" style="display: none;" id="nulti">
                    Send Selected
                </button>
                <button type="button" class="btn btn-image" onclick="refreshPage()"><img src="/images/resend2.png"/>
                </button>
            </div>

        </div>
    </div>

    @include('partials.flash_messages')

    <div class="row">
        <div class="col-md-12">
            <div class="panel-group">
                <div class="panel mt-5 panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" href="#collapse1">Pending Issues</a>
                        </h4>
                    </div>
                    <div id="collapse1" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div class="pull-right">
                                <form action="{{ route('broadcasts.index') }}" method="GET">
                                    <div class="form-group">
                                        <div class="row">

                                        </div>
                                    </div>
                                </form>
                            </div>
                            <table class="table table-bordered table-striped" id="phone-table">
                                <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>User</th>
                                    <!-- <th>Count</th> -->

                                </tr>
                                </thead>
                                <tbody>
                                @foreach($array as $row)
                                    <tr>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="mt-3 col-md-12">
        <table class="table table-bordered table-striped" id="log-table">
            <thead>
            <tr>
                <th style="width: 1% !important;">Sr.No</th>
                <th style="width: 1% !important;">Date</th>
                <th style="width: 1% !important;">Sent ?</th>
                <th style="width: 3% !important;">REceiver Number</th>
                <th style="width: 40% !important;">Text</th>
                <th style="width: 3% !important;">Action</th>
            </tr>
            
            </thead>
            <tbody id="content_data" class="infinite-scroll-pending-inner">
                @php
                    $sr_no = 1;
                @endphp
            @foreach($array as $row)
            @php
                $message = strpos($row['error_message1'],'"message');
                $message_str = strtok(substr($row['error_message1'],$message), ',');
                // dump(str_replace($message_str,"",$row['error_message1']));
                $message1 = strpos($row['error_message1'],'whatsapp_number');
                $receiver_number = substr($row['error_message1'],$message1+18,12);
                $null = substr($row['error_message1'],$message1+17,4);
            @endphp
                <tr>
                    <td>{{ $sr_no++ }}</td>
                    <td>{{ $row['date'] }}</td>
                    <td>No</td>
                    @if ($message1 == '' || $null == "null")
                        <td></td>
                    @else
                        <td>{{ $receiver_number }}</td>
                    @endif
                    <td class="errorLog">
                        <div class="log-text-style">
                            @if ($isAdmin)
                            Message1 : {{$row['error_message1']}} <br>
                        @else
                            @if ($message)
                                Message1 : {{str_replace($message_str,"",$row['error_message1'])}} <br>    
                            @else
                                Message1 : {{$row['error_message1']}} <br>
                            @endif
                        @endif
                        Message2 : {{$row['error_message2']}}
                        </div>
                    </td>
                    <td>
                        @if((isset($row['error_message1']) && getStr($row['error_message1'])) || (isset($row['error_message2']) && getStr($row['error_message2'])))
                            @if ($isAdmin)
                                <button class="btn btn-success sentMessage text-center" >
                                    Resend
                                </button>
                            @else
                                <button class="btn btn-success text-center" disabled>
                                    Resend
                                </button>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        
    </table>
    <div class="load-mored d-flex justify-content-center" style="display: none">
        <svg preserveAspectRatio="xMidYMid meet" id="comp-kma9ypuwsvgcontent" viewBox="19.999998092651367 28.5 160 143" height="40" width="40" xmlns="http://www.w3.org/2000/svg" data-type="color" role="img">
            <g>
                <path d="M159.9 37.8c0 5.1-4.1 9.3-9.2 9.3s-9.3-4.1-9.3-9.3c0-5.1 4.1-9.3 9.3-9.3 5 0 9.1 4.1 9.2 9.3z" fill="#757575" data-color="1"></path>
                <path d="M172.8 56c0 4.8-3.9 8.8-8.7 8.8-4.8 0-8.8-3.9-8.8-8.8 0-4.8 3.9-8.8 8.8-8.8 4.7 0 8.7 4 8.7 8.8z" fill="#757575" data-color="1"></path>
                <path d="M179.5 76.6c0 4.6-3.7 8.3-8.2 8.3-4.6 0-8.2-3.7-8.3-8.3 0-4.6 3.7-8.3 8.3-8.3 4.5.1 8.2 3.8 8.2 8.3z" fill="#757575" data-color="1"></path>
                <path d="M180 98.2c0 4.3-3.5 7.8-7.7 7.8-4.3 0-7.7-3.5-7.7-7.7 0-4.3 3.5-7.8 7.7-7.7 4.2-.1 7.7 3.3 7.7 7.6z" fill="#757575" data-color="1"></path>
                <path d="M174.3 119.3c0 4-3.2 7.3-7.2 7.3s-7.2-3.2-7.2-7.2 3.2-7.2 7.2-7.2c4-.1 7.2 3.1 7.2 7.1z" fill="#757575" data-color="1"></path>
                <path d="M162.5 138.5c0 3.7-3 6.7-6.7 6.8s-6.7-3-6.7-6.7 3-6.7 6.7-6.7c3.7-.1 6.7 2.9 6.7 6.6z" fill="#757575" data-color="1"></path>
                <path d="M145 153.9c0 3.4-2.8 6.2-6.2 6.2-3.4 0-6.2-2.8-6.2-6.2 0-3.4 2.8-6.2 6.2-6.2 3.5-.1 6.2 2.7 6.2 6.2z" fill="#757575" data-color="1"></path>
                <path d="M124.6 163.2c0 3.2-2.6 5.7-5.7 5.7-3.2 0-5.7-2.6-5.7-5.7 0-3.2 2.6-5.7 5.7-5.7 3.1-.1 5.7 2.5 5.7 5.7z" fill="#757575" data-color="1"></path>
                <path d="M102.7 166.3c0 2.9-2.3 5.2-5.2 5.2-2.9 0-5.2-2.3-5.2-5.2 0-2.9 2.3-5.2 5.2-5.2 2.9-.1 5.2 2.3 5.2 5.2z" fill="#757575" data-color="1"></path>
                <path d="M80.8 163.2c0 2.6-2.1 4.7-4.7 4.7s-4.7-2.1-4.7-4.7 2.1-4.7 4.7-4.7c2.6-.1 4.7 2.1 4.7 4.7z" fill="#757575" data-color="1"></path>
                <path d="M60.4 153.9c0 2.3-1.9 4.2-4.2 4.2-2.3 0-4.2-1.9-4.2-4.2 0-2.3 1.9-4.2 4.2-4.2 2.3-.1 4.2 1.8 4.2 4.2z" fill="#757575" data-color="1"></path>
                <path d="M42.9 138.5c0 2.1-1.7 3.7-3.7 3.7s-3.7-1.7-3.7-3.7c0-2.1 1.7-3.7 3.7-3.7 2.1 0 3.7 1.7 3.7 3.7z" fill="#757575" data-color="1"></path>
                <path d="M31.1 119.3c0 1.8-1.4 3.2-3.2 3.2-1.8 0-3.2-1.4-3.2-3.2 0-1.8 1.4-3.2 3.2-3.2 1.7 0 3.2 1.4 3.2 3.2z" fill="#757575" data-color="1"></path>
                <path d="M25.4 98.2c0 1.5-1.2 2.7-2.7 2.7-1.5 0-2.7-1.2-2.7-2.7 0-1.5 1.2-2.7 2.7-2.7 1.5 0 2.7 1.2 2.7 2.7z" fill="#757575" data-color="1"></path>
                <path d="M25.9 76.6c0 1.2-1 2.2-2.2 2.2-1.2 0-2.2-1-2.2-2.2 0-1.2 1-2.2 2.2-2.2 1.2 0 2.2 1 2.2 2.2z" fill="#757575" data-color="1"></path>
                <path d="M32.6 56c0 .9-.8 1.7-1.7 1.7-.9 0-1.7-.8-1.7-1.7 0-.9.8-1.7 1.7-1.7 1 0 1.7.8 1.7 1.7z" fill="#757575" data-color="1"></path>
                <path d="M45.6 37.8c0 .7-.5 1.2-1.2 1.2s-1.2-.5-1.2-1.2.5-1.2 1.2-1.2c.6 0 1.2.5 1.2 1.2z" fill="#757575" data-color="1"></path>
                <path d="M70.3 124c0 3.1-2.5 5.7-5.7 5.7-3.1 0-5.7-2.5-5.7-5.7 0-3.1 2.5-5.7 5.7-5.7s5.7 2.5 5.7 5.7z" fill="#c7c7c7" data-color="2"></path>
                <path d="M61.8 112.8c0 3-2.4 5.4-5.4 5.4-3 0-5.4-2.4-5.4-5.4 0-3 2.4-5.4 5.4-5.4 3 0 5.4 2.5 5.4 5.4z" fill="#c7c7c7" data-color="2"></path>
                <path d="M57.1 100.2c0 2.8-2.3 5.1-5.1 5.1s-5.1-2.3-5.1-5.1 2.3-5.1 5.1-5.1 5.1 2.3 5.1 5.1z" fill="#c7c7c7" data-color="2"></path>
                <path d="M56.1 87c0 2.6-2.1 4.8-4.7 4.8s-4.7-2.1-4.7-4.8 2.1-4.8 4.7-4.8 4.7 2.1 4.7 4.8z" fill="#c7c7c7" data-color="2"></path>
                <path d="M59 74c0 2.5-2 4.4-4.4 4.4-2.4 0-4.4-2-4.4-4.4s2-4.4 4.4-4.4c2.4 0 4.4 2 4.4 4.4z" fill="#c7c7c7" data-color="2"></path>
                <path d="M65.6 62.3c0 2.3-1.8 4.1-4.1 4.1s-4.1-1.8-4.1-4.1 1.9-4.1 4.1-4.1c2.3-.1 4.1 1.8 4.1 4.1z" fill="#c7c7c7" data-color="2"></path>
                <path d="M75.7 52.9c0 2.1-1.7 3.8-3.8 3.8-2.1 0-3.8-1.7-3.8-3.8 0-2.1 1.7-3.8 3.8-3.8 2.1-.1 3.8 1.6 3.8 3.8z" fill="#c7c7c7" data-color="2"></path>
                <path d="M87.6 47.2c0 1.9-1.6 3.5-3.5 3.5s-3.5-1.6-3.5-3.5 1.6-3.5 3.5-3.5c1.9-.1 3.5 1.5 3.5 3.5z" fill="#c7c7c7" data-color="2"></path>
                <path d="M100.4 45.3c0 1.8-1.4 3.2-3.2 3.2S94 47 94 45.3c0-1.8 1.4-3.2 3.2-3.2s3.2 1.4 3.2 3.2z" fill="#c7c7c7" data-color="2"></path>
                <path d="M113.2 47.2c0 1.6-1.3 2.9-2.9 2.9s-2.9-1.3-2.9-2.9c0-1.6 1.3-2.9 2.9-2.9s2.9 1.3 2.9 2.9z" fill="#c7c7c7" data-color="2"></path>
                <path d="M125.1 52.9c0 1.4-1.2 2.6-2.6 2.6-1.4 0-2.6-1.2-2.6-2.6s1.2-2.6 2.6-2.6c1.4 0 2.6 1.1 2.6 2.6z" fill="#c7c7c7" data-color="2"></path>
                <path d="M135.2 62.3c0 1.3-1 2.3-2.3 2.3-1.3 0-2.3-1-2.3-2.3 0-1.3 1-2.3 2.3-2.3 1.2 0 2.3 1 2.3 2.3z" fill="#c7c7c7" data-color="2"></path>
                <path d="M141.8 74c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2c1.1.1 2 .9 2 2z" fill="#c7c7c7" data-color="2"></path>
                <path d="M144.7 87c0 .9-.7 1.7-1.7 1.7-.9 0-1.7-.7-1.7-1.7 0-.9.7-1.7 1.7-1.7.9 0 1.6.7 1.7 1.7z" fill="#c7c7c7" data-color="2"></path>
                <path d="M143.7 100.2c0 .7-.6 1.4-1.3 1.4-.7 0-1.3-.6-1.3-1.4 0-.7.6-1.4 1.3-1.4.7 0 1.3.6 1.3 1.4z" fill="#c7c7c7" data-color="2"></path>
                <path d="M139 112.8c0 .6-.5 1-1 1-.6 0-1-.5-1-1 0-.6.5-1 1-1s1 .4 1 1z" fill="#c7c7c7" data-color="2"></path>
                <path d="M130.5 124c0 .4-.3.7-.7.7-.4 0-.7-.3-.7-.7s.3-.7.7-.7c.3-.1.7.3.7.7z" fill="#c7c7c7" data-color="2"></path>
            </g>
        </svg>
    </div>
    </div>
    <div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                </div>
                <div class="modal-body" style="background-color: #999999;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @include('partials.modals.task-module')
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script type="text/javascript">


        $(document).on('click', '.sentMessage', function () {

            var msg = $(this).parents('tr').find('.errorLog').text();


            var myStr = msg;
            var matches = myStr.match(/\[(.*?)\]/);
            var submatch = '';
            if (matches) {
                submatch = matches[1];
            }

            var chat_id = null;
            // submatch = '{"number":"$number","whatsapp_number":"$sendNumber","message":"$text","validation":"$validation","chat_message_id":"1786391"}'
            if (submatch !== '') {
                var json = JSON.parse(submatch)
                if (typeof json.chat_message_id !== 'undefined') {
                    chat_id = json.chat_message_id;
                }
            }


            // console.log(json.chat_message_id);

            if (chat_id !== null) {

                $.ajax({
                    url: 'whatsapp/' + chat_id + '/resendMessage',
                    dataType: "json",
                    type: 'post',
                }).done(function (data) {

                    console.log(data);

                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                });

            }


        })


        $(document).ready(function () {
            $(".select-multiple").multiselect();
            $(".select-multiple2").select2();
        });


    $(document).ready(function() {
       $(".select-multiple").multiselect();
       $(".select-multiple2").select2();
    });


    $(".sentMessage").click(function() {
        console.log($(this).attr('data-details'));
    });

        var page = 1;
        function getScrollTop() {
            return (window.pageYOffset !== undefined) ? window.pageYOffset : (document.documentElement || document.body.parentNode || document.body).scrollTop;
        }
        window.onscroll = function() {
            if (getScrollTop() < getDocumentHeight() - window.innerHeight) return;
            loadMore(++page);
        };

        function getDocumentHeight() {
            const body = document.body;
            const html = document.documentElement;

            return Math.max(
                body.scrollHeight, body.offsetHeight,
                html.clientHeight, html.scrollHeight, html.offsetHeight
            );
        };


        function loadMore(page) {

            page = page + 1;
            $.ajax({
                url: "/whatsapp-log?page="+page,
                type: 'GET',
                data: $('.form-search-data').serialize(),
                beforeSend:function(){
                        $('.load-more').show();
                },
                success: function (data) {
                    if (data == '') {
                        $('.load-more').hide();
                    }
                    $('.load-more').hide();
                    $('.infinite-scroll-pending-inner').append(data);
                },
                error: function () {
                    $('.load-more').hide();
                }
            });
        }




    </script>
@endsection