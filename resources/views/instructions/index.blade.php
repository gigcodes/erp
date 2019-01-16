@extends('layouts.app')

@section("styles")
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Instructions List</h2>
            {{-- <div class="pull-left">

            </div>
            <div class="pull-right">
              <a class="btn btn-secondary" href="{{ route('order.create') }}">+</a>
            </div> --}}
        </div>
    </div>

    @if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('HOD of CRM'))
      <div class="row mb-3">
        <div class="col-md-10 col-sm-12">
          <form action="{{ route('instruction.index') }}" method="GET" class="form-inline align-items-start" id="searchForm">
            <div class="row full-width" style="width: 100%;">
              <div class="col-md-4 col-sm-12">
                <div class="form-group mr-3">
                  <select class="form-control select-multiple" name="user[]" multiple>
                    @foreach ($users_array as $index => $name)
                      <option value="{{ $index }}" {{ isset($user) && in_array($index, $user) ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-2"><button type="submit" class="btn btn-image"><img src="/images/search.png" /></button></div>
            </div>
          </form>
        </div>
      </div>
    @endif

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div id="exTab3" class="container">
      <ul class="nav nav-tabs">
        <li class="active">
          <a href="#4" data-toggle="tab">Instructions</a>
        </li>
        <li><a href="#5" data-toggle="tab">Complete</a></li>
      </ul>
    </div>

    <div class="tab-content ">
      <div class="tab-pane active mt-3" id="4">

        <div class="table-responsive">
            <table class="table table-bordered">
            <tr>
              <th>Client Name</th>
              <th>Number</th>
              <th>Assigned to</th>
              <th>Instructions</th>
              <th colspan="2" class="text-center">Action</th>
              <th>Created at</th>
              {{-- <th>Remark</th> --}}
            </tr>
            @foreach ($instructions as $instruction)
                <tr>
                  <td><a href="{{ route('customer.show', $instruction->customer_id) }}">{{ $instruction->customer->name }}</a></td>
                  <td>
                    <span data-twilio-call data-context="leads" data-id="{{ $instruction->id }}">{{ $instruction->customer->phone }}</span>
                  </td>
                  <td>{{ $users_array[$instruction->assigned_to] }}</td>
                  <td>{{ $instruction->instruction }}</td>
                  <td>
                    @if ($instruction->completed_at)
                      {{ Carbon\Carbon::parse($instruction->completed_at)->format('d-m H:i') }}
                    @else
                      <a href="#" class="btn-link complete-call" data-id="{{ $instruction->id }}">Complete</a>
                    @endif
                  </td>
                  <td>
                    @if ($instruction->completed_at)
                      Completed
                    @else
                      @if ($instruction->pending == 0)
                        <a href="#" class="btn-link pending-call" data-id="{{ $instruction->id }}">Mark as Pending</a>
                      @else
                        Pending
                      @endif
                    @endif
                  </td>
                  <td>{{ $instruction->created_at->diffForHumans() }}</td>
                </tr>
            @endforeach
          </table>
        </div>
        {!! $instructions->appends(Request::except('page'))->links() !!}
      </div>

        <div class="tab-pane mt-3" id="5">

          <div class="table-responsive">
              <table class="table table-bordered">
              <tr>
                <th>Client Name</th>
                <th>Number</th>
                <th>Assigned to</th>
                <th>Instructions</th>
                <th colspan="2" class="text-center">Action</th>
                <th>Created at</th>
                {{-- <th>Remark</th> --}}
              </tr>
              @foreach ($completed_instructions as $instruction)
                  <tr>
                    <td><a href="{{ route('customer.show', $instruction->customer_id) }}">{{ $instruction->customer->name }}</a></td>
                    <td>
                      <span data-twilio-call data-context="leads" data-id="{{ $instruction->id }}">{{ $instruction->customer->phone }}</span>
                    </td>
                    <td>{{ $users_array[$instruction->assigned_to] }}</td>
                    <td>{{ $instruction->instruction }}</td>
                    <td>
                      @if ($instruction->completed_at)
                        {{ Carbon\Carbon::parse($instruction->completed_at)->format('d-m H:i') }}
                      @else
                        <a href="#" class="btn-link complete-call" data-id="{{ $instruction->id }}">Complete</a>
                      @endif
                    </td>
                    <td>
                      @if ($instruction->completed_at)
                        Completed
                      @else
                        @if ($instruction->pending == 0)
                          <a href="#" class="btn-link pending-call" data-id="{{ $instruction->id }}">Mark as Pending</a>
                        @else
                          Pending
                        @endif
                      @endif
                    </td>
                    <td>{{ $instruction->created_at->diffForHumans() }}</td>
                  </tr>
              @endforeach
          </table>
          </div>
          {!! $completed_instructions->appends(Request::except('completed_page'))->links() !!}
      </div>


    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function() {
         $(".select-multiple").multiselect();
      });

      $(document).on('click', '.complete-call', function(e) {
        e.preventDefault();

        var thiss = $(this);
        var token = "{{ csrf_token() }}";
        var url = "{{ route('instruction.complete') }}";
        var id = $(this).data('id');

        $.ajax({
          type: 'POST',
          url: url,
          data: {
            _token: token,
            id: id
          },
          beforeSend: function() {
            $(thiss).text('Loading');
          }
        }).done( function(response) {
          $(thiss).parent().html(moment(response.time).format('DD-MM HH:mm'));
          $(thiss).remove();
          window.location.href = response.url;
        }).fail(function(errObj) {
          console.log(errObj);
          alert("Could not mark as completed");
        });
      });

      $(document).on('click', '.pending-call', function(e) {
        e.preventDefault();

        var thiss = $(this);
        var token = "{{ csrf_token() }}";
        var url = "{{ route('instruction.pending') }}";
        var id = $(this).data('id');

        $.ajax({
          type: 'POST',
          url: url,
          data: {
            _token: token,
            id: id
          },
          beforeSend: function() {
            $(thiss).text('Loading');
          }
        }).done( function(response) {
          $(thiss).parent().html('Pending');
          $(thiss).remove();
        }).fail(function(errObj) {
          console.log(errObj);
          alert("Could not mark as completed");
        });
      });

      // function addNewRemark(id){
      //   var remark = $('#remark-text_'+id).val();
      //
      //   $.ajax({
      //       type: 'POST',
      //       headers: {
      //           'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
      //       },
      //       url: '{{ route('task.addRemark') }}',
      //       data: {
      //         id:id,
      //         remark:remark,
      //         module_type: 'instruction'
      //       },
      //   }).done(response => {
      //       alert('Remark Added Success!')
      //       window.location.reload();
      //   }).fail(function(response) {
      //     console.log(response);
      //   });
      // }
      //
      // $(".view-remark").click(function () {
      //   var id = $(this).attr('data-id');
      //
      //     $.ajax({
      //         type: 'GET',
      //         headers: {
      //             'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
      //         },
      //         url: '{{ route('task.gettaskremark') }}',
      //         data: {
      //           id:id,
      //           module_type: "instruction"
      //         },
      //     }).done(response => {
      //         var html='';
      //
      //         $.each(response, function( index, value ) {
      //           html+=' <p> '+value.remark+' <br> <small>By ' + value.user_name + ' updated on '+ moment(value.created_at).format('DD-M H:mm') +' </small></p>';
      //           html+"<hr>";
      //         });
      //         $("#view-remark-list").find('#remark-list').html(html);
      //     });
      // });
    </script>

@endsection
