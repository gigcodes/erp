@php
$currentRoutes = \Route::current();
//$metaData = \App\Routes::where(['url' => $currentRoutes->uri])->first();
$metaData = '';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
if (isset($metaData->page_title) && $metaData->page_title != '') {
    $title = $metaData->page_title;
} else {
    $title = trim($__env->yieldContent('title'));
}
?>
    @if (trim($__env->yieldContent('favicon')))
    <link rel="shortcut icon" type="image/png" href="/favicon/@yield ('favicon')" />
    @elseif (!\Auth::guest())
    <link rel="shortcut icon" type="image/png" href="/generate-favicon?title={{$title}}" />
    @endif
    <title>{!! $title !!}</title>
    <!-- CSRF Token -->

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(isset($metaData->page_description) && $metaData->page_description!='')
        <meta name="description" content="{{ $metaData->page_description }}">
    @else
        <meta name="description" content="{{ config('app.name') }}">
    @endif


    {{-- <title>{{ config('app.name', 'ERP for Sololuxury') }}</title> --}}

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/richtext.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    {{--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />--}}

    <script src="{{siteJs('site.js')}}" defer></script>
    <script>var BASE_URL = "{{config('app.url')}}";</script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{asset('js/readmore.js')}}" defer></script>
    <script src="{{asset('/js/generic.js')}}" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script> -->
   <style type="text/css">
        .select2-container--open{
            z-index:9999999
        }

        .ipusersSelect{
            margin-top:-30px;
            font-size: 14px;
        }
        #select-user .select2-container--default {
            display: inline-block;
            margin-bottom: 28px;
            font-size: 14px;
        }

        #message-chat-data-box .p1[data-count]:after{
          position:absolute;
          right:10%;
          top:8%;
          content: attr(data-count);
          font-size:90%;
          padding:.1em;
          border-radius:50%;
          line-height:1em;
          color: white;
          background:rgba(255,0,0,.85);
          text-align:center;
          min-width: 1em;
          //font-weight:bold;
        }
        #quick-sidebar {
            padding-top: 35px;
        }
        #notification_unread{
            color:#fff;
        }

    #message-chat-data-box .p1[data-count]:after {
        position: absolute;
        right: 10%;
        top: 8%;
        content: attr(data-count);
        font-size: 90%;
        padding: .1em;
        border-radius: 50%;
        line-height: 1em;
        color: white;
        background: rgba(255, 0, 0, .85);
        text-align: center;
        min-width: 1em;
        //font-weight:bold;
    }

    #quick-sidebar {
        padding-top: 35px;
    }

    #notification_unread {
        color: #fff;
    }

    .refresh-btn-stop {
        color: red
    }

    .refresh-btn-start {
        color: green
    }

    .openmodel {
        overflow: hidden;
    }

    .modal {
        overflow-y: auto !important;
    }
    .shortcut-estimate-search-container .select2.select2-container{
        width: 200px!important
    }

    #event-alerts .event-alert-badge {
        position: absolute;
        top: -4px;
        left: 25px;
        border-radius: 50%;
        background-color: red;
        border: 1px solid white;
        color: white;
        height: 10px;
        width: 10px;
    }

    </style>
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>--}}
    @stack('link-css')
    @yield('link-css')
{{--    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />--}}
    <script>
    let Laravel = {};
    Laravel.csrfToken = "{{csrf_token()}}";
    window.Laravel = Laravel;
    </script>
    {{--I/m geting error in console thats why commented--}}

    {{-- <script>--}}
    {{-- $('.readmore').readmore({--}}
    {{-- speed: 75,--}}
    {{-- moreLink: '<a href="#">Read more</a>',--}}
    {{-- lessLink: '<a href="#">Read less</a>'--}}
    {{-- });--}}
    {{-- </script>--}}
    @stack("jquery")
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script> --}}

    {{-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.bundle.min.js"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script> --}}

    {{-- When jQuery UI is included tooltip doesn't work --}}
    {{-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.js"></script>

    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script type="text/javascript" src="https://media.twiliocdn.com/sdk/js/client/v1.14/twilio.min.js"></script>
    <script src="https://sdk.twilio.com/js/taskrouter/v1.21/taskrouter.min.js"></script>

    <script type="text/javascript" src="https://unpkg.com/tabulator-tables@4.0.5/dist/js/tabulator.min.js"></script>

    <script src="{{ asset('js/bootstrap-notify.js') }}"></script>
    <script src="{{ asset('js/calls.js') }}"></script>

    @if (Auth::id() == 3 || Auth::id() == 6 || Auth::id() == 23 || Auth::id() == 56)
    @endif

    <script src="{{ asset('js/custom.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.3.3/bootstrap-slider.min.js"></script>

    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js"></script>


    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet" />

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js">
    </script>

    @if(Auth::user())
    {{--<link href="{{ url('/css/chat.css') }}" rel="stylesheet">--}}
    <script>
    window.userid = "{{Auth::user()->id}}";

    window.username = "{{Auth::user()->name}}";

    loggedinuser = "{{Auth::user()->id}}";
    </script>
    @endif
    <script type="text/javascript">
    var BASE_URL = '{{ config('
    app.url ') }}';
    </script>


    <!-- Fonts -->

    <link rel="dns-prefetch" href="https://fonts.gstatic.com">

    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


    <!-- Styles -->

    <link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet">


    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

    {{-- <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"> --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.3.3/css/bootstrap-slider.min.css">

    <link href="https://unpkg.com/tabulator-tables@4.0.5/dist/css/tabulator.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.css">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/global_custom.css') }}">
    @yield("styles")
    <script src="https://www.gstatic.com/firebasejs/8.3.2/firebase.js"></script>
    <script>
        const firebaseConfig = {
            apiKey: '{{env('FCM_API_KEY')}}',
            authDomain: '{{env('FCM_AUTH_DOMAIN')}}',
            projectId: '{{env('FCM_PROJECT_ID')}}',
            storageBucket: '{{env('FCM_STORAGE_BUCKET')}}',
            messagingSenderId: '{{env('FCM_MESSAGING_SENDER_ID')}}',
            appId: '{{env('FCM_APP_ID')}}',
            measurementId: '{{env('FCM_MEASUREMENT_ID')}}'
        };
        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();
        messaging
            .requestPermission()
            .then(function () {
                return messaging.getToken()
            })
            .then(function (response) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{{ route("store.token") }}',
                    type: 'POST',
                    data: {
                        token: response
                    },
                    dataType: 'JSON',
                    success: function (response) {
                    },
                    error: function (error) {
                        console.error(error);
                    },
                });
            }).catch(function (error) {
            alert(error);
        });
        messaging.onMessage(function (payload) {
            const title = payload.notification.title;
            const options = {
                body: payload.notification.body,
                icon: payload.notification.icon,
            };
            new Notification(title, options);
        });
    </script>
    <script>
    window.Laravel = '{{!!json_encode(['
    csrfToken '=>csrf_token(),'
    user '=>['
    authenticated '=>auth()->check(),'
    id '=>auth()->check() ? auth()->user()->id : null,'
    name '=>auth()->check() ? auth()->user()-> name : null,]])!!}';
    </script>


    {{-- <script src="https://js.pusher.com/4.3/pusher.min.js"></script>

    <script>
      // Enable pusher logging - don't include this in production
      Pusher.logToConsole = true;

      var pusher = new Pusher('df4fad9e0f54a365c85c', {
          cluster: 'ap2',
          forceTLS: true
      });
    </script> --}}

    <script>
    initializeTwilio();
    </script>
    @if (Auth::id() == 3 || Auth::id() == 6 || Auth::id() == 23 || Auth::id() == 56)


    @endif

    {{-- <script src="{{ asset('js/pusher.chat.js') }}"></script>

    <script src="{{ asset('js/chat.js') }}"></script> --}}

    <style type="text/css">
    .back-to-top {
        position: fixed;
        bottom: 25px;
        right: 25px;
        display: none;

    }

    .dropdown.dots>a:after {
        display: none;
    }

    .dropdown.dots>a {
        line-height: 30px;
    }

    #navbarSupportedContent {
        display: flex !important;
    }

    .nav-item.dropdown.dots {
        min-width: 35px;
        padding-right: 15px;
    }

    @media(max-width:1350px) {


        .navbar-nav>li {
            min-width: 94px;
            padding-right: 15px;
        }
    }

    .navbar {
        padding: 0.1rem 0.8rem;
        border-bottom: 1px solid #ddd;
        /*margin-bottom: 8px !important;*/
        border-radius: 0px;
    }

    .navbar-brand {
        padding: 15px 4px;
        font-size: 20px;
        font-weight: 700;
        margin-right: 0;
    }

    @media(min-width:1700px) {
        #navs {
            padding-left: 40px;
        }
    }

    .navbar-nav>li {
        min-width: 40px;
        /*padding-right: 30px;*/
    }

    .time_doctor_project_section,
    .time_doctor_account_section{
        display: none;
    }

    /*.navbar-brand{*/
    /*    margin-right: 20px;*/
    /*}*/
    </style>
    @stack("styles")
</head>

<body>
    @stack('modals')
    <!-- sop-search Modal-->
    <div id="menu-sop-search-model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg"  role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Sop Search</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="d-flex" id="search-bar">
                                <input type="text" value="" name="search" id="menu_sop_search" class="form-control" placeholder="Search Here.." style="width: 30%;">
                                <a title="Sop Search" type="button" class="sop_search_menu btn btn-sm btn-image " style="padding: 10px"><span>
                                    <img src="{{asset('images/search.png')}}" alt="Search"></span></a>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered page-notes" style="font-size:13.8px;border:0px !important; table-layout:fixed" id="NameTable">
                                    <thead>
                                    <tr>
                                        <th width="2%">ID</th>
                                        <th width="10%">Name</th>
                                        <th width="14%">Content</th>
                                        <th width="5%">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody class="sop_search_result">
                                    @php
                                        $usersop = \App\Sop::all();
                                        $users = \App\User::all();
                                    @endphp
                                    @foreach ($usersop as $key => $value)
                                        <tr id="sid{{ $value->id }}" class="parent_tr" data-id="{{ $value->id }}">
                                            <td class="sop_table_id">{{ $value->id }}</td>
                                            <td class="expand-row-msg" data-name="name" data-id="{{$value->id}}">
                                                <span class="show-short-name-{{$value->id}}">{{ Str::limit($value->name, 17, '..')}}</span>
                                                <span style="word-break:break-all;" class="show-full-name-{{$value->id}} hidden">{{$value->name}}</span>
                                            </td>
                                            <td class="expand-row-msg Website-task " data-name="content" data-id="{{$value->id}}">
                                                <span class="show-short-content-{{$value->id}}">{{ Str::limit($value->content, 50, '..')}}</span>
                                                <span style="word-break:break-all;" class="show-full-content-{{$value->id}} hidden">{{$value->content}}</span>
                                            </td>
                                            <td class="p-1">
                                                <a href="javascript:;" data-id="{{ $value->id }}" class="menu_editor_edit btn btn-xs p-2" >
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="javascript:;" data-id="{{ $value->id }}" data-content="{{$value->content}}" class="menu_editor_copy btn btn-xs p-2" >
                                                    <i class="fa fa-copy"></i>
                                                </a>
                                            </td>
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
    </div>

    <!-- email-search Modal-->
    <div id="menu-email-search-model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg"  role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Email Search</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="d-flex" id="search-bar">
                                <input type="text" value="" name="search" id="menu_email_search" class="form-control" placeholder="Search Here.." style="width: 30%;">
                                <a title="Email Search" type="button" class="email_search_menu btn btn-sm btn-image " style="padding: 10px"><span>
                                    <img src="{{asset('images/search.png')}}" alt="Search"></span></a>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered page-notes" style="font-size:13.8px;border:0px !important;" id="emailNameTable">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Sender</th>
                                        <th>Receiver</th>
                                        <th>Subject</th>
                                        <th>Body</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody class="email_search_result">
                                        @php
                                            $userEmails = \App\Email::where('type', 'incoming')->orderBy('created_at', 'desc')->limit(5)->get();
                                        @endphp
                                        @foreach ($userEmails as $key => $userEmail)
                                            <tr>
                                                <td>{{ Carbon\Carbon::parse($userEmail->created_at)->format('d-m-Y H:i:s') }}</td>
                                                <td>{{ substr($userEmail->from, 0,  20) }} {{strlen($userEmail->from) > 20 ? '...' : '' }}</td>
                                                <td>{{ substr($userEmail->to, 0,  15) }} {{strlen($userEmail->to) > 10 ? '...' : '' }}</td>
                                                <td>{{ substr($userEmail->subject, 0,  15) }} {{strlen($userEmail->subject) > 10 ? '...' : '' }}</td>
                                                <td>{{ substr($userEmail->message, 0,  25) }} {{strlen($userEmail->message) > 20 ? '...' : '' }}</td>
                                                <td>
                                                    <a href="javascript:;" data-id="{{ $userEmail->id }}" data-content="{{$userEmail->message}}" class="menu_editor_copy btn btn-xs p-2" >
                                                        <i class="fa fa-copy"></i>
                                                </a></td>
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
    </div>

    <div id="menu-sopupdate" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Data</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form action="<?php echo route('updateName'); ?>" id="menu_sop_edit_form">
                        <input type="text" hidden name="id" id="sop_edit_id">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="hidden" class="form-control sop_old_name" name="sop_old_name" id="sop_old_name"
                                   value="">
                            <input type="text" class="form-control sopname" name="name" id="sop_edit_name">
                        </div>
                        <div class="form-group">
                            <label for="name">Category</label>
                            <input type="hidden" class="form-control sop_old_category" name="sop_old_category" id="sop_old_category"
                                   value="">
                            <input type="text" class="form-control sopcategory" name="category" id="sop_edit_category">
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
    <!-- sop-search Modal-->



    <div class="modal fade" id="instructionAlertModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Instruction Reminder</h3>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <a href="" id="instructionAlertUrl" class="btn btn-secondary mx-auto">OK</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="developerAlertModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Developer Task Reminder</h3>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <a href="" id="developerAlertUrl" class="btn btn-secondary mx-auto">OK</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="masterControlAlertModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Master Control Alert</h3>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <a href="" id="masterControlAlertUrl" class="btn btn-secondary mx-auto">OK</a>
                </div>
            </div>
        </div>
    </div>

    <div class="notifications-container">

        <div class="stack-container stacked" id="leads-notification"></div>

        <div class="stack-container stacked" id="orders-notification"></div>

        {{-- <div class="stack-container stacked" id="messages-notification"></div> --}}

        <div class="stack-container stacked" id="tasks-notification"></div>

    </div>

    <div id="app">

        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">

            <!--<div class="container container-wide">-->

            <div class="container-fluid pr-0">

                <a class="navbar-brand pl-0" href="{{ url('/task') }}">

                    {{ config('app.name', 'Laravel') }}

                </a>

                <button class="navbar-toggler" type="button" data-toggle="collapse"
                    data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">

                    <span class="navbar-toggler-icon"></span>

                </button>


                <div class="collapse navbar-collapse pr-0" id="navbarSupportedContent">

                    <!-- Left Side Of Navbar -->

                    <ul class="navbar-nav mr-auto">


                    </ul>


                    <!-- Right Side Of Navbar -->

                    <ul id="navs" class="navbar-nav ml-auto "
                        style="display:flex;text-align: center;flex-grow: 1;justify-content: space-between">

                        <!-- Authentication Links -->

                        @guest

                        <li class="nav-item">

                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>

                        </li>

                        {{--<li class="nav-item">

                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>

                        </li>--}}

                        @else

                        <?php

//getting count of unreach notification
$unread = 0;
if (!empty($notifications)) {
    foreach ($notifications as $notification) {
        if (!$notification->isread) {
            $unread++;
        }

    }
}

/* ?>
                        @include('partials.notifications')
                        <?php */?>
                        {{-- <li class="nav-item">
                            <a class="nav-link" href="{{ route('pushNotification.index') }}">New Notifications</a>
                        </li> --}}


                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button"
                                aria-haspopup="true" aria-expanded="false">Product <span class="caret"></span></a>
                            <ul class="dropdown-menu multi-level">
                                {{-- Sub Menu Product --}}


                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" data-toggle="dropdown" role="button"
                                        aria-haspopup="true" aria-expanded="false">Product Templates <span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('templates') }}">Templates</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('product.templates') }}">List</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('templates.type') }}">New List</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('product.index.image') }}">Processed
                                                Image</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('product.templates.log') }}">Product
                                                Template Log</a>
                                        </li>

                                    </ul>
                                </li>

                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Listing<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Selection<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item"
                                                    href="{{ route('productselection.index') }}">Selections Grid</a>
                                                @if(auth()->user()->checkPermission('productselection-create'))
                                                <a class="dropdown-item"
                                                    href="{{ route('productselection.create') }}">Add New</a>
                                                @endif
                                                <a class="dropdown-item" href="{{ url('/excel-importer') }}">Excel
                                                    Import </a>
                                                <a class="dropdown-item" href="{{ url('/excel-importer/mapping') }}">Add
                                                    Mapping For Master </a>
                                                <a class="dropdown-item"
                                                    href="{{ url('/excel-importer/tools-brand') }}">Add Mapping For
                                                    Excel</a>
                                                <a class="dropdown-item" href="{{ url('/excel-importer/log') }}">Excel
                                                    Importer Log</a>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a class="dropdown-item"
                                                href="{{ route('products.magentoConditionsCheck') }}">Mangento condition
                                                check</a>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a class="dropdown-item"
                                                href="{{ route('products.magentoPushStatus') }}">Magento push status</a>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Supervisor<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item"
                                                    href="{{ route('productsupervisor.index') }}">Supervisor Grid</a>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Image Cropper<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item"
                                                    href="{{ route('productimagecropper.index') }}">Image Cropper
                                                    Grid</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('product.crop.approved') }}">Approved
                                                    Crop grid</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('product.auto.cropped') }}">Crop
                                                    Approval Grid</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('product.crop.issue.summary') }}">Crop
                                                    Issue Summary</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('product.rejected.auto.cropped') }}">Crop-Rejected
                                                    Grid</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('product.order.cropped.images') }}">Crop-Sequencer</a>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Images<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item"
                                                    href="{{ route('google.search.product') }}">Google Image Search</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('manual.image.upload') }}">Manual Image Upload</a>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Attribute<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                @if(auth()->user()->checkPermission('productlister-list'))
                                                <a class="dropdown-item"
                                                    href="{{ route('products.listing') }}?cropped=on">Attribute edit
                                                    page</a>
                                                @endif
                                                <a class="dropdown-item"
                                                    href="{{ route('products.push.conditions') }}">Magento product push
                                                    conditions</a>
                                                <a class="dropdown-item"
                                                    href="{{ action('\App\Http\Controllers\ProductController@approvedListing') }}?cropped=on">Approved
                                                    listing</a>
                                                <a class="dropdown-item"
                                                    href="{{ action('\App\Http\Controllers\ProductController@approvedListing') }}?cropped=on&status_id=2">Listings
                                                    awaiting scraping</a>
                                                <a class="dropdown-item"
                                                    href="{{ action('\App\Http\Controllers\ProductController@approvedListing') }}?cropped=on&status_id=13">Listings
                                                    unable to scrape</a>
                                                <a class="dropdown-item"
                                                    href="{{ action('\App\Http\Controllers\ProductController@showRejectedListedProducts') }}">Rejected
                                                    Listings</a>
                                                <a class="dropdown-item"
                                                    href="{{ action('\App\Http\Controllers\AttributeReplacementController@index') }}">Attribute
                                                    Replacement</a>
                                                <a class="dropdown-item"
                                                    href="{{ action('\App\Http\Controllers\UnknownAttributeProductController@index') }}">Incorrect Attributes</a>
                                                <a class="dropdown-item"
                                                    href="{{ action('\App\Http\Controllers\CropRejectedController@index') }}">Crop Rejected<br>Final Approval Images</a>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Approver<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item"
                                                    href="{{ route('productapprover.index') }}">Approver Grid</a>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>In Stock<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item"
                                                    href="{{ route('productinventory.instock') }}">In Stock</a>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>In Delivered<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item"
                                                    href="{{ route('productinventory.indelivered') }}">In Delivered</a>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Inventory<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item"
                                                    href="{{ route('productinventory.index') }}">Inventory Grid</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('productinventory.list') }}">Inventory List</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('productinventory.inventory-list-new') }}">New
                                                    Product Inventory List</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('productinventory.inventory-list') }}">Inventory
                                                    Data</a>
                                                <a class="dropdown-item" href="{{ route('product-inventory.new') }}">New
                                                    Inventory List</a>
                                                <a class="dropdown-item" href="{{ route('productinventory.out-of-stock') }}">Sold Out Products</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('listing.history.index') }}">Product Listing
                                                    history</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('product.category.index.list') }}">Product
                                                    Category</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('product.color.index.list') }}">Product Color
                                                    history</a>
                                            </ul>
                                        </li>
                                        @if(auth()->user()->isAdmin())

                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Quick Sell<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ route('quicksell.index') }}">Quick
                                                    Sell</a>

                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a class="dropdown-item" href="/drafted-products">Quick Sell List</a>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a class="dropdown-item" href="{{ route('stock.index') }}">Inward Stock</a>
                                        </li>
                                        @endif
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Scraping<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">

                                                <a class="dropdown-item"
                                                    href="{{ url('scrap/statistics') }}">Statistics</a>
                                                <a class="dropdown-item"
                                                    href="{{ url('scrap/statistics/server-history') }}">Server
                                                    History</a>
                                                <a class="dropdown-item"
                                                    href="{{ url('scrap/generic-scraper') }}">Generic Supplier
                                                    Scraper</a>
                                                <a class="dropdown-item"
                                                    href="{{ action('\App\Http\Controllers\CategoryController@brandMinMaxPricing') }}">Min/Max
                                                    Pricing</a>
                                                <a class="dropdown-item" href="{{ route('supplier.count') }}">Supplier
                                                    Category Count</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('supplier.brand.count') }}">Supplier Brand Count</a>
                                                <a class="dropdown-item"
                                                    href="{{ url('price-comparison-scraper') }}">Price comparison</a>
                                                <a class="dropdown-item"
                                                    href="{{ url('scrap/servers/statistics') }}">Scrap server
                                                    statistics</a>

                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>SKU<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item"
                                                    href="{{ action('\App\Http\Controllers\Logging\LogScraperController@logSKU') }}">SKU
                                                    log</a>
                                                <a class="dropdown-item"
                                                    href="{{ action('\App\Http\Controllers\Logging\LogScraperController@logSKUErrors') }}">SKU
                                                    warnings/errors</a>
                                                <a class="dropdown-item" href="{{ route('sku-format.index') }}">SKU
                                                    Format</a>
                                                <a class="dropdown-item" href="{{ route('sku.color-codes') }}">SKU Color
                                                    Codes</a>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('google.search.product') }}">Search
                                                Products by Text</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ route('google.search.multiple') }}">Multiple products by
                                                Text</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('google.search.image') }}">Search
                                                Products by Image</a>
                                        </li>

                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Purchase<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('purchase.index') }}">Purchase</a>
                                            <a class="dropdown-item" href="{{ route('purchase.grid') }}">Purchase
                                                Grid</a>
                                            <a class="dropdown-item" href="{{ route('purchase.calendar') }}">Purchase
                                                Calendar</a>
                                            <a class="dropdown-item"
                                                href="{{ route('purchase.grid', 'canceled-refunded') }}">Cancel/Refund
                                                Grid</a>
                                            <a class="dropdown-item"
                                                href="{{ route('purchase.grid', 'ordered') }}">Ordered Grid</a>
                                            <a class="dropdown-item"
                                                href="{{ route('purchase.grid', 'delivered') }}">Delivered Grid</a>
                                            <a class="dropdown-item"
                                                href="{{ route('purchase.grid', 'non_ordered') }}">Non Ordered Grid</a>
                                            <a class="dropdown-item"
                                                href="{{ route('purchaseproductorders.list') }}">Purchase Product
                                                Orders</a>
                                            <a class="dropdown-item"
                                                href="{{ route('list.magento.product-push-information') }}">Product
                                                information update </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Supplier<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('supplier.index') }}">Supplier
                                                List</a></a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ route('supplier.product.history') }}">Supplier Product
                                                History</a></a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ route('supplier/category/permission') }}">Supplier Category
                                                <br> Permission</a></a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ route('supplier.discount.files') }}">Supplier Discount
                                                Files</a></a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Scraping<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
											<a class="dropdown-item"
												href="{{ action('\App\Http\Controllers\CodeShortcutController@index') }}">
												Node Code Shortcut</a>
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\ProductController@productStats') }}">Product
                                                Statistics</a>
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\ProductController@showAutoRejectedProducts') }}">Auto
                                                Reject Statistics</a>
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\ListingPaymentsController@index') }}">Product Listing
                                                Payments</a>
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\ScrapStatisticsController@index') }}">Scrap
                                                Statistics</a>
                                            <a class="dropdown-item" href="{{ route('statistics.quick') }}">Quick Scrap
                                                Statistics</a>
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\ScrapController@scrapedUrls') }}">Scrap Urls</a>
                                            <a class="dropdown-item" href="{{ route('scrap.activity') }}">Scrap
                                                activity</a>
                                            <a class="dropdown-item"
                                                href="{{ route('scrap.scrap_server_status') }}">Scrapper Server
                                                Status</a>
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\ScrapController@showProductStat') }}">Products
                                                Scrapped</a>
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\SalesItemController@index') }}">Sale Items</a>
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\DesignerController@index') }}">Designer List</a>
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\GmailDataController@index') }}">Gmail Inbox</a>
                                            <a class="dropdown-item" href="{{ action('\App\Http\Controllers\ScrapController@index') }}">Google
                                                Images</a>
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\GoogleSearchImageController@searchImageList') }}">Image
                                                Search By Google</a>
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\SocialTagsController@index') }}">Social Tags</a>
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\DubbizleController@index') }}">Dubzzle</a>
                                            <a class="dropdown-item" href="{{ route('log-scraper.index') }}">Scraper
                                                log</a>
                                            <a class="dropdown-item" href="{{ route('log-scraper.api') }}">Scraper Api
                                                log</a>
                                            <a class="dropdown-item" href="{{ route('scrap-brand') }}">Scrap Brand</a>
                                            <a class="dropdown-item" href="{{ url('scrap/log/list') }}">Scrapper Task
                                                Logs</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Crop Reference<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\CroppedImageReferenceController@grid') }}">Crop
                                                Reference Grid</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Magento<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\Logging\LogListMagentoController@index') }}">Log List
                                                Magento</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="/magento/status">Order Status Mapping</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="/languages">Language</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ route('logging.magento.product_push_journey') }}">Product Push
                                                Journey</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Logs<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\ProductController@productScrapLog') }}">Status Logs</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\ScrapLogsController@index') }}">Scrap Logs</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\LaravelLogController@index') }}">Laravel Log</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('api-log-list') }}">Laravel API
                                                Log</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\LaravelLogController@liveLogs') }}">Live Laravel
                                                Log</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\LaravelLogController@scraperLiveLogs') }}">Live Scraper
                                                Log</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ route('social-webhook-log.index') }}">Social Webhook Log</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('website.file.list.log') }}">Magento
                                                Logs</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('website.log.view') }}">Magento Logs
                                                View</a>
                                        </li>

                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item"
                                        href="{{action('\App\Http\Controllers\ProductController@productDescription')}}">Product
                                        Description</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{route('products.product-translation')}}">Product
                                        translate</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{route('products.product-assign')}}">Assign
                                        Products</a>
                                </li>

                                <li class="nav-item dropdown">
                                    <a class="dropdown-item"
                                        href="{{route('products.listing.approved.images')}}/images">Final Apporval
                                        Images</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{route('customer.charity')}}">Charity Products</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button"
                                aria-haspopup="true" aria-expanded="false">CRM <span class="caret"></span></a>
                            <ul class="dropdown-menu multi-level">
                                {{-- Sub Menu Product --}}
                                <li class="nav-item">
                                    <a class="dropdown-item" target="_blank" href="{{ route('logs.index') }}">Image
                                        Logs</a>
                                </li>
                                <li class="nav-item">
                                    <a class="dropdown-item" target="_blank"
                                        href="{{ route('order.call-management') }}">Call Management</a>
                                </li>
                                <li class="nav-item">
                                    <a class="dropdown-item" target="_blank" href="/web-message">Communication</a>
                                    <a class="dropdown-item" href="{{route('translation.list')}}">Translations</a>
                                    <a class="dropdown-item" href="{{route('translation.log')}}">Translations Logs</a>
                                    <a class="dropdown-item" href="{{route('pushfcmnotification.list')}}">FCM
                                        Notifications</a>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" data-toggle="dropdown" role="button"
                                        aria-haspopup="true" aria-expanded="false">Scheduled Flows<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('flow.index') }}">Flows</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ route('flow.schedule-emails') }}">Scheduled Emails</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ route('flow.schedule-messages') }}">Scheduled Messages</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">Customers<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ url('/erp-customer') }}">Customers -
                                                NEW</a>
                                            <a class="dropdown-item"
                                                href="{{ route('customer.index') }}?type=unread">Customers - unread</a>
                                            <a class="dropdown-item"
                                                href="{{ route('customer.index') }}?type=unapproved">Customers -
                                                unapproved</a>
                                            <a class="dropdown-item"
                                                href="{{ route('customer.index') }}?type=Refund+to+be+processed">Customers
                                                - refund</a>
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\VisitorController@index') }}">Livechat Visitor Logs</a>
                                            <a class="dropdown-item" href="{{ url('livechat/setting') }}">Livechat
                                                Setting</a>
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\ProductController@attachedImageGrid') }}">Attach
                                                Images</a>
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\ProductController@suggestedProducts') }}">Sent
                                                Images</a>
                                            <a class="dropdown-item" href="{{ route('chat.dndList') }}">DND Manage</a>
                                            <a class="dropdown-item" href="{{ url('customer/credit') }}">Customer
                                                Credit</a>
                                            <a class="dropdown-item" href="{{ url('chatbot-message-log') }}">Chatbot
                                                Message Log</a>
                                            <a class="dropdown-item" href="{{ url('watson-journey') }}">Watson
                                                Journey</a>
                                            <a class="dropdown-item" href="{{ url('customers/accounts') }}">Store
                                                website customer</a>
                                            <a class="dropdown-item"
                                                href="{{ route('product.customer-reviews') }}">Customer Reviews</a>
                                            <a class="dropdown-item"
                                                href="{{ route('customer.priority.points') }}">Customer Priority</a>
                                            <a class="dropdown-item"
                                                href="{{ route('customer.get.priority.range.points') }}">Customer Range
                                                Priority Point</a>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Cold Leads<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item"
                                                    href="{{ action('\App\Http\Controllers\ColdLeadsController@index') }}?via=hashtags">Via
                                                    Hashtags</a>
                                                <a class="dropdown-item"
                                                    href="{{ action('\App\Http\Controllers\ColdLeadsController@showImportedColdLeads') }}">Imported
                                                    Cold leads</a>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>

                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Instructions<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Instructions<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item"
                                                    href="{{ route('instruction.index') }}">Instructions</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('instruction.list') }}">Instructions List</a>
                                                <a class="dropdown-item"
                                                    href="{{ action('\App\Http\Controllers\KeywordInstructionController@index') }}">Instruction
                                                    Keyword Instructions</a>
                                                <a class="dropdown-item" href="/instruction/quick-instruction">Quick
                                                    instructions</a>
                                                <a class="dropdown-item"
                                                    href="/instruction/quick-instruction?type=price">Quick instructions
                                                    (price)</a>
                                                <a class="dropdown-item"
                                                    href="/instruction/quick-instruction?type=image">Quick instructions
                                                    (attach)</a>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Referral System<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Referral Programs<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item"
                                                    href="{{ route('referralprograms.list') }}">List Referral
                                                    Programs</a>
                                                <a class="dropdown-item" href="{{ route('referralprograms.add') }}">Add
                                                    Referral Programs</a>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Friend Referral<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ route('referfriend.list') }}">List
                                                    Friend Referral</a>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Leads<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('leads.index') }}">Leads</a>
                                        <a class="dropdown-item" href="{{ action('\App\Http\Controllers\LeadsController@erpLeads') }}">Leads
                                            (new)</a>
                                        <a class="dropdown-item"
                                            href="{{ action('\App\Http\Controllers\LeadsController@erpLeadsHistory') }}">Leads History</a>
                                        <a class="dropdown-item" href="{{ route('lead-queue.approve') }}">Leads Queue
                                            Approval</a>
                                        <a class="dropdown-item" href="{{ route('lead-queue.index') }}">Leads Queue
                                            (Approved)</a>
                                        <a class="dropdown-item" href="{{ route('leads.create') }}">Add new lead</a>
                                        <a class="dropdown-item" href="{{ route('leads.image.grid') }}">Leads Image
                                            grid</a>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Refunds<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('refund.index') }}">Refunds</a>
                                        </li>
                                    </ul>

                                </li>
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{ route('quick-replies') }}">Quick Replies</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{ route('quick.customer.index') }}">Quick
                                        Customer</a>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Orders<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Orders<span class="caret"></span></a>

                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ route('order.index') }}">Orders</a>
                                                <a class="dropdown-item" href="{{ route('order.create') }}">Add
                                                    Order</a>
                                                <a class="dropdown-item" href="{{ route('order.products') }}">Order
                                                    Product List</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('return-exchange.list') }}">Return-Exchange</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('return-exchange.status') }}">Return-Exchange
                                                    Status</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('order.status.messages') }}">Order Status
                                                    Messages</a>
                                                <a class="dropdown-item" href="{{ route('lead-order.index') }}">Lead
                                                    order</a>
                                                <a class="dropdown-item" href="{{ url('order/charity-order') }}">Charity
                                                    order</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('order.get.email.send.journey.logs') }}">Order email
                                                    journey</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('order.get.order.journey') }}">Order
                                                    journey</a>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class=""
                                                href="{{ action('\App\Http\Controllers\OrderController@viewAllInvoices') }}" role="button"
                                                aria-haspopup="true" aria-expanded="false"
                                                v-pre>Invoices<span></span></a>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a class="" href="{{ route('store-website.all.status') }}" role="button"
                                                aria-haspopup="true" aria-expanded="false">Magento order status<span></span></a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Customer<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('complaint.index') }}">Customer
                                                Complaints</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                    <a href="{{ route('livechat.get.chats') }}">Live Chat</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a href="{{ route('livechat.get.tickets') }}">Live Chat Tickets</a>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Missed<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('order.missed-calls') }}">Missed
                                                Calls List</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Call<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('order.calls-history') }}">Call
                                                history</a>

                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Private<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('stock.private.viewing') }}">Private
                                                Viewing</a>

                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Bulk Customer Replies<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item"
                                            href="{{ action('\App\Http\Controllers\BulkCustomerRepliesController@index') }}">Bulk Messages</a>
                                        <a class="dropdown-item"
                                            href="{{ action('\App\Http\Controllers\CustomerCategoryController@index') }}">Categories</a>
                                        <a class="dropdown-item"
                                            href="{{ action('\App\Http\Controllers\KeywordToCategoryController@index') }}">Keywords</a>

                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Delivery<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ route('deliveryapproval.index') }}">Delivery Approvals</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Broadcast<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('broadcast.index') }}">Broadcast
                                                Grid</a>
                                            <a class="dropdown-item" href="{{ route('broadcast.images') }}">Broadcast
                                                Images</a>
                                            <a class="dropdown-item" href="{{ route('broadcast.calendar') }}">Broadcast
                                                Calender</a>
                                            <a class="dropdown-item" href="/marketing/instagram-broadcast">Instagram
                                                Broadcast</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Marketing<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ route('whatsapp.config.index') }}">WhatsApp Config</a>
                                            <a class="dropdown-item" href="/marketing/accounts/instagram">Instagram
                                                Config</a>
                                            <a class="dropdown-item" href="/marketing/accounts/facebook">Facebook
                                                Config</a>
                                            <a class="dropdown-item" href="{{ route('platforms.index') }}">Platforms</a>
                                            <a class="dropdown-item"
                                                href="{{ route('broadcasts.index') }}">BroadCast</a>
                                            <a class="dropdown-item" href="/marketing/services">Mailing Service</a>
                                            <a class="dropdown-item" href="{{ route('mailingList') }}">Mailinglist</a>
                                            <a class="dropdown-item" href="{{ route('mailingList.log') }}">Mailinglist
                                                Log</a>
                                            <a class="dropdown-item"
                                                href="{{ route('mailingList.flowlog') }}">Mailinglist Flow Log</a>
                                            <a class="dropdown-item"
                                                href="{{ route('mailingList.customerlog') }}">Customer Mailinglist
                                                Log</a>
                                            <a class="dropdown-item"
                                                href="{{ route('mailingList-template') }}">Mailinglist Templates</a>
                                            <a class="dropdown-item"
                                                href="{{ route('mailingList-emails') }}">Mailinglist Emails</a>
                                            <a class="dropdown-item" href="/mail-templates/mailables">Mailables</a>
                                            <a class="dropdown-item" href="{{ route('emailleads') }}">Email Leads</a>
                                            <a class="dropdown-item" href="{{ url('twillio')}}">Messages</a>
                                            <a class="dropdown-item" href="{{ url('email-data-extraction')}}">Auto Email
                                                Records</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Checkout<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('coupons.index') }}">Coupons</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ route('negative.coupon.response') }}">Negative Coupons
                                                Response</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a id="navbarDropdown" class="" href="{{ route('keywordassign.index') }}"
                                        role="button">Keyword Assign</a>
                                </li>
                                {{-- START - Purpose : Add new Menu Keyword Response Logs - DEVTASK-4233 --}}
                                <li class="nav-item">
                                    <a id="navbarDropdown" class="" href="{{ route('keywordreponse.logs') }}"
                                        role="button">Keyword Response Logs</a>
                                </li>
                                {{-- END - DEVTASK-4233 --}}
                                <li class="nav-item">
                                    <a id="navbarDropdown" class="" href="{{ route('purchase-product.index') }}"
                                        role="button">Purchase</a>
                                </li>
                                <li class="nav-item">
                                    <a id="navbarDropdown" class="" href="{{ route('status-mapping.index') }}"
                                        role="button">Status Mappings</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button"
                                aria-haspopup="true" aria-expanded="false">Vendor <span class="caret"></span></a>
                            <ul class="dropdown-menu multi-level">
                                {{-- Sub Menu Product --}}
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{ route('vendors.index') }}">Vendor Info</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{ route('vendor-category.index') }}">Vendor
                                        Category</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{ route('vendors.product.index') }}">Product
                                        Info</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{ route('developer.vendor.form') }}">Vendor Form</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{ route('developer.supplier.form') }}">Supplier
                                        Form</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{ route('vendor-category.permission') }}">Vendor
                                        Category Permission</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{ route('vendor.cv.index') }}">Vendors CV</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{ route('vendor.meeting.list') }}">Vendor
                                        Meeting List</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button"
                                aria-haspopup="true" aria-expanded="false">Users <span class="caret"></span></a>
                            <ul class="dropdown-menu multi-level">
                                {{-- Sub Menu Product --}}
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>User Management<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('users.index') }}">List Users</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('users.create') }}">Add New</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('userlogs.index') }}">User Logs</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('users.login.index') }}">User
                                                Logins</a>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Roles<span class="caret"></span></a>

                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">

                                                <a class="dropdown-item" href="{{ route('roles.index') }}">List
                                                    Roles</a>
                                                <a class="dropdown-item" href="{{ route('roles.create') }}">Add New</a>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Permissions<span class="caret"></span></a>

                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ route('permissions.index') }}">List
                                                    Permissions</a>
                                                <a class="dropdown-item" href="{{ route('permissions.create') }}">Add
                                                    New</a>
                                                <a class="dropdown-item" href="{{ route('permissions.users') }}">User
                                                    Permission List</a>
                                                <a class="dropdown-item" href="{{ route('users.login.ips') }}">User
                                                    Login IP(s)</a>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('user-management.index') }}">New
                                                Management</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ url('api/documentation') }}">API
                                                Documentation</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{route('user.get-feedback-table-data')}}">User Feedback</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{route('user-management.user-schedules.index')}}">User
                                                Schedules</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{route('user-management.user-delivered.index')}}">User
                                                Delivered</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Activity<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('activity') }}">View</a>
                                        </li>


                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('graph_user') }}">User Graph</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('benchmark.create') }}">Add
                                                Benchmark</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\ProductController@showListigByUsers') }}">User Product
                                                Assignment</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a class="dropdown-item" href="/calendar">Calendar</a>
                                </li>
                                <li class="nav-item">
                                    <a class="dropdown-item" href="/event">Event</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button"
                                aria-haspopup="true" aria-expanded="false">Platforms <span class="caret"></span></a>
                            <ul class="dropdown-menu multi-level">
                                {{-- Sub Menu Product --}}
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{ action('\App\Http\Controllers\PreAccountController@index') }}">Other
                                        Email Accounts
                                    </a>
                                </li>
                                @if(auth()->user()->isAdmin())
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Instagram<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\InstagramPostsController@grid') }}">Instagram Posts
                                                (Grid)</a>
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\InstagramPostsController@index') }}">Instagram
                                                Posts</a>
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\HashtagController@influencer') }}">Influencers</a>
                                            <a class="dropdown-item" href="/instagram/hashtag/comments/">Hashtag
                                                Comments</a>
                                            <a class="dropdown-item" href="/instagram/direct-message">Direct Message</a>
                                            <a class="dropdown-item" href="/instagram/post">Posts</a>
                                            <a class="dropdown-item" href="/instagram/post/create">Create Post</a>
                                            <a class="dropdown-item" href="/instagram/direct-message">Media</a>
                                            <a class="dropdown-item" href="/instagram/users">Get User Post</a>
                                        </li>

                                        <hr />

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\InstagramController@index') }}">Dashboard</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\InstagramController@accounts') }}">Accounts</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ url('instagram/hashtag') }}">Hashtags</a>
                                        </li>


                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\HashtagController@showGrid', 'sololuxury') }}">Hashtag
                                                monitoring & manual Commenting</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\HashtagController@showNotification') }}">Recent
                                                Comments (Notifications)</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\InstagramController@showPosts') }}">All Posts</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\TargetLocationController@index') }}">Target
                                                Location</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\KeywordsController@index') }}">Keywords For
                                                comments</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\HashtagController@showProcessedComments') }}">Processed
                                                Comments</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\CompetitorPageController@index') }}?via=instagram">All
                                                Competitors On Instagram</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\InstagramAutoCommentsController@index') }}">Quick
                                                Reply</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\ReplyController@replyList') }}">Quick Reply List</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\UsersAutoCommentHistoriesController@index') }}">Bulk
                                                Commenting</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\AutoCommentHistoryController@index') }}">Auto Comments
                                                Statistics</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\InstagramProfileController@index') }}">Customers
                                                followers</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\InstagramProfileController@edit', 1) }}">#tags Used by
                                                top customers.</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\InstagramController@accounts') }}">Accounts</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('social.ads.schedules')}}">Ad
                                                Schedules</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('social.ad.create')}}">Create New
                                                Ad</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('social.ad.adset.create')}}">Create
                                                New Adset</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{route('social.ad.campaign.create')}}">Create New Campaign </a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('social.get-post.page')}}">See
                                                Posts</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('social.post.page')}}">Post to
                                                Page</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('social.report')}}">Ad Reports</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('social.adCreative.report')}}">Ad
                                                Creative Reports</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('complaint.index') }}">Customer
                                                Complaints</a>
                                        </li>

                                    </ul>
                                </li>
                                @endif

                                @if(auth()->user()->isAdmin())
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>LiveChat, Inc.<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\VisitorController@index') }}">LiveChat Visitor Log</a>
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\LiveChatController@setting') }}">LiveChat Settings</a>
                                        </li>
                                    </ul>
                                </li>
                                @endif

                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Facebook<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\InstagramController@showImagesToBePosted') }}">Create
                                                Post</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\InstagramController@showSchedules') }}">Schedule A
                                                Post</a>
                                        </li>

                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Facebook<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item"
                                                    href="{{ action('\App\Http\Controllers\FacebookController@index') }}">Facebook Post</a>
                                            </ul>
                                        </li>

                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Facebook Groups<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item"
                                                    href="{{ action('\App\Http\Controllers\FacebookController@show', 'group') }}">Facebook
                                                    Groups</a>
                                            </ul>
                                        </li>

                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Facebook Brand Fan Page<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item"
                                                    href="{{ action('\App\Http\Controllers\FacebookController@show', 'brand') }}">Facebook
                                                    Brand Fan Page</a>
                                            </ul>
                                        </li>

                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>All Adds<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{route('social.get-post.page')}}">See
                                                    Posts</a>
                                                <a class="dropdown-item" href="{{route('social.post.page')}}">Post On
                                                    pgae</a>
                                                <a class="dropdown-item" href="{{route('social.report')}}">Ad report</a>
                                                <a class="dropdown-item" href="{{route('social.adCreative.report')}}">Ad
                                                    Creative Reports</a>
                                                <a class="dropdown-item"
                                                    href="{{route('social.ad.campaign.create')}}">Create New
                                                    Campaign</a>
                                                <a class="dropdown-item"
                                                    href="{{route('social.ad.adset.create')}}">Create New adset</a>
                                                <a class="dropdown-item" href="{{route('social.ad.create')}}">Create New
                                                    ad</a>
                                                <a class="dropdown-item" href="{{route('social.ads.schedules')}}">Ad
                                                    Schedule</a>
                                            </ul>

                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\FacebookPostController@index') }}">Facebook Posts</a>
                                        </li>

                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Sitejabber<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\SitejabberQAController@accounts') }}">Account</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ action('\App\Http\Controllers\QuickReplyController@index') }}">Quick Reply</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Pinterest<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ route('pinterest.accounts') }}">Accounts</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Images<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('image.grid') }}">Lifestyle Image
                                                Grid</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('image.grid.new') }}">Lifestyle
                                                Image Grid New</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('image.grid.approved') }}">Final
                                                Images</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ route('image.grid.final.approval') }}">Final Approval</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{ route('review.index') }}">Reviews
                                    </a>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Bloggers<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('blogger.index')}}">Bloggers</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="seoMenu" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre="">SEO<span class="caret">
                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="seoMenu">
                                                <li class="nav-item dropdown dropdown-submenu">
                                                    @if(auth()->user()->hasRole(['Admin', 'user', 'Seo Head']))
                                                        <a class="dropdown-item" href="{{ route('seo.content.index') }}">Content</a>
                                                    @endif
                                                    <a class="dropdown-item" href="{{ route('seo.company.index') }}">Company</a>
                                                    <a class="dropdown-item"
                                                        href="{{ action('\App\Http\Controllers\BackLinkController@displayBackLinkDetails') }}">Back
                                                        Link Details</a>
                                                    <a class="dropdown-item"
                                                        href="{{ action('\App\Http\Controllers\BrokenLinkCheckerController@displayBrokenLinkDetails') }}">Broken
                                                        Link Details</a>
                                                    <a class="dropdown-item"
                                                        href="{{ action('\App\Http\Controllers\AnalyticsController@showData') }}">New Google
                                                        Analytics</a>
                                                    <a class="dropdown-item"
                                                        href="{{ action('\App\Http\Controllers\AnalyticsController@customerBehaviourByPage') }}">Customer
                                                        Behaviour By Page</a>
                                                    <a class="dropdown-item"
                                                        href="{{ action('\App\Http\Controllers\SERankingController@getSites') }}">SE
                                                        Ranking</a>
                                                    <a class="dropdown-item"
                                                        href="{{ action('\App\Http\Controllers\ArticleController@index') }}">Article
                                                        Approval</a>
                                                    <a class="dropdown-item"
                                                        href="{{ action('\App\Http\Controllers\ProductController@getSupplierScrappingInfo') }}">Supplier
                                                        Scrapping Info</a>
                                                    <a class="dropdown-item"
                                                        href="{{ action('\App\Http\Controllers\NewDevTaskController@index') }}">New Dev Task
                                                        Planner</a>
                                                    <a class="dropdown-item" href="{{ route('seo-tool') }}">Semrush
                                                        details</a>
                                                </li>
                                            </ul>
                                </li>

                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Chatbot<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <!-- <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('chatbot.keyword.list')}}">Entities</a>
                                        </li> -->
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('chatbot.question.list')}}">Intents /
                                                Entities</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('chatbot.dialog.list')}}">Dialog</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('chatbot.dialog-grid.list')}}">Dialog
                                                Grid</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('chatbot.mostUsedWords')}}">Most used
                                                words</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('chatbot.mostUsedPhrases')}}">Most
                                                used phrases</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{route('chatbot.mostUsedPhrasesDeleted')}}">Most used phrases
                                                Updated</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{route('chatbot.analytics.list')}}">Analytics</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{route('chatbot.messages.list')}}">Messages</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('chatbot.messages.logs')}}">Logs</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('chatbot-simulator')}}">Simulator</a>
                                        </li>
                                    </ul>
                                </li>


                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>App Store<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{route('appconnect.app-users')}}">Usage</a>
                                            <a class="dropdown-item"
                                                href="{{route('appconnect.app-sales')}}">Sales</a>
                                                 <a class="dropdown-item"
                                                href="{{route('appconnect.app-sub')}}">Subscription</a>
                                                 <a class="dropdown-item"
                                                href="{{route('appconnect.app-ads')}}">Ads</a>
                                                 <a class="dropdown-item"
                                                href="{{route('appconnect.app-rate')}}">Ratings</a>
                                                 <a class="dropdown-item"
                                                href="{{route('appconnect.app-pay')}}">Payments</a>
                                        </li>
                                    </ul>
                                </li>


                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Google<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Search<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{route('google.search.keyword')}}">Keywords</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{route('google.search.results')}}">Search Results</a>
                                                </li>

                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Affiliate<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{route('affiliates.list')}}">Manual
                                                        Affiliates</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{route('google.affiliate.keyword')}}">Keywords</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{route('google.affiliate.results')}}">Search Results</a>
                                                </li>
                                            </ul>
                                        </li>
                                          <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Developer API<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{route('google.developer-api.crash')}}">Crash Report</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{route('google.developer-api.anr')}}">ANR Report</a>
                                                </li>
                                                 <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{route('google.developer-api.logs')}}">Logs</a>
                                                </li>

                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{ route('google-drive.new') }}">Google Drive</a>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Google Web Master<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('googlewebmaster.index')}}">Sites</a>
                                        </li>

                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Bing Web Master<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('bingwebmaster.index')}}">Sites</a>
                                        </li>

                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a class="dropdown-item" href="{{ route('googleadsaccount.index') }}">Google AdWords</a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown" style="width: fit-content !important;">
                                        <li  class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('googleadsaccount.index')}}">Google AdWords Account</a>
                                        </li>
                                        <li  class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('googlecampaigns.campaignslist')}}">Google Campaign</a>
                                        </li>
                                        <li  class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('googleadsaccount.adsgroupslist')}}">Google Ads groups</a>
                                        </li>
                                        <li  class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('googlecampaigns.adslist')}}">Google Ads</a>
                                        </li>
                                        <li  class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('googlecampaigns.displayads')}}">Google Responsive Display Ads</a>
                                        </li>
                                        <li  class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('googleadsaccount.appadlist')}}">Google App Ads</a>
                                        </li>
                                        <li  class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('googleadreport.index')}}">Google Ads Report</a>
                                        </li>
                                        <li  class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('googleadslogs.index')}}">Google Ads Logs</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{ route('digital-marketing.index') }}">Social
                                        Digital Marketing
                                    </a>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Plesk<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('plesk.domains')}}">Domains</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{ route('logging.flow.log') }}">Flow Log
                                    </a>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                       aria-haspopup="true" aria-expanded="false" v-pre>Affiliate Marketing<span
                                                class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('affiliate-marketing.providers')}}">Providers</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('affiliate-marketing.providerAccounts')}}">Providers Accounts</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button"
                                aria-haspopup="true" aria-expanded="false">Social <span class="caret"></span></a>
                            <ul class="dropdown-menu multi-level">
                                {{-- Sub Menu Product --}}
                                <li class="nav-item dropdown">
                                    <a href="{{route('social.config.index')}}">Social Config</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a href="{{route('social.post.grid')}}">Social Posts Grid</a>
                                </li>
                                @if(auth()->user()->isAdmin())
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Instagram<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="/instagram/post">Posts</a>
                                            <a class="dropdown-item" href="/instagram/post/create">Create Post</a>
                                            <a class="dropdown-item" href="/instagram/direct-message">Media</a>
                                            <a class="dropdown-item" href="/instagram/direct">Direct</a>
                                        </li>
                                    </ul>
                                </li>

                                 <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Blog<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">

                                            <a class="dropdown-item" href="/blog/list">Blog</a>
                                             <a class="dropdown-item" href="/blog/history/list">View History</a>
                                        </li>
                                    </ul>
                                </li>


                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre>Youtube<span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="/youtube/add-chanel">Create Chanel</a>


                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a href="{{ route('social.direct-message') }}">Direct Messsage</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a href="{{route('social.config.index')}}">Social Config</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a href="{{route('social.adcreative.index')}}">Social Ad Creative</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a href="{{route('social.ad.index')}}">Social Ads</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a href="{{ route('chatgpt.index') }}">Chat GPT</a>
                                </li>
                                @endif
                            </ul>
                        </li>



                    </ul>
                    <div>
                        <div id="nav-dotes" class="nav-item dropdown dots mr-3 ml-3">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button"
                                aria-haspopup="true" aria-expanded="false">
                                <svg width="16" height="18" viewBox="0 0 16 4" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M4 2C4 0.9 3.1 -1.35505e-07 2 -8.74228e-08C0.9 -3.93402e-08 -1.35505e-07 0.9 -8.74228e-08 2C-3.93402e-08 3.1 0.9 4 2 4C3.1 4 4 3.1 4 2ZM6 2C6 3.1 6.9 4 8 4C9.1 4 10 3.1 10 2C10 0.9 9.1 -3.97774e-07 8 -3.49691e-07C6.9 -3.01609e-07 6 0.9 6 2ZM12 2C12 3.1 12.9 4 14 4C15.1 4 16 3.1 16 2C16 0.899999 15.1 -6.60042e-07 14 -6.11959e-07C12.9 -5.63877e-07 12 0.9 12 2Z"
                                        fill="#757575"></path>
                                </svg>
                            </a>

                            <ul id="nav_dots" class="dropdown-menu multi-level ">


                                @if(auth()->user()->isAdmin())
                                <li class="nav-item dropdown dropdown-submenu">
                                    {{--                                            <a href="#" class="nav-link dropdown-items" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Multi Site<span class="caret"></span></a>--}}
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre="">Multi Site<span
                                            class="caret"></span></a>

                                    <ul class="dropdown-menu multi-level">
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ url('products/pushproductlist') }}">
                                                Push Product List</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('magento-cron-data') }}">Magento Cron
                                                Data</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('magento-productt-errors.index') }}">Magento product push
                                                errors</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('magento_module_categories.index') }}">Magento Module
                                                Category</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('magento_module_types.index') }}">Magento Module Type</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('magento_modules.index') }}">Magento
                                                Modules</a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('store-website.index') }}">Store Website</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('store-website.apiToken') }}">Store Website API Token</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('payment-responses.index') }}">Payment Responses</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('site-development-status.stats') }}">Multi Site
                                                status</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('content-management.index') }}">Content Management</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('store-website.brand.list') }}">Store Brand</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('store-website.category.list') }}">Store Category</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('store-website.color.list') }}">Store Color</a>
                                            <a class="dropdown-item" href="{{ route('size.index') }}">Size</a>
                                            <a class="dropdown-item" href="{{ route('system.size') }}">System Size</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('landing-page.index') }}">Landing
                                                Page</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('newsletters.index') }}">Newsletters</a>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false" v-pre="">Review Newsletters Translate<span
                                                class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="twilioDropdown">
                                                   <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{ route('newsletters.review.translate') }}">Review Arabic Newsletters Translate </a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{ route('newsletters.review.translate','English') }}">Review English Newsletters Translate </a>
                                                    </li>
                                                   <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{ route('newsletters.review.translate','Chinese') }}">Review Chinese Newsletters Translate </a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{ route('newsletters.review.translate','Japanese') }}">Review Japanese Newsletters Translate </a>
                                                    </li>
                                                   <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{ route('newsletters.review.translate','Korean') }}">Review Korean Newsletters Translate </a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{ route('newsletters.review.translate','Urdu') }}">Review Urdu Newsletters Translate </a>
                                                    </li>
                                                   <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{ route('newsletters.review.translate','Russian') }}">Review Russian Newsletters Translate </a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{ route('newsletters.review.translate','Italian') }}">Review Italian Newsletters Translate </a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{ route('newsletters.review.translate','French') }}">Review French Newsletters Translate </a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{ route('newsletters.review.translate','Spanish') }}">Review Spanish Newsletters Translate </a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{ route('newsletters.review.translate','Dutch') }}">Review Dutch Newsletters Translate </a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{ route('newsletters.review.translate','German') }}">Review German Newsletters Translate </a>
                                                    </li>

                                                </ul>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('store-website.price-override.index') }}">Price
                                                Override</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('country.duty.list') }}">Country
                                                duty list</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('country.duty.index') }}">Country
                                                duty search</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('store-website.country-group.index') }}">Country
                                                Group</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('store-website.websites.index') }}">Website</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('store-website.website-stores.index') }}">Website
                                                Store</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('store-website.website-store-views.index') }}">Website
                                                Store View</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('store-website.page.index') }}">Website Page</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('store-website.page.histories') }}">Website Page
                                                History</a>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false" v-pre="">Website Page Review Translate<span
                                                class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="twilioDropdown">
                                                   <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{ route('store-website.page.review.translate','Arabic') }}">Arabic Page Review Translate </a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{ route('store-website.page.review.translate','English') }}">English Page Review Translate </a>
                                                    </li>
                                                   <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{ route('store-website.page.review.translate','Chinese') }}">Chinese Page Review Translate </a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{ route('store-website.page.review.translate','Japanese') }}">Japanese Page Review Translate </a>
                                                    </li>
                                                   <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{ route('store-website.page.review.translate','Korean') }}">Korean Page Review Translate </a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{ route('store-website.page.review.translate','Urdu') }}">Urdu Page Review Translate </a>
                                                    </li>
                                                   <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{ route('store-website.page.review.translate','Russian') }}">Russian Page Review Translate </a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{ route('store-website.page.review.translate','Italian') }}">Italian Page Review Translate </a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{ route('store-website.page.review.translate','French') }}">French Page Review Translate </a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{ route('store-website.page.review.translate','Spanish') }}">Spanish Page Review Translate </a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{ route('store-website.page.review.translate','Dutch') }}">Dutch Page Review Translate </a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{ route('store-website.page.review.translate','German') }}">German Page Review Translate </a>
                                                    </li>

                                                </ul>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('store-website.product-attribute.index') }}">Product
                                                Attribute</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('scrapper.phyhon.index') }}">Site
                                                Scrapper Python</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('scrapper.image.urlList') }}">Scrapper Phyhon Urls</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('store-website.site-attributes.index') }}">Site
                                                Attributes</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('store-website.category-seo.index') }}">Category seo</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('store-website.cancellation') }}">Cancellation Policy</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ route('logging.magento.product.api.call') }}">Magento API
                                                call</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('product.pricing') }}">Magento
                                                Product Pricing</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ url('/product-generic-pricing') }}">Product Generic Pricing</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                                href="{{ url('/store-website-product-prices') }}">Store website product
                                                price</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ url('/site-assets') }}">Site assets</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ url('/site-check-list') }}">Site check
                                                list</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('list.daily-push-log') }}">Magento
                                                Daily Product Push Log</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('google.bigdata') }}">Google Big
                                                Data</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('zabbix.index') }}">Zabbix Items</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('zabbix.problem') }}">Zabbix
                                                Problems</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('uicheck') }}">U I Check</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('updateLog.get') }}">Update Log</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('uicheck.responsive') }}">U I
                                                Responsive</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('uicheck.translation') }}">U I
                                                Languages</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('site-development.store-website-category') }}">Store Website Category</a>
                                        </li>
                                    </ul>
                                </li>

                                @endif
                                @if(auth()->user()->isAdmin())
                                <li class="nav-item dropdown dropdown-submenu">
                                    {{--                                            <a href="#" class="nav-link dropdown-items" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin <span class="caret"></span></a>--}}

                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" v-pre="">Admin<span
                                            class="caret"></span></a>

                                    <ul class="dropdown-menu multi-level">
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="twilioDropdown" href="#" class="nav-link dropdown-toggle"
                                                data-toggle="dropdown" role="button" aria-haspopup="true"
                                                aria-expanded="false">Twilio<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="twilioDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ route('twilio.errors') }}">Twilio
                                                        Errors</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ route('twilio.webhook.error.logs') }}">Twilio
                                                        Webhook Error Logs</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a href="{{ route('twilio-manage-accounts') }}">Twilio Account
                                                        Management</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a href="{{ route('twilio.account_logs') }}">Twilio Account Logs</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a href="{{ route('twilio.view_tone') }}">Twilio Message Tones</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ url('twilio/getChats') }}">SMS</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('twilio.erp_logs') }}">Twilio ERP Logs</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a href="{{ route('twilio.call_journey') }}">Twilio call journey</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ route('get.python.log') }}">Python
                                                        Site Logs</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('twilio.get_website_wise_key_data_options') }}">Twilio
                                                        Key Options</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('twilio.twilio_delivery_logs') }}">Twilio
                                                        Delivery Logs</a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>

                                    <ul class="dropdown-menu multi-level">

                                        <li class="nav-item dropdown ">
                                            <a id="queueDropdown" href="{{ url('task-summary') }}" class="nav-link "
                                                role="button" aria-haspopup="true" aria-expanded="false">Task Summary</a>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="queueDropdown" href="#" class="nav-link dropdown-toggle"
                                                data-toggle="dropdown" role="button" aria-haspopup="true"
                                                aria-expanded="false">Queue<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="queueDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('message-queue.index') }}">Message Queue</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('message-queue.approve') }}">Message Queue
                                                        Approval</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('message-queue-history.index') }}">Queue
                                                        History</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="queueDropdown" href="#" class="nav-link dropdown-toggle"
                                                data-toggle="dropdown" role="button" aria-haspopup="true"
                                                aria-expanded="false">Zabbix<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="zabbixDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('zabbix.index') }}">Items</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('zabbix.problem') }}">Problems</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown ">
                                            <a id="queueDropdown" href="{{ url('todolist') }}" class="nav-link "
                                                role="button" aria-haspopup="true" aria-expanded="false">TodoList</a>
                                        </li>
                                        <li class="nav-item dropdown ">
                                            <a id="queueDropdown" href="{{ url('test-cases') }}" class="nav-link "
                                                role="button" aria-haspopup="true" aria-expanded="false">Test Cases</a>
                                        </li>

                                        <li class="nav-item dropdown ">
                                            <a id="queueDropdown" href="{{ url('test-suites') }}" class="nav-link "
                                                role="button" aria-haspopup="true" aria-expanded="false">Test Suites</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{route('messages.index')}}">Broadcast
                                                messages</a>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="bugDropdown" href="#" class="nav-link dropdown-toggle"
                                                data-toggle="dropdown" role="button" aria-haspopup="true"
                                                aria-expanded="false">Bug Track<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="bugDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{route('bug-tracking.index')}}">Bug Track</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('bug-tracking.website') }}">Bug Tracking Summary</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('get.model.name') }}">Model Name</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('plan.index')}}">Plan</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a href="{{ route('custom-chat-message.index') }}">Chat Messages</a>
                                        </li>

                                        {{-- Sub Menu Product --}}
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Cash Flow<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ route('cashflow.index') }}">Cash
                                                        Flow</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('cashflow.hubstuff.log') }}">Hubstuff Command
                                                        Log</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ url('monetary-account') }}">Monetary Account</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('voucher.index') }}">Convience Voucher</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('cashflow.mastercashflow') }}">Master Cash
                                                        Flow</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('dailycashflow.index') }}">Daily Cash Flow</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('budget.index') }}">Budget</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{route('settings.index')}}">Settings</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{url('conversion/rates')}}">Currency
                                                        Conversion Rate</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{url('magento-admin-settings')}}">Magento Admin
                                                        Settings</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{route('auto.refresh.index')}}">Auto
                                                        Refresh page</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('budget.index') }}">Hubstaff</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ url('page-notes') }}">Page
                                                        Notes</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ url('page-notes-categories') }}">Page Notes
                                                        Categories</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="/totem">Cron Package</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ route('charity') }}">Charity</a>
                                                </li>
                                            </ul>
                                        </li>
                                        @if(auth()->user()->isAdmin())


                                        <li class="nav-item dropdown">
                                            <a href="{{ route('watson-accounts') }}">Watson Account Management</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a href="{{ route('google-chatbot-accounts') }}">Google Dialogflow Account Management</a>
                                        </li>

                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Call Management<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('twilio-call-management') }}"> Call
                                                        Management</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{route('twilio-speech-to-text-logs')}}">Twilio Speech to
                                                        text Logs</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{route('chatbot.type.error.log')}}">Twilio Chat Bot Not
                                                        Recognised</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{route('twilio.call.blocks')}}">Twilio Call Blocks</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{route('twilio.call.statistic')}}">Twilio Call
                                                        Statistic</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{route('twilio.conditions')}}">Twilio Conditions</a>
                                                </li>
                                            </ul>
                                        </li>

                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Legal<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{route('lawyer.index')}}">
                                                        Lawyers</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{route('case.index')}}">Cases</a>
                                                </li>
                                            </ul>
                                        </li>
                                        @endif
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Old Issues<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ url('/old/') }}">Old Info</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ url('/old/?type=1') }}">Old Out
                                                        going</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ url('/old/?type=2') }}">Old
                                                        Incoming</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Duty<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('simplyduty.category.index') }}">SimplyDuty
                                                        Categories</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('simplyduty.currency.index') }}">SimplyDuty
                                                        Currency</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('simplyduty.country.index') }}">SimplyDuty
                                                        Country</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('simplyduty.calculation') }}">SimplyDuty
                                                        Calculation</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('simplyduty.hscode.index') }}">HsCode</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ action('\App\Http\Controllers\ProductController@hsCodeIndex') }}">HsCode
                                                        Generator</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ action('\App\Http\Controllers\HsCodeController@mostCommon') }}">Most
                                                        Common</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ action('\App\Http\Controllers\HsCodeController@mostCommonByCategory') }}">Most
                                                        Common Category</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('assets-manager.index') }}">Assets
                                                Manager</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('email-addresses.index') }}">Email
                                                Addresses</a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('api-response-message') }}">Api
                                                Response Messages</a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('services') }}">Services</a>
                                        </li>

                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>System<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{route('jobs.list')}}">Laravel
                                                        Queue</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{route('failedjobs.list')}}">Laravel
                                                        Failed Queue</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{route('wetransfer.list')}}">Wetransfer Queue</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{route('cron.index')}}">Cron</a>
                                                </li>
                                            </ul>
                                        </li>

                                        <!-- Github -->
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="githubsubmenu" href="#" role="button" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false" v-pre>Github<span
                                                    class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="githubsubmenu">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ url('/github/organizations') }}">Organizations</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ url('/github/repos') }}">Repositories</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ url('/github/branches') }}">Branches</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ url('/github/actions') }}">Actions</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ url('/github/users') }}">Users</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ url('/github/groups') }}">Groups</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ url('/github/pullRequests') }}">Pull requests</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ url('/github/sync') }}">Synchronise from online</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('gitDeplodError') }}">Migration Error</a>
                                                </li>
                                            </ul>
                                        </li>

                                        <!-- hubstaff -->
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="hubstaffsubmenu" href="#" role="button" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false" v-pre>Hubstaff<span
                                                    class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="hubstaffsubmenu">
                                                {{-- Sub Menu Product --}}

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ url('hubstaff/members')  }}">Members</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ url('hubstaff/projects') }}">Projects</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ url('hubstaff/tasks') }}">Tasks</a>
                                                </li>
                                                <!-- <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ url('hubstaff/payments') }}">Payments</a>
                                        </li> -->
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ url('hubstaff-payment') }}">Payments Report</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ url('hubstaff-activities/notification') }}">Activity
                                                        Notofication</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ url('hubstaff-activities/activities') }}">Activities</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('hubstaff-acitivties.acitivties.userTreckTime') }}">User
                                                        Track Time</a>
                                                </li>
                                            </ul>
                                        </li>

                                        <!-- time doctor -->
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a href="#" role="button" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false" v-pre>Time Doctor<span
                                                    class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('time-doctor.members') }}">Time Doctor Members</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ url('time-doctor/projects') }}">Time Doctor Projects</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ url('time-doctor/tasks') }}">Time Doctor Tasks</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ url('time-doctor-activities/notification') }}">Time Doctor Activity
                                                        Notification</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ url('time-doctor-activities/activities') }}">Time Doctor Activities</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('time-doctor-acitivties.acitivties.userTreckTime') }}">Time Doctor User
                                                        Track Time</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('time-doctor.task_creation_logs') }}">Time Doctor Task Creation Logs</a>
                                                </li>
                                            </ul>
                                        </li>

                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Database<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('database.index') }}">Historical Data</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('database.states') }}">Query Process List</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Encryption<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                        href="{{route('encryption.index')}}">Encryption Key</a>
                                                </li>
                                            </ul>
                                        </li>

                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                v-pre>Courier<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item"
                                                    href="{{ route('shipment.index') }}">Shipment</a>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('email.index') }}">Emails</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('activity') }}">Activity</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ url('env-manager') }}">Env Manager</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('routes.index') }}">Routes</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ url('/store-website-analytics/index') }}">Store Website
                                                Analytics</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('store-website-country-shipping.index') }}">Store Website
                                                country shipping</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('googlefiletranslator.list') }}">Google File
                                                Translator</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('google-docs.index') }}">Google
                                                Docs</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('google-drive-screencast.index') }}">Google Drive Screencast</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ url('/google-traslation-settings') }}">Google Translator
                                                Setting</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('googlewebmaster.index') }}">Google
                                                webmaster</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('gt-metrix') }}">GTMetrix
                                                analysis</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('gt-metrix-url') }}">GTMetrix
                                                Url's</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                                href="{{ route('GtMetrixAccount.index') }}">GTMetrix Account</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('gtm.cetegory.web') }}">GTMetrix
                                                Category Website</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ url('checklist') }}">Checklist</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ url('sop') }}">SOP</a>
                                        </li>
                                        <li>
                                        <a class="dropdown-item"
                                            href="{{ route('gtmetrix.error.index.list') }}">GTMetrix Error log</a>
                                </li>
                                <li class="nav-item">
                                    <a class="dropdown-item" href="{{ url('/postman') }}">Magento Request</a>
                                </li>
                                <li class="nav-item">
                                    <a class="dropdown-item" href="{{ url('/postman') }}">Post Man</a>
                                </li>
                                <li class="nav-item">
                                    <a class="dropdown-item" href="{{ route('email.event.journey') }}">Sendgrid Event
                                        Journey</a>
                                </li>
                                <li class="nav-item">
                                    <a class="dropdown-item" href="{{ route('magento.command') }}">Magento Command</a>
                                </li>

                            </ul>
                            </li>
                            @endif
                            <li  class="nav-item dropdown">
                                <a class="dropdown-item" href="{{route('csvTranslator.list')}}">Csv translator</a>
                            </li>
                            <li  class="nav-item dropdown">
                                <a class="dropdown-item" href="{{route('reply.replyTranslateList')}}">Reply Translate List</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="dropdown-item" href="{{ route('redis.jobs') }}">Redis Job</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="dropdown-item" href="{{ route('redisQueue.list') }}">Larvel Queue</a>
                            </li>
                            <li class="nav-item dropdown dropdown-submenu">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button"
                                    aria-haspopup="true" aria-expanded="false">Development <span
                                        class="caret"></span></a>
                                <ul class="dropdown-menu multi-level">
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ url('development/change-user') }}">Change
                                            User</a>
                                    </li>
                                     <li class="nav-item">
                                        <a class="dropdown-item" href="{{ url('development/document/list') }}">Document Upload List</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item"
                                            href="{{ action('\App\Http\Controllers\NewDevTaskController@index') }}">Devtask Planner</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ route('development.overview') }}">Overview</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ url('scrap/development/list') }}">Scrapper
                                            Tasks</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item"
                                            href="{{ url('development/automatic/tasks') }}">Automatic Tasks</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ url('development/list') }}">Tasks</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ url('development/summarylist') }}">Quick Dev
                                            Task</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item"
                                            href="{{url('task?daily_activity_date=&term=&selected_user=&is_statutory_query=3')}}">Discussion
                                            tasks</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ route('task-types.index') }}">Task Types</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ route('development.issue.create') }}">Submit
                                            Issue</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ url('deploy-node') }}">Deploy Node</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ route('master.dev.task') }}">Dev Master
                                            Control</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ route('database.index') }}">Database Size</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ route('database.states') }}">Database
                                        Query Process List</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ url('admin/database-log') }}">Database Log</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ route('manage-modules.index') }}">Manage
                                            Module</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ route('manage-task-category.index') }}">Manage
                                            Task Category</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ route('erp-log') }}">ERP Log</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ route('whatsapp.log') }}">Whatsapp Log</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ url('horizon') }}">Jobs</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ url('project-file-manager') }}">Project
                                            Directory manager</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ route('sentry-log') }}">Sentry Log</a>
                                        <a class="dropdown-item" href="{{ route('development.tasksSummary') }}">Developer Task Summary</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ url('settings/telescope') }}">Manage Telescope </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ url('telescope/dashboard') }}">View Telescope Dashboard</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown dropdown-submenu">
                                <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false"
                                    v-pre="">{{{ isset(Auth::user()->name) ? Auth::user()->name : 'Settings' }}} <span
                                        class="caret"></span></a>

                                <ul class="dropdown-menu multi-level">
                                    {{-- Sub Menu Product --}}

                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{ route('mastercontrol.index') }}">Master
                                            Control</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{ route('dailyplanner.index') }}">Daily
                                            Planner</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{ route('task.list') }}">Tasks List</a>
                                    </li>
                                    @if(auth()->user()->isAdmin())
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{route('password.index')}}">Password Manager</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{route('password.manage')}}">Multiple User
                                            Passwords Manager</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{route('document.index')}}">Document manager</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{ route('resourceimg.index') }}">Resource
                                            Center</a>
                                    </li>
                                    @endif
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false" v-pre>Product<span
                                                class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('products.index')}}">Product</a>
                                            </li>

                                            <li class="nav-item dropdown">

                                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#"
                                                    role="button" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false" v-pre>
                                                    Development<span class="caret"></span>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <a class="dropdown-item"
                                                        href="{{ route('development.index') }}">Tasks</a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('development.flagtask') }}">Flag Tasks</a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('development.issue.index') }}">Issue List</a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('development.issue.create') }}">Submit Issue</a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('development.overview') }}">Overview</a>
                                                </div>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                    href="{{route('category-segment.index')}}">Category Segment</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('category')}}">Category</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                    href="{{action('\App\Http\Controllers\CategoryController@mapCategory')}}">Category
                                                    Reference</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="/category/new-references">New Category
                                                    Reference</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('brand.index')}}">Brands</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                    href="{{route('missing-brands.index')}}">Missing Brands</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('brand/size/chart')}}">Brand Size
                                                    Chart</a>
                                            </li>
                                            @if(auth()->user()->checkPermission('category-edit'))
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('color-reference.index')}}">Color
                                                    Reference</a>
                                            </li>
                                            @endif
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                    href="{{route('compositions.index')}}">Composition</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="/descriptions">Description</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false" v-pre>Customer<span
                                                class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            @if(auth()->user()->isAdmin())
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('task_category.index')}}">Task
                                                    Category</a>
                                            </li>
                                            @endif
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('reply.index')}}">Quick
                                                    Replies</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('autoreply.index')}}">Auto
                                                    Reples</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{route('brand.index')}}">Brands</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{route('brand.logo_data')}}">Brand Logos</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{route('missing-brands.index')}}">Missing
                                            Brands</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{route('brand/size/chart')}}">Brand Size
                                            Chart</a>
                                    </li>
                                    @if(auth()->user()->checkPermission('category-edit'))
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{route('color-reference.index')}}">Color
                                            Reference</a>
                                    </li>
                                    @endif
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{url('/kb/')}}" target="_blank">Knowledge
                                            Base</a>
                                    </li>

                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre="">Time Doctor<span class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('time-doctor-acitivties.pending-payments')}}">Time Doctor Approved Timings</a>
                                            </li>
                                        </ul>
                                    </li>

                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}</a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </li>

                            <!------    System Menu     !-------->
                            <li class="nav-item dropdown dropdown-submenu">
                                {{--                                        <a href="#" class="nav-link dropdown-items" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">System <span class="caret"></span></a>--}}
                                <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false" v-pre="">System <span
                                        class="caret"></span></a>

                                <ul class="dropdown-menu multi-level">
                                    {{-- Sub Menu Product --}}

                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{route('jobs.list')}}">Queue</a>
                                    </li>
                                </ul>
                            </li>



                            <li class="nav-item dropdown dropdown-submenu">
                                {{--                                        <a href="#" class="nav-link dropdown-item dropdown-items" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin Menu <span class="caret"></span></a>--}}
                                <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false" v-pre="">Admin Menu <span
                                        class="caret"></span></a>

                                <ul class="dropdown-menu multi-level">
                                    {{-- Sub Menu Admin Menu --}}
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false" v-pre>Database Menu<span
                                                class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                    href="{{route('admin.databse.menu.direct.dbquery')}}">Direct DB
                                                    Query</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a class="dropdown-item" href="{{ url('learning') }}">Learning Menu</a>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a class="dropdown-item" href="{{ url('order/invoices/saveLaterList') }}">Save Later Invoices</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="dropdown-item" href="{{ route('monitor-jenkins-build.index') }}">Monitor Jenkins Build</a>
                                <a class="dropdown-item" href="{{ route('monitor-server.index') }}">Website Monitor</a>
                            </li>
                            </ul>
                        </div>
                    </div>
                    @endif
                    <div>
                        <div class="nav-item dropdown" id="search_li">
                            <input type="text" class="form-control nav-link w-100" placeholder="Search"
                                style="margin-top : 1%;min-width:120px;" onkeyup="filterFunction()" id="search">
                            <ul class="dropdown-menu multi-level" id="search_container">
                            </ul>
                        </div>
                    </div>
                    @if(Auth::check())
                    <nav id="quick-sidebars">
                        <ul class="list-unstyled components mr-1">      
                            <li>
                                <a title="Event Alerts" id="event-alerts" type="button" class="quick-icon" style="padding: 0px 1px;">
                                    <span><i class="fa fa-clock-o fa-2x" aria-hidden="true"></i></span>
                                    <span class="event-alert-badge hide"></span>
                                </a>
                            </li>
                            @if (Auth::user()->hasRole('Admin'))
                            
                            <li>  
                                @php
                                    $status = \App\Models\MonitorServer::where('status', 'off')->first();
                                @endphp
                                @if ($status)
                                <a title="Search Monitor Status" type="button" data-toggle="modal" data-target="#searchmonitorStatus" class="quick-icon">
                                    <span><i class="fa fa-desktop fa-2x off-status"style="background-color: red" aria-hidden="true"></i></span>
                                </a>
                                @else
                                <a title="Search Monitor Status" type="button" data-toggle="modal" data-target="#searchmonitorStatus" class="quick-icon" style="padding: 0px 1px;">
                                    <span><i class="fa fa-desktop fa-2x" aria-hidden="true"></i></span>
                                </a>
                                @endif
                            </li>
                            <li>
                                <a title="Search Password" type="button" data-toggle="modal" data-target="#searchPassswordModal" class="quick-icon" style="padding: 0px 1px;"><span><i
                                            class="fa fa-key fa-2x" aria-hidden="true"></i></span></a>
                            </li>
                            <li>
                                <a title="User availability" type="button" data-toggle="modal" data-target="#searchUserSchedule" class="quick-icon" style="padding: 0px 1px;">
                                    <span>
                                        <i class="fa fa-clock-o fa-2x" aria-hidden="true"></i>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a title="Create Google Doc" type="button" data-toggle="modal" data-target="#createGoogleDocModal" class="quick-icon" style="padding: 0px 1px;"><span><i
                                            class="fa fa-file-text fa-2x" aria-hidden="true"></i></span></a>
                            </li>
                            <li>
                                <a title="Search Google Doc" type="button" data-toggle="modal" data-target="#SearchGoogleDocModal" class="quick-icon" style="padding: 0px 1px;"><span><i
                                            class="fa fa-file-text fa-2x" aria-hidden="true"></i></span></a>
                            </li>
                            <li>
                                <a title="Quick Dev Task" type="button" class="quick-icon menu-show-dev-task" style="padding: 0px 1px;"><span><i
                                            class="fa fa-tasks fa-2x" aria-hidden="true"></i></span></a>
                            </li>

                            @php
                                $route = request()->route()->getName();
                            @endphp
                            @if (in_array($route, ["development.issue.index", "task.index", "development.summarylist", "chatbot.messages.list"]))
                                <li>
                                    <a title="Time Estimations" type="button" class="quick-icon show-estimate-time" data-task="{{$route == "development.issue.index" ? "DEVTASK" : "TASK"}}">
                                        <span>
                                            <i class="fa fa-clock-o fa-2x" aria-hidden="true"></i>
                                        </span>
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a title="Task & Activity" type="button" class="quick-icon menu-show-task" style="padding: 0px 1px;"><span><i
                                            class="fa fa-tasks fa-2x" aria-hidden="true"></i></span></a>
                            </li>
                            <li>
                                <a title="Create database" type="button" class="quick-icon menu-create-database" style="padding: 0px 1px;"><span><i
                                            class="fa fa-database fa-2x" aria-hidden="true"></i></span></a>
                            </li>
                            <li>
                                <a title="Create Event" type="button" class="quick-icon" data-toggle="modal" data-target="#shortcut-user-event-model" style="padding: 0px 1px;"><span><i
                                            class="fa fa-calendar-o fa-2x" aria-hidden="true"></i></span></a>
                            </li>
                            <li>
                                <a title="Create Resource" type="button" class="quick-icon" data-toggle="modal" data-target="#shortcut_addresource" style="padding: 0px 1px;"><span><i
                                            class="fa fa-file-image-o fa-2x" aria-hidden="true"></i></span></a>
                            </li>
                            <li>
                                <a title="Sop Search" type="button" class="quick-icon menu-sop-search" style="padding: 0px 1px;"><span><i
                                                class="fa fa-search fa-2x" aria-hidden="true"></i></span></a>
                            </li>
                            <li>
                                <a title="Email Search" type="button" class="quick-icon menu-email-search" style="padding: 0px 1px;"><span><i
                                                class="fa fa-envelope fa-2x" aria-hidden="true"></i></span></a>
                            </li>
                            <li>
                                <img src="https://p1.hiclipart.com/preview/160/386/395/cloud-symbol-cloud-computing-business-telephone-system-itc-technology-workflow-ip-pbx-vmware-png-clipart.jpg"
                                    class="system-request" data-toggle="modal"
                                    style="width:25px; height:25px;background: #dddddd9c;padding: 0px;"
                                    data-target="#system-request" title="System Request" />
                            </li>
                            <li>
                                <a class="quick-icon todolist-request" href="#"><span><i
                                            class="fa fa-plus fa-2x"></i></span></a>
                            </li>
                            <li>
                                <a class="quick-icon todolist-get" href="#"><span><i
                                            class="fa fa-list fa-2x"></i></span></a>
                            </li>
                            <li>
                                <a class="quick-icon permission-request" href="#"><span><i
                                            class="fa fa-reply fa-2x"></i>{{-- $permissionRequest --}}</span></a>
                            </li>
                            @endif
                            <li>
                                <a class="notification-button quick-icon" href="#"><span><i
                                            class="fa fa-bell fa-2x"></i></span></a>
                            </li>
                            <li>
                                <a class="instruction-button quick-icon" href="#"><span><i
                                            class="fa fa-question-circle fa-2x" aria-hidden="true"></i></span></a>
                            </li>
                            <li>
                                <a class="daily-planner-button quick-icon" target="__blank"
                                    href="{{ route('dailyplanner.index') }}">
                                    <span><i class="fa fa-calendar-check-o fa-2x" aria-hidden="true"></i></span>
                                </a>
                            </li>


                            <li>
                                <a id="message-chat-data-box" class="quick-icon">
                                    <span class="p1 fa-stack has-badge" id="new_message"
                                        data-count="@if(isset($newMessageCount)) {{ $newMessageCount }} @else 0 @endif">
                                        <i class="fa fa-comment fa-2x xfa-inverse" data-count="4b"></i>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a class="create-zoom-meeting quick-icon" data-toggle="modal"
                                    data-target="#quick-zoomModal">
                                    <span><i class="fa fa-video-camera fa-2x" aria-hidden="true"></i></span>
                                </a>
                            </li>
                            <li>
                                <a class="create-easy-task quick-icon" data-toggle="modal"
                                    data-target="#quick-create-task">
                                    <span><i class="fa fa-tasks fa-2x" aria-hidden="true"></i></span>
                                </a>
                            </li>
                            @if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('HOD of CRM'))
                            <li>
                                <a title="Manual Payment" class="manual-payment-btn quick-icon">
                                    <span><i class="fa fa-money fa-2x" aria-hidden="true"></i></span>
                                </a>
                            </li>
                            @endif
                            <li>
                                <a title="Manual Request" class="manual-request-btn quick-icon">
                                    <span><i class="fa fa-credit-card-alt fa-2x" aria-hidden="true"></i></span>
                                </a>
                            </li>
                            <li>
                                <a title="Auto Refresh" class="auto-refresh-run-btn quick-icon">
                                    <span><i class="fa fa-refresh fa-2x" aria-hidden="true"></i></span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <div id="permission-request-model" class="modal fade" role="dialog">
                        <div class="modal-dialog modal-lg">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Permission request list</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <button type="button" class="btn btn-default permission-delete-grant">Delete
                                        All</button>
                                    <div class="col-md-12" id="permission-request">
                                        <table class="table fixed_header">
                                            <thead>
                                                <tr>
                                                    <th>User name</th>
                                                    <th>Permission name</th>
                                                    <th>Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="show-list-records">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

            </div>

        </nav>

        @php
        $route = request()->route()->getName();
        @endphp
        @if (in_array($route, ["development.issue.index", "task.index", "development.summarylist", "chatbot.messages.list"]))
            @php
                $d_taskList = App\DeveloperTask::select('id')->orderBy('id', 'desc')->get()->pluck('id');
                $g_taskList = App\Task::select('id')->orderBy('id', 'desc')->get()->pluck('id');
            @endphp
            {{-- @if ($route == "development.issue.index")
            @else
            @php
                @endphp
            @endif --}}
            <div id="showLatestEstimateTime" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Estimation</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body shortcut-estimate-search-container">
                            <div class="from-group ">
                                <label>Search</label>
                                <br>
                                <select name="task_id" id="shortcut-estimate-search" class="form-control">
                                    <option selected value>Select task</option>
                                    @foreach ($d_taskList as $val)
                                        <option value="DEVTASK-{{$val}}">DEVTASK-{{$val}}</option>
                                    @endforeach
                                    @foreach ($g_taskList as $val)
                                        <option value="TASK-{{$val}}">TASK-{{$val}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="modal-table">

                            </div>
                        </div>
                    </div>

                </div>
            </div>
            @include('development.partials.estimate-shortcut')
        @endif

        <div id="todolist-request-model" class="modal fade" role="dialog">
            <div class="modal-content modal-dialog modal-md">
                <form action="{{ route('todolist.store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h4 class="modal-title">Create Todo List</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body show-list-records" id="todolist-request">
                        <div class="form-group">
                            <strong>Title:</strong>
                            <input type="text" name="title" class="form-control add_todo_title"
                                value="{{ old('title') }}" required="">

                            @if ($errors->has('title'))
                            <div class="alert alert-danger">{{ $errors->first('title') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <strong>Subject:</strong>
                            <input type="text" name="subject" class="form-control add_todo_subject"
                                value="{{ old('subject') }}" required="">

                            @if ($errors->has('subject'))
                            <div class="alert alert-danger">{{ $errors->first('subject') }}</div>
                            @endif
                        </div>
                        @php
                        $todoCategories = \App\TodoCategory::get();
                        @endphp
                         <div class="form-group">
                             <strong>Category:</strong>
                             {{-- <input type="text" name="" class="form-control" value="{{ old('') }}" required> --}}
                             <select name="todo_category_id" class="form-control">
                             <option value="">Select Category</option>
                                @foreach($todoCategories as $todoCategory)
                                    <option value="{{$todoCategory->id}}" @if($todoCategory->id == old('todo_category_id')) selected @endif>{{$todoCategory->name}}</option>
                                @endforeach
                             </select>
                             @if ($errors->has('status'))
                                 <div class="alert alert-danger">{{ $errors->first('status') }}</div>
                             @endif
                         </div>
                        @php
                        $statuses = \App\TodoStatus::all()->toArray();
                        @endphp
                        <div class="form-group">
                            <strong>Status:</strong>
                            {{-- <input type="text" name="status" class="form-control" value="{{ old('status') }}" required> --}}
                            <select name="status" class="form-control">
                                @foreach ($statuses as $status )
                                <option value="{{$status['id']}}" @if (old('status') == $status['id']) selected @endif>{{$status['name']}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('status'))
                                <div class="alert alert-danger">{{ $errors->first('status') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <strong>Date:</strong>

                            <div class='input-group date' id='todo-date' required="">
                                <input type="text" class="form-control global" name="todo_date" placeholder="Date"
                                    value="{{ old('todo_date') }}">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>

                            @if ($errors->has('todo_date'))
                            <div class="alert alert-danger">{{ $errors->first('todo_date') }}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong>Remark:</strong>
                            <input type="text" name="remark" class="form-control" value="{{ old('remark') }}" required>

                            @if ($errors->has('remark'))
                            <div class="alert alert-danger">{{ $errors->first('remark') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Store</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="menu-create-database-model" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg" role="document" style="width:500px !important">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Database</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form id="database-form">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="database_user_id" class="app-database-user-id" id="database-user-id" value="">
                                        <div class="row">
                                            <div class="col">
                                                <select class="form-control choose-db" name="connection">
                                                    <?php foreach (\App\StoreWebsite::DB_CONNECTION as $k => $connection) {?>
                                                    <option {{($connection == $k)?"selected='selected'":''}} value="<?php echo $k; ?>"><?php echo $connection; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col">
                                                <select class="form-control choose-username" name="username">
                                                    <option value="">Select User</option>
                                                    <?php
                                                    $users = \App\User::select('id', 'name', 'email')->orderBy('name')->get();
                                                    foreach ($users as $k => $connection) {?>
                                                    <option value="<?php echo $connection->id; ?>" data-name="{{$connection->name}}"><?php echo $connection->name; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col">
                                                <input type="text" name="password" class="database_password" class="form-control" placeholder="Enter password">
                                            </div>
                                            <div class="col">
                                                <button type="button" class="btn btn-secondary btn-database-add" data-id="">ADD</button>

                                                <button type="button" class="btn btn-secondary btn-delete-database-access d-none" data-connection="" data-id="">DELETE ACCESS</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="row mt-5">
                                <form>
                                    <?php echo csrf_field(); ?>
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col">
                                                <input type="hidden" name="connection"  value="">
                                                <input type="text" name="search" class="form-control app-search-table" placeholder="Search Table name">
                                            </div>
                                            <div class="col">
                                                <div class="form-group col-md-5">
                                                    <select class="form-control assign-permission-type" name="assign_permission">
                                                        <option value="read">Read</option>
                                                        <option value="write">Write</option>
                                                    </select>
                                                </div>
                                                <button type="button" class="btn btn-secondary btn-assign-permission assign-permission" data-id="">Assign Permission</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mt-2">
                                        <table class="table table-bordered" id="database-table-list1">
                                            <thead>
                                            <tr>
                                                <th width="5%"></th>
                                                <th width="95%">Table name</th>
                                            </tr>
                                            </thead>
                                            <tbody class="menu_tbody">
                                                @php
                                                  $database_table_name = \DB::table('information_schema.TABLES')->where('table_schema', env('DB_DATABASE'))->get();
                                                @endphp
                                                @foreach(json_decode($database_table_name) as $name)
                                                <tr>
                                                    <td><input type="checkbox" name="tables[]" value="{{$name->TABLE_NAME}}"></td>
                                                    <td>{{$name->TABLE_NAME}}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                            </div>
                        </div>
                </div>
            </div>
        </div>

        <div id="menu-show-task-model" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Task & Activity</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <form id="database-form">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-12 pb-3">
                                            <input type="text" name="task_search" class="task-search-table" class="form-control" placeholder="Enter Task Id">
                                            <button type="button" class="btn btn-secondary btn-task-search-menu" ><i class="fa fa-search"></i></button>
                                        </div>
                                        <div class="col-12">
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                <tr>
                                                    <th width="5%">ID</th>
                                                    <th width="10%">Assign To</th>
                                                    <th width="10%">Communication</th>
                                                </tr>
                                                </thead>
                                                <tbody class="show-search-task-list">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="menu-show-dev-task-model" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Quick Dev Task</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <form id="database-form">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-12 pb-3">
                                            <input type="text" name="task_search" class="dev-task-search-table" class="form-control" placeholder="Enter Dev Task Id">
                                            <button type="button" class="btn btn-secondary btn-dev-task-search-menu" ><i class="fa fa-search"></i></button>
                                        </div>
                                        <div class="col-12">
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                <tr>
                                                    <th width="5%">ID</th>
                                                    <th width="10%">Assign To</th>
                                                    <th width="10%">Communication</th>
                                                </tr>
                                                </thead>
                                                <tbody class="show-search-dev-task-list">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="menu_user_history_modal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">User history</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-12" id="user_history_div">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>User type</th>
                                        <th>Previous user</th>
                                        <th>New User</th>
                                        <th>Updated by</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="menu_confirmMessageModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Confirm Message</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <form action="{{ route('task_category.store') }}" method="POST" onsubmit="return false;">
                        @csrf

                        <div class="modal-body">


                            <div class="form-group">
                                <div id="message_confirm_text"></div>
                                <input name="task_id" id="confirm_task_id" type="hidden" />
                                <input name="message" id="confirm_message" type="hidden" />
                                <input name="status" id="confirm_status" type="hidden" />
                            </div>
                            <div class="form-group">
                                <p>Send to Following</p>
                                <input checked="checked" name="send_message_recepients[]" class="send_message_recepients" type="checkbox" value="assign_by">Assign By
                                <input checked="checked" name="send_message_recepients[]" class="send_message_recepients" type="checkbox" value="assigned_to">Assign To
                                <input checked="checked" name="send_message_recepients[]" class="send_message_recepients" type="checkbox" value="master_user_id">Lead 1
                                <input checked="checked" name="send_message_recepients[]" class="send_message_recepients" type="checkbox" value="second_master_user_id">Lead 2
                                <input checked="checked" name="send_message_recepients[]" class="send_message_recepients" type="checkbox" value="contacts">Contacts
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-secondary menu-confirm-messge-button">Send</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

        <!-- Modal -->
        <div id="menu-upload-document-modal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Upload Document</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form id="menu-upload-task-documents">
                        <div class="modal-body">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" id="hidden-identifier" name="developer_task_id" value="">
                            <div class="row">
                                <div class="col-md-10 col-md-offset-1">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Subject</label>
                                                <?php echo Form::text("subject",null, ["class" => "form-control", "placeholder" => "Enter subject"]); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Description</label>
                                                <?php echo Form::textarea("description",null, ["class" => "form-control", "placeholder" => "Enter Description"]); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Documents</label>
                                                <input type="file" name="files[]" id="filecount" multiple="multiple">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-default">Save</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div id="menu-blank-modal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"></h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        @include('googledocs.partials.create-doc')
        @include('googledocs.partials.search-doc')
        @include('passwords.search-password')
        @include('user-management.search-user-schedule')
        @include('partials.modals.shortcut-user-event-modal')
        @include('partials.modals.event-alerts-modal')
        @include('resourceimg.partials.short-cut-modal-create-resource-center')
        <div id="menu-file-upload-area-section" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('task.save-documents') }}" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="task_id" id="hidden-task-id" value="">
                        <div class="modal-header">
                            <h4 class="modal-title">Upload File(s)</h4>
                        </div>
                        <div class="modal-body" style="background-color: #999999;">
                            @csrf
                            <div class="form-group">
                                <label for="document">Documents</label>
                                <input type="file" name="document" class="needsclick" id="document-dropzone" multiple>
{{--                                <div class="needsclick dropzone" id="document-dropzone">--}}

{{--                                </div>--}}
                            </div>
                            <div class="form-group add-task-list">

                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default menu-btn-save-documents">Save</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="menu-preview-task-image" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th style="width:1%;">No</th>
                                    <th style=" width: 30%">Files</th>
                                    <th style="word-break: break-all; width:12%">Send to</th>
                                    <th style="width: 1%;">User</th>
                                    <th style="width: 11%">Created at</th>
                                    <th style="width: 6%">Action</th>
                                </tr>
                                </thead>
                                <tbody class="menu-task-image-list-view">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    @if (Auth::check())

		<div id="todolist-get-model" class="modal fade" role="dialog">
             <div class="modal-content modal-dialog modal-lg">
                    <div class="modal-header">
                        <h4 class="modal-title">Todo List</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
			@php
				$todoLists = \App\TodoList::where('user_id',\Auth()->user()->id)->where('status',1)->orderByRaw('if(isnull(todo_lists.todo_date) >= curdate() , todo_lists.todo_date, todo_lists.created_at) desc')->with('category')->limit(10)->get();
            $statuses = \App\TodoStatus::get();
			@endphp
			<div class="modal-body show-list-records" id="todolist-request">
				@if($todoLists->count())
				<table class="table table-bordered">
					 <tbody>
					  <tr>
						<th>Title</th>
						<th>Subject</th>
						<th>Category</th>
						<th>Status</th>
						<th>Date</th>
					  </tr>
					@foreach($todoLists as $todoList)
					  <tr>
						<td>{{ $todoList->title }}</td>
						<td>{{ $todoList->subject }}</td>
						<td>{{ isset($todoList->category->name) ? $todoList->category->name : ''; }}</td>
						<td>
                            <select name="status" class="form-control" onchange="todoHomeStatusChange({{$todoList->id}}, this.value)" >
                                @foreach ($statuses as $status )
                                <option value="{{$status->id}}" @if ($todoList->status == $status->id) selected @endif>{{$status->name}}</option>
                                @endforeach
                            </select>
						</td>
                        </div>

						<td>{{ $todoList->todo_date}}</td>
					  </tr>
					@endforeach
					</tbody>
				</table>
				@else
					<h4 class="modal-title">No Records</h4>
				@endif
			</div>
             </div>
        </div>


        @if(1 == 2 && auth()->user()->isAdmin())
        <div class="float-container developer-float hidden-xs hidden-sm">
            @php
            $lukas_pending_devtasks_count = \App\DeveloperTask::where('user_id', 3)->where('status', '!=',
            'Done')->count();
            $lukas_completed_devtasks_count = \App\DeveloperTask::where('user_id', 3)->where('status', 'Done')->count();
            $rishab_pending_devtasks_count = \App\DeveloperTask::where('user_id', 65)->where('status', '!=',
            'Done')->count();
            $rishab_completed_devtasks_count = \App\DeveloperTask::where('user_id', 65)->where('status',
            'Done')->count();
            @endphp

            <a href="{{ route('development.index') }}">
                <span class="badge badge-task-pending">L-{{ $lukas_pending_devtasks_count }}</span>
            </a>

            <a href="{{ route('development.index') }}">
                <span class="badge badge-task-completed">L-{{ $lukas_completed_devtasks_count }}</span>
            </a>

            <a href="{{ route('development.index') }}">
                <span class="badge badge-task-other">R-{{ $rishab_pending_devtasks_count }}</span>
            </a>

            <a href="{{ route('development.index') }}">
                <span class="badge badge-task-other right completed">R-{{ $rishab_completed_devtasks_count }}</span>
            </a>
            <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#quickDevelopmentModal">+
                DEVELOPMENT</button>
        </div>

        <div class="float-container instruction-float hidden-xs hidden-sm">
            @php
            $pending_instructions_count = \App\Instruction::where('assigned_to',
            Auth::id())->whereNull('completed_at')->count();
            $completed_instructions_count = \App\Instruction::where('assigned_to',
            Auth::id())->whereNotNull('completed_at')->count();
            $sushil_pending_instructions_count = \App\Instruction::where('assigned_from',
            Auth::id())->where('assigned_to', 7)->whereNull('completed_at')->count();
            $andy_pending_instructions_count = \App\Instruction::where('assigned_from',
            Auth::id())->where('assigned_to', 56)->whereNull('completed_at')->count();
            @endphp

            <a href="{{ route('instruction.index') }}">
                <span class="badge badge-task-pending">{{ $pending_instructions_count }}</span>
            </a>

            <a href="{{ route('instruction.index') }}#verify-instructions">
                <span class="badge badge-task-completed">{{ $completed_instructions_count }}</span>
            </a>

            <a href="{{ route('instruction.list') }}">
                <span class="badge badge-task-other">S-{{ $sushil_pending_instructions_count }}</span>
            </a>

            <a href="{{ route('instruction.list') }}">
                <span class="badge badge-task-other right">A-{{ $andy_pending_instructions_count }}</span>
            </a>
            <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#quickInstructionModal">+
                INSTRUCTION</button>
        </div>

        <div class="float-container hidden-xs hidden-sm">
            @php
            $pending_tasks_count = \App\Task::where('is_statutory', 0)->where('assign_to',
            Auth::id())->whereNull('is_completed')->count();
            $completed_tasks_count = \App\Task::where('is_statutory', 0)->where('assign_to',
            Auth::id())->whereNotNull('is_completed')->count();
            $sushil_pending_tasks_count = \App\Task::where('is_statutory', 0)->where('assign_to',
            7)->whereNull('is_completed')->count();
            $andy_pending_tasks_count = \App\Task::where('is_statutory', 0)->where('assign_to',
            56)->whereNull('is_completed')->count();
            @endphp

            <a href="/#1">
                <span class="badge badge-task-pending">{{ $pending_tasks_count }}</span>
            </a>

            <a href="/#3">
                <span class="badge badge-task-completed">{{ $completed_tasks_count }}</span>
            </a>

            <a href="{{ route('task.list') }}">
                <span class="badge badge-task-other">S-{{ $sushil_pending_tasks_count }}</span>
            </a>

            <a href="{{ route('task.list') }}">
                <span class="badge badge-task-other right">A-{{ $andy_pending_tasks_count }}</span>
            </a>
            <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#quickTaskModal">+
                TASK</button>
        </div>
        @endif
        @include('twilio.receive_call_popup')
        @include('partials.modals.quick-task')
        @include('partials.modals.quick-instruction')
        @include('partials.modals.quick-development-task')
        @include('partials.modals.quick-instruction-notes')
        @include('partials.modals.quick-user-event-notification')

        @include('partials.modals.quick-zoom-meeting-window')
        @include('partials.modals.quick-create-task-window')
        @include('partials.modals.quick-notes') {{-- Purpose : Import notes modal - DEVTASK-4289 --}}

        @php
        $liveChatUsers = \App\LiveChatUser::where('user_id',Auth::id())->first();
        $key = \App\LivechatincSetting::first();
        @endphp


        <input type="hidden" id="live_chat_key" value="@if(isset($key)){{ $key->key}}@else @endif">
        @include('partials.chat')

        @include('partials.modals.quick-chatbox-window')
        @endif
        {{--        @if(Auth::check())--}}
        {{--            <!---start section for the sidebar toggle -->--}}
        {{--            <nav id="quick-sidebar">--}}
        {{--                <ul class="list-unstyled components">--}}
        {{--                    <li>--}}
        {{--                        <a class="notification-button quick-icon" href="#"><span><i class="fa fa-bell fa-2x"></i></span></a>--}}
        {{--                    </li>--}}
        {{--                    <li>--}}
        {{--                        <a class="instruction-button quick-icon" href="#"><span><i class="fa fa-question-circle fa-2x" aria-hidden="true"></i></span></a>--}}
        {{--                    </li>--}}
        {{--                    <li>--}}
        {{--                        <a class="daily-planner-button quick-icon" target="__blank" href="{{ route('dailyplanner.index') }}">--}}
        {{--                            <span><i class="fa fa-calendar-check-o fa-2x" aria-hidden="true"></i></span>--}}
        {{--                        </a>--}}
        {{--                    </li>--}}
        {{--                    --}}
        {{--                    <li>--}}
        {{--                        <a id="message-chat-data-box" class="quick-icon">--}}
        {{--                           <span class="p1 fa-stack has-badge" id="new_message" data-count="@if(isset($newMessageCount)) {{ $newMessageCount }}
        @else 0 @endif">--}}
        {{--                                <i class="fa fa-comment fa-2x xfa-inverse" data-count="4b"></i>--}}
        {{--                           </span>--}}
        {{--                        </a>--}}
        {{--                    </li>--}}
        {{--                    <li>--}}
        {{--                        <a class="create-zoom-meeting quick-icon" data-toggle="modal" data-target="#quick-zoomModal">--}}
        {{--                            <span><i class="fa fa-video-camera fa-2x" aria-hidden="true"></i></span>--}}
        {{--                        </a>--}}
        {{--                    </li>--}}
        {{--                    <li>--}}
        {{--                        <a class="create-easy-task quick-icon" data-toggle="modal" data-target="#quick-create-task">--}}
        {{--                            <span><i class="fa fa-tasks fa-2x" aria-hidden="true"></i></span>--}}
        {{--                        </a>--}}
        {{--                    </li>--}}
        {{--                    @if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('HOD of CRM'))--}}
        {{--                        <li>--}}
        {{--                            <a title="Manual Payment" class="manual-payment-btn quick-icon">--}}
        {{--                                <span><i class="fa fa-money fa-2x" aria-hidden="true"></i></span>--}}
        {{--                            </a>--}}
        {{--                        </li>--}}
        {{--                    @endif--}}
        {{--                    <li>--}}
        {{--                        <a title="Manual Request" class="manual-request-btn quick-icon">--}}
        {{--                            <span><i class="fa fa-credit-card-alt fa-2x" aria-hidden="true"></i></span>--}}
        {{--                        </a>--}}
        {{--                    </li>--}}
        {{--                    <li>--}}
        {{--                        <a title="Auto Refresh" class="auto-refresh-run-btn quick-icon">--}}
        {{--                            <span><i class="fa fa-refresh fa-2x" aria-hidden="true"></i></span>--}}
        {{--                        </a>--}}
        {{--                    </li>--}}
        {{--                </ul>--}}
        {{--            </nav>--}}
        {{--            <!-- end section for sidebar toggle -->--}}
        {{--        @endif--}}
        @if (trim($__env->yieldContent('large_content')))
        <div class="col-md-12">
            @yield('large_content')
        </div>
        @elseif (trim($__env->yieldContent('core_content')))
        @yield('core_content')
        @else
        <main class="container container-grow" style="display: inline-block;">
            <!-- Showing fb like page div to all pages  -->
            {{-- @if(Auth::check())
                <div class="fb-page" data-href="https://www.facebook.com/devsofts/" data-small-header="true" data-adapt-container-width="false" data-hide-cover="true" data-show-facepile="false"><blockquote cite="https://www.facebook.com/devsofts/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/devsofts/">Development</a></blockquote></div>

                @endif --}}
            @yield('content')
            <!-- End of fb page like  -->
        </main>
        @endif


        <a id="back-to-top" href="javascript:;" class="btn btn-light btn-lg back-to-top" role="button"><i class="fa fa-chevron-up"></i></a>
    </div>

    @if(Auth::check())


    <div class="chat-button-wrapper">
        {{--        <div class="chat-button-float">--}}
        {{--            <button class="chat-button">--}}
        {{--                <img src="/images/chat.png" class="img-responsive"/>--}}
        {{--                <span id="new_message_count">@if(isset($newMessageCount)) {{ $newMessageCount }} @else 0
        @endif</span>--}}
        {{--            </button>--}}
        {{--        </div>--}}
        {{--        <div class="notification-badge">--}}
        {{--            <button class="chat-button">--}}
        {{--                <a href="{{route('notifications')}}">--}}
        {{--                <img src="/images/notification-icon.png" class="img-responsive"/>--}}
        {{--                <span id="notification_unread">@if(isset($unread)) {{ $unread }} @else 0 @endif</span>--}}
        {{--                </a>--}}
        {{--            </button>--}}
        {{--        </div>--}}
        <div class="col-md-12 page-chat-list-rt dis-none">
            <div class="help-list well well-lg">
                <div class="row">
                    <div class="col-md-3 chat" style="margin-top : 0px !important;">
                        <div class="card_chat mb-sm-3 mb-md-0 contacts_card">
                            <div class="card-header">
                                <div class="input-group">
                                    {{-- <input type="text" placeholder="Search..." name="" class="form-control search">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text search_btn"><i class="fa fa-search"></i></span>
                                        </div> --}}
                                </div>
                            </div>
                            <div class="card-body contacts_body">
                                @php
                                $chatIds = cache()->remember('CustomerLiveChat::with::customer::orderby::seen_asc', 60 *
                                60 * 24 * 1, function(){
                                return \App\CustomerLiveChat::with('customer')->orderBy('seen','asc')
                                ->orderBy('status','desc')
                                ->get();
                                });
                                $newMessageCount = \App\CustomerLiveChat::where('seen',0)->count();
                                @endphp
                                <ul class="contacts" id="customer-list-chat">
                                    @foreach ($chatIds as $chatId)
                                    @php
                                    $customer = $chatId->customer;
                                    if($customer) {
                                    $customerInital = substr($customer->name, 0, 1);
                                    @endphp
                                    <li onclick="getChats('{{ $customer->id }}')" id="user{{ $customer->id }}"
                                        style="cursor: pointer;">
                                        <div class="d-flex bd-highlight">
                                            <div class="img_cont">
                                                <soan class="rounded-circle user_inital">{{ $customerInital }}</soan>
                                                {{-- <img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img"> --}}
                                                <span
                                                    class="online_icon @if($chatId->status == 0) offline @endif "></span>
                                            </div>
                                            <div class="user_info">
                                                <span>{{ $customer->name }}</span>
                                                <p>{{ $customer->name }} is @if($chatId->status == 0) offline @else
                                                    online @endif </p>
                                            </div>
                                            @if($chatId->seen == 0)<span class="new_message_icon"></span>@endif
                                        </div>
                                    </li>
                                    @php } @endphp

                                    @endforeach


                                </ul>
                            </div>
                            <div class="card-footer"></div>
                        </div>
                    </div>
                    <div class="col-md-6 chat">
                        <div class="card_chat">
                            <div class="card-header msg_head">
                                <div class="d-flex bd-highlight align-items-center justify-content-between">
                                    <div class="img_cont">
                                        <soan class="rounded-circle user_inital" id="user_inital"></soan>
                                        {{-- <img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img"> --}}

                                    </div>
                                    <div class="user_info" id="user_name">
                                        {{-- <span>Chat with Khalid</span>
                                            <p>1767 Messages</p> --}}
                                    </div>
                                    <div class="video_cam">
                                        <span><i class="fa fa-video"></i></span>
                                        <span><i class="fa fa-phone"></i></span>
                                    </div>
                                    @php
                                    $path = storage_path('/');
                                    $content = File::get($path."languages.json");
                                    $language = json_decode($content, true);
                                    @endphp
                                    <div class="selectedValue">
                                        <select id="autoTranslate" class="form-control auto-translate">
                                            <option value="">Translation Language</option>
                                            @foreach ($language as $key => $value)
                                            <option value="{{$value}}">{{$key}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <span id="action_menu_btn"><i class="fa fa-ellipsis-v"></i></span>
                                <div class="action_menu">
                                    {{-- <ul>
                                            <li><i class="fa fa-user-circle"></i> View profile</li>
                                            <li><i class="fa fa-users"></i> Add to close friends</li>
                                            <li><i class="fa fa-plus"></i> Add to group</li>
                                            <li><i class="fa fa-ban"></i> Block</li>
                                        </ul> --}}
                                </div>
                            </div>
                            <div class="card-body msg_card_body" id="message-recieve">

                            </div>
                            <div class="typing-indicator" id="typing-indicator"></div>
                            <div class="card-footer">
                                <div class="input-group">
                                    {{-- <div class="input-group-append">
                                        <span class="input-group-text attach_btn" onclick="sendMessage()"><i class="fa fa-paperclip"></i></span>
                                        <input type="file" id="imgupload" style="display:none" />
                                    </div> --}}
                                    <div class="card-footer">
                                        <div class="input-group">
                                            <div class="input-group-append">
                                                <span class="input-group-text attach_btn" onclick="sendImage()"><i
                                                        class="fa fa-paperclip"></i></span>
                                                <input type="file" id="imgupload" style="display:none" />
                                            </div>
                                            <input type="hidden" id="message-id" name="message-id" />
                                            <textarea name="" class="form-control type_msg"
                                                placeholder="Type your message..." id="message"></textarea>
                                            <div class="input-group-append">
                                                <span class="input-group-text send_btn" onclick="sendMessage()"><i
                                                        class="fa fa-location-arrow"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 customer-info">
                        <div class="chat-righbox">
                            <div class="title">General Info</div>
                            <div id="chatCustomerInfo"></div>

                        </div>
                        <div class="chat-righbox">
                            <div class="title">Visited Pages</div>
                            <div id="chatVisitedPages">

                            </div>
                        </div>
                        <div class="chat-righbox">
                            <div class="title">Additional info</div>
                            <div class="line-spacing" id="chatAdditionalInfo">

                            </div>
                        </div>
                        <div class="chat-righbox">
                            <div class="title">Technology</div>
                            <div class="line-spacing" id="chatTechnology">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="create-manual-payment" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content" id="create-manual-payment-content">

            </div>
        </div>
    </div>

    <!--Sop Create Modal -->
    <div id="Create-Sop-Shortcut" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Create Shortcut Model</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="createShortcutForm">
                    <td><input type="file" name="image" hidden></td>
                    <td><input type="text" name="tags[0][name]" hidden></td>
                    <td><input type="text" name="tags[0][value]" hidden></td>
                    <div class="modal-body add_sop_modal">
                        <div class="mb-3">
                            <select class="form-control sop_drop_down ">
                                <option value="sop">Sop</option>
                                <option value="knowledge_base">Knowledge Base</option>
                            </select>
                        </div>
                        <input type="hidden" name="chat_message_id" value="" class="chat_message_id" />
                        <div class="add_sop_div mt-3">
                            <tr>
                                <select class="form-control knowledge_base mb-3" name="sop_knowledge_base" hidden>
                                    <option value="">Select</option>
                                    <option value="book">Book</option>
                                    <option value="chapter">Chapter</option>
                                    <option value="page">Page</option>
                                    <option value="shelf">Shelf</option>
                                </select>
                            </tr>
                            <tr>
                                <select class="form-control knowledge_base_book mb-3" name="knowledge_base_book" hidden>
                                    <option value="">Select Books</option>
                                    @php
                                    $books =
                                    Illuminate\Support\Facades\Cache::remember('Modules\BookStack\Entities\Book::get',
                                    60 * 60 * 24 * 7, function(){
                                    return Modules\BookStack\Entities\Book::get();
                                    });
                                    @endphp
                                    @foreach ($books as $book)
                                    <option value="{{ $book->name }}">{{ $book->name }}</option>
                                    @endforeach
                                </select>
                                <span class="books_error" style="color:red;"></span>
                            </tr>
                            <tr>
                                <td>Name:</td>
                                <td><input type="text" name="name" class="form-control mb-3 name"></td>
                            </tr>
                            <tr>
                                <td>Category:</td>
                                <td><input type="text" name="category" class="form-control mb-3 category"></td>
                            </tr>
                            <tr>
                                <td>Description:</td>
                                <td><textarea name="description" id="" cols="30" rows="10"
                                        class="form-control sop_description"></textarea></td>
                            </tr>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default create_shortcut_submit">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    @endif
    <div id="system-request" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" style="width: 1000px; max-width: 1000px;">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">System IPs</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12" id="permission-request">
                        @php
                        use App\User;
                        $userlist = [];
                        $userLists = User::where('is_active', 1)->orderBy('name','asc')->get();

                        $shell_list = shell_exec('bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . '/webaccess-firewall.sh
                        -f list');
                        $final_array = [];
                        if ($shell_list != '') {
                        $lines = explode(PHP_EOL, $shell_list);
                        $final_array = [];
                        foreach ($lines as $line) {
                        $values = [];
                        $values = explode(' ', $line);
                        array_push($final_array, $values);
                        }
                        }
                        @endphp

                        <div id="select-user">
                            <input type="text" name="add-ip" class="form-control col-md-3" placeholder="Add IP here...">
                            <select class="form-control col-md-2 ml-3 ipusersSelect" name="user_id" id="ipusers">
                                <option value="">Select user</option>
                                @foreach ($userLists as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                                <option value="other">Other</option>
                            </select>
                            <input type="text" name="other_user_name" id="other_user_name"
                                class="form-control col-md-2 ml-3" style="display:none;" placeholder="other name">
                            <input type="text" name="ip_comment" class="form-control col-md-2 ml-3 mr-3""
                            placeholder="Add comment...">
                            <button class="btn-success btn addIp ml-3 mb-5">Add</button>
                            <button class="btn-warning btn bulkDeleteIp ml-3 mb-5">Delete All IPs</button>
                        </div>



                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Index</th>
                                <th>IP</th>
                                <th>User</th>
                                <th>Source</th>
                                <th>Comment</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody id="userAllIps">
                            </tbody>
                            <!-- @if (!empty($final_array)) @foreach (array_reverse($final_array) as $values)
                                    <tr>
                                        <td>{{ isset($values[0]) ? $values[0] : '' }}</td>
                                        <td>{{ isset($values[1]) ? $values[1] : '' }}</td>
                                        <td>{{ isset($values[2]) ? $values[2] : '' }}</td>
                                        <td><button class="btn-warning btn deleteIp" data-index="{{ $values[0] }}">Delete</button></td>
                                    </tr> @endforeach
                            @endif -->
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @php

    $url = strtolower(str_replace(array('https://', 'http://'),array('', ''),config('app.url')));
    $url = str_replace('/','',$url);
    $site_account_id = App\StoreWebsiteAnalytic::where('website',$url)->first();
    $account_id = "";
    if(!empty($site_account_id)){
    $account_id = $site_account_id->account_id;
    }
    @endphp


    <!-- Scripts -->

    {{--  @include('partials.chat')--}}
    <div id="loading-image-preview"
        style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')50% 50% no-repeat;display:none;">
    </div>


    <!-- Like page plugin script  -->
    @yield('models')

    {{-- <script>(function(d, s, id) {

  var js, fjs = d.getElementsByTagName(s)[0];

  if (d.getElementById(id)) return;

  js = d.createElement(s); js.id = id;

  js.src = 'https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v3.2&appId=2045896142387545&autoLogAppEvents=1';

  fjs.parentNode.insertBefore(js, fjs);

}(document, 'script', 'facebook-jssdk'));</script> --}}

    @yield('scripts')

{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>--}}
    <script type="text/javascript" src="{{asset('js/jquery.richtext.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/jquery.cookie.js')}}"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.js"></script>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>

    <script type="text/javascript" src="{{asset('js/jquery-ui.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/custom_global_script.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/common-function.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.min.js"></script>


        <script src="https://cdn.ckeditor.com/4.11.4/standard/ckeditor.js"></script>
    <script>
        $('#ipusers').select2({width: '20%'});
        //$('.select-multiple').select2({margin-top: '-32px'});
        CKEDITOR.replace('content');
        CKEDITOR.replace('sop_edit_content');
    </script>
    <script>
    // $('#chat-list-history').on('hidden.bs.modal', function (e) {
    //     document.body.addClass('sasadasd')
    // })

    $(document).on('click', '.menu_editor_copy', function() {
        var content = $(this).data('content');

        menucopyToClipboard(content);
        /* Alert the copied text */
        toastr['success']("Copied the text: " + content);
        //alert("Copied the text: " + remark_text);
    });

    function menucopyToClipboard(text) {
        var sampleTextarea = document.createElement("textarea");
        document.body.appendChild(sampleTextarea);
        sampleTextarea.value = text; //save main text in it
        sampleTextarea.select(); //select textarea contenrs
        document.execCommand("copy");
        document.body.removeChild(sampleTextarea);
    }


    $(document).on('click', '.menu_editor_edit', function() {

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
            $('#sop_edit_category').val(data.sopedit.category)
            $('#sop_old_name').val(data.sopedit.name)
            $('#sop_old_category').val(data.sopedit.category)

            CKEDITOR.instances['sop_edit_content'].setData(data.sopedit.content)

            $("#menu-sopupdate #menu_sop_edit_form").attr('data-id', $($this).attr('data-id'));
            $("#menu-sopupdate").modal("show");

        }).fail(function(data) {
            console.log(data);
        });
    });

    $(document).on('submit', '#menu_sop_edit_form', function(e) {
        e.preventDefault();
        const $this = $(this)
        $(this).attr('data-id', );

        $.ajax({
            type: "POST",
            data: $(this).serialize(),
            url: "{{ route('updateName') }}",
            datatype: "json"
        }).done(function(data) {

            if(data.success==false){
                toastr["error"](data.message, "Message");
                return false;
            }

            if(data.type=='edit'){
                var content = data.sopedit.content.replace( /(<([^>]+)>)/ig, '');

                let id = $($this).attr('data-id');

                $('#sid' + id + ' td:nth-child(1)').html(data.sopedit.id);
                $('#sid' + id + ' td:nth-child(2)').html(`
                            <span class="show-short-name-`+data.sopedit.id+`">`+data.sopedit.name.replace(/(.{17})..+/, "$1..")+`</span>
                            <span style="word-break:break-all;" class="show-full-name-`+data.sopedit.id+` hidden">`+data.sopedit.name+`</span>
                        `);
                $('#sid' + id + ' td:nth-child(3)').html(`
                            <span class="show-short-category-`+data.sopedit.id+`">`+data.sopedit.category.replace(/(.{17})..+/, "$1..")+`</span>
                            <span style="word-break:break-all;" class="show-full-category-`+data.sopedit.id+` hidden">`+data.sopedit.category+`</span>
                        `);
                $('#sid' + id + ' td:nth-child(4)').html(`
                            <span class="show-short-content-`+data.sopedit.id+`">`+content.replace(/(.{50})..+/, "$1..")+`</span>
                            <span style="word-break:break-all;" class="show-full-content-`+data.sopedit.id+` hidden">`+content+`</span>
                        `);
                $("#menu_sopupdate").modal("hide");
                toastr["success"]("Data Updated Successfully!", "Message")
            }else{
                //var content_class = data.sopedit.content.length < 270 ? '' : 'expand-row';
                //var content = data.sopedit.content.length < 270 ? data.sopedit.content : data.sopedit.content.substr(0, 270) + '.....';
                $("#NameTable tbody").prepend(`
                        <tr id="sid`+data.sopedit.id+`" data-id="`+data.sopedit.id+`" class="parent_tr">
                                <td class="sop_table_id">`+data.sopedit.id+`</td>
                                <td class="expand-row-msg" data-name="name" data-id="`+data.sopedit.id+`">
                                    <span class="show-short-name-`+data.sopedit.id+`">`+data.sopedit.name.replace(/(.{17})..+/, "$1..")+`</span>
                                    <span style="word-break:break-all;" class="show-full-name-`+data.sopedit.id+` hidden">`+data.sopedit.name+`</span>
                                </td>
                                <td class="expand-row-msg" data-name="category" data-id="`+data.sopedit.id+`">
                                    <span class="show-short-category-`+data.sopedit.id+`">`+data.sopedit.category.replace(/(.{17})..+/, "$1..")+`</span>
                                    <span style="word-break:break-all;" class="show-full-category-`+data.sopedit.id+` hidden">`+data.sopedit.category+`</span>
                                </td>
                                <td class="expand-row-msg" data-name="content" data-id="`+data.sopedit.id+`">
                                    <span class="show-short-content-`+data.sopedit.id+`">`+data.sopedit.content.replace(/(.{50})..+/, "$1..")+`</span>
                                    <span style="word-break:break-all;" class="show-full-content-`+data.sopedit.id+` hidden">`+data.sopedit.content+`</span>
                                </td>
                                <td class="table-hover-cell p-1">
                                    <div>
                                        <div class="w-75 pull-left">
                                            <textarea rows="1" class="form-control" id="messageid_`+data.sopedit.user_id+`" name="message" placeholder="Message"></textarea>
                                        </div>
                                        <div class="w-25 pull-left">
                                            <button class="btn btn-xs send-message-open pull-left" data-user_id="`+data.sopedit.user_id+`">
                                                <i class="fa fa-paper-plane"></i>
                                            </button>
                                             <button type="button"
                                                    class="btn btn-xs load-communication-modal pull-left"
                                                    data-id="`+data.sopedit.user_id+`" title="Load messages"
                                                    data-object="SOP">
                                                    <i class="fa fa-comments"></i>
                                            </button>
                                        </div>
                                   </div>
                                </td>
                                <td>`+data.only_date+`</td>
                                <td class="p-1">
                                    <a href="javascript:;" data-id="`+data.sopedit.id+`" class="menu_editor_edit btn btn-xs p-2" >
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a class="btn btn-image deleteRecord p-2 text-secondary" data-id="`+data.sopedit.id+`">
                                        <i class="fa fa-trash" ></i>
                                    </a>
                                    <a class="btn btn-xs view_log p-2 text-secondary" title="status-log"
                                        data-name="`+data.params.header_name+`"
                                        data-id="`+data.sopedit.id+`" data-toggle="modal" data-target="#ViewlogModal">
                                        <i class="fa fa-info-circle"></i>
                                    </a>
                                    <a title="Download Invoice" class="btn btn-xs p-2" href="sop/DownloadData/`+data.sopedit.id+`">
                                            <i class="fa fa-download downloadpdf"></i>
                                    </a>
                                    <button type="button" class="btn send-email-common-btn p-2" data-toemail="`+data.user_email[0].email+`" data-object="Sop" data-id="`+data.sopedit.user_id+`">
                                        <i class="fa fa-envelope-square"></i>
                                    </button>
                                    <button data-target="#Sop-User-Permission-Modal" data-toggle="modal" class="btn btn-secondaryssss sop-user-list  p-2" title="Sop User" data-sop_id="`+data.sopedit.user_id+`">
                                        <i class="fa fa-user-o"></i>
                                    </button>
                                </td>
                        </tr>
                        `);

                $("#menu_sopupdate").modal("hide");
                toastr["success"]("Data Updated Successfully!", "Message")
            }


        }).fail(function(data) {
            console.log(data);
        });
    });

    $(document).on("click", ".menu-sop-search", function(e) {
        e.preventDefault();
        $("#menu-sop-search-model").modal("show");
    });

    $(document).on("click", ".menu-email-search", function(e) {
        e.preventDefault();
        $("#menu-email-search-model").modal("show");
    });

    $(document).on("click", ".sop_search_menu", function(e) {
        let $this = $('#menu_sop_search').val();
        var q = $this;
        $.ajax({
            url: '{{route('menu.sop.search')}}',
            type: 'GET',
            data: {
                search: q,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            // dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(response) {
                $("#loading-image").hide();
                $('.sop_search_result').empty();
                $('.sop_search_result').append(response);
                toastr['success']('Data updated successfully', 'success');
            },
            error: function() {
                $("#loading-image").hide();
                toastr["Error"]("An error occured!");
            }
        });
    });

    $(document).on("click", ".email_search_menu", function(e) {
        let $this = $('#menu_email_search').val();
        var q = $this;
        $.ajax({
            url: '{{route('menu.email.search')}}',
            type: 'GET',
            data: {
                search: q,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            // dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(response) {
                $("#loading-image").hide();
                $('.email_search_result').empty();
                $('.email_search_result').append(response);
                toastr['success']('Data updated successfully', 'success');
            },
            error: function() {
                $("#loading-image").hide();
                toastr["Error"]("An error occured!");
            }
        });
    });

    $(document).on('click', '.send-message-open-menu', function (event) {
        var thiss = $(this);
        var $this = $(this);
        var data = new FormData();
        var sop_user_id = $(this).data('user_id');
        var id = $(this).data('id');
        var sop_user_id = $('#user_'+id).val();
        var message = $(this).parents('td').find("#messageid_"+id).val();

        if (message.length > 0) {
            //  let self = textBox;
            $.ajax({
                url: "{{action([\App\Http\Controllers\WhatsAppController::class, 'sendMessage'], 'SOP-Data')}}",
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

    $(document).on('hidden.bs.modal', '#chat-list-history', function() {
        $('body').removeClass('openmodel');
    });
    $(document).on('shown.bs.modal', '#chat-list-history', function() {
        $('body').addClass('openmodel');
    });

    $(document).on('change', '.sop_drop_down', function() {
        var val = $(this).val();
        if ($(this).val() == "knowledge_base") {
            $(this).parents('.add_sop_modal').find('.knowledge_base').removeAttr('hidden');
        } else {
            $(this).parents('.add_sop_modal').find('.knowledge_base').attr('hidden', true).val('');
            $(this).parents('.add_sop_modal').find('.knowledge_base_book').attr('hidden', true).val('');
        }
    })

    $(document).on("keyup", ".app-search-table", function (e) {
        var keyword = $(this).val();
        table = document.getElementById("database-table-list1");
        tr = table.getElementsByTagName("tr");
        // Loop through all table rows, and hide those who don't match the search query
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[1];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.indexOf(keyword) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    });

    $(document).on("click", ".btn-task-search-menu", function (e) {
        var keyword = $('.task-search-table').val();
        var selectedValues = [];

        $.ajax({
            url: '{{route('task.module.search')}}',
            type: 'GET',
            data: {
                term: keyword,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            // dataType: 'json',
            beforeSend: function () {
                $("#loading-image").show();
            },
            success: function (response) {
                $("#loading-image").hide();
                $('.show-search-task-list').html(response);
            },
            error: function () {
                $("#loading-image").hide();
                toastr["Error"]("An error occured!");
            }
        });
    });

    $(document).on("click", ".btn-dev-task-search-menu", function (e) {
        var keyword = $('.dev-task-search-table').val();
        var selectedValues = [];

        $.ajax({
            url: '{{route('devtask.module.search')}}',
            type: 'GET',
            data: {
                subject: keyword,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            // dataType: 'json',
            beforeSend: function () {
                $("#loading-image").show();
            },
            success: function (response) {
                $("#loading-image").hide();
                $('.show-search-dev-task-list').html(response);
            },
            error: function () {
                $("#loading-image").hide();
                toastr["Error"]("An error occured!");
            }
        });
    });

    $(document).on('change', '.assign-user-menu', function () {
        let id = $(this).attr('data-id');
        let userId = $(this).val();

        if (userId == '') {
            return;
        }

        $.ajax({
            url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'assignUser']) }}",
            data: {
                assigned_to: userId,
                issue_id: id
            },
            success: function () {
                toastr["success"]("User assigned successfully!", "Message")
            },
            error: function (error) {
                toastr["error"](error.responseJSON.message, "Message")

            }
        });

    });

    $(document).on('click', '.expand-row-msg-menu', function () {
        var id = $(this).data('id');
        var full = '.expand-row-msg-menu .td-full-container-' + id;
        var mini = '.expand-row-msg-menu .td-mini-container-' + id;
        $(full).toggleClass('hidden');
        $(mini).toggleClass('hidden');
    });

    $(document).on('click', '.send-message-open-quick-menu', function (event) {
        var textBox = $(this).closest(".communication-td").find(".send-message-textbox");
        var sendToStr = $(this).closest(".communication-td").next().find(".send-message-number").val();
        let issueId = textBox.attr('data-id');
        let message = textBox.val();
        if (message == '') {
            return;
        }

        let self = textBox;

        $.ajax({
            url: "{{ action([\App\Http\Controllers\WhatsAppController::class, 'sendMessage'], 'issue') }}",
            type: 'POST',
            data: {
                "issue_id": issueId,
                "message": message,
                "sendTo": sendToStr,
                "_token": "{{ csrf_token() }}",
                "status": 2
            },
            dataType: "json",
            success: function (response) {
                toastr["success"]("Message sent successfully!", "Message");
                $('#message_list_' + issueId).append('<li>' + response.message.created_at + " : " +
                    response.message.message + '</li>');
                $(self).removeAttr('disabled');
                $(self).val('');
            },
            beforeSend: function () {
                $(self).attr('disabled', true);
            },
            error: function () {
                alert('There was an error sending the message...');
                $(self).removeAttr('disabled', true);
            }
        });
    });

    $(document).on("click", ".btn-file-upload-menu", function() {
        var $this = $(this);
        var task_id = $this.data("id");
        $("#menu-file-upload-area-section").modal("show");
        $("#hidden-task-id").val(task_id);
        $("#loading-image").hide();
    });

    $(document).on('change', '.menu-task-assign-user', function() {
        let id = $(this).attr('data-id');
        let userId = $(this).val();
        if (userId == '') {
            return;
        }
        $.ajax({
            url: "{{route('task.AssignTaskToUser')}}",
            data: {
                user_id: userId,
                issue_id: id
            },
            success: function() {
                toastr["success"]("User assigned successfully!", "Message")
            },
            error: function(error) {
                toastr["error"](error.responseJSON.message, "Message")
            }
        });
    });

    $(document).on("click", ".menu-upload-document-btn", function () {
        var id = $(this).data("id");
        $("#menu-upload-document-modal").find("#hidden-identifier").val(id);
        $("#menu-upload-document-modal").modal("show");
    });

    $(document).on('click', '.menu-show-user-history', function() {
        var issueId = $(this).data('id');
        $('#user_history_div table tbody').html('');
        $.ajax({
            url: "{{ route('task/user/history') }}",
            data: {
                id: issueId
            },
            success: function(data) {
                $.each(data.users, function(i, item) {
                    $('#user_history_div table tbody').append(
                        '<tr>\
                                <td>' + moment(item['created_at']).format('DD/MM/YYYY') + '</td>\
                                    <td>' + ((item['user_type'] != null) ? item['user_type'] : '-') + '</td>\
                                    <td>' + ((item['old_name'] != null) ? item['old_name'] : '-') + '</td>\
                                    <td>' + ((item['new_name'] != null) ? item['new_name'] : '-') + '</td>\
                                    <td>' + item['updated_by'] + '</td>\
                                </tr>'
                    );
                    $("#menu_user_history_modal").css('z-index','-1');
                });
            }
        });
        $('#menu_user_history_modal').modal('show');
    });

    $(document).on('click', '.menu-send-message', function() {
        var thiss = $(this);
        var data = new FormData();
        var task_id = $(this).data('taskid');
        // var message = $(this).siblings('input').val();
        if ($(this).hasClass("onpriority")) {
            var message = $('#getMsgPopup' + task_id).val();
        } else {
            var message = $('#getMsg' + task_id).val();
        }
        if (message != "") {
            $("#message_confirm_text").html(message);
            $("#confirm_task_id").val(task_id);
            $("#confirm_message").val(message);
            $("#confirm_status").val(1);
            $("#menu_confirmMessageModal").modal();
        }
    });

    $(document).on('click', '.menu-confirm-messge-button', function() {
        var thiss = $(this);
        var data = new FormData();
        var task_id = $("#confirm_task_id").val();
        var message = $("#confirm_message").val();
        var status = $("#confirm_status").val();
        //    alert(message)
        data.append("task_id", task_id);
        data.append("message", message);
        data.append("status", status);
        // var checkedValue = $('.send_message_recepients:checked').val();
        var checkedValue = [];
        var i = 0;
        $('.send_message_recepients:checked').each(function() {
            checkedValue[i++] = $(this).val();
        });
        data.append("send_message_recepients", checkedValue);
        //  console.log(checkedValue);
        if (message.length > 0) {
            if (!$(thiss).is(':disabled')) {
                $.ajax({
                    //  url: '/whatsapp/sendMessage/task',
                    url: "{{ route('whatsapp.send','task')}}",
                    type: 'POST',
                    "dataType": 'json', // what to expect back from the PHP script, if anything
                    "cache": false,
                    "contentType": false,
                    "processData": false,
                    "data": data,
                    beforeSend: function() {
                        $(thiss).attr('disabled', true);
                    }
                }).done(function(response) {
                    $(thiss).siblings('input').val('');
                    $('#getMsg' + task_id).val('');
                    $('#menu_confirmMessageModal').modal('hide');
                    toastr["success"]("Message sent successfully!", "Message");
                    if (cached_suggestions) {
                        suggestions = JSON.parse(cached_suggestions);
                        if (suggestions.length == 10) {
                            suggestions.push(message);
                            suggestions.splice(0, 1);
                        } else {
                            suggestions.push(message);
                        }
                        localStorage['message_suggestions'] = JSON.stringify(suggestions);
                        cached_suggestions = localStorage['message_suggestions'];
                    } else {
                        suggestions.push(message);
                        localStorage['message_suggestions'] = JSON.stringify(suggestions);
                        cached_suggestions = localStorage['message_suggestions'];
                    }
                    $(thiss).attr('disabled', false);
                }).fail(function(errObj) {
                    $('#menu_confirmMessageModal').modal('hide');
                    $(thiss).attr('disabled', false);
                    alert("Could not send message");
                });
            }
        } else {
            alert('Please enter a message first');
        }
    });

    $(document).on("submit", "#menu-upload-task-documents", function (e) {
        e.preventDefault();
        var form = $(this);
        var postData = new FormData(form[0]);
        $.ajax({
            method: "post",
            url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'uploadDocument']) }}",
            data: postData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (response) {
                if (response.code == 200) {
                    toastr["success"]("Status updated!", "Message")
                    $("#menu-upload-document-modal").modal("hide");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    $(document).on("click", ".menu-list-document-btn", function () {
        var id = $(this).data("id");
        $.ajax({
            method: "GET",
            url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'getDocument']) }}",
            data: {
                id: id
            },
            dataType: "json",
            success: function (response) {
                if (response.code == 200) {
                    $("#menu-blank-modal").find(".modal-title").html("Document List");
                    $("#menu-blank-modal").find(".modal-body").html(response.data);
                    $("#menu-blank-modal").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    $(document).on("click", ".menu-btn-save-documents", function(e) {
        e.preventDefault();
        var $this = $(this);
        var formData = new FormData($this.closest("form")[0]);
        $.ajax({
            url: '/task/save-documents',
            type: 'POST',
            enctype: 'multipart/form-data',
            // contentType: 'multipart/form-data',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(data) {
            $("#loading-image").hide();
            if (data.code == 500) {
                toastr["error"](data.message);
            } else {
                toastr["success"]("Document uploaded successfully");
                //location.reload();
            }
        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            toastr["error"](jqXHR.responseJSON.message);
            $("#loading-image").hide();
        });
    });

    $(document).on('change', '.choose-username', function() {
        var val = $(this).val();
        var db =$('.choose-db').val();
        $('.app-database-user-id').val(val);
        $('.btn-database-add').attr('data-id',val);
        $('.btn-delete-database-access').attr('data-id',val);
        $('.btn-delete-database-access').attr('data-connection',db);
        $('.btn-assign-permission').attr('data-id',val);
        var database_user_id = val;
        var url = '{{ route("user-management.get-database", ":id") }}';
        url = url.replace(':id', database_user_id);

        $.ajax({
            url: url ,
            type: 'GET',
            data: {
                id: database_user_id,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            // dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(response) {
                $("#loading-image").hide();
                if (response.code == 200) {
                    $('.database_password').val(response.data.password);
                    console.log(response.data.password);
                    if(response.data.password)
                    {
                        $('.btn-delete-database-access').removeClass('d-none');
                    }else{
                        $('.btn-delete-database-access').addClass('d-none');
                    }
                    var aa = '';
                    $('.menu_tbody').html('');
                    $.each(response.data.tables, function(i, record) {
                        var checkvalue = '';
                        if(record.checked)
                        {
                            checkvalue = 'checked';
                        }

                        aa += '<tr role="row"><td><input type="checkbox" name="tables[]" value='+record.table+' '+checkvalue+'></td><td>'+record.table+'</td></tr>';
                    });
                    $('.menu_tbody').html(aa);
                } else {
                    toastr['error'](response.message, 'error');
                }
            },
            error: function() {
                $("#loading-image").hide();
                toastr["Error"]("An error occured!");
            }
        });

    })

    $(document).on('change', '.knowledge_base', function() {
        var val = $(this).val();
        if ($(this).val() == "chapter" || $(this).val() == "page") {
            $(this).parents('.add_sop_modal').find('.knowledge_base_book').removeAttr('hidden');
        } else {
            $(this).parents('.add_sop_modal').find('.knowledge_base_book').attr('hidden', true).val('');
        }
    })

    $(document).on('change', '.knowledge_base_book', function() {
        var val = $(this).val();
        if (val.length > 0) {
            $(this).parents('#createShortcutForm').find('.books_error').text('');
        }
    })

    $(document).on('click', '.create_shortcut_submit', function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var formdata = $('#createShortcutForm').serialize();
        var val = $(this).parents('#createShortcutForm').find('.knowledge_base').val();
        var chatID = $(this).parents('#createShortcutForm').find('[name="chat_message_id"]').val();
        var name = $(this).parents('#createShortcutForm').find('[name="name"]').val();
        var category = $(this).parents('#createShortcutForm').find('[name="category"]').val();
        var content = $(this).parents('#createShortcutForm').find('[name="description"]').text();
        var book_name = $(this).parents('#createShortcutForm').find('.knowledge_base_book').val();
        if (val.length === 0) {
            $.ajax({
                type: "POST",
                url: "{{ route('shortcut.sop.create') }}",
                data: formdata,
                success: function(response) {
                    toastr.success('Sop Added Successfully');
                    $('#Create-Sop-Shortcut').modal('hide');
                }
            })
        }
        if (val == "book") {
            $.ajax({
                type: "POST",
                url: `/kb/books`,
                data: formdata,
                success: function(response) {
                    toastr.success('Book Added Successfully');
                    $('#Create-Sop-Shortcut').modal('hide');
                }
            })
        }
        if (val == "chapter") {
            if (book_name.length == 0) {
                $(this).parents('#createShortcutForm').find('.books_error').text('Please select Book');
                return;
            }
            $.ajax({
                type: "POST",
                url: `/kb/books/${book_name}/create-chapter`,
                data: formdata,
                success: function(response) {
                    toastr.success('Chapter Added Successfully');
                    $('#Create-Sop-Shortcut').modal('hide');
                }
            })
        }
        if (val == "page") {
            if (book_name.length == 0) {
                $(this).parents('#createShortcutForm').find('.books_error').text('Please select Book');
                return;
            }
            $.ajax({
                type: "get",
                url: `kb/books/${book_name}/create-page`,
                data: formdata,
                success: function(response) {
                    console.log(response, '======')
                    toastr.success('Page Added Successfully');
                    $('#Create-Sop-Shortcut').modal('hide');
                }
            })
        }
        if (val == "shelf") {
            $.ajax({
                type: "POST",
                url: `/kb/shelves/${name}/add`,
                data: formdata,
                success: function(response) {
                    toastr.success('Bookshelf Added Successfully');
                    $('#Create-Sop-Shortcut').modal('hide');
                }
            })
        }
    })

    $(document).on('click', '.system-request', function() {
        loadUsersList();
    })
    $(document).on("click", ".addIp", function(e) {
        e.preventDefault();
        if ($('input[name="add-ip"]').val() != '') {
            if ($('#ipusers').val() === '') {
                alert('Please select User OR Other from list.');
            }
            else if($('#ipusers').val() === 'other' && $('input[name="other_user_name"]').val()==='')
            {
                alert('Please enter other name.');
            }
            else{
                $.ajax({
                url: '/users/add-system-ip',
                type: 'GET',
                data: {
                    _token: "{{ csrf_token() }}",
                    ip: $('input[name="add-ip"]').val(),
                    user_id: $('#ipusers').val(),
                    other_user_name: $('input[name="other_user_name"]').val(),
                    comment: $('input[name="ip_comment"]').val()
                },
                dataType: 'json',
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(result) {
                    $("#loading-image").hide();
                    toastr["success"]("IP added successfully");
                    location.reload();
                },
                error: function() {
                    $("#loading-image").hide();
                    toastr["Error"]("An error occured!");
                }
            });
            }

        } else {
            alert('please enter IP');
        }
    });

    $(document).on("click", ".btn-database-add", function(e) {
        e.preventDefault();
        // var ele = this;
        var connection = $('.choose-db').val();
        var username = $('.choose-username').find(':selected').attr('data-name');
         username = username.replace(/ /g,"_").toLowerCase();
        var password = $('.database_password').val();
        var database_user_id = $(this).data("id");
        var url = '{{ route("user-management.create-database", ":id") }}';
        url = url.replace(':id', database_user_id);

        $.ajax({
                url: url ,
                type: 'POST',
                data: {
                    database_user_id: database_user_id,
                    connection: connection,
                    username: username,
                    password: password,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // dataType: 'json',
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    $("#loading-image").hide();
                    if (response.code == 200) {
                        toastr['success'](response.message, 'success');
                    } else {
                        toastr['error'](response.message, 'error');
                    }
                },
                error: function() {
                    $("#loading-image").hide();
                    toastr["Error"]("An error occured!");
                }
            });
    });

    $(document).on("click", ".btn-assign-permission", function(e) {
        e.preventDefault();
        // var ele = this;
        var connection = $('.choose-db').val();
        var assign_permission = $('.assign-permission-type').find(':selected').val();
        var search = $('.app-search-table').val();
        var tables = $('.database_password').val();
        var checked = []
        $("input[name='tables[]']:checked").each(function ()
        {
            checked.push($(this).val());
        });

        var database_user_id = $('#database-user-id').val();
        if(database_user_id == '')
        {
            toastr['error']('Please select the user first', 'error');
            return false
        }
        var url = '{{ route("user-management.assign-database-table", ":id") }}';
        url = url.replace(':id', database_user_id);

        $.ajax({
            url: url ,
            type: 'POST',
            data: {
                database_user_id: database_user_id,
                connection: connection,
                search: search,
                assign_permission: assign_permission,
                tables: checked,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            // dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(response) {
                $("#loading-image").hide();
                if (response.code == 200) {
                    toastr['success'](response.message, 'success');
                } else {
                    toastr['error'](response.message, 'error');
                }
            },
            error: function() {
                $("#loading-image").hide();
                toastr["Error"]("An error occured!");
            }
        });
    });

    $(document).on("click", ".btn-delete-database-access", function(e) {
        e.preventDefault();
        if (!confirm("Are you sure you want to remove access for this user?")) {
            return false;
        } else {
            var connection = $('.choose-db').val();
            var database_user_id = $('#database-user-id').val();
            if (database_user_id == '') {
                toastr['error']('Please select the user first', 'error');
                return false
            }
            var url = '{{ route("user-management.delete-database-access", ":id") }}';
            url = url.replace(':id', database_user_id);

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    connection: connection,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // dataType: 'json',
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function (response) {
                    $("#loading-image").hide();
                    if (response.code == 200) {
                        toastr['success'](response.message, 'success');
                        $("#menu-create-database-model").modal("hide");
                    } else {
                        toastr['error'](response.message, 'error');
                        $("#menu-create-database-model").modal("hide");
                    }
                },
                error: function () {
                    $("#loading-image").hide();
                    toastr["Error"]("An error occured!");
                }
            });
        }
    });

    $(document).ready(function() {
        $('#ipusers').change(function() {
            var selected = $(this).val();
            if (selected == 'other') {
                $('#other_user_name').show();
            } else {
                $('#other_user_name').hide();
            }
        });
    });
    $(document).on("click", ".deleteIp", function(e) {
        e.preventDefault();
        var btn = $(this);
        $.ajax({
            url: '/users/delete-system-ip',
            type: 'GET',
            data: {
                _token: "{{ csrf_token() }}",
                usersystemid: $(this).data('usersystemid')
            },
            dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(result) {
                btn.parents('tr').remove();
                $("#loading-image").hide();
                toastr["success"]("IP Deteted successfully");
            },
            error: function() {
                $("#loading-image").hide();
                toastr["Error"]("An error occured!");
            }
        });
    });
    $(document).on("click", ".bulkDeleteIp", function(e) {
        e.preventDefault();
        var btn = $(this);
        if(confirm('Are you sure you want to perform this Action?') == false)
        {
            return false;
        }
        $.ajax({
            url: '/users/bulk-delete-system-ip',
            type: 'GET',
            data: {
                _token: "{{ csrf_token() }}",
            },
            dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(result) {
                $("#userAllIps").empty();
                $("#loading-image").hide();
                toastr["success"]("IPs Deteted successfully");
            },
            error: function() {
                $("#loading-image").hide();
                toastr["Error"]("An error occured!");
            }
        });
    });
    function loadUsersList() {
        var t = "";
        var ip = "";
        $.ajax({
            url: '{{ route("get-user-list") }}',
            type: 'GET',
            data: {
                _token: "{{ csrf_token() }}",
            },
            dataType: 'json',
            success: function(result) {


                 const arr = object.entries(result.data)?.sort((a,b) => a[1] - b[1]);

                t += '<option value="">Select user</option>';
                arr.forEach(([key,value]) => {
                    t+=`<option value="${key}">${value}</option>`
                })
                //$.each(arr, function([key, value], j) {
                 //   console.log('index->', i , 'j index', j );
                 //   t += '<option value="' + i + '">' + j + '</option>'
                //});
                t += '<option value="other">Other</option>';
                // console.log(t);
                $("#ipusers").html(t);
                console.log(result.usersystemips);
                $.each(result.usersystemips, function(k, v) {
                    ip += '<tr>';
                    ip += '<td> ' + v.index_txt + ' </td>';
                    ip += '<td> ' + v.ip + '</td>';
                    ip += '<td>' +( (v.user!=null) ? v.user.name : v.other_user_name )+ '</td>';
                    ip += '<td> ' + v.source + '</td>';
                    ip += '<td>' + v.notes + '</td>';
                    ip += '<td><button class="btn-warning btn deleteIp" data-usersystemid="' + v
                        .id + '">Delete</button></td>';
                    ip += '</tr>';
                });
                $("#userAllIps").html(ip);
            },
            error: function() {
                // alert('fail');
            }
        });
    }
    </script>

    @stack('scripts')

    <script>
    $(document).ready(function() {
        //$.cookie('auto_refresh', '0', { path: '/{{ Request::path() }}' });

        var autoRefresh = $.cookie('auto_refresh');
        if (typeof autoRefresh == "undefined" || autoRefresh == 1) {
            $(".auto-refresh-run-btn").attr("title", "Stop Auto Refresh");
            $(".auto-refresh-run-btn").find("i").removeClass("refresh-btn-stop").addClass("refresh-btn-start");
        } else {
            $(".auto-refresh-run-btn").attr("title", "Start Auto Refresh");
            $(".auto-refresh-run-btn").find("i").removeClass("refresh-btn-start").addClass("refresh-btn-stop");
        }
        //auto-refresh-run-btn

        $(document).on("click", ".auto-refresh-run-btn", function() {
            let autoRefresh = $.cookie('auto_refresh');
            if (autoRefresh == 0) {
                alert("Auto refresh has been enable for this page");
                $.cookie('auto_refresh', '1', {
                    path: '/{{ Request::path() }}'
                });
                $(".auto-refresh-run-btn").find("i").removeClass("refresh-btn-stop").addClass(
                    "refresh-btn-start");
            } else {
                alert("Auto refresh has been disable for this page");
                $.cookie('auto_refresh', '0', {
                    path: '/{{ Request::path() }}'
                });
                $(".auto-refresh-run-btn").find("i").removeClass("refresh-btn-start").addClass(
                    "refresh-btn-stop");
            }
        });

        $('#editor-note-content').richText();
        $('#editor-instruction-content').richText();

        $('#editor-notes-content').richText(); //Purpose : Add Text content - DEVTASK-4289

        $('#notification-date').datetimepicker({
            format: 'YYYY-MM-DD'
        });

        $('#notification-time').datetimepicker({
            format: 'HH:mm'
        });

        $('#repeat_end').datetimepicker({
            format: 'YYYY-MM-DD'
        });

        $(".selectx-vendor").select2({
            tags: true
        });
        $(".selectx-users").select2({
            tags: true
        });
    });
    window.token = "{{ csrf_token() }}";

    var url = window.location;
    window.collectedData = [{
            type: 'key',
            data: ''
        },
        {
            type: 'mouse',
            data: []
        }
    ];

    $(document).keypress(function(event) {
        var x = event.charCode || event.keyCode; // Get the Unicode value
        var y = String.fromCharCode(x);
        collectedData[0].data += y;
    });

    // started for help button
    $('.help-button').on('click', function() {
        $('.help-button-wrapper').toggleClass('expanded');
        $('.page-notes-list-rt').toggleClass('dis-none');
    });

    $('.instruction-button').on('click', function() {
        $("#quick-instruction-modal").modal("show");
        //$('.help-button-wrapper').toggleClass('expanded');
        //$('.instruction-notes-list-rt').toggleClass('dis-none');
    });

    //START - Purpose : Open Modal - DEVTASK-4289
    $('.create_notes_btn').on('click', function() {
        $("#quick_notes_modal").modal("show");
    });

    $('.btn_save_notes').on('click', function(e) {
        e.preventDefault();
        var data = $('#editor-notes-content').val();

        if ($(data).text() == '') {
            toastr['error']('Note Is Required');
            return false;
        }


        var url = window.location.href;
        $.ajax({
            type: "POST",
            url: "{{ route('notesCreate') }}",
            data: {
                data: data,
                url: url,
                _token: "{{ csrf_token() }}",
            },
            dataType: "json",
            success: function(data) {
                if (data.code == 200) {
                    toastr['success'](data.message, 'success');
                    $("#quick_notes_modal").modal("hide");
                }

            },
            error: function(xhr, status, error) {

            }
        });
    });
    //END - DEVTASK-4289

    $('.notification-button').on('click', function() {
        $("#quick-user-event-notification-modal").modal("show");
    });

    $('select[name="repeat"]').on('change', function() {
        $(this).val() == 'weekly' ? $('#repeat_on').removeClass('hide') : $('#repeat_on').addClass('hide');
    });

    $('select[name="ends_on"]').on('change', function() {
        $(this).val() == 'on' ? $('#repeat_end_date').removeClass('hide') : $('#repeat_end_date').addClass(
            'hide');
    });

    $('select[name="repeat"]').on('change', function() {
        $(this).val().length > 0 ? $('#ends_on').removeClass('hide') : $('#ends_on').addClass('hide');
    });

    $(document).on("submit", "#notification-submit-form", function(e) {
        e.preventDefault();
        var $form = $(this).closest("form");
        $.ajax({
            type: "POST",
            url: $form.attr("action"),
            data: $form.serialize(),
            dataType: "json",
            success: function(data) {
                if (data.code == 200) {
                    $form[0].reset();
                    $("#quick-user-event-notification-modal").modal("hide");
                    toastr['success'](data.message, 'Message');
                } else {
                    toastr['error'](data.message, 'Message');
                }
            },
            error: function(xhr, status, error) {
                var errors = xhr.responseJSON;
                $.each(errors, function(key, val) {
                    $("#" + key + "_error").text(val[0]);
                });
            }
        });
    });

    //setup before functions
    var typingTimer; //timer identifier
    var doneTypingInterval = 5000; //time in ms, 5 second for example
    var $input = $('#editor-instruction-content');
    //on keyup, start the countdown
    $input.on('keyup', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(doneTyping, doneTypingInterval);
    });

    //on keydown, clear the countdown
    $input.on('keydown', function() {
        clearTimeout(typingTimer);
    });

    //user is "finished typing," do something
    function doneTyping() {
        //do something
    }

    // started for chat button
    // open chatbox now into popup

    var chatBoxOpen = false;

    $("#message-chat-data-box").on("click", function(e) {
        e.preventDefault();
        $("#quick-chatbox-window-modal").modal("show");
        chatBoxOpen = true;
        openChatBox(true);
    });

    $('#quick-chatbox-window-modal').on('hidden.bs.modal', function() {
        chatBoxOpen = false;
        openChatBox(false);
    });

    $('.chat_btn').on('click', function(e) {
        e.preventDefault();
        $("#quick-chatbox-window-modal").modal("show");
        chatBoxOpen = true;
        openChatBox(true);
    });

    // $('.chat-button').on('click', function () {
    //     $('.chat-button-wrapper').toggleClass('expanded');
    //     $('.page-chat-list-rt').toggleClass('dis-none');
    //     if($('.chat-button-wrapper').hasClass('expanded')){
    //         chatBoxOpen = true;
    //         openChatBox(true);
    //     }else{
    //         chatBoxOpen = false;
    //         openChatBox(false);
    //     }
    // });

    var notesBtn = $(".save-user-notes");

    notesBtn.on("click", function(e) {
        e.preventDefault();
        var $form = $(this).closest("form");
        $.ajax({
            type: "POST",
            url: $form.attr("action"),
            data: {
                _token: window.token,
                note: $form.find("#note").val(),
                category_id: $form.find("#category_id").val(),
                url: "<?php echo request()->url() ?>"
            },
            dataType: "json",
            success: function(data) {
                if (data.code > 0) {
                    $form.find("#note").val("");
                    var listOfN = "<tr>";
                    listOfN += "<td scope='row'>" + data.notes.id + "</td>";
                    listOfN += "<td>" + data.notes.note + "</td>";
                    listOfN += "<td>" + data.notes.category_name + "</td>";
                    listOfN += "<td>" + data.notes.name + "</td>";
                    listOfN += "<td>" + data.notes.created_at + "</td>";
                    listOfN += "</tr>";

                    $(".page-notes-list").prepend(listOfN);
                }
            },
        });
    });

    @if(session()->has('encrpyt'))

    var inactivityTime = function() {
        var time;
        window.onload = resetTimer;
        // DOM Events
        document.onmousemove = resetTimer;
        document.onkeypress = resetTimer;

        function remove_key() {
            $.ajax({
                    url: "{{ route('encryption.forget.key') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        private: '1',
                        "_token": "{{ csrf_token() }}",
                    },
                })
                .done(function() {
                    alert('Please Insert Private Key');
                    location.reload();
                    console.log("success");
                })
                .fail(function() {
                    console.log("error");
                })
        }

        function resetTimer() {
            clearTimeout(time);
            time = setTimeout(remove_key, 1200000);
            // 1000 milliseconds = 1 second
        }
    };

    window.onload = function() {
        inactivityTime();
    }

    @endif

    var getNotesList = function() {
        //$.ajax({
        //            type: "GET",
        //          url: "/page-notes/list",
        //        data: {
        //          _token: window.token,
        //        url: "<?php echo request()->url() ?>"
        //  },
        //            dataType: "json",
        //          success: function (data) {
        //            if (data.code > 0) {
        //              var listOfN = "";
        //            $.each(data.notes, function (k, v) {
        //              listOfN += "<tr>";
        //            listOfN += "<td scope='row'>" + v.id + "</td>";
        //          listOfN += "<td>" + v.note + "</td>";
        //        listOfN += "<td>" + v.category_name + "</td>";
        //      listOfN += "<td>" + v.name + "</td>";
        //    listOfN += "<td>" + v.created_at + "</td>";
        //  listOfN += "</tr>";
        //                    });
        //
        //                  $(".page-notes-list").prepend(listOfN);
        //            }
        //      },
        //});
    }

    if ($(".help-button-wrapper").length > 0) {
        getNotesList();
    }


    // $(document).click(function() {
    //     if (collectedData[0].data.length > 10) {
    //         let data_ = collectedData[0].data;
    //         let type_ = collectedData[0].type;
    //
    //         $.ajax({
    //             url: "/track",
    //             type: 'post',
    //             csrf: token,
    //             data: {
    //                 url: url,
    //                 item: type_,
    //                 data: data_
    //             }
    //         });
    //     }
    // });
    @if(Auth::check())
    $(document).ready(function() {
        var url = window.location.href;
        var user_id = "{{ Auth::id() }}";
        user_name = "{{ Auth::user()->name }}";
        $.ajax({
            type: "POST",
            url: "/api/userLogs",
            data: {
                "_token": "{{ csrf_token() }}",
                "url": url,
                "user_id": user_id,
                "user_name": user_name
            },
            dataType: "json",
            success: function(message) {}
        });
    });
    @endif
    </script>
    @if ( !empty($_SERVER['HTTP_HOST']) && !empty($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] != "127.0.0.1" &&
    !stristr($_SERVER['HTTP_HOST'], '.mac') )
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $account_id }}"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());
    //gtag('config', 'UA-171553493-1');
    </script>
    @endif
    <script>
    <?php
if (!\Auth::guest()) {
    $path = Request::path();
    $hasPage = \App\AutoRefreshPage::where("page", $path)->where("user_id", \Auth()->user()->id)->first();
    if ($hasPage) {
        ?>

    var idleTime = 0;

    function reloadPageFun() {
        idleTime = idleTime + 1000;
        var autoRefresh = $.cookie('auto_refresh');
        if (idleTime > <?php echo $hasPage->time * 1000; ?> && (typeof autoRefresh == "undefined" || autoRefresh ==
                1)) {
            window.location.reload();
        }
    }

    $(document).ready(function() {
        //Increment the idle time counter every minute.
        setInterval(function() {
            reloadPageFun()
        }, 3000);
        //Zero the idle timer on mouse movement.
        $(this).mousemove(function(e) {
            idleTime = 0;
        });
        $(this).keypress(function(e) {
            idleTime = 0;
        });
    });

    <?php }}?>

    function filterFunction() {
        var input, filter, ul, li, a, i;
        //getting search values
        input = document.getElementById("search");
        //String to upper for search
        filter = input.value.toUpperCase();

        //Getting Values From DOM
        a = document.querySelectorAll("#navbarSupportedContent a");
        //Class to open bar
        $("#search_li").addClass('open');
        //Close when search becomes zero
        if (a.length == 0) {
            $("#search_li").removeClass('open');
        }
        //Limiting Search Count
        count = 1;
        //Empty Existing Values
        $("#search_container").empty();

        //Getting All Values
        for (i = 0; i < a.length; i++) {
            txtValue = a[i].textContent || a[i].innerText;
            href = a[i].href;
            //If value doesnt have link
            if (href == "#" || href == '' || href.indexOf('#') > -1) {
                continue;
            }
            //Removing old search Result From DOM
            if (a[i].getAttribute('class') != null && a[i].getAttribute('class') != '') {
                if (a[i].getAttribute('class').indexOf('old_search') > -1) {
                    continue;
                }
            }
            //break when count goes above 30
            if (count > 30) {
                break;
            }
            //Pusing values to DOM Search Input
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                $("#search_container").append('<li class="nav-item dropdown dropdown-submenu"><a class="dropdown-item old_search" href=' + href + '>' + txtValue + '</a></li>');
                count++
            } else {

            }
        }

        if(filter.length == 0)
        {
            $("#search_container").empty();
            $("#search_li").removeClass('open');
        }
    }

    $(document).on('change', '#autoTranslate', function(e) {
        e.preventDefault();
        var customerId = $("input[name='message-id']").val();
        var language = $(".auto-translate").val();
        let self = $(this);
        $.ajax({
            url: "/customer/language-translate/" + customerId,
            method: "PUT",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: customerId,
                language: language
            },
            cache: true,
            success: function(res) {
                $('.selectedValue option[value="' + language + '"]').prop('selected', true);
                alert(res.success);
            }
        })
    });

    $(document).ready(function() {
        $(window).scroll(function() {
            if ($(this).scrollTop() > 50) {
                $('#back-to-top').fadeIn();
            } else {
                $('#back-to-top').fadeOut();
            }
        });
        // scroll body to 0px on click
        $('#back-to-top').click(function() {
            $('body,html').animate({
                scrollTop: 0
            }, 400);
            return false;
        });

        $('#sidebarCollapse').on('click', function() {
            $('#sidebar').toggleClass('active');
        });
        $(".select2-vendor").select2({});

        @php
            $route = request()->route()->getName();
        @endphp
        @if (in_array($route, ["development.issue.index", "task.index", "development.summarylist", "chatbot.messages.list"]))
            $(".show-estimate-time").click(function (e) {
                e.preventDefault();
                var tasktype = $(this).data('task');
                $.ajax({
                    type: "GET",
                    url: "{{route('task.estimate.list')}}",
                    // data: {
                    //     task: tasktype
                    // },
                    success: function (response) {
                        $("#showLatestEstimateTime").modal('show');
                        $("#showLatestEstimateTime .modal-table").html(response);
                    },
                    error: function (error) {

                    }

                });
            });
            $("#shortcut-estimate-search").select2();

            $("#shortcut-estimate-search").change(function (e) {
                e.preventDefault();
                let task_id = $(this).val();
                @if ($route == "development.issue.index")
                    var  tasktype = "DEVTASK";
                @else
                    var tasktype = "TASK";
                @endif
                $.ajax({
                    type: "GET",
                    url: "{{route('task.estimate.list')}}",
                    data: {
                        task: tasktype,
                        task_id
                    },
                    success: function (response) {
                        $("#showLatestEstimateTime").modal('show');
                        $("#showLatestEstimateTime .modal-table").html(response);
                    },
                    error: function (error) {
                        toastr["error"]("Error while fetching data.");
                    }

                });
            });
        @endif

        $('#showLatestEstimateTime').on('hide.bs.modal', function (e) {
            $("#modalTaskInformationUpdates .modal-body .row").show()
            $("#modalTaskInformationUpdates .modal-body hr").show()
            // $("#modalTaskInformationUpdates .modal-body .row").eq(4).show()
            // $("#modalTaskInformationUpdates .modal-body hr").eq(4).show()
            // $("#modalTaskInformationUpdates .modal-body .row").eq(5).show()
            // $("#modalTaskInformationUpdates .modal-body .row").eq(6).show()
        })


        $(document).on("click", ".approveEstimateFromshortcutButton", function (event) {
            event.preventDefault();
            let type = $(this).data('type');
            let task_id = $(this).data('task');
            let history_id = $(this).data('id');
            // console.log(type,
            // task_id,
            // history_id);
            // return
            if (type == "TASK") {
                $.ajax({
                url: "/task/time/history/approve",
                type: "POST",
                data: {
                    _token: "{{csrf_token()}}",
                    approve_time: history_id,
                    developer_task_id: task_id,
                    user_id: 0
                },
                success: function (response) {
                    toastr["success"]("Successfully approved", "success");
                    $("#showLatestEstimateTime").modal("hide");
                },
                error: function (error) {
                    toastr["error"](error.responseJSON.message);
                },
                });
            } else {
                $.ajax({
                url: "/development/time/history/approve",
                type: "POST",
                data: {
                    _token: "{{csrf_token()}}",
                    approve_time: history_id,
                    developer_task_id: task_id,
                    user_id: 0
                },
                success: function (response) {
                    toastr["success"]("Successfully approved", "success");
                    $("#showLatestEstimateTime").modal("hide");
                },
                error: function (error) {
                    toastr["error"](error.responseJSON.message);
                },
                });
            }
        });
    });

    $(document).on('click', '.save-meeting-zoom', function() {
        var user_id = $('#quick_user_id').val();
        var meeting_topic = $('#quick_meeting_topic').val();
        var csrf_token = $('#quick_csrfToken').val();
        var meeting_url = $('#quick_meetingUrl').val();
        $.ajax({
            url: meeting_url,
            type: 'POST',
            success: function(response) {
                var status = response.success;
                if (false == status) {
                    toastr['error'](response.data.msg);
                } else {
                    $('#quick-zoomModal').modal('toggle');
                    window.open(response.data.meeting_link);
                    var html = '';
                    html += response.data.msg + '<br>';
                    html += 'Meeting URL: <a href="' + response.data.meeting_link +
                        '" target="_blank">' + response.data.meeting_link + '</a><br><br>';
                    html += '<a class="btn btn-primary" target="_blank" href="' + response.data
                        .start_meeting + '">Start Meeting</a>';
                    $('#qickZoomMeetingModal').modal('toggle');
                    $('.meeting_link').html(html);
                    toastr['success'](response.data.msg);
                }
            },
            data: {
                user_id: user_id,
                meeting_topic: meeting_topic,
                _token: csrf_token,
                user_type: "vendor"
            },
            beforeSend: function() {
                $(this).text('Loading...');
            }
        }).fail(function(response) {
            toastr['error'](response.responseJSON.message);

        });
    });

    $(document).on('change', '.task_for', function(e) {
        var getTask = $(this).val();
        if(getTask == 'time_doctor'){
            $('.time_doctor_project_section').show();
            $('.time_doctor_account_section').show();
        } else {
            $('.time_doctor_project_section').hide();
            $('.time_doctor_account_section').hide();
        }
    });

    $(document).on("click", ".save-task-window", function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        $.ajax({
            url: form.attr("action"),
            type: 'POST',
            data: form.serialize(),
            beforeSend: function() {
                $(this).text('Loading...');
            },
            success: function(response) {
                if (response.code == 200) {
                    form[0].reset();
                    toastr['success'](response.message);
                    $("#quick-create-task").modal("hide");
                    $("#auto-reply-popup").modal("hide");
                    $("#auto-reply-popup-form").trigger('reset');
                    location.reload();
                } else {
                    toastr['error'](response.message);
                }
            }
        }).fail(function(response) {
            toastr['error'](response.responseJSON.message);
        });
    });

    $('select.select2-discussion').select2({
        tags: true
    });

    $(document).on("change", ".type-on-change", function(e) {
        e.preventDefault();
        var task_type = $(this).val();
        console.log(task_type);
        if (task_type == 3) {
            // $('.normal-subject').hide();
            // $('.discussion-task-subject').show();
            $.ajax({
                url: '/task/get-discussion-subjects',
                type: 'GET',
                success: function(response) {
                    $('select.select2-discussion').select2({
                        tags: true
                    });
                    var option = '<option value="" >Select</option>';
                    $.each(response.discussion_subjects, function(i, item) {
                        console.log(item);

                        option = option + '<option value="' + i + '">' + item + '</option>';
                    });
                    $('.add-discussion-subjects').html(option);
                }
            }).fail(function(response) {
                toastr['error'](response.responseJSON.message);
            });
        } else {
            // $('select.select2-discussion').select2({tags: true});
            $("select.select2-discussion").empty().trigger('change');
        }


    });

    $(document).on('change', '#keyword_category', function() {
        console.log("inside");
        if ($(this).val() != "") {
            var category_id = $(this).val();
            var store_website_id = $('#live_selected_customer_store').val();
            $.ajax({
                url: "{{ url('get-store-wise-replies') }}" + '/' + category_id + '/' + store_website_id,
                type: 'GET',
                dataType: 'json'
            }).done(function(data) {
                console.log(data);
                if (data.status == 1) {
                    $('#live_quick_replies').empty().append('<option value="">Quick Reply</option>');
                    var replies = data.data;
                    replies.forEach(function(reply) {
                        $('#live_quick_replies').append($('<option>', {
                            value: reply.reply,
                            text: reply.reply,
                            'data-id': reply.id
                        }));
                    });
                }
            });

        }
    });

    $('.quick_comment_add_live').on("click", function() {
        var textBox = $(".quick_comment_live").val();
        var quickCategory = $('#keyword_category').val();

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
                'category_id': quickCategory,
                'reply': textBox,
                'store_website_id': $('#live_selected_customer_store').val()
            }
        }).done(function(data) {
            console.log(data);
            $(".live_quick_comment").val('');
            $('#live_quick_replies').append($('<option>', {
                value: data.data,
                text: data.data
            }));
        })
    });

    $('#live_quick_replies').on("change", function() {
        $('.type_msg').text($(this).val());
    });

    $(document).on('click', '.show_sku_long', function() {
        $(this).hide();
        var id = $(this).attr('data-id');
        $('#sku_small_string_' + id).hide();
        $('#sku_long_string_' + id).css({
            'display': 'block'
        });
    });

    $(document).on('click', '.show_prod_long', function() {
        $(this).hide();
        var id = $(this).attr('data-id');
        $('#prod_small_string_' + id).hide();
        $('#prod_long_string_' + id).css({
            'display': 'block'
        });
    });

    $(document).on('click', '.manual-payment-btn', function(e) {
        e.preventDefault();
        var thiss = $(this);
        var type = 'GET';
        $.ajax({
            url: '/voucher/manual-payment',
            type: type,
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            $('#create-manual-payment').modal('show');
            $('#create-manual-payment-content').html(response);

            $('#date_of_payment').datetimepicker({
                format: 'YYYY-MM-DD'
            });
            $('.select-multiple').select2({
                width: '100%'
            });

            $(".currency-select2").select2({
                width: '100%',
                tags: true
            });
            $(".payment-method-select2").select2({
                width: '100%',
                tags: true
            });

        }).fail(function(errObj) {
            $("#loading-image").hide();
        });
    });

    $(document).on('click', '.manual-request-btn', function(e) {
        e.preventDefault();
        var thiss = $(this);
        var type = 'GET';
        $.ajax({
            url: '/voucher/payment/request',
            type: type,
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            $('#create-manual-payment').modal('show');
            $('#create-manual-payment-content').html(response);

            $('#date_of_payment').datetimepicker({
                format: 'YYYY-MM-DD'
            });
            $('.select-multiple').select2({
                width: '100%'
            });

        }).fail(function(errObj) {
            $("#loading-image").hide();
        });
    });

    $(document).on('click','#event-alerts',function(e){
        e.preventDefault();
        getEventAlerts(true);
    });
    $(document).ready(function() {
        getEventAlerts();
    });

    function getEventAlerts(showModal = false) {
        $.ajax({
            type: "GET",
            url: "{{route('event.getEventAlerts')}}",
            dataType:"json",
            beforeSend:function(data){
                $('.ajax-loader').show();
            }
        }).done(function (response) {
            $('.ajax-loader').hide();
            $('#event-alerts-modal-html').empty().html(response.html);
            if (showModal) {
                $('#event-alerts-modal').modal('show');
            }
            if(response.count > 0) {
                $('.event-alert-badge').removeClass("hide");
            }
        }).fail(function (response) {
            $('.ajax-loader').hide();
            console.log(response);
        });
    }

        $(document).on('submit', '#event-alert-date-form', function(event) {
            event.preventDefault();
            var dateValue = $('input[name="event_alert_date"]').val();
            $.ajax({
                    type: "GET",
                    url: "{{route('event.getEventAlerts')}}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        date : dateValue
                    },
            }).done(function (response) {
                $('.ajax-loader').hide();
                $('#event-alerts-modal-html').empty().html(response.html);
                if (showModal) {
                    $('#event-alerts-modal').modal('show');
                }
                if(response.count > 0) {
                    $('.event-alert-badge').removeClass("hide");
                }
            }).fail(function (response) {
                $('.ajax-loader').hide();
                console.log(response);
            });
         });


    $(document).on('click','.event-alert-log-modal',function(e){
        var event_type = $(this).data("event_type");
        var event_id = $(this).data("event_id");
        var event_schedule_id = $(this).data("event_schedule_id");
        var assets_manager_id = $(this).data("assets_manager_id");
        var event_alert_date = $(this).data("event_alert_date");
        var is_read = $(this).prop('checked');

        $.ajax({
            type: "POST",
            url: "{{route('event.saveAlertLog')}}",
            data: {
                _token: "{{ csrf_token() }}",
                event_type,
                event_id,
                event_schedule_id,
                assets_manager_id,
                event_alert_date,
                is_read
            },
            dataType:"json",
            beforeSend:function(data){
                $('.ajax-loader').show();
            }
        }).done(function (response) {
            toastr["success"](response.message, "Message");
            $('.ajax-loader').hide();
        }).fail(function (response) {
            $('.ajax-loader').hide();
        });
    });

    $(document).on("click", ".permission-request", function(e) {
        e.preventDefault();
        $.ajax({
            url: '/user-management/request-list',
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(result) {
                $("#loading-image").hide();
                if (result.code == 200) {
                    var t = '';
                    $.each(result.data, function(k, v) {
                        t += `<tr><td>` + v.name + `</td>`;
                        t += `<td>` + v.permission_name + `</td>`;
                        t += `<td>` + v.request_date + `</td>`;
                        t += `<td><button class="btn btn-secondary btn-xs permission-grant" data-type="accept" data-id="` +
                            v.permission_id + `" data-user="` + v.user_id +
                            `">Accept</button>
                                 <button class="btn btn-secondary btn-xs permission-grant" data-type="reject" data-id="` +
                            v.permission_id + `" data-user="` + v
                            .user_id + `">Reject</button>
                              </td></tr>`;
                    });
                    if (t == '') {
                        t = '<tr><td colspan="4" class="text-center">No data found</td></tr>';
                    }
                }
                $("#permission-request-model").find(".show-list-records").html(t);
                $("#permission-request-model").modal("show");
            },
            error: function() {
                $("#loading-image").hide();
            }
        });
    });

    $('.add_todo_title').change(function() {
        if ($('.add_todo_subject').val() == "") {
            $('.add_todo_subject').val("");
            $('.add_todo_subject').val($('.add_todo_title').val());
        }
    })

    $('#todo-date').datetimepicker({
        format: 'YYYY-MM-DD',
    });

    $(document).on("click", ".todolist-request", function(e) {
        e.preventDefault();
        $("#todolist-request-model").modal("show");
    });

	$(document).on("click", ".todolist-get", function(e) {
			e.preventDefault();
			$("#todolist-get-model").modal("show");
	});

    $(document).on("click", ".menu-create-database", function(e) {
        e.preventDefault();
        $("#menu-create-database-model").modal("show");
    });

    $(document).on("click", ".menu-show-task", function(e) {
        e.preventDefault();
        $("#menu-show-task-model").modal("show");
    });

    $(document).on("click", ".menu-show-dev-task", function(e) {
        e.preventDefault();
        $("#menu-show-dev-task-model").modal("show");
    });

    $(document).on('click', '.menu-preview-img-btn', function(e) {
        e.preventDefault();
        id = $(this).data('id');
        if (!id) {
            alert("No data found");
            return;
        }
        $.ajax({
            url: "/task/preview-img/" + id,
            type: 'GET',
            success: function(response) {
                $("#menu-preview-task-image").modal("show");
                $(".menu-task-image-list-view").html(response);
                initialize_select2()
            },
            error: function() {}
        });
    });

    $(document).on("click", ".permission-grant", function(e) {
        e.preventDefault();
        var permission = $(this).data('id');
        var user = $(this).data('user');
        var type = $(this).data('type');

        $.ajax({
            url: '/user-management/modifiy-permission',
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                permission: permission,
                user: user,
                type: type
            },
            dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(result) {
                $("#loading-image").hide();
                if (result.code == 200) {
                    toastr["success"](result.data, "");
                } else {
                    toastr["error"](result.data, "");
                }
            },
            error: function() {
                $("#loading-image").hide();
            }
        });
    });

    $(document).on("click", ".permission-delete-grant", function(e) {
        e.preventDefault();
        $.ajax({
            url: '/user-management/request-delete',
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(result) {
                $("#loading-image").hide();
                if (result.code == 200) {
                    $("#permission-request").find(".show-list-records").html('');
                    toastr["success"](result.data, "");
                } else {
                    toastr["error"](result.data, "");
                }
            },
            error: function() {
                $("#loading-image").hide();
            }
        });
    });

	function todoHomeStatusChange(id, xvla) {
			$.ajax({
			type: "POST",
					url: "{{ route('todolist.status.update') }}",
					data: {
					"_token": "{{ csrf_token() }}",
					"id": id,
					"status":xvla
				},
			dataType: "json",
			success: function(message) {
					$c = message.length;
					if ($c == 0) {
							alert('No History Exist');
					} else {
							toastr['success'](message.message, 'success');
					}
			},
			error: function(error) {
					toastr['error'](error, 'error');
			}
		});
	}

    function estimateFunTaskDetailHandler(elm) {
        let tasktype = $(elm).data('task');
        let taskid = $(elm).data('id');
        if(tasktype == "DEVTASK") {
            // $("#modalTaskInformationUpdates .modal-body .row").eq(1).hide()
            // $("#modalTaskInformationUpdates .modal-body hr").eq(1).hide()
            // $("#modalTaskInformationUpdates .modal-body .row").eq(4).hide()
            // $("#modalTaskInformationUpdates .modal-body hr").eq(4).hide()
            // $("#modalTaskInformationUpdates .modal-body .row").eq(5).hide()
            // $("#modalTaskInformationUpdates .modal-body .row").eq(6).hide()
            estimatefunTaskInformationModal(elm, taskid, tasktype)
        } else {
            // $("#modalTaskInformationUpdates .modal-body .row").eq(3).hide()
            // $("#modalTaskInformationUpdates .modal-body hr").eq(3).hide()
            // $("#modalTaskInformationUpdates .modal-body .row").eq(4).hide()
            // $("#modalTaskInformationUpdates .modal-body hr").eq(4).hide()
            estimatefunTaskInformationModal(elm, taskid, tasktype)
        }
    }

    </script>
    @if ($message = Session::get('actSuccess'))
    <script>
    toastr['success']('<?php echo $message; ?>', 'success');
    </script>
    @endif
    @if ($message = Session::get('actError'))
    <script>
    toastr['error']('<?php echo $message; ?>', 'error');
    </script>
    @endif

</body>

</html>
