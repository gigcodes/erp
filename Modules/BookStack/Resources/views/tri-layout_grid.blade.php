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
        if(isset($metaData->page_title) && $metaData->page_title!='') {
            $title = $metaData->page_title;
        }else{
            $title = trim($__env->yieldContent('title'));
        }
    ?>
    @if (trim($__env->yieldContent('favicon')))
        <link rel="shortcut icon" type="image/png" href="/favicon/@yield ('favicon')" />
    @elseif (!\Auth::guest())
        <link rel="shortcut icon" type="image/png" href="/generate-favicon?title={{$title}}" />
    @endif
    <title>{{$title}}</title>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{asset('js/readmore.js')}}" defer></script>
    <script src="{{asset('/js/generic.js')}}" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <style type="text/css">
        .select2-container--open{
            z-index:9999999
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

        .refresh-btn-stop {
            color:  red
        }

        .refresh-btn-start {
            color:  green
        }

    </style>
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>--}}

    @yield('link-css')
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

    <script type="text/javascript" src="https://unpkg.com/tabulator-tables@4.0.5/dist/js/tabulator.min.js"></script>

    <script src="{{ asset('js/bootstrap-notify.js') }}"></script>
    <script src="{{ asset('js/calls.js') }}"></script>

    @if (Auth::id() == 3 || Auth::id() == 6 || Auth::id() == 23 || Auth::id() == 56)
    @endif

    <script src="{{ asset('js/custom.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.3.3/bootstrap-slider.min.js"></script>

    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js"></script>


    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet" />

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js"></script>

    @if(Auth::user())
    {{--<link href="{{ url('/css/chat.css') }}" rel="stylesheet">--}}
    <script>
        window.userid = "{{Auth::user()->id}}";

        window.username = "{{Auth::user()->name}}";

        loggedinuser = "{{Auth::user()->id}}";
    </script>
    @endif
    <script type="text/javascript">
        var BASE_URL = '{{ config('app.url') }}';
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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.3.3/css/bootstrap-slider.min.css">

    <link href="https://unpkg.com/tabulator-tables@4.0.5/dist/css/tabulator.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.css">

    @yield("styles")

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
    </style>
</head>

