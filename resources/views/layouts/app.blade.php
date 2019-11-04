<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield ('title', 'ERP') - {{ config('app.name') }}</title>

    <!-- CSRF Token -->

    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- <title>{{ config('app.name', 'ERP for Sololuxury') }}</title> --}}

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{asset('js/readmore.js')}}" defer></script>
    <script src="/js/generic.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">

    @yield('link-css')
    <script>
        let Laravel = {};
        Laravel.csrfToken = "{{csrf_token()}}";
        window.Laravel = Laravel;
    </script>
    <script>
        jQuery('.readmore').readmore({
            speed: 75,
            moreLink: '<a href="#">Read more</a>',
            lessLink: '<a href="#">Read less</a>'
        });
    </script>
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

    <script type="text/javascript" src="//media.twiliocdn.com/sdk/js/client/v1.6/twilio.min.js"></script>

    <script type="text/javascript" src="https://unpkg.com/tabulator-tables@4.0.5/dist/js/tabulator.min.js"></script>

    <script src="{{ asset('js/bootstrap-notify.js') }}"></script>

    @if (Auth::id() == 3 || Auth::id() == 6 || Auth::id() == 23 || Auth::id() == 56)
        <script src="{{ asset('js/calls.js') }}"></script>
    @endif

    <script src="{{ asset('js/custom.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.3.3/bootstrap-slider.min.js"></script>

    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js"></script>


    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet"/>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js"></script>

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

    {{-- <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"> --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

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


    {{-- <script src="https://js.pusher.com/4.3/pusher.min.js"></script>

    <script>
      // Enable pusher logging - don't include this in production
      Pusher.logToConsole = true;

      var pusher = new Pusher('df4fad9e0f54a365c85c', {
          cluster: 'ap2',
          forceTLS: true
      });
    </script> --}}

    @if (Auth::id() == 3 || Auth::id() == 6 || Auth::id() == 23 || Auth::id() == 56)

        <script>

            initializeTwilio();

        </script>

    @endif

    {{-- <script src="{{ asset('js/pusher.chat.js') }}"></script>

    <script src="{{ asset('js/chat.js') }}"></script> --}}


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

<div class="modal fade" id="masterControlAlertModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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

            <a class="navbar-brand" href="{{ url('/task') }}">

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


                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Product <span class="caret"></span></a>
                            <ul class="dropdown-menu multi-level">
                                {{-- Sub Menu Product --}}
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Listing<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Selection<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ route('productselection.index') }}">Selections Grid</a>
                                                @if(auth()->user()->checkPermission('productselection-create'))
                                                    <a class="dropdown-item" href="{{ route('productselection.create') }}">Add New</a>
                                                @endif
                                                <a class="dropdown-item" href="{{ url('/excel-importer') }}">Excel Import </a>
                                                <a class="dropdown-item" href="{{ url('/excel-importer/mapping') }}">Add Mapping For Master </a>
                                                <a class="dropdown-item" href="{{ url('/excel-importer/tools-brand') }}">Add Mapping For Excel</a>
                                                <a class="dropdown-item" href="{{ url('/excel-importer/log') }}">Excel Importer Log</a>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Supervisor<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ route('productsupervisor.index') }}">Supervisor Grid</a>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Image Cropper<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ route('productimagecropper.index') }}">Image Cropper Grid</a>
                                                <a class="dropdown-item" href="{{ action('ProductCropperController@getApprovedImages') }}">Approved Crop grid</a>
                                                <a class="dropdown-item" href="{{ action('ProductCropperController@getListOfImagesToBeVerified') }}">Crop Approval Grid</a>
                                                <a class="dropdown-item" href="{{ action('ProductCropperController@cropIssuesPage') }}">Crop Issue Summary</a>
                                                <a class="dropdown-item" href="{{ action('ProductCropperController@showRejectedCrops') }}">Crop-Rejected Grid</a>
                                                <a class="dropdown-item" href="{{ action('ProductCropperController@showCropVerifiedForOrdering') }}">Crop-Sequencer</a>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Attribute<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                @if(auth()->user()->checkPermission('productlister-list'))
                                                    <a class="dropdown-item" href="{{ route('products.listing') }}?cropped=on">Attribute edit page</a>
                                                @endif
                                                @if(auth()->user()->isAdmin())
                                                    <a class="dropdown-item" href="{{ action('ProductController@approvedListing') }}?cropped=on">Approved listing</a>
                                                    <a class="dropdown-item" href="{{ action('ProductController@approvedListing') }}?cropped=on&status_id=2">Listings awaiting scraping</a>
                                                    <a class="dropdown-item" href="{{ action('ProductController@approvedListing') }}?cropped=on&status_id=13">Listings unable to scrape</a>
                                                    <a class="dropdown-item" href="{{ action('ProductController@showRejectedListedProducts') }}">Rejected Listings</a>
                                                    <a class="dropdown-item" href="{{ action('AttributeReplacementController@index') }}">Attribute Replacement</a>

                                                @endif
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Stats<span class="caret"></span></a>
                                            {{-- Child Menu Stats--}}
                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ action('ProductController@productStats') }}">Product Statistics</a>
                                                <a class="dropdown-item" href="{{ action('ProductController@showAutoRejectedProducts') }}">Auto Reject Statistics</a>
                                                <a class="dropdown-item" href="{{ action('ListingPaymentsController@index') }}">Product Listing Payments</a>
                                                <a class="dropdown-item" href="{{ action('ScrapStatisticsController@index') }}">Scrap Statistics</a>
                                                <a class="dropdown-item" href="{{ route('scrap.activity') }}">Scrap activity</a>
                                                <a class="dropdown-item" href="{{ action('ScrapController@showProductStat') }}">Products Scrapped</a>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Approver<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ route('productapprover.index') }}">Approver Grid</a>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>In Stock<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ route('productinventory.instock') }}">In Stock</a>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>In Delivered<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ route('productinventory.indelivered') }}">In Delivered</a>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Inventory<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ route('productinventory.index') }}">Inventory Grid</a>
                                                <a class="dropdown-item" href="{{ route('productinventory.list') }}">Inventory List</a>
                                            </ul>
                                        </li>
                                        @if(auth()->user()->isAdmin())
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Quick Sell<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                    <a class="dropdown-item" href="{{ route('quicksell.index') }}">Quick Sell</a>
                                                </ul>
                                            </li>
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a class="dropdown-item" href="{{ route('stock.index') }}">Inward Stock</a>
                                            </li>
                                        @endif
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Scraping<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ url('scrap/statistics') }}">Statistics</a>
                                                <a class="dropdown-item" href="{{ action('CategoryController@brandMinMaxPricing') }}">Min/Max Pricing</a>
                                                <a class="dropdown-item" href="{{ route('supplier.count') }}">Supplier Category Count</a>
                                                <a class="dropdown-item" href="{{ route('supplier.brand.count') }}">Supplier Brand Count</a>
                                                <a class="dropdown-item" href="{{ url('price-comparison-scraper') }}">Price comparison</a>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>SKU<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ route('sku-format.index') }}">SKU Format</a>
                                                <a class="dropdown-item" href="{{ route('sku.color-codes') }}">SKU Color Codes</a>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Purchase<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('purchase.index') }}">Purchase</a>
                                            <a class="dropdown-item" href="{{ route('purchase.grid') }}">Purchase Grid</a>
                                            <a class="dropdown-item" href="{{ route('purchase.calendar') }}">Purchase Calendar</a>
                                            <a class="dropdown-item" href="{{ route('purchase.grid', 'canceled-refunded') }}">Cancel/Refund Grid</a>
                                            <a class="dropdown-item" href="{{ route('purchase.grid', 'ordered') }}">Ordered Grid</a>
                                            <a class="dropdown-item" href="{{ route('purchase.grid', 'delivered') }}">Delivered Grid</a>
                                            <a class="dropdown-item" href="{{ route('purchase.grid', 'non_ordered') }}">Non Ordered Grid</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a class="dropdown-item" href="{{ route('supplier.index') }}">Supplier List</a></a>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Scraping<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ action('SalesItemController@index') }}">Sale Items</a>
                                            <a class="dropdown-item" href="{{ action('DesignerController@index') }}">Designer List</a>
                                            <a class="dropdown-item" href="{{ action('GmailDataController@index') }}">Gmail Inbox</a>
                                            <a class="dropdown-item" href="{{ action('ScrapController@index') }}">Google Images</a>
                                            <a class="dropdown-item" href="{{ action('SocialTagsController@index') }}">Social Tags</a>
                                            <a class="dropdown-item" href="{{ action('DubbizleController@index') }}">Dubzzle</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">CRM <span class="caret"></span></a>
                            <ul class="dropdown-menu multi-level">
                                {{-- Sub Menu Product --}}
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Customers<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('customer.index') }}?type=unread">Customers - unread</a>
                                            <a class="dropdown-item" href="{{ route('customer.index') }}?type=unapproved">Customers - unapproved</a>
                                            <a class="dropdown-item" href="{{ route('customer.index') }}?type=Refund+to+be+processed">Customers - refund</a>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Cold Leads<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ action('ColdLeadsController@index') }}?via=hashtags">Via Hashtags</a>
                                                <a class="dropdown-item" href="{{ action('ColdLeadsController@showImportedColdLeads') }}">Imported Cold leads</a>
                                            </ul>
                                        </li>

                                    </ul>
                                </li>

                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Instructions<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Instructions<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ route('instruction.index') }}">Instructions</a>
                                                <a class="dropdown-item" href="{{ route('instruction.list') }}">Instructions List</a>
                                                <a class="dropdown-item" href="{{ action('KeywordInstructionController@index') }}">Instruction Keyword Instructions</a>
                                                <a class="dropdown-item" href="/instruction/quick-instruction">Quick instructions</a>
                                                <a class="dropdown-item" href="/instruction/quick-instruction?type=price">Quick instructions (price)</a>
                                                <a class="dropdown-item" href="/instruction/quick-instruction?type=image">Quick instructions (attach)</a>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Leads<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('leads.index') }}">Leads</a>
                                        <a class="dropdown-item" href="{{ action('LeadsController@erpLeads') }}">Leads (new)</a>
                                        <a class="dropdown-item" href="{{ route('leads.create') }}">Add new lead</a>
                                        <a class="dropdown-item" href="{{ route('leads.image.grid') }}">Leads Image grid</a>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Refunds<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('refund.index') }}">Refunds</a>
                                        </li>
                                    </ul>

                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Orders<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Orders<span class="caret"></span></a>

                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ route('order.index') }}">Orders</a>
                                                <a class="dropdown-item" href="{{ route('order.create') }}">Add Order</a>
                                                <a class="dropdown-item" href="{{ route('order.products') }}">Order Product List</a>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Customer<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('complaint.index') }}">Customer Complaints</a>
                                        </li>
                                    </ul>

                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Missed<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('order.missed-calls') }}">Missed Calls List</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Call<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('order.calls-history') }}">Call history</a>

                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Private<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('stock.private.viewing') }}">Private Viewing</a>

                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Bulk Customer Replies<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ action('BulkCustomerRepliesController@index') }}">Bulk Messages</a>
                                        <a class="dropdown-item" href="{{ action('CustomerCategoryController@index') }}">Categories</a>
                                        <a class="dropdown-item" href="{{ action('KeywordToCategoryController@index') }}">Keywords</a>

                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Delivery<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('deliveryapproval.index') }}">Delivery Approvals</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Broadcast<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('broadcast.index') }}">Broadcast Messages</a>
                                            <a class="dropdown-item" href="{{ route('broadcast.images') }}">Broadcast Images</a>
                                            <a class="dropdown-item" href="{{ route('broadcast.calendar') }}">Broadcast Calender</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Vendor <span class="caret"></span></a>
                            <ul class="dropdown-menu multi-level">
                                {{-- Sub Menu Product --}}
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{ route('vendor.index') }}">Vendor Info</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{ route('vendor.product.index') }}">Product Info</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Users <span class="caret"></span></a>
                            <ul class="dropdown-menu multi-level">
                                {{-- Sub Menu Product --}}
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>User Management<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('users.index') }}">List Users</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('users.create') }}">Add New</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('users.login.index') }}">User Logins</a>
                                        </li>

                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Roles<span class="caret"></span></a>

                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                                <a class="dropdown-item" href="{{ route('roles.index') }}">List Roles</a>
                                                <a class="dropdown-item" href="{{ route('roles.create') }}">Add New</a>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Permissions<span class="caret"></span></a>

                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ route('permissions.index') }}">List Permissions</a>
                                                <a class="dropdown-item" href="{{ route('permissions.create') }}">Add New</a>
                                                <a class="dropdown-item" href="{{ route('permissions.users') }}">User Permission List</a>


                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Activity<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('activity') }}">View</a>
                                        </li>


                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('graph_user') }}">User Graph</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('benchmark.create') }}">Add Benchmark</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ action('ProductController@showListigByUsers') }}">User Product Assignment</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Platforms <span class="caret"></span></a>
                            <ul class="dropdown-menu multi-level">
                                {{-- Sub Menu Product --}}
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{ action('PreAccountController@index') }}">Email Accounts
                                    </a>
                                </li>
                                @if(auth()->user()->isAdmin())
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Instagram<span class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ action('InstagramController@index') }}">Dashboard</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ action('InstagramPostsController@index') }}">Manual Instagram Post</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ action('InstagramController@accounts') }}">Accounts</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ action('HashtagController@showGrid', 'sololuxury') }}">Hashtag monitoring & manual Commenting</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ action('HashtagController@showNotification') }}">Recent Comments (Notifications)</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ action('InstagramController@showPosts') }}">All Posts</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ action('TargetLocationController@index') }}">Target Location</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ action('KeywordsController@index') }}">Keywords For comments</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ action('HashtagController@showProcessedComments') }}">Processed Comments</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ action('CompetitorPageController@index') }}?via=instagram">All Competitors On Instagram</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ action('InstagramAutoCommentsController@index') }}">Quick Reply</a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ action('UsersAutoCommentHistoriesController@index') }}">Bulk Commenting</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ action('AutoCommentHistoryController@index') }}">Auto Comments Statistics</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ action('InstagramProfileController@index') }}">Customers followers</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ action('InstagramProfileController@edit', 1) }}">#tags Used by top customers.</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ action('InstagramController@accounts') }}">Accounts</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('social.ads.schedules')}}">Ad Schedules</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('social.ad.create')}}">Create New Ad</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('social.ad.adset.create')}}">Create New Adset</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('social.ad.campaign.create')}}">Create New Campaign </a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('social.get-post.page')}}">See Posts</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('social.post.page')}}">Post to Page</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('social.report')}}">Ad Reports</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('social.adCreative.report')}}">Ad Creative Reports</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('complaint.index') }}">Customer Complaints</a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Facebook<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ action('InstagramController@showImagesToBePosted') }}">Create Post</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ action('InstagramController@showSchedules') }}">Schedule A Post</a>
                                        </li>

                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Facebook<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ action('FacebookController@index') }}">Facebook Post</a>
                                            </ul>
                                        </li>

                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Facebook Groups<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ action('FacebookController@show', 'group') }}">Facebook Groups</a>
                                            </ul>
                                        </li>

                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Facebook Brand Fan Page<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ action('FacebookController@show', 'brand') }}">Facebook Brand Fan Page</a>
                                            </ul>
                                        </li>

                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>All Adds<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{route('social.get-post.page')}}">See Posts</a>
                                                <a class="dropdown-item" href="{{route('social.post.page')}}">Post On pgae</a>
                                                <a class="dropdown-item" href="{{route('social.report')}}">Ad report</a>
                                                <a class="dropdown-item" href="{{route('social.adCreative.report')}}">Ad Creative Reports</a>
                                                <a class="dropdown-item" href="{{route('social.ad.campaign.create')}}">Create New Campaign</a>
                                                <a class="dropdown-item" href="{{route('social.ad.adset.create')}}">Create New adset</a>
                                                <a class="dropdown-item" href="{{route('social.ad.create')}}">Create New ad</a>
                                                <a class="dropdown-item" href="{{route('social.ads.schedules')}}">Ad Schedule</a>
                                            </ul>

                                        </li>

                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Sitejabber<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ action('SitejabberQAController@accounts') }}">Account</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ action('QuickReplyController@index') }}">Quick Reply</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Pinterest<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ action('PinterestAccountAcontroller@index') }}">Accounts</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Images<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('image.grid') }}">Image Grid</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('image.grid.approved') }}">Final Images</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('image.grid.final.approval') }}">Final Approval</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{ route('review.index') }}">Reviews
                                    </a>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Bloggers<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('blogger.index')}}">Bloggers</a>
                                        </li>
                                    </ul>

                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="seoMenu" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre="">SEO<span class="caret">
                          <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="seoMenu">
                                          <li class="nav-item dropdown dropdown-submenu">
                                              <a class="dropdown-item" href="{{ action('BackLinkController@displayBackLinkDetails') }}">Back Link Details</a>
                                              <a class="dropdown-item" href="{{ action('BrokenLinkCheckerController@displayBrokenLinkDetails') }}">Broken Link Details</a>
                                              <a class="dropdown-item" href="{{ action('AnalyticsController@showData') }}">Analytics Data</a>
                                              <a class="dropdown-item" href="{{ action('AnalyticsController@customerBehaviourByPage') }}">Customer Behaviour By Page</a>
                                              <a class="dropdown-item" href="{{ action('SERankingController@getSites') }}">SE Ranking</a>
                                              <a class="dropdown-item" href="{{ action('ArticleController@index') }}">Article Approval</a>
                                              <a class="dropdown-item" href="{{ action('ProductController@getSupplierScrappingInfo') }}">Supplier Scrapping Info</a>
                                              <a class="dropdown-item" href="{{ action('NewDevTaskController@index') }}">New Dev Task Planner</a>
                                          </li>
                                      </ul>
                                </li>

                                <!-- mailchimp -->
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="seoMenu" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre="">MailChimp<span class="caret">
                          <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="seoMenu">
                                          <li class="nav-item dropdown dropdown-submenu">
                                              <a href="{{ route('manage.mailchimp') }}">Manage MailChimp</a>

                                          </li>
                                      </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Development <span class="caret"></span></a>
                            <ul class="dropdown-menu multi-level">
                                {{-- Sub Menu Development --}}
                                <li class="nav-item">
                                    <a class="dropdown-item" href="{{ route('development.overview') }}">Overview</a>
                                </li>
                                <li class="nav-item">
                                    <a class="dropdown-item" href="{{ route('development.index') }}">Tasks</a>
                                </li>
                                <li class="nav-item">
                                    <a class="dropdown-item" href="{{ route('task-types.index') }}">Task Types</a>
                                </li>
                                <li class="nav-item">
                                    <a class="dropdown-item" href="{{ route('development.issue.index') }}">Issue list</a>
                                </li>
                                <li class="nav-item">
                                    <a class="dropdown-item" href="{{ route('development.issue.create') }}">Submit Issue</a>
                                </li>
                            </ul>
                        </li>
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin <span class="caret"></span></a>
                                <ul class="dropdown-menu multi-level">
                                    {{-- Sub Menu Product --}}
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Cash Flow<span class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('cashflow.index') }}">Cash Flow</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('voucher.index') }}">Convience Voucher</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('cashflow.mastercashflow') }}">Master Cash Flow</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('dailycashflow.index') }}">Daily Cash Flow</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('budget.index') }}">Budget</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('settings.index')}}">Settings</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('budget.index') }}">Hubstaff</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ url('page-notes') }}">Page Notes</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ url('page-notes-categories') }}">Page Notes Categories</a>
                                            </li>

                                        </ul>
                                    </li>
                                    @if(auth()->user()->isAdmin())
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Legal<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{route('lawyer.index')}}"> Lawyers</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{route('case.index')}}">Cases</a>
                                                </li>
                                            </ul>
                                        </li>
                                    @endif
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Old Issues<span class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ url('/old/') }}">Old Info</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ url('/old/?type=1') }}">Old Out going</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ url('/old/?type=2') }}">Old Incoming</a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{{ isset(Auth::user()->name) ? Auth::user()->name : 'Settings' }}} <span class="caret"></span></a>
                            <ul class="dropdown-menu multi-level">
                                {{-- Sub Menu Product --}}

                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{ route('mastercontrol.index') }}">Master Control</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{ route('dailyplanner.index') }}">Daily Planner</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{ route('task.list') }}">Tasks List</a>
                                </li>
                                @if(auth()->user()->isAdmin())
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{route('password.index')}}">Password Manager</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{route('document.index')}}">Document manager</a>
                                    </li>

                                    @if (Auth::id() == 3 || Auth::id() == 6 || Auth::id() == 56 || Auth::id() == 65 || Auth::id() == 90)
                                        <a class="dropdown-item" href="{{route('password.index')}}">Passwords Manager</a>
                                        <a class="dropdown-item" href="{{route('password.manage')}}">Multiple User Passwords Manager</a>
                                        <a class="dropdown-item" href="{{route('document.index')}}">Documents Manager</a>
                                    @endif

                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{ route('resourceimg.index') }}">Resource Center</a>
                                    </li>
                                @endif
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Product<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('products.index')}}">Product</a>
                                        </li>

                                        <li class="nav-item dropdown">

                                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                                Development<span class="caret"></span>
                                            </a>

                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ route('development.index') }}">Tasks</a>
                                                <a class="dropdown-item" href="{{ route('development.issue.index') }}">Issue List</a>
                                                <a class="dropdown-item" href="{{ route('development.issue.create') }}">Submit Issue</a>
                                                <a class="dropdown-item" href="{{ route('development.overview') }}">Overview</a>
                                            </div>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('category')}}">Category</a>

                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{action('CategoryController@mapCategory')}}">Category Reference</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('brand.index')}}">Brands</a>
                                        </li>
                                        @if(auth()->user()->checkPermission('category-edit'))
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('color-reference.index')}}">Color Reference</a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-submenu">
                                    <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>Customer<span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        @if(auth()->user()->isAdmin())
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('task_category.index')}}">Task Category</a>
                                            </li>
                                        @endif
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('reply.index')}}">Quick Replies</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('autoreply.index')}}">Auto Reples</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{url('/kb/')}}" target="_blank">Knowledge Base</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}</a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endif

                </ul>

            </div>

        </div>

    </nav>

    @if (Auth::check())

        @if(auth()->user()->isAdmin())
            <div class="float-container developer-float hidden-xs hidden-sm">
                @php
                    $lukas_pending_devtasks_count = \App\DeveloperTask::where('user_id', 3)->where('status', '!=', 'Done')->count();
                    $lukas_completed_devtasks_count = \App\DeveloperTask::where('user_id', 3)->where('status', 'Done')->count();
                    $rishab_pending_devtasks_count = \App\DeveloperTask::where('user_id', 65)->where('status', '!=', 'Done')->count();
                    $rishab_completed_devtasks_count = \App\DeveloperTask::where('user_id', 65)->where('status', 'Done')->count();
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
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#quickDevelopmentModal">+ DEVELOPMENT</button>
            </div>

            <div class="float-container instruction-float hidden-xs hidden-sm">
                @php
                    $pending_instructions_count = \App\Instruction::where('assigned_to', Auth::id())->whereNull('completed_at')->count();
                    $completed_instructions_count = \App\Instruction::where('assigned_to', Auth::id())->whereNotNull('completed_at')->count();
                    $sushil_pending_instructions_count = \App\Instruction::where('assigned_from', Auth::id())->where('assigned_to', 7)->whereNull('completed_at')->count();
                    $andy_pending_instructions_count = \App\Instruction::where('assigned_from', Auth::id())->where('assigned_to', 56)->whereNull('completed_at')->count();
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
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#quickInstructionModal">+ INSTRUCTION</button>
            </div>

            <div class="float-container hidden-xs hidden-sm">
                @php
                    $pending_tasks_count = \App\Task::where('is_statutory', 0)->where('assign_to', Auth::id())->whereNull('is_completed')->count();
                    $completed_tasks_count = \App\Task::where('is_statutory', 0)->where('assign_to', Auth::id())->whereNotNull('is_completed')->count();
                    $sushil_pending_tasks_count = \App\Task::where('is_statutory', 0)->where('assign_to', 7)->whereNull('is_completed')->count();
                    $andy_pending_tasks_count = \App\Task::where('is_statutory', 0)->where('assign_to', 56)->whereNull('is_completed')->count();
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
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#quickTaskModal">+ TASK</button>
            </div>
        @endif

        @include('partials.modals.quick-task')
        @include('partials.modals.quick-instruction')
        @include('partials.modals.quick-development-task')
    @endif

    <main class="container">

        <!-- Showing fb like page div to all pages  -->

    {{-- @if(Auth::check())

     <div class="fb-page" data-href="https://www.facebook.com/devsofts/" data-small-header="true" data-adapt-container-width="false" data-hide-cover="true" data-show-facepile="false"><blockquote cite="https://www.facebook.com/devsofts/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/devsofts/">Development</a></blockquote></div>

     @endif --}}



    <!-- End of fb page like  -->



        @yield('content')

    </main>

    <div class="col-md-12">
        @yield('large_content')
    </div>

