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
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script>
        let Laravel = {};
        Laravel.csrfToken = "{{csrf_token()}}";
        window.Laravel = Laravel;
    </script>
    <script src="{{ asset('js/app.js') }}"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script> --}}

    {{-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.bundle.min.js"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script> --}}

    {{-- When jQuery UI is included tooltip doesn't work --}}
    {{-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.js"></script>

    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script type="text/javascript" src="//media.twiliocdn.com/sdk/js/client/v1.6/twilio.min.js"></script>

    <script type="text/javascript" src="https://unpkg.com/tabulator-tables@4.0.5/dist/js/tabulator.min.js"></script>

    <script src="{{ asset('js/bootstrap-notify.js') }}"></script>

    @if (Auth::id() == 3 || Auth::id() == 6 || Auth::id() == 23 || Auth::id() == 56)
      <script src="{{ asset('js/calls.js') }}"></script>
    @endif

    <script src="{{ asset('js/custom.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.3.3/bootstrap-slider.min.js"></script>

    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js"></script>



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



    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"

          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.3.3/css/bootstrap-slider.min.css">

    <link href="https://unpkg.com/tabulator-tables@4.0.5/dist/css/tabulator.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.css">

    @yield("styles")

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

    @if (Auth::id() == 3 || Auth::id() == 6 || Auth::id() == 23 || Auth::id() == 56)

        <script>

            initializeTwilio();

        </script>

    @endif

    <script src="{{ asset('js/pusher.chat.js') }}"></script>

    <script src="{{ asset('js/chat.js') }}"></script>



</head>

<body>

  <div class="modal fade" id="instructionAlertModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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

  <div class="modal fade" id="developerAlertModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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

                        {{-- <li class="nav-item">
                            <a class="nav-link" href="{{ route('pushNotification.index') }}">New Notifications</a>
                        </li> --}}




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

                                            <a class="dropdown-item" href="{{ route('productinventory.list') }}">Inventory List</a>

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
                                    <a class="dropdown-item" href="{{ route('broadcast.index') }}">Broadcast Messages</a>
                                    <a class="dropdown-item" href="{{ route('broadcast.calendar') }}">Broadcast Calendar</a>
                                    <a class="dropdown-item" href="{{ route('instruction.index') }}">Instructions</a>
                                    <a class="dropdown-item" href="{{ route('leads.index') }}">Leads</a>

                                    @can('lead-create')

                                        <a class="dropdown-item" href="{{ route('leads.create') }}">Add New</a>

                                    @endcan

                                    <a class="dropdown-item" href="{{ route('leads.image.grid') }}">Leads Image Grid</a>

                                    {{--<a class="dropdown-item" href="{{ route('task.index') }}">Task</a>--}}

                                    {{--<a class="dropdown-item" href="{{ route('task.create') }}">Add Task</a>--}}

                                    <a class="dropdown-item" href="{{ route('refund.index') }}">Refunds</a>

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

                                      <a class="dropdown-item" href="{{ route('order.calls-history') }}">Calls History</a>

                                      <a class="dropdown-item" href="{{ route('stock.index') }}">Inward Stock</a>
                                      <a class="dropdown-item" href="{{ route('stock.private.viewing') }}">Private Viewing</a>



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
                                    <a class="dropdown-item" href="{{ route('purchase.grid', 'canceled-refunded') }}">Canc\Ref Grid</a>
                                    <a class="dropdown-item" href="{{ route('purchase.grid', 'ordered') }}">Ordered Grid</a>
                                    <a class="dropdown-item" href="{{ route('purchase.grid', 'delivered') }}">Delivered Grid</a>

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


                                <ul class="dropdown-menu multi-level">

                                    {{-- <li class="nav-item dropdown dropdown-submenu"> --}}
                                {{-- <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown"> --}}

                                      <a class="dropdown-item" href="{{ route('users.index') }}">List Users</a>
                                    {{-- </li> --}}

                                    @can('user-create')
                                      {{-- <li class="nav-item dropdown dropdown-submenu"> --}}
                                        <a class="dropdown-item" href="{{ route('users.create') }}">Add New</a>
                                      {{-- </li> --}}
                                    @endcan

                                    {{-- <li class="nav-item dropdown dropdown-submenu"> --}}
                                      <a class="dropdown-item" href="{{ route('users.login.index') }}">User Logins</a>
                                    {{-- </li> --}}

                                    @can('role-list')
                                      <li class="nav-item dropdown dropdown-submenu">
                                        {{-- <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown"> --}}

                                          {{-- <li class="nav-item dropdown dropdown-submenu"> --}}

                                              <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"

                                                 aria-haspopup="true" aria-expanded="false" v-pre>

                                                  Roles<span class="caret"></span>
                                              </a>

                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                            <a class="dropdown-item" href="{{ route('roles.index') }}">List Roles</a>

                                            @can('role-create')

                                                <a class="dropdown-item" href="{{ route('roles.create') }}">Add New</a>

                                            @endcan

                                        </ul>
                                      </li>

                                    @endcan

                                </ul>

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

                        @can('social-create')
                        <li class="nav-item dropdown">
                          <a id="instagramMenu" class="nav-link dropdown-toggle" href="#" role="button"
                             data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                              Instagram <span class="caret"></span>
                          </a>

                          <div class="dropdown-menu dropdown-menu-left" aria-labelledby="instagramMenu">
                            <a class="dropdown-item" href="{{ action('InstagramController@showPosts') }}">All Posts</a>
                            <a class="dropdown-item" href="{{ action('InstagramController@showImagesToBePosted') }}">Create A Post</a>
                            <a class="dropdown-item" href="{{ action('InstagramController@showSchedules') }}">Scheduled Posts</a>
                          </div>
                        </li>
                      @endcan

                            <li class="nav-item dropdown">

                                <a id="scrapMenu" class="nav-link dropdown-toggle" href="#" role="button"

                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                    Scrap <span class="caret"></span>

                                </a>

                                <div class="dropdown-menu dropdown-menu-left" aria-labelledby="scrapMenu">
                                    <a class="dropdown-item" href="{{ action('ScrapController@excel_import') }}">Import Excel Document Type 1</a>
                                    <a class="dropdown-item" href="{{ action('ScrapController@index') }}">Google Images</a>
                                    <a class="dropdown-item" href="{{ action('ScrapController@showProducts', 'G&B') }}">G&B Product</a>
                                    <a class="dropdown-item" href="{{ action('ScrapController@showProducts', 'Wiseboutique') }}">Wiseboutique Product</a>
                                    <a class="dropdown-item" href="{{ action('ScrapController@showProducts', 'DoubleF') }}">TheDoubleF Product</a>
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
                              @can('social-view')
                                <a class="dropdown-item" href="{{ route('image.grid.approved') }}">Approved Images</a>
                                <a class="dropdown-item" href="{{ route('image.grid.final.approval') }}">Final Approval</a>
                              @endcan

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

                                    <a class="dropdown-item" href="{{route('social.ads.schedules')}}">Ad Schedules</a>



                            </div>

                        </li>

                        <li class="nav-item dropdown">
                          <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                             data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                              Development<span class="caret"></span>
                          </a>

                          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            @can('developer-tasks')
                              <a class="dropdown-item" href="{{ route('development.index') }}">Tasks</a>
                              <a class="dropdown-item" href="{{ route('development.issue.index') }}">Issue List</a>
                            @endcan
                            <a class="dropdown-item" href="{{ route('development.issue.create') }}">Submit Issue</a>
                          </div>
                        </li>

                        @can('voucher')
                          <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Admin<span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                              <a class="dropdown-item" href="{{ route('voucher.index') }}">Convenience Vouchers</a>
                            </div>
                          </li>
                        @endcan

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

    <div class="col-md-10 col-md-offset-1">
        @yield('large_content')
    </div>

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

</body>

</html>