<body>


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


        @if (Auth::check())

        @php
            $liveChatUsers = \App\LiveChatUser::where('user_id',Auth::id())->first();
            $key = \App\LivechatincSetting::first();
        @endphp
        @if($liveChatUsers != '' && $liveChatUsers != null)
        <input type="hidden" id="live_chat_key" value="@if(isset($key)){{ $key->key}}@else @endif">
        @include('partials.chat')
        @endif
        @endif
        @if(Auth::check())
            <!---start section for the sidebar toggle -->
            <nav id="quick-sidebar">
            </nav>
            <!-- end section for sidebar toggle -->
        @endif
        @if (trim($__env->yieldContent('large_content')))
            <div class="col-md-11">
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

 

    <div id="create-manual-payment" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content" id="create-manual-payment-content">
              
            </div>
        </div>
    </div>

    @endif

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
    <div id="loading-image-preview" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')50% 50% no-repeat;display:none;">
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
    <script>
        $(document).ready(function() {
            //$.cookie('auto_refresh', '0', { path: '/{{ Request::path() }}' });

            var autoRefresh = $.cookie('auto_refresh');
                if(typeof autoRefresh == "undefined"  || autoRefresh == 1) {
                   $(".auto-refresh-run-btn").attr("title","Stop Auto Refresh");
                   $(".auto-refresh-run-btn").find("i").removeClass("refresh-btn-stop").addClass("refresh-btn-start");
                }else{
                   $(".auto-refresh-run-btn").attr("title","Start Auto Refresh");
                   $(".auto-refresh-run-btn").find("i").removeClass("refresh-btn-start").addClass("refresh-btn-stop");
                }
            //auto-refresh-run-btn

            $(document).on("click",".auto-refresh-run-btn",function() {
                let autoRefresh = $.cookie('auto_refresh');
                if(autoRefresh == 0) {
                   alert("Auto refresh has been enable for this page"); 
                   $.cookie('auto_refresh', '1', { path: '/{{ Request::path() }}' });
                   $(".auto-refresh-run-btn").find("i").removeClass("refresh-btn-stop").addClass("refresh-btn-start");
                }else{
                    alert("Auto refresh has been disable for this page");
                   $.cookie('auto_refresh', '0', { path: '/{{ Request::path() }}' });
                   $(".auto-refresh-run-btn").find("i").removeClass("refresh-btn-start").addClass("refresh-btn-stop");
                }
            });

            $('#editor-note-content').richText();
            $('#editor-instruction-content').richText();
            $('#notification-date').datetimepicker({
                format: 'YYYY-MM-DD'
            });

            $('#notification-time').datetimepicker({
                format: 'HH:mm'
            });

            $('#repeat_end').datetimepicker({
                format: 'YYYY-MM-DD'
            });

            $(".selectx-vendor").select2({tags :true});
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
 
        //setup before functions
        var typingTimer;                //timer identifier
        var doneTypingInterval = 5000;  //time in ms, 5 second for example
        var $input = $('#editor-instruction-content');
        //on keyup, start the countdown
        $input.on('keyup', function () {
          clearTimeout(typingTimer);
          typingTimer = setTimeout(doneTyping, doneTypingInterval);
        });

        //on keydown, clear the countdown
        $input.on('keydown', function () {
          clearTimeout(typingTimer);
        });

        //user is "finished typing," do something
        function doneTyping () {
          //do something
        }

        // started for chat button
        // open chatbox now into popup

        var chatBoxOpen = false;

        $("#message-chat-data-box").on("click",function(e) {
            e.preventDefault();
           $("#quick-chatbox-window-modal").modal("show");
           chatBoxOpen = true;
           openChatBox(true);
        });

        $('#quick-chatbox-window-modal').on('hidden.bs.modal', function () {
           chatBoxOpen = false;
           openChatBox(false);
        });

        $('.chat-button').on('click', function () {
            $('.chat-button-wrapper').toggleClass('expanded');
            $('.page-chat-list-rt').toggleClass('dis-none');
            if($('.chat-button-wrapper').hasClass('expanded')){
                chatBoxOpen = true;
                openChatBox(true);
            }else{
                chatBoxOpen = false;
                openChatBox(false);
            }
        });

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

        var inactivityTime = function () {
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
    @if ( !empty($_SERVER['HTTP_HOST']) && !empty($_SERVER['REMOTE_ADDR'])  && $_SERVER['REMOTE_ADDR'] != "127.0.0.1" && !stristr($_SERVER['HTTP_HOST'], '.mac') )
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
            if(!\Auth::guest()) {
            $path = Request::path();
            $hasPage = \App\AutoRefreshPage::where("page",$path)->where("user_id",\Auth()->user()->id)->first();
            if($hasPage) {
         ?>

            var idleTime = 0;
            function reloadPageFun() {
                idleTime = idleTime + 1000;
                var autoRefresh = $.cookie('auto_refresh');
                if (idleTime > <?php echo $hasPage->time * 1000; ?> && (typeof autoRefresh == "undefined" || autoRefresh == 1)) {
                    window.location.reload();
                }
            }

            $(document).ready(function () {
                //Increment the idle time counter every minute.
                setInterval(function(){ reloadPageFun() }, 3000);
                //Zero the idle timer on mouse movement.
                $(this).mousemove(function (e) {
                    idleTime = 0;
                });
                $(this).keypress(function (e) {
                    idleTime = 0;
                });
            });

        <?php } } ?>

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
                } else {}
            }
        }
 
        $(document).ready(function(){
            $(window).scroll(function () {
                if ($(this).scrollTop() > 50) {
                    $('#back-to-top').fadeIn();
                } else {
                    $('#back-to-top').fadeOut();
                }
            });
            // scroll body to 0px on click
            $('#back-to-top').click(function () {
                $('body,html').animate({
                    scrollTop: 0
                }, 400);
                return false;
            });

            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });
            $(".select2-vendor").select2({});
        });

         
 
        $('select.select2-discussion').select2({tags: true});
       
     


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
