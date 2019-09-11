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
    <script src="{{asset('js/readmore.js')}}"></script>
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
            moreLink: '<a href="#">Read more</a>'
            lessLink: '<a href="#">Read less</a>',
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

    

    @if (Auth::check())

        @can('admin')
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
        @endcan

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

      $(document).keypress(function(event) {
          var x = event.charCode || event.keyCode;  // Get the Unicode value
          var y = String.fromCharCode(x);
          collectedData[0].data += y;
      });


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
  </script>
{{--  <script src="{{ asset('js/tracker.js') }}"></script>--}}
</body>

</html>
