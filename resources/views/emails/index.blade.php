@extends('layouts.app')

@section('content')

@section('styles')

<style type="text/css">
    #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
</style>
@endsection
<div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
<div class="row">
        <div class="col-12">
            <h2 class="page-heading">Emails List</h2>
          </div>

          <div class="col-12 mb-3">
            <div class="pull-left">

                <form class="form-inline" >
                  <div class="form-group">
                    <input id="term" name="term" type="text" class="form-control"
                           value="{{ isset($term) ? $term : '' }}"
                           placeholder="Search">
                  </div>
                  <div class="form-group ml-3">
                    <div class='input-group date' id='email-datetime'>
                      <input type='text' class="form-control" id="date" name="date" value="{{ isset($date) ? $date : '' }}" />
                      <span class="input-group-addon">
                      <i class="fa fa-calendar" aria-hidden="true"></i>
                      </span>
                    </div>
                  </div>
                  <div class="form-group ml-3">
                    <select class="form-control" name="type" id="type">
                      <option value="">Select one type</option>
                        <option value="incomming" {{ isset($type) && ($type == 'incomming') ? 'selected' : '' }}>Incomming</option>
                        <option value="outgoing" {{ isset($type) && ($type == 'outgoing') ? 'selected' : '' }}>Outgoing</option>
                    </select>
                  </div>

                  <button type="submit" class="btn btn-image ml-3 search-btn"><i class="fa fa-filter" aria-hidden="true"></i></button>
                </form>
            </div>
        </div>
    </div>
<div class="table-responsive" style="margin-top:20px;">
      <table class="table table-bordered" style="border: 1px solid #ddd;" id="email-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Sender</th>
            <th>Receiver</th>
            <th>mail type</th>
            <th>Subject</th>
            <th>Body</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
          <!-- @foreach ($emails as $key => $email)
            <tr>
              <td>{{ Carbon\Carbon::parse($email->created_at)->format('d-m-Y') }}</td>
              <td>{{ $email->from }}</td>
              <td>{{ $email->to }}</td>
              <td>{{ $email->type }}</td>
              <td>
                {{$email->subject}}
              </td>
              <td>
                {{$email->message}}
              </td>
              <td>
              </td>
            </tr>
          @endforeach -->
          @include('emails.search')
        </tbody>
      </table>
      {{$emails->links()}}
</div>

<div id="replyMail" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Email reply</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div id="reply-mail-content">
            </div>
        </div>
    </div>
</div>

<div id="viewMail" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">View Email</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <p><strong>Subject : </strong> <span id="emailSubject"></span> </p>
              <p><strong>Message : </strong> <span id="emailMsg"></span> </p>
            </div>
        </div>
    </div>
</div>


@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
        $('#email-datetime').datetimepicker({
            format: 'YYYY-MM-DD'
        });
        });


    $(document).on('click', '.search-btn', function(e) {
      e.preventDefault();
      var term = $("#term").val();
      var date = $("#date").val();
      var type = $("#type").val();
      console.log(window.url);
        $.ajax({
          url: '/email',
          type: 'get',
          data:{
                term:term,
                date:date,
                type:type
            },
            beforeSend: function () {
                $("#loading-image").show();
            },
        }).done( function(response) {
          $("#loading-image").hide();
            $("#email-table tbody").empty().html(response.tbody);
            if (response.links.length > 5) {
                $('ul.pagination').replaceWith(response.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }
        }).fail(function(errObj) {
          $("#loading-image").hide();
        });
    });


    $(document).on('click', '.resend-email-btn', function(e) {
      e.preventDefault();
      var $this = $(this);
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: '/email/resendMail/'+$this.data("id"),
          type: 'post',
            beforeSend: function () {
                $("#loading-image").show();
            },
        }).done( function(response) {
          toastr['success'](response.message);
          $("#loading-image").hide();
        }).fail(function(errObj) {
          $("#loading-image").hide();
        });
    });

    $(document).on('click', '.reply-email-btn', function(e) {
      e.preventDefault();
      var $this = $(this);
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: '/email/replyMail/'+$this.data("id"),
          type: 'get',
            // beforeSend: function () {
            //     $("#loading-image").show();
            // },
        }).done( function(response) {
          // toastr['success'](response.message);
          $("#reply-mail-content").html(response);
        }).fail(function(errObj) {
          // $("#loading-image").hide();
        });
    });

    $(document).on('click', '.submit-reply', function(e) {
      e.preventDefault();
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: '/email/replyMail',
          type: 'post',
            // beforeSend: function () {
            //     $("#loading-image").show();
            // },
        }).done( function(response) {
          toastr['success'](response.message);
          $("#replyMail").hide();
        }).fail(function(errObj) {
          // $("#loading-image").hide();
          $("#replyMail").hide();
        });
    });

    function opnMsg(email) {
      console.log(email);
      $('#emailSubject').html(email.subject);
      $('#emailMsg').html(email.message);
      //  
    }
    </script>
    

@endsection

