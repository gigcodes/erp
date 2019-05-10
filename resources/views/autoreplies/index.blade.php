@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Auto Replies</h2>
            <div class="pull-left">
              {{-- <form action="{{ route('review.index') }}" method="GET">
                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <select class="form-control" name="platform">
                        <option value="">Select Platform</option>
                        <option value="instagram" {{ 'instagram' == $filter_platform ? 'selected' : '' }}>Instagram</option>
                        <option value="facebook" {{ 'facebook' == $filter_platform ? 'selected' : '' }}>Facebook</option>
                        <option value="sitejabber" {{ 'sitejabber' == $filter_platform ? 'selected' : '' }}>Sitejabber</option>
                        <option value="google" {{ 'google' == $filter_platform ? 'selected' : '' }}>Google</option>
                        <option value="trustpilot" {{ 'trustpilot' == $filter_platform ? 'selected' : '' }}>Trustpilot</option>
                      </select>
                    </div>
                  </div>

                  <div class="col">
                    <div class="form-group ml-3">
                      <div class='input-group date' id='filter_posted_date'>
                        <input type='text' class="form-control" name="posted_date" value="{{ $filter_posted_date }}" />

                        <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col">
                    <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
                  </div>
                </div>
              </form> --}}
            </div>
            <div class="pull-right">
              <div class="form-inline">
                <div class="form-inline">
                  <input type="checkbox" id="turn_off_automated" name="show_automated_messages" value="" {{ $show_automated_messages == 1 ? 'checked' : '' }}>
                  <label for="#turn_off_automated">Show Automated Messages</label>

                    <span class="text-success change_status_message" style="display: none;">Successfully saved</span>
                </div>

                <button type="button" class="btn btn-secondary ml-3" data-toggle="modal" data-target="#autoReplyCreateModal">Create</a>
              </div>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div id="exTab2" class="container">
      <ul class="nav nav-tabs">
        <li class="active">
          <a href="#text-autoreplies" data-toggle="tab">Text Auto Replies</a>
        </li>
        <li>
          <a href="#priority-customers" data-toggle="tab">Priority Customers</a>
        </li>
      </ul>
    </div>

    <div class="tab-content">
      <div class="tab-pane active mt-3" id="text-autoreplies">
        <div class="table-responsive mt-3">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>ID</th>
                <th>Keyword</th>
                <th>Reply</th>
                {{-- <th>Actions</th> --}}
              </tr>
            </thead>

            <tbody>
              @php
                $count = 0;
              @endphp
              @foreach ($auto_replies as $reply => $data)
                <tr>
                  <td>{{ $count + 1 }}</td>
                  <td>{{ $reply }}</td>
                  <td>
                    <ul>
                      @foreach ($data as $key => $auto_reply)
                        <li>
                          {{ $auto_reply['keyword'] }}

                          <button type="button" class="btn btn-image edit-auto-reply" data-toggle="modal" data-target="#autoReplyEditModal" data-reply="{{ json_encode($auto_reply) }}"><img src="/images/edit.png" /></button>

                          {!! Form::open(['method' => 'DELETE','route' => ['autoreply.destroy', $auto_reply['id']],'style'=>'display:inline']) !!}
                            <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                          {!! Form::close() !!}
                        </li>
                      @endforeach
                    </ul>
                  </td>

                  @php
                    $count++;
                  @endphp
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {!! $auto_replies->appends(Request::except('page'))->links() !!}
      </div>

      <div class="tab-pane mt-3" id="priority-customers">
        <div class="table-responsive mt-3">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>ID</th>
                <th>Keyword</th>
                <th>Sending Time</th>
                <th>Repeat</th>
                <th>Actions</th>
              </tr>
            </thead>

            <tbody>
              @foreach ($priority_customers_replies as $key => $reply)
                <tr>
                  <td>{{ $key + 1 }}</td>
                  <td>{{ $reply->reply }}</td>
                  <td>{{ $reply->sending_time ? \Carbon\Carbon::parse($reply->sending_time)->format('H:i d-m') : '' }}</td>
                  <td>{{ $reply->repeat }}</td>
                  <td>
                    <button type="button" class="btn btn-image edit-auto-reply" data-toggle="modal" data-target="#autoReplyEditModal" data-reply="{{ $reply }}"><img src="/images/edit.png" /></button>

                    {!! Form::open(['method' => 'DELETE','route' => ['autoreply.destroy', $reply->id],'style'=>'display:inline']) !!}
                      <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                    {!! Form::close() !!}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {!! $priority_customers_replies->appends(Request::except('priority-page'))->links() !!}
      </div>
    </div>

    @include('autoreplies.partials.autoreply-modals')

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script type="text/javascript">
    $('#sending-datetime, #edit-sending-datetime').datetimepicker({
      format: 'YYYY-MM-DD HH:mm'
    });

    $(document).on('click', '.edit-auto-reply', function() {
      var autoreply = $(this).data('reply');
      var url = "{{ url('autoreply') }}/" + autoreply.id;

      $('#autoReplyEditModal form').attr('action', url);
      $('#autoreply_type').val(autoreply.type);
      $('#autoreply_keyword').val(autoreply.keyword);
      $('#autoreply_reply').val(autoreply.reply);
      $('#autoreply_sending_time').val(autoreply.sending_time);
      $('#autoreply_repeat').val(autoreply.repeat);
    });

    $('#turn_off_automated').on('click', function() {
      var checked = $(this).prop('checked');
      var thiss = $(this);

      $.ajax({
        type: "POST",
        url: "{{ route('settings.update.automessages') }}",
        data: {
          _token: "{{ csrf_token() }}",
          value: checked ? 1 : 0
        }
      }).done(function() {
        $(thiss).siblings('.change_status_message').fadeIn(400);

        setTimeout(function () {
          $(thiss).siblings('.change_status_message').fadeOut(400);
        }, 2000);
      }).fail(function(response) {
        console.log(response);

        alert('Could not saved the changes');
      })
    });
  </script>
@endsection
