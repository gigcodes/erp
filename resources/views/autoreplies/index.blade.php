@extends('layouts.app')

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
              <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#autoReplyCreateModal">Create</a>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="table-responsive mt-3">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>ID</th>
            <th>Keyword</th>
            <th>Reply</th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($auto_replies as $key => $auto_reply)
            <tr>
              <td>{{ $key + 1 }}</td>
              <td>{{ $auto_reply->keyword }}</td>
              <td>{{ $auto_reply->reply }}</td>
              <td>
                <button type="button" class="btn btn-image edit-auto-reply" data-toggle="modal" data-target="#autoReplyEditModal" data-reply="{{ $auto_reply }}"><img src="/images/edit.png" /></button>

                {!! Form::open(['method' => 'DELETE','route' => ['autoreply.destroy', $auto_reply->id],'style'=>'display:inline']) !!}
                  <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                {!! Form::close() !!}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {!! $auto_replies->appends(Request::except('page'))->links() !!}

    @include('autoreplies.partials.autoreply-modals')

@endsection

@section('scripts')

  <script type="text/javascript">
    $(document).on('click', '.edit-auto-reply', function() {
      var autoreply = $(this).data('reply');
      var url = "{{ url('autoreply') }}/" + autoreply.id;

      $('#autoReplyEditModal form').attr('action', url);
      $('#autoreply_keyword').val(autoreply.keyword);
      $('#autoreply_reply').val(autoreply.reply);
    });
  </script>
@endsection
