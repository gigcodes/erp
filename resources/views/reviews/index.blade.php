@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Reviews</h2>
            <div class="pull-left">
              <form action="{{ route('review.index') }}" method="GET">
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
              </form>

            </div>
            <div class="pull-right">
              <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#accountCreateModal">Create Account</a>
              <button type="button" class="btn btn-secondary ml-3" data-toggle="modal" data-target="#scheduleReviewModal">Schedule Review</a>
              <button type="button" class="btn btn-secondary ml-3" data-toggle="modal" data-target="#complaintCreateModal">Create Complaint</a>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div id="exTab2" class="container mt-3">
      <ul class="nav nav-tabs">
        <li class="active">
          <a href="#accounts_tab" data-toggle="tab">Accounts</a>
        </li>
        <li>
          <a href="#reviews_tab" data-toggle="tab">Reviews</a>
        </li>
        <li>
          <a href="#posted_tab" data-toggle="tab">Posted Reviews</a>
        </li>
        <li>
          <a href="#complaints_tab" data-toggle="tab">Customer Complaints</a>
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
                  <td>{{ \Carbon\Carbon::parse($schedule->review_schedule->date)->format('d-m') }}</td>
                  <td>{{ ucwords($schedule->platform) }}</td>
                  <td>{{ $schedule->review_schedule->review_count }}</td>
                  <td class="{{ $schedule->is_approved == 1 ? 'text-success' : ($schedule->is_approved == 2 ? 'text-danger' : '') }}">
                    @php
                      preg_match_all('/(#\w*)/', $schedule->review, $match);

                      $new_review = $schedule->review;
                      foreach ($match[0] as $hashtag) {
                        $exploded_review = explode($hashtag, $new_review);
                        $new_hashtag = "<a target='_new' href='https://www.instagram.com/explore/tags/" . str_replace('#', '', $hashtag) . "'>" . $hashtag . "</a> ";
                        $new_review = implode($new_hashtag, $exploded_review);
                      }
                    @endphp

                    <span class="review-container">
                      {!! $new_review !!}
                    </span>

                    <textarea name="review" class="form-control review-edit-textarea hidden" rows="8" cols="80">{{ $schedule->review }}</textarea>

                    @if ($schedule->is_approved == 0)
                       -
                      <a href="#" class="btn-link review-approve-button" data-status="1" data-id="{{ $schedule->id }}">Approve</a>
                      <a href="#" class="btn-link review-approve-button" data-status="2" data-id="{{ $schedule->id }}">Reject</a>
                    @endif

                    <a href="#" class="btn-link quick-edit-review-button" data-id="{{ $schedule->id }}">Edit</a>
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

                    @if (count($schedule->status_changes) > 0)
                      <button type="button" class="btn btn-xs btn-secondary change-history-toggle">?</button>

                      <div class="change-history-container hidden">
                        <ul>
                          @foreach ($schedule->status_changes as $status_history)
                            <li>
                              {{ array_key_exists($status_history->user_id, $users_array) ? $users_array[$status_history->user_id] : 'Unknown User' }} - <strong>from</strong>: {{ $status_history->from_status }} <strong>to</strong> - {{ $status_history->to_status }} <strong>on</strong> {{ \Carbon\Carbon::parse($status_history->created_at)->format('H:i d-m') }}
                            </li>
                          @endforeach
                        </ul>
                      </div>
                    @endif
                  </td>
                  <td>
                    {{-- <button type="button" class="btn btn-image edit-schedule" data-toggle="modal" data-target="#scheduleEditModal" data-schedule="{{ $schedule }}" data-reviews="{{ $schedule }}"><img src="/images/edit.png" /></button> --}}
                    <button type="button" class="btn btn-image edit-review" data-toggle="modal" data-target="#reviewEditModal" data-review="{{ $schedule }}"><img src="/images/edit.png" /></button>

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

      <div class="tab-pane mt-3" id="posted_tab">
        <div class="table-responsive mt-3">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Posted Date</th>
                <th>Account</th>
                <th>Customer</th>
                <th>Platform</th>
                <th>Reviews for Approval</th>
                <th>Review Link</th>
                {{-- <th>Status</th> --}}
                <th>Actions</th>
              </tr>
            </thead>

            <tbody>
              @foreach ($posted_reviews as $review)
                <tr>
                  <td>{{ $review->posted_date ? \Carbon\Carbon::parse($review->posted_date)->format('d-m') : '' }}</td>
                  <td>{{ $review->account->email ?? '' }} ({{ ucwords($review->account->platform ?? '') }})</td>
                  <td>
                    @if ($review->customer)
                      <a href="{{ route('customer.show', $review->customer->id) }}" target="_blank">{{ $review->customer->name }}</a>
                    @endif
                  </td>
                  <td>{{ ucwords($review->review_schedule->platform) }}</td>
                  <td>
                    @php
                      preg_match_all('/(#\w*)/', $review->review, $match);

                      $new_review = $review->review;
                      foreach ($match[0] as $hashtag) {
                        $exploded_review = explode($hashtag, $new_review);
                        $new_hashtag = "<a target='_new' href='https://www.instagram.com/explore/tags/" . str_replace('#', '', $hashtag) . "'>" . $hashtag . "</a> ";
                        $new_review = implode($new_hashtag, $exploded_review);
                      }
                    @endphp

                    {!! $new_review !!}
                  </td>
                  <td>
                    <a href="{{ $review->review_link }}" target="_blank">{{ $review->review_link }}</a>
                  </td>
                  {{-- <td>
                    <div class="form-group">
                      <select class="form-control update-schedule-status" name="status" data-id="{{ $review->id }}" required>
                        <option value="prepare" {{ 'prepare' == $review->status ? 'selected' : '' }}>Prepare</option>
                        <option value="prepared" {{ 'prepared' == $review->status ? 'selected' : '' }}>Prepared</option>
                        <option value="posted" {{ 'posted' == $review->status ? 'selected' : '' }}>Posted</option>
                        <option value="pending" {{ 'pending' == $review->status ? 'selected' : '' }}>Pending</option>
                      </select>

                      <span class="text-success change_status_message" style="display: none;">Successfully changed schedule status</span>
                    </div>
                  </td> --}}
                  <td>
                    <button type="button" class="btn btn-image edit-review" data-toggle="modal" data-target="#reviewEditModal" data-review="{{ $review }}"><img src="/images/edit.png" /></button>

                    {!! Form::open(['method' => 'DELETE','route' => ['review.destroy', $review->id],'style'=>'display:inline']) !!}
                      <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                    {!! Form::close() !!}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {!! $posted_reviews->appends(Request::except('posted-page'))->links() !!}
      </div>

      <div class="tab-pane mt-3" id="complaints_tab">
        <div class="table-responsive mt-3">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Date</th>
                <th>Customer</th>
                <th>Platform</th>
                <th>Conversation thread</th>
                <th>Link</th>
                <th>Notes & Instructions</th>
                <th>Actions</th>
              </tr>
            </thead>

            <tbody>
              @foreach ($complaints as $complaint)
                <tr>
                  <td>{{ \Carbon\Carbon::parse($complaint->date)->format('d-m') }}</td>
                  <td>
                    @if ($complaint->customer)
                      <a href="{{ route('customer.show', $complaint->customer->id) }}" target="_blank">{{ $complaint->customer->name }}</a>
                    @endif
                  </td>
                  <td>{{ ucwords($complaint->platform) }}</td>
                  <td>
                    {{ $complaint->complaint }}

                    @if ($complaint->threads)
                      <ul class="mx-0 px-4">
                        @foreach ($complaint->threads as $key => $thread)
                          <li class="ml-{{ $key + 1 }}">{{ $thread->thread }}</li>
                        @endforeach
                      </ul>
                    @endif
                  </td>
                  <td>
                    <a href="{{ $complaint->link }}" target="_blank">{{ $complaint->link }}</a>
                  </td>
                  <td>
                    <a href class="add-task" data-toggle="modal" data-target="#addRemarkModal" data-id="{{ $complaint->id }}">Add</a>
                    <span> | </span>
                    <a href class="view-remark" data-toggle="modal" data-target="#viewRemarkModal" data-id="{{ $complaint->id }}">View</a>
                  </td>
                  <td>
                    <button type="button" class="btn btn-image edit-complaint" data-toggle="modal" data-target="#complaintEditModal" data-complaint="{{ $complaint }}" data-threads="{{ $complaint->threads }}"><img src="/images/edit.png" /></button>

                    {!! Form::open(['method' => 'DELETE','route' => ['complaint.destroy', $complaint->id],'style'=>'display:inline']) !!}
                      <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                    {!! Form::close() !!}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {!! $complaints->appends(Request::except('complaints-page'))->links() !!}
      </div>
    </div>

    @include('reviews.partials.account-modals')
    @include('reviews.partials.review-schedule-modals')
    @include('reviews.partials.review-modals')
    @include('reviews.partials.complaint-modals')
    @include('reviews.partials.remark-modals')

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>

  <script type="text/javascript">
    $(document).ready(function() {
      $('#birthday-datetime, #account_birthday, #review_date, #edit_review_date, #edit_posted_date, #filter_posted_date, #complaint_date').datetimepicker({
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

    $('#add-complaint-button').on('click', function() {
      var complaint_html = '<div class="form-group"><strong>Thread:</strong><input type="text" name="thread[]" class="form-control" value=""><button type="button" class="btn btn-image btn-secondary remove-review-button"><img src="/images/delete.png" /></button></div>';

      $('#complaint-container').append(complaint_html);
    });

    $('#add-edit-review-button').on('click', function() {
      var review_html = '<div class="form-group"><strong>Review:</strong><input type="text" name="review[]" class="form-control" value=""><button type="button" class="btn btn-image btn-secondary remove-review-button"><img src="/images/delete.png" /></button></div>';

      $('#edit-review-container').append(review_html);
    });

    $('#add-edit-complaint-button').on('click', function() {
      var complaint_html = '<div class="form-group"><strong>Thread:</strong><input type="text" name="thread[]" class="form-control" value=""><button type="button" class="btn btn-image btn-secondary remove-review-button"><img src="/images/delete.png" /></button></div>';

      $('#complaint-container-extra').append(complaint_html);
    });

    $(document).on('click', '.remove-review-button', function() {
      $(this).closest('.form-group').remove();
    });

    $(document).on('click', '.edit-schedule', function() {
      fillEditSchedule(this);
    });

    $(document).on('click', '.edit-review', function() {
      fillEditReview(this);
    });

    $(document).on('click', '.edit-complaint', function() {
      fillEditComplaint(this);
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

      if (status == 'posted') {
        fillEditReview(thiss);
        $('#reviewEditModal').modal();
      }
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
          is_approved: status
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
          $(thiss).closest('td').addClass('text-success');
          $(thiss).remove();
        } else {
          $(thiss).siblings('a').remove();
          $(thiss).closest('td').addClass('text-danger');
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

    function fillEditSchedule(thiss) {
      var schedule = $(thiss).data('schedule');
      var reviews = $(thiss).data('reviews');
      var url = "{{ url('review/schedule') }}/" + schedule.id;

      $('#scheduleEditModal form').attr('action', url);
      $('#edit_review_date').val(schedule.date);
      $('#schedule_platform').val(schedule.platform);
      $('#schedule_review_count').val(schedule.review_count);
      $('#schedule_status  option[value="' + schedule.status + '"]').prop('selected', true);
      // $('#edit_posted_date input').val(schedule.posted_date);
      // $('#edit_review_link').val(schedule.review_link);
      // $('#edit_review_account option[value="' + schedule.account_id + '"]').prop('selected', true);
      // $('#edit_customer_id option[value="' + schedule.customer_id + '"]').prop('selected', true);

      // console.log($('#schedule_status  option[value="' + schedule.status + '"]'));

      if (reviews.length > 0) {
        console.log(reviews);
        $('#edit-review-container #review-container-extra').empty();

        Object.keys(reviews).forEach(function (index) {
          if (index == 0) {
            $('#edit_schedule_review').val(reviews[index].review);
          } else {
            var html = '<div class="form-group"><strong>Review:</strong><input type="text" name="review[]" class="form-control" value="' + reviews[index].review + '"><button type="button" class="btn btn-image btn-secondary remove-review-button"><img src="/images/delete.png" /></button></div>';

            $('#edit-review-container #review-container-extra').append(html);
          }
        });
      } else {
        $('#edit_schedule_review').val('');
        $('#edit-review-container #review-container-extra').empty();
      }
    }

    function fillEditReview(thiss) {
      var review = $(thiss).data('review');
      var url = "{{ url('review') }}/" + review.id;

      $('#reviewEditModal form').attr('action', url);
      $('#edit_posted_date input').val(review.posted_date);
      $('#edit_review_link').val(review.review_link);
      $('#edit_review_review').val(review.review);
      $('#edit_review_account option[value="' + review.account_id + '"]').prop('selected', true);
      $('#edit_customer_id option[value="' + review.customer_id + '"]').prop('selected', true);
    }

    function fillEditComplaint(thiss) {
      var complaint = $(thiss).data('complaint');
      var threads = $(thiss).data('threads');
      var url = "{{ url('complaint') }}/" + complaint.id;

      $('#complaintEditModal form').attr('action', url);
      $('#complaint_customer_id option[value="' + complaint.customer_id + '"]').prop('selected', true);
      $('#edit_complaint_date input').val(complaint.date);
      $('#complaint_platform option[value="' + complaint.platform + '"]').prop('selected', true);
      $('#complaint_complaint').val(complaint.complaint);
      $('#complaint_link').val(complaint.link);

      $('#complaint-container-extra').empty();
      Object.keys(threads).forEach(function(index) {
        var complaint_html = '<div class="form-group"><strong>Thread:</strong><input type="text" name="thread[]" class="form-control" value="' + threads[index].thread + '"><button type="button" class="btn btn-image btn-secondary remove-review-button"><img src="/images/delete.png" /></button></div>';

        $('#complaint-container-extra').append(complaint_html);
      });
    }

    // $(document).on('keyup', '.review-input-field', function() {
    //   if (/(#\w*)/.test($(this).val())) {
    //     console.log('yra');
    //   } else {
    //     // console.log();
    //   }
    // });

    $('.add-task').on('click', function(e) {
      e.preventDefault();
      var id = $(this).data('id');
      $('#add-remark input[name="id"]').val(id);
    });

    $('#addRemarkButton').on('click', function() {
      var id = $('#add-remark input[name="id"]').val();
      var remark = $('#add-remark textarea[name="remark"]').val();

      $.ajax({
          type: 'POST',
          headers: {
              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
          },
          url: '{{ route('task.addRemark') }}',
          data: {
            id:id,
            remark:remark,
            module_type: 'complaint'
          },
      }).done(response => {
          alert('Remark Added Success!')
          window.location.reload();
      }).fail(function(response) {
        console.log(response);
      });
    });


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
              module_type: "complaint"
            },
        }).done(response => {
            var html='';

            $.each(response, function( index, value ) {
              html+=' <p> '+value.remark+' <br> <small>By ' + value.user_name + ' updated on '+ moment(value.created_at).format('DD-M H:mm') +' </small></p>';
              html+"<hr>";
            });
            $("#viewRemarkModal").find('#remark-list').html(html);
        });
    });

    $(document).on('click', '.quick-edit-review-button', function(e) {
      e.preventDefault();

      var id = $(this).data('id');

      $(this).siblings('.review-edit-textarea').removeClass('hidden');
      $(this).siblings('.review-container').addClass('hidden');

      $(this).siblings('.review-edit-textarea').keypress(function(e) {
        var key = e.which;
        var thiss = $(this);

        if (key == 13) {
          e.preventDefault();
          var review = $(thiss).val();

          $.ajax({
            type: 'POST',
            url: "{{ url('review') }}/" + id + '/updateReview',
            data: {
              _token: "{{ csrf_token() }}",
              review: review,
            }
          }).done(function() {
            $(thiss).addClass('hidden');
            $(thiss).siblings('.review-container').text(review);
            $(thiss).siblings('.review-container').removeClass('hidden');
          }).fail(function(response) {
            console.log(response);

            alert('Could not update review');
          });
        }
      });
    });

    $(document).on('click', '.change-history-toggle', function() {
      $(this).siblings('.change-history-container').toggleClass('hidden');
    });
  </script>
@endsection