</div>

@if(Auth::check())
    <div class="help-button-wrapper">
        <div class="col-md-10 page-notes-list-rt dis-none">
            <div class="help-list well well-lg">
                <form action="<?php echo route("createPageNote"); ?>">
                    <div class="form-group">
                        <label for="note">Notes:</label>
                        <textarea class="form-control" name="note" id="note"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="category_id">Category:</label>
                        <?php
                        $category = \App\PageNotesCategories::pluck('name', 'id')->toArray();
                        ?>
                        {!! Form::select('category_id', ['' => "-- select --"] + $category, null, ['class'=>'form-control', 'id'=> 'category_id']) !!}
                    </div>
                    <button type="button" class="btn btn-secondary ml-3 save-user-notes">Submit</button>
                </form>
                <table class="table table-fixed-page-notes page-notes-header-fixed" style="min-width: 402px;">
                    <thead>
                    <tr>
                        <th class="col-xs-1" scope="col">#</th>
                        <th class="col-xs-3" scope="col">Note</th>
                        <th class="col-xs-3" scope="col">Category</th>
                        <th class="col-xs-2" scope="col">Created By</th>
                        <th class="col-xs-3" scope="col">Created At</th>
                    </tr>
                    </thead>
                    <tbody class="page-notes-list">

                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-3">
            <button class="help-button"><span>+</span></button>
        </div>
    </div>
