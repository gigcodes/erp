<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">



    <!-- CSRF Token -->

    <meta name="csrf-token" content="{{ csrf_token() }}">



    <title>{{ config('app.name', 'ERP for Sololuxury') }}</title>



    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <script src="{{ asset('js/app.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.js"></script>

    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script type="text/javascript" src="//media.twiliocdn.com/sdk/js/client/v1.5/twilio.min.js"></script>

    <script type="text/javascript" src="https://unpkg.com/tabulator-tables@4.0.5/dist/js/tabulator.min.js"></script>

    <script src="{{ asset('js/bootstrap-notify.js') }}"></script>

    <script src="{{ asset('js/calls.js') }}"></script>

    <script src="{{ asset('js/custom.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.3.3/bootstrap-slider.min.js"></script>



    {{-- @if( str_contains(Route::current()->getName(),['sales','activity','leads','task','home', 'customer'] ) ) --}}

        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet"/>

        <script type="text/javascript"

                src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js"></script>

    {{-- @endif --}}



    @if(Auth::user())

        {{--<link href="{{ url('/css/chat.css') }}" rel="stylesheet">--}}

        <script>

            window.userid = {{Auth::user()->id}};

            window.username = "{{Auth::user()->name}}";

            loggedinuser = {{Auth::user()->id}};

        </script>

    @endif



