@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Reviews</h2>
            <div class="pull-left">
              {{-- <form action="/order/" method="GET">
                  <div class="form-group">
                      <div class="row">
                          <div class="col-md-12">
                              <input name="term" type="text" class="form-control"
                                     value="{{ isset($term) ? $term : '' }}"
                                     placeholder="Search">
                          </div>
                          <div class="col-md-4">
                              <button hidden type="submit" class="btn btn-primary">Submit</button>
                          </div>
                      </div>
                  </div>
              </form> --}}
            </div>
            <div class="pull-right">
              <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#accountCreateModal">Create Account</a>
              <button type="button" class="btn btn-secondary ml-3" data-toggle="modal" data-target="#scheduleReviewModal">Schedule Review</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div id="exTab2" class="container mt-3">
      <ul class="nav nav-tabs">
        <li class="active">
          <a href="#accounts_tab" data-toggle="tab">Accounts</a>
        </li>
        <li>
          <a href="#reviews_tab" data-toggle="tab">Reviews</a>
        </li>
      </u>
    </div>

    <div class="tab-content">
      <div class="tab-pane active mt-3" id="accounts_tab">
        <div class="table-responsive mt-3">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Password</th>
                <th>DOB</th>
                <th>Platform</th>
                <th>Followers</th>
                <th>Posts</th>
                <th>DP</th>
                <th>Actions</th>
              </tr>
            </thead>

            <tbody>
              @foreach ($accounts as $account)
                <tr>
                  <td>{{ $account->first_name }} {{ $account->last_name }}</td>
                  <td>{{ $account->email }}</td>
                  <td>{{ $account->password }}</td>
                  <td>{{ $account->dob }}</td>
                  <td>{{ ucwords($account->platform) }}</td>
                  <td> {{ $account->followers_count }}</td>
                  <td> {{ $account->posts_count }}</td>
                  <td> {{ $account->dp_count }}</td>
                  <td>
                    <button type="button" class="btn btn-image edit-account" data-toggle="modal" data-target="#accountEditModal" data-account="{{ $account }}"><img src="/images/edit.png" /></button>

                    {!! Form::open(['method' => 'DELETE','route' => ['account.destroy', $account->id],'style'=>'display:inline']) !!}
                      <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                    {!! Form::close() !!}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {!! $accounts->appends(Request::except('page'))->links() !!}
      </div>

      <div class="tab-pane mt-3" id="reviews_tab">
        <div class="table-responsive mt-3">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Date</th>
                <th>Platform</th>
                <th>Number of Reviews</th>
                <th>Reviews for Approval</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>

            <tbody>
              @foreach ($review_schedules as $schedule)
                <tr>
                  <td>{{ \Carbon\Carbon::parse($schedule->date)->format('d-m') }}</td>
                  <td>{{ ucwords($schedule->platform) }}</td>
                  <td>{{ $schedule->review_count }}</td>
                  <td>
                    @if ($schedule->reviews)
                      <ul>
                        @foreach ($schedule->reviews as $review)
                          <li class="{{ $review->status == 1 ? 'text-success' : ($review->status == 2 ? 'text-danger' : '') }}">
                            {{ $review->review }}
                            @if ($review->status == 0)
                               -
                              <a href="#" class="btn-link review-approve-button" data-status="1" data-id="{{ $review->id }}">Approve</a>
                              <a href="#" class="btn-link review-approve-button" data-status="2" data-id="{{ $review->id }}">Reject</a>
                            @endif
                          </li>
                        @endforeach
                      </ul>
                    @endif
                  </td>
                  <td>
                    <div class="form-group">
                      <select class="form-control update-schedule-status" name="status" data-id="{{ $schedule->id }}" required>
                        <option value="prepare" {{ 'prepare' == $schedule->status ? 'selected' : '' }}>Prepare</option>
                        <option value="prepared" {{ 'prepared' == $schedule->status ? 'selected' : '' }}>Prepared</option>
                        <option value="posted" {{ 'posted' == $schedule->status ? 'selected' : '' }}>Posted</option>
                        <option value="pending" {{ 'pending' == $schedule->status ? 'selected' : '' }}>Pending</option>
                      </select>

                      <span class="text-success change_status_message" style="display: none;">Successfully changed schedule status</span>
                    </div>
                  </td>
                  <td>
                    <button type="button" class="btn btn-image edit-schedule" data-toggle="modal" data-target="#scheduleEditModal" data-schedule="{{ $schedule }}" data-reviews="{{ $schedule->reviews }}"><img src="/images/edit.png" /></button>

                    {!! Form::open(['method' => 'DELETE','route' => ['review.schedule.destroy', $schedule->id],'style'=>'display:inline']) !!}
                      <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                    {!! Form::close() !!}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {!! $review_schedules->appends(Request::except('review-page'))->links() !!}
      </div>
    </div>

    @include('reviews.partials.account-modals')
    @include('reviews.partials.review-modals')

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

  <script type="text/javascript">
    $(document).ready(function() {
      $('#birthday-datetime, #account_birthday, #review_date, #edit_review_date').datetimepicker({
        format: 'YYYY-MM-DD'
      });
    });

    $(document).on('click', '.edit-account', function() {
      var account = $(this).data('account');
      var url = "{{ url('account') }}/" + account.id;

      $('#accountEditModal form').attr('action', url);
      $('#account_first_name').val(account.first_name);
      $('#account_last_name').val(account.last_name);
      $('#account_email').val(account.email);
      $('#account_password').val(account.password);
      $('#account_platform').val(account.platform);
      $('#account_followers').val(account.followers_count);
      $('#account_posts').val(account.posts_count);
      $('#account_dp').val(account.dp_count);
      $('#account_birthday').val(account.dob);
    });

    $('#add-review-button').on('click', function() {
      var review_html = '<div class="form-group"><strong>Review:</strong><input type="text" name="review[]" class="form-control" value=""><button type="button" class="btn btn-image btn-secondary remove-review-button"><img src="/images/delete.png" /></button></div>';

      $('#review-container').append(review_html);
    });

    $('#add-edit-review-button').on('click', function() {
      var review_html = '<div class="form-group"><strong>Review:</strong><input type="text" name="review[]" class="form-control" value=""><button type="button" class="btn btn-image btn-secondary remove-review-button"><img src="/images/delete.png" /></button></div>';

      $('#edit-review-container').append(review_html);
    });

    $(document).on('click', '.remove-review-button', function() {
      $(this).closest('.form-group').remove();
    });

    $(document).on('click', '.edit-schedule', function() {
      var schedule = $(this).data('schedule');
      var reviews = $(this).data('reviews');
      var url = "{{ url('review/schedule') }}/" + schedule.id;

      $('#scheduleEditModal form').attr('action', url);
      $('#edit_review_date').val(schedule.date);
      $('#schedule_platform').val(schedule.platform);
      $('#schedule_review_count').val(schedule.review_count);
      $('#schedule_status').val(schedule.status);

      console.log(reviews);

      if (reviews.length > 0) {
        Object.keys(reviews).forEach(function (index) {
          if (index == 0) {
            $('#edit_schedule_review').val(reviews[index].review);
          } else {
            var html = '<div class="form-group"><strong>Review:</strong><input type="text" name="review[]" class="form-control" value="' + reviews[index].review + '"><button type="button" class="btn btn-image btn-secondary remove-review-button"><img src="/images/delete.png" /></button></div>';

            $('#edit-review-container').append(html);
          }
        });
      }
    });

    $(document).on('change', '.update-schedule-status', function() {
      var status = $(this).val();
      var id = $(this).data('id');
      var thiss = $(this);

      $.ajax({
        type: "POST",
        url: "{{ url('review/schedule') }}/" + id + '/status',
        data: {
          _token: "{{ csrf_token() }}",
          status: status
        }
      }).done(function() {
        $(thiss).siblings('.change_status_message').fadeIn(400);

        setTimeout(function () {
          $(thiss).siblings('.change_status_message').fadeOut(400);
        }, 2000);
      }).fail(function(response) {
        alert('Could not change the status');
        console.log(response);
      });
    });

    $(document).on('click', '.review-approve-button', function(e) {
      e.preventDefault();

      var id = $(this).data('id');
      var status = $(this).data('status');
      var thiss = $(this);

      $.ajax({
        type: "POST",
        url: "{{ url('review') }}/" + id + '/updateStatus',
        data: {
          _token: "{{ csrf_token() }}",
          status: status
        },
        beforeSend: function () {
          if (status == 1) {
            $(thiss).text('Approving');
          } else {
            $(thiss).text('Rejecting');
          }
        }
      }).done(function(response) {
        if (response.status == 1) {
          $(thiss).siblings('a').remove();
          $(thiss).closest('li').addClass('text-success');
          $(thiss).remove();
        } else {
          $(thiss).siblings('a').remove();
          $(thiss).closest('li').addClass('text-danger');
          $(thiss).remove();
        }
      }).fail(function(response) {
        alert('Could not change status');
        console.log(response);

        if (status == 1) {
          $(thiss).text('Approve');
        } else {
          $(thiss).text('Reject');
        }
      });
    });
  </script>
@endsection