@endif

<!-- Scripts -->

{{-- @include('partials.chat') --}}



<!-- Like page plugin script  -->



{{-- <script>(function(d, s, id) {

  var js, fjs = d.getElementsByTagName(s)[0];

  if (d.getElementById(id)) return;

  js = d.createElement(s); js.id = id;

  js.src = 'https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v3.2&appId=2045896142387545&autoLogAppEvents=1';

  fjs.parentNode.insertBefore(js, fjs);

}(document, 'script', 'facebook-jssdk'));</script> --}}

@yield('scripts')

<script>
    window.token = "{{ csrf_token() }}";

    var url = window.location;
    window.collectedData = [
        {
            type: 'key',
            data: ''
        },
        {
            type: 'mouse',
            data: []
        }
    ];

    $(document).keypress(function (event) {
        var x = event.charCode || event.keyCode;  // Get the Unicode value
        var y = String.fromCharCode(x);
        collectedData[0].data += y;
    });

    // started for help button
    $('.help-button').on('click', function () {
        $('.help-button-wrapper').toggleClass('expanded');
        $('.page-notes-list-rt').toggleClass('dis-none');
    });

    var notesBtn = $(".save-user-notes");

    notesBtn.on("click", function (e) {
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
            success: function (data) {
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

    var getNotesList = function () {
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
    @if (Auth::check())
    $(document).ready(function () {
        var url = window.location.href;
        var user_id = {{ Auth::id() }};
        user_name = "{{ Auth::user()->name }}";
        $.ajax({
            type: "POST",
            url: "/api/userLogs",
            data: {"_token": "{{ csrf_token() }}", "url": url, "user_id": user_id, "user_name": user_name},
            dataType: "json",
            success: function (message) {
            }
        });
    });
    @endif
</script>
@if ( !empty($_SERVER['HTTP_HOST']) && !stristr($_SERVER['HTTP_HOST'], '.mac') )
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-147736165-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());
        gtag('config', 'UA-147736165-1');
    </script>
@endif

</body>

</html>