<!-- Fonts -->

    <link rel="dns-prefetch" href="https://fonts.gstatic.com">

    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">



    <!-- Styles -->

    <link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"

          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.3.3/css/bootstrap-slider.min.css">

    <link href="https://unpkg.com/tabulator-tables@4.0.5/dist/css/tabulator.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.css">

    @yield("styles")

    <style>

        .row:before, .row:after {

            display: inline-block;

        }



        #toast-container > div {

            opacity: 1;

            color: black;

            padding: 15px 15px 0 15px;

        }



        #toast-container .toast-info {

            background-color: #eee;

            background-image: none !important;

            position: absolute;

            right: 0px;

        }



        #toast-container .toast-info:nth-child(2) {

            top: 10px;

        }



        #toast-container .toast-stack {

            position: relative;

            min-height: 100px;

            max-height: 135px;

            overflow: hidden;

        }



        #toast-container .toast-stack:nth-child(2) {

            top: 0;

        }



        #notification_count {

            z-index: 100;

            cursor: pointer;

        }



        #toast-container > div#notification_count {

            top: -10px;

            left: -310px;

        }



        .toast-container-stacked > div#notification_count {

            left: -10px !important;

        }



        .toast-message a {

            color: black;

        }



        .btn-group-justified > .btn, .btn-group-justified > .btn-group {

            width: 32.9%;

        }



        .notification-row {

            margin-right: -34px;

            margin-left: -30px;

        }



        .btn-notification {

            background-color: #eee;

            border: 1px solid black;

            margin: 0 !important;

        }



        .dropdown-submenu > .dropdown-menu {

            top: 0;

            left: 100%;

            margin-top: -6px;

            margin-left: -1px;

            -webkit-border-radius: 0 6px 6px 6px;

            -moz-border-radius: 0 6px 6px;

            border-radius: 0 6px 6px 6px;

        }



        .dropdown-submenu:hover > .dropdown-menu {

            display: block;

        }



        .dropdown-submenu > a:after {

            display: block;

            content: " ";

            float: right;

            width: 0;

            height: 0;

            border-color: transparent;

            border-style: solid;

            border-width: 5px 0 5px 5px;

            border-left-color: #ccc;

            margin-top: 5px;

            margin-right: -10px;

        }



        .dropdown-submenu:hover > a:after {

            border-left-color: #fff;

        }



        .nav-item.dropdown-submenu[data-count]:after {

            left: 0;

            right: auto;

        }



        .new-message {

            background-color: #007bff;

            color: white;

        }



        /* .talk-bubble.blue {

          margin: 40px;

          position: relative;

          width: 90%;

          height: auto;

          background-color: #33AEFF;

        }



        .tri-right.right-in.blue:after {

          border-color: #33AEFF transparent transparent #33AEFF

        } */



        .message-img {

            width: 100%;

            margin-top: 10px;

        }



        .talktext p {

            /* min-height: 65px; */

            height: auto;

            overflow-y: hidden;



        }



        .talktext .expanded {

            height: auto;

        }



        .notifications-container {

            position: fixed;

            top: 10px;

            right: 10px;

            z-index: 99;

        }



        .notifications-hide {

            right: -280px;

        }



        .stack-container {

            position: relative;

            width: 300px;

            /* height: 135px; */

            display: none;

            margin-bottom: 10px;

        }



        .notification {

            background: #eee;

            width: 300px;

            min-height: 100px;

            max-height: 135px;

            padding: 15px 15px 0 15px;

            margin-bottom: 6px;

            position: relative;

            -moz-box-shadow: 0 0 12px #999999;

            -webkit-box-shadow: 0 0 12px #999999;

            box-shadow: 0 0 12px #999999;

            -moz-border-radius: 3px 3px 3px 3px;

            -webkit-border-radius: 3px 3px 3px 3px;

            border-radius: 3px 3px 3px 3px;

            pointer-events: auto;

        }



        .notification:hover {

            -moz-box-shadow: 0 0 12px #000000;

            -webkit-box-shadow: 0 0 12px #000000;

            box-shadow: 0 0 12px #000000;

        }



        @media (max-width: 414px) {

            .notifications-container {

                left: 10px;

            }



            .stack-container {

                width: auto;

            }



            .notification {

                width: 100%;

            }



            .notifications-hide {

                left: auto;

            }



            .notifications-hide .stack-container, .notifications-hide .notification {

                width: 300px;

            }

        }



        .notification a {

            color: black;

        }



        .notification a:hover {

            color: #cccccc;

            text-decoration: none;

        }



        /* .notification-stacked {

          position: absolute;

        }



        .notification-stacked:nth-child(2) {

          top: 10px;

        } */



        .notification .notification-close {

            border: none;

            background: transparent;

            color: white;

            font-size: 20px;

            font-weight: bold;

            top: 5px;

            position: absolute;

            right: 10px;

            padding: 0;

            /* z-index: 100; */

        }



        .notification .notification-close:hover {

            color: black;

            opacity: 0.4;

        }



        .stacked {

            height: 135px;

        }



        .stacked .notification {

            position: absolute;

        }



        .stacked .notification:first-child {

            top: 10px;

        }



        .task-subject:hover {

            cursor: pointer;

        }



        .thumbnail-wrapper {

            position: relative;

            width: 200px;

            display: inline-block;

        }



        .thumbnail-200 {

            width: 200px;

        }



        /* .thumbnail-200:before {

          background: red;

          width: 10px;

          height: 10px;

        } */



        .thumbnail-delete {

            position: absolute;

            padding: 5px 10px;

            top: 0;

            right: 0;

            font-weight: 800;

            cursor: pointer;

        }



        /* .thumbnail-200::after {

          content: "x";

          position: absolute;

          padding: 5px 10px;

          top: 0;

          right: 0;

          font-weight: 800;

          cursor: pointer;

        } */



        .thumbnail-delete:hover {

            color: rgba(0, 0, 0, 0.7);

        }



        .btn-secondary {

            color: #fff;

            background-color: #6c757d;

            border-color: #6c757d;

        }



        .btn-secondary:hover {

            color: #fff !important;

        }



        .btn-image img {

            width: 25px;

        }



        .supplier-wrapper {

            padding: 0 0 10px 0;

            margin: 0 0 10px 0;

            border-bottom: 1px solid #6f6767;

        }



        .user-status {

            display: inline-block;

            width: 10px;

            height: 10px;

            border-radius: 50%;

            background-color: #eee;

        }



        .user-status.is-online {

            background-color: #00FF00;

        }



        .row-highlight {

            background-color: #eee;

        }


        .btn.btn-image {
          background-color: transparent;
        }
    </style>



    <script>

        window.Laravel = {!! json_encode([

            'csrfToken'=> csrf_token(),

            'user'=> [

                'authenticated' => auth()->check(),

                'id' => auth()->check() ? auth()->user()->id : null,

                'name' => auth()->check() ? auth()->user()->name : null,

                ]

            ])

        !!};

    </script>



    <script src="https://js.pusher.com/4.3/pusher.min.js"></script>

    <script>



        // Enable pusher logging - don't include this in production

        Pusher.logToConsole = true;



        var pusher = new Pusher('df4fad9e0f54a365c85c', {

            cluster: 'ap2',

            forceTLS: true

        });

    </script>

    @if (Auth::user())

        <script>

            initializeTwilio();

        </script>

    @endif

    <script src="{{ asset('js/pusher.chat.js') }}"></script>

    <script src="{{ asset('js/chat.js') }}"></script>



</head>

<body>

{{-- <div id="fb-root"></div> --}}



