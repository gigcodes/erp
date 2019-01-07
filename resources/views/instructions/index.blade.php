@extends('layouts.app')

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

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered">
        <tr>
          <th>Client Name</th>
          <th>Number</th>
          <th>Instructions</th>
          <th colspan="2" class="text-center">Action</th>
          <th>Created at</th>
          <th>Remark</th>
        </tr>
        @foreach ($instructions as $instruction)
            <tr>
              <td>{{ $instruction->customer->name }}</td>
              <td>
                <span data-twilio-call data-context="leads" data-id="{{ $instruction->id }}">{{ $instruction->customer->phone }}</span>
              </td>
              <td>{{ $instruction->instruction }}</td>
              <td>
                @if ($instruction->completed_at)
                  {{ Carbon\Carbon::parse($instruction->completed_at)->format('d-m') }}
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
              <td>
                <a href id="add-new-remark-btn" class="add-task" data-toggle="modal" data-target="#add-new-remark_{{ $instruction->id }}" data-id="{{ $instruction->id }}">Add</a>
                <span> | </span>
                <a href id="view-remark-list-btn" class="view-remark" data-toggle="modal" data-target="#view-remark-list" data-id="{{ $instruction->id }}">View</a>
              </td>

              <!-- Modal -->
              <div id="add-new-remark_{{ $instruction->id }}" class="modal fade" role="dialog">
                <div class="modal-dialog">

                  <!-- Modal content-->
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title">Add New Remark</h4>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                      <form id="add-remark">
                        <input type="hidden" name="id" value="{{ $instruction->id }}">
                        <textarea id="remark-text_{{ $instruction->id }}" rows="1" name="remark" class="form-control"></textarea>
                        <button type="button" class="mt-2 " onclick="addNewRemark({{ $instruction->id }})">Add Remark</button>
                      </form>
                    </div>

                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                  </div>

                </div>
              </div>

              <!-- Modal -->
              <div id="view-remark-list" class="modal fade" role="dialog">
                <div class="modal-dialog">

                  <!-- Modal content-->
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title">View Remark</h4>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>

                    </div>
                    <div class="modal-body">
                      <div id="remark-list">

                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                  </div>

                </div>
              </div>
            </tr>
        @endforeach
    </table>
    </div>

    {!! $instructions->appends(Request::except('page'))->links() !!}

    <script type="text/javascript">
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

      function addNewRemark(id){
        var remark = $('#remark-text_'+id).val();

        $.ajax({
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: '{{ route('task.addRemark') }}',
            data: {
              id:id,
              remark:remark,
              module_type: 'instruction'
            },
        }).done(response => {
            alert('Remark Added Success!')
            window.location.reload();
        }).fail(function(response) {
          console.log(response);
        });
      }

      $(".view-remark").click(function () {
        var id = $(this).attr('data-id');

          $.ajax({
              type: 'GET',
              headers: {
                  'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              url: '{{ route('task.gettaskremark') }}',
              data: {
                id:id,
                module_type: "instruction"
              },
          }).done(response => {
              var html='';

              $.each(response, function( index, value ) {
                html+=' <p> '+value.remark+' <br> <small>By ' + value.user_name + ' updated on '+ moment(value.created_at).format('DD-M H:mm') +' </small></p>';
                html+"<hr>";
              });
              $("#view-remark-list").find('#remark-list').html(html);
          });
      });
    </script>

@endsection