<div class="notifications-container">

    <div class="stack-container stacked" id="leads-notification"></div>

    <div class="stack-container stacked" id="orders-notification"></div>

    {{-- <div class="stack-container stacked" id="messages-notification"></div> --}}

    <div class="stack-container stacked" id="tasks-notification"></div>

</div>



<div id="app">

    <nav class="navbar navbar-expand-md navbar-light navbar-laravel">

        <!--<div class="container container-wide">-->

        <div class="container-fluid">

            <a class="navbar-brand" href="{{ url('/') }}">

                {{ config('app.name', 'Laravel') }}

            </a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"

                    aria-controls="navbarSupportedContent" aria-expanded="false"

                    aria-label="{{ __('Toggle navigation') }}">

                <span class="navbar-toggler-icon"></span>

            </button>



            <div class="collapse navbar-collapse" id="navbarSupportedContent">

                <!-- Left Side Of Navbar -->

                <ul class="navbar-nav mr-auto">



                </ul>



                <!-- Right Side Of Navbar -->

                <ul class="navbar-nav ml-auto " style="text-align: center;">

                    <!-- Authentication Links -->

                    @guest

                    <li class="nav-item">

                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>

                    </li>

                    {{--<li class="nav-item">

                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>

                    </li>--}}

                    @else





                        @include('partials.notifications')

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('pushNotification.index') }}">New Notifications</a>
                        </li>




                        <li class="nav-item dropdown" data-count="

                             {{ \App\Http\Controllers\NotificaitonContoller::salesCount() }}">

                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                Sale<span class="caret"></span>

                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                <a class="dropdown-item" href="{{ route('sales.index') }}">Sale List</a>

                                <a class="dropdown-item" href="{{ route('sales.create') }}">Add new</a>

                            </div>

                        </li>



                        @can('admin')

                            <li class="nav-item dropdown">

                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button"

                                   aria-haspopup="true" aria-expanded="false">Product <span class="caret"></span></a>

                                <ul class="dropdown-menu multi-level">

                                    <li class="nav-item dropdown dropdown-submenu">

                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"

                                           aria-haspopup="true" aria-expanded="false" v-pre>

                                            Selection<span class="caret"></span>

                                        </a>



                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                            <a class="dropdown-item" href="{{ route('productselection.index') }}">Selections

                                                Grid</a>

                                            {{--                                        <a class="dropdown-item" href="{{route('productselection.list')}}">Selections List</a>--}}

                                            @can('selection-create')

                                                <a class="dropdown-item" href="{{ route('productselection.create') }}">Add

                                                    New</a>

                                            @endcan

                                        </ul>

                                    </li>



                                    <li class="nav-item dropdown dropdown-submenu">

                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"

                                           aria-haspopup="true" aria-expanded="false" v-pre>

                                            Searcher<span class="caret"></span>

                                        </a>



                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                            <a class="dropdown-item" href="{{ route('productsearcher.index') }}">Searcher

                                                Grid</a>

                                            {{--<a class="dropdown-item" href="{{ route('productattribute.list') }}">Searcher List</a>--}}

                                        </ul>

                                    </li>

                                    <li class="nav-item dropdown dropdown-submenu">

                                      <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"

                                         aria-haspopup="true" aria-expanded="false" v-pre>

                                          Supervisor<span class="caret"></span>

                                      </a>

                                      <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                          <a class="dropdown-item" href="{{ route('productsupervisor.index') }}">Supervisor Grid</a>


                                      </ul>

                                    </li>



                                    {{-- <li class="nav-item dropdown dropdown-submenu" data-count="{{

                                          \App\Http\Controllers\ProductAttributeController::rejectedProductCountByUser()

                                   }}">

                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"

                                           aria-haspopup="true" aria-expanded="false" v-pre>

                                            Attribute<span class="caret"></span>

                                        </a>



                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                            <a class="dropdown-item" href="{{ route('productattribute.index') }}">Attribute

                                                Grid</a>

                                            {{--<a class="dropdown-item" href="{{ route('productattribute.list') }}">Searcher List</a>

                                        </ul>

                                    </li> --}}



                                    <li class="nav-item dropdown dropdown-submenu"

                                        data-count="{{ \App\Http\Controllers\ProductCropperController::rejectedProductCountByUser() }}">

                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"

                                           aria-haspopup="true" aria-expanded="false" v-pre>

                                            ImageCropper<span class="caret"></span>

                                        </a>



                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                            <a class="dropdown-item" href="{{ route('productimagecropper.index') }}">ImageCropper

                                                Grid</a>

                                        </ul>

                                    </li>



                                    <li class="nav-item dropdown dropdown-submenu">

                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"

                                           aria-haspopup="true" aria-expanded="false" v-pre>

                                            Lister<span class="caret"></span>

                                        </a>



                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                            <a class="dropdown-item" href="{{ route('productlister.index') }}">Lister

                                                Grid</a>

                                        </ul>

                                    </li>



                                    <li class="nav-item dropdown dropdown-submenu">

                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"

                                           aria-haspopup="true" aria-expanded="false" v-pre>

                                            Approver<span class="caret"></span>

                                        </a>

                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                            <a class="dropdown-item" href="{{ route('productapprover.index') }}">Approver

                                                Grid</a>

                                        </ul>

                                    </li>



                                    <li class="nav-item dropdown dropdown-submenu">

                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"

                                           aria-haspopup="true" aria-expanded="false" v-pre>

                                            Inventory<span class="caret"></span>

                                        </a>

                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                            <a class="dropdown-item" href="{{ route('productinventory.index') }}">Inventory

                                                Grid</a>

                                            <a class="dropdown-item" href="{{ route('productinventory.instock') }}">In

                                                stock</a>

                                        </ul>

                                    </li>

                                    <li class="nav-item">

                                        <a class="dropdown-item" href="{{ route('quicksell.index') }}">Quick Sell</a>

                                    </li>

                                </ul>

                            </li>

                        @else

                            <li class="nav-item dropdown">

                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                    Product<span class="caret"></span>

                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                    <a class="dropdown-item" href="{{ route('quicksell.index') }}">Quick Sell</a>

                                </div>

                            </li>

                            @can('inventory-list')

                                <li class="nav-item dropdown">

                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                        Inventory<span class="caret"></span>

                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                        <a class="dropdown-item" href="{{ route('productinventory.index') }}">Inventory

                                            Grid</a>

                                        <a class="dropdown-item" href="{{ route('productinventory.instock') }}">In

                                            stock</a>

                                    </div>

                                </li>

                            @endcan



                            @can('approver-list')

                                <li class="nav-item dropdown">

                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                        Approver<span class="caret"></span>

                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                        <a class="dropdown-item" href="{{ route('productapprover.index') }}">Approver

                                            Grid</a>

                                    </div>

                                </li>

                            @endcan



                            @can('lister-list')

                                <li class="nav-item dropdown">

                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                        Lister<span class="caret"></span>

                                    </a>



                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                        <a class="dropdown-item" href="{{ route('productlister.index') }}">Lister

                                            Grid</a>

                                    </div>

                                </li>

                            @endcan



                            @can('imagecropper-list')

                                <li class="nav-item dropdown"

                                    data-count="{{ \App\Http\Controllers\ProductCropperController::rejectedProductCountByUser() }}">

                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                        ImageCropper<span class="caret"></span>

                                    </a>



                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                        <a class="dropdown-item" href="{{ route('productimagecropper.index') }}">ImageCropper

                                            Grid</a>

                                    </div>

                                </li>

                            @endcan



                            @can('supervisor-list')

                                <li class="nav-item dropdown">

                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                        Supervisor<span class="caret"></span>

                                    </a>



                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                        <a class="dropdown-item" href="{{ route('productsupervisor.index') }}">Supervisor Grid</a>

                                        {{--<a class="dropdown-item" href="{{ route('productattribute.list') }}">Searcher List</a>--}}

                                    </div>

                                </li>

                            @endcan



                            {{-- @can('attribute-list')

                                <li class="nav-item dropdown" data-count="{{

                                          \App\Http\Controllers\ProductAttributeController::rejectedProductCountByUser()

                                   }}">

                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                        Attribute<span class="caret"></span>

                                    </a>



                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                        <a class="dropdown-item" href="{{ route('productattribute.index') }}">Attribute

                                            Grid</a>


                                    </div>

                                </li>

                            @endcan --}}



                            @can('searcher-list')

                                <li class="nav-item dropdown">

                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                        Searcher<span class="caret"></span>

                                    </a>



                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                        <a class="dropdown-item" href="{{ route('productsearcher.index') }}">Searcher

                                            Grid</a>

                                        {{--<a class="dropdown-item" href="{{ route('productattribute.list') }}">Searcher List</a>--}}

                                    </div>

                                </li>

                            @endcan



                            @can('selection-list')

                                <li class="nav-item dropdown">

                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                        Selection<span class="caret"></span>

                                    </a>



                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                        <a class="dropdown-item" href="{{ route('productselection.index') }}">Selections

                                            Grid</a>

                                        {{--                                        <a class="dropdown-item" href="{{route('productselection.list')}}">Selections List</a>--}}

                                        @can('selection-create')

                                            <a class="dropdown-item" href="{{ route('productselection.create') }}">Add

                                                New</a>

                                        @endcan

                                    </div>

                                </li>

                            @endcan

                        @endcan



                        @can('crm')

                            <li class="nav-item dropdown">

                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                    CRM<span class="caret"></span>

                                </a>



                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                    <a class="dropdown-item" href="{{ route('customer.index') }}">Customers</a>
                                    <a class="dropdown-item" href="{{ route('instruction.index') }}">Instructions</a>
                                    <a class="dropdown-item" href="{{ route('leads.index') }}">Leads</a>

                                    @can('lead-create')

                                        <a class="dropdown-item" href="{{ route('leads.create') }}">Add New</a>

                                    @endcan

                                    <a class="dropdown-item" href="{{ route('leads.image.grid') }}">Leads Image Grid</a>

                                    {{--<a class="dropdown-item" href="{{ route('task.index') }}">Task</a>--}}

                                    {{--<a class="dropdown-item" href="{{ route('task.create') }}">Add Task</a>--}}



                                    @can('order-view')

                                        <a class="dropdown-item" href="{{ route('order.index') }}">Orders</a>

                                        @can('order-create')

                                            <a class="dropdown-item" href="{{ route('order.create') }}">Add Order</a>

                                        @endcan

                                        <a class="dropdown-item" href="{{ route('order.products') }}">Order Product

                                            List</a>

                                    @endcan

                                    <a class="dropdown-item" href="{{ route('order.missed-calls') }}">Missed calls

                                        List</a>



                                    {{-- <a class="dropdown-item" href="{{ route('task.index') }}">Tasks</a> --}}

                                </div>



                            </li>

                        @endcan



                        @can('purchase')

                            <li class="nav-item dropdown">

                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                    Purchase<span class="caret"></span>

                                </a>



                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                    <a class="dropdown-item" href="{{ route('purchase.index') }}">Purchases</a>

                                    <a class="dropdown-item" href="{{ route('purchase.grid') }}">Purchase Grid</a>

                                </div>

                            </li>

                        @endcan



                    <!--<li class="nav-item dropdown">

                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                Images<span class="caret"></span>

                            </a>



                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                <a class="dropdown-item" href="{{ route('image.grid') }}">Image Grid</a>

                                {{-- <a class="dropdown-item" href="{{ route('purchase.grid') }}">Purchase Grid</a> --}}

                            </div>

                        </li>-->



                        {{-- @can('product-list')

                             <li class="nav-item dropdown">

                                 <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                     Product<span class="caret"></span>

                                 </a>



                                 <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                     <a class="dropdown-item" href="{{ route('products.index') }}">List Products</a>

                                     @can('product-create')

                                         <a class="dropdown-item" href="{{ route('products.create') }}">Add New</a>

                                     @endcan

                                 </div>

                             </li>

                         @endcan--}}



                        @can('user-list')

                            <li class="nav-item dropdown">

                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                    Users<span class="caret"></span>

                                </a>



                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                    <a class="dropdown-item" href="{{ route('users.index') }}">List Users</a>

                                    @can('user-create')

                                        <a class="dropdown-item" href="{{ route('users.create') }}">Add New</a>

                                    @endcan

                                    @can('role-list')

                                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                            Roles<span class="caret"></span>

                                        </a>

                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                            <a class="dropdown-item" href="{{ route('roles.index') }}">List Roles</a>

                                            @can('role-create')

                                                <a class="dropdown-item" href="{{ route('roles.create') }}">Add New</a>

                                            @endcan

                                        </div>

                                    @endcan

                                </div>

                            </li>

                        @endcan



                        <!--@can('role-list')

                            <li class="nav-item dropdown">

                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                    Roles<span class="caret"></span>

                                </a>



                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                    <a class="dropdown-item" href="{{ route('roles.index') }}">List Roles</a>

                                    @can('role-create')

                                        <a class="dropdown-item" href="{{ route('roles.create') }}">Add New</a>

                                    @endcan

                                </div>

                            </li>

                        @endcan-->





                        @can('view-activity')

                            <li class="nav-item dropdown">

                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                    Activity<span class="caret"></span>

                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                    <a class="dropdown-item" href="{{ route('activity') }}">View</a>

                                    <a class="dropdown-item" href="{{ route('graph') }}">View Graph</a>

                                    <a class="dropdown-item" href="{{ route('graph_user') }}">User Graph</a>

                                    <a class="dropdown-item" href="{{ route('benchmark.create') }}">Add benchmark</a>

                                </div>

                            </li>

                        @endcan

                        <li class="nav-item dropdown">

                            <a id="instagramMenu" class="nav-link dropdown-toggle" href="#" role="button"

                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                Instagram <span class="caret"></span>

                            </a>

                            <div class="dropdown-menu dropdown-menu-left" aria-labelledby="instagramMenu">

                                @can('social-create')
                                    <a class="dropdown-item" href="{{ action('InstagramController@showPosts') }}">All Posts</a>
                                @endcan
                            </div>


                        </li>



                        <li class="nav-item dropdown">

                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                Social <span class="caret"></span>

                            </a>



                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                              @can('social-create')
                                <a class="dropdown-item" href="{{ route('image.grid') }}">Image Grid</a>
                              @endcan
                              @can('social-manage')
                              @endcan

                              <a class="dropdown-item" href="{{ route('image.grid.approved') }}">Approved Images</a>
                              <a class="dropdown-item" href="{{ route('image.grid.final.approval') }}">Final Approval</a>



                                <a class="dropdown-item" href="{{route('social.get-post.page')}}">See Posts

                                   </a>

                                    <a class="dropdown-item" href="{{route('social.post.page')}}">Post to Page

                                    </a>

                                    <a class="dropdown-item" href="{{route('social.report')}}">Ad Reports

                                    </a>

                                    <a class="dropdown-item" href="{{route('social.adCreative.report')}}">Ad Creative Reports

                                    <a class="dropdown-item" href="{{route('social.ad.campaign.create')}}">Create New Campaign

                                    </a>

                                    <a class="dropdown-item" href="{{route('social.ad.adset.create')}}">Create New Adset

                                    </a>

                                     <a class="dropdown-item" href="{{route('social.ad.create')}}">Create New Ad

                                    </a>



                            </div>

                        </li>



                        <li class="nav-item dropdown">

                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                {{ Auth::user()->name }} <span class="caret"></span>

                            </a>



                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                @can('setting-list')

                                    <a class="dropdown-item" href="{{route('settings.index')}}">Settings</a>

                                @endcan

                                @can('category-edit')

                                    <a class="dropdown-item" href="{{route('category')}}">Category</a>

                                @endcan

                                @can('brand-edit')

                                    <a class="dropdown-item" href="{{route('brand.index')}}">Brands</a>

                                @endcan

                                @can('product-delete')

                                    <a class="dropdown-item" href="{{route('products.index')}}">Product</a>

                                @endcan

                                @can('admin')

                                    <a class="dropdown-item" href="{{route('task_category.index')}}">Task Category</a>

                                @endcan

                                @can('reply-edit')

                                    <a class="dropdown-item" href="{{route('reply.index')}}">Quick Replies</a>

                                @endcan

                                <a class="dropdown-item" href="{{ route('logout') }}"

                                   onclick="event.preventDefault();

                                                     document.getElementById('logout-form').submit();">

                                    {{ __('Logout') }}

                                </a>



                                <form id="logout-form" action="{{ route('logout') }}" method="POST"

                                      style="display: none;">

                                    @csrf

                                </form>

                            </div>

                        </li>



                        @endguest

                </ul>

            </div>

        </div>

    </nav>



    <main class="container">

        <!-- Showing fb like page div to all pages  -->

    {{-- @if(Auth::check())

     <div class="fb-page" data-href="https://www.facebook.com/devsofts/" data-small-header="true" data-adapt-container-width="false" data-hide-cover="true" data-show-facepile="false"><blockquote cite="https://www.facebook.com/devsofts/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/devsofts/">Development</a></blockquote></div>

     @endif --}}



    <!-- End of fb page like  -->



        @yield('content')

    </main>

</div>

<!-- Scripts -->

@include('partials.chat')



<!-- Like page plugin script  -->



{{-- <script>(function(d, s, id) {

  var js, fjs = d.getElementsByTagName(s)[0];

  if (d.getElementById(id)) return;

  js = d.createElement(s); js.id = id;

  js.src = 'https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v3.2&appId=2045896142387545&autoLogAppEvents=1';

  fjs.parentNode.insertBefore(js, fjs);

}(document, 'script', 'facebook-jssdk'));</script> --}}

@yield('scripts')
@yield('styles')

</body>

</html>
