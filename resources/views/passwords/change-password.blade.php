@extends('layouts.app')

@section('styles')
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet">
    <style src="https://cdnjs.cloudflare.com/ajax/libs/prettify/r298/prettify.min.css"></style>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
@endsection
@section('content')
    
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Passwords Manager</h2>
            <div class="pull-left">

            </div>
            <div class="pull-right">
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#passwordCreateModal">+</button>
            </div>
            <div>
                {{ Form::open(array('url' => route('password.change'), 'method' => 'post')) }}
                    <input type="hidden" name="users" id="userIds">
                    <button type="submit" class="btn btn-secondary"> Generate password </button>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    @include('partials.flash_messages')
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
              <table class="table table-bordered" id="passwords-table">
                <thead>
                  <tr>
                    <th>#ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Send WhatsApp</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                      <tr>
                            <td > <input type="checkbox" class="checkbox_ch" id="u{{ $user->id }}" name="userIds[]" value="{{ $user->id }}"></td>
                            <td><label for="u{{ $user->id }}"> {{ $user->name }} </label></td>
                            <td><label for="u{{ $user->id }}" > {{ $user->email }}</label></td>
                            <td>Send WhatsApp</td>
                            <td>
                                <button class="btn btn-xs btn-none-border show_password_history" data-id="{{ $user->id }}" data-email="{{ $user->email }}" title="Password Email History"><i class="fa fa-eye"></i></button>
                                <button class="btn btn-xs btn-none-border send_password_email" data-id="{{ $user->id }}" data-email="{{ $user->email }}" title="Send Email" data-toggle="modal" data-target="#passwordSendEmailModal"><i class="fa fa-envelope"></i></button>
                            </td>
                      </tr>
                    @endforeach
                </tbody>
              </table>
            </div>
        </div>

       {{-- <div class="col-xs-5">
            <h3>Select Users To Change Password</h3>
            <select name="from[]" id="keepRenderingSort" class="form-control" size="8" multiple="multiple">
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
 --}}
        {{-- <div class="col-xs-2">
            <h3>Action</h3>
            <button type="button" id="keepRenderingSort_rightAll" class="btn btn-block"><i class="glyphicon glyphicon-forward"></i></button>
            <button type="button" id="keepRenderingSort_rightSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
            <button type="button" id="keepRenderingSort_leftSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
            <button type="button" id="keepRenderingSort_leftAll" class="btn btn-block"><i class="glyphicon glyphicon-backward"></i></button>
        </div> --}}

       {{--  <div class="col-xs-5">
            <h3>Selected Users</h3>
            {{ Form::open(array('url' => route('password.change'), 'method' => 'post')) }}
            @csrf
            <select name="users[]" id="keepRenderingSort_to" class="form-control" size="8" multiple="multiple"></select>
            <br>
            <div class="pull-right">
                <button type="submit" class="btn btn-primary btn-md">Proceed</button>
            </div>
            {{ Form::close() }}
        </div> --}}

    </div>
<div id="passwordSendEmailModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <form action="{{ route('password.send.email') }}" method="POST">
            @csrf

            <div class="modal-header">
              <h4 class="modal-title">Send Email</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="value">Send To</label>
                    <input type="email" name="email" id="email" class="form-control"  placeholder="Enter Email" required>
                    @if ($errors->has('email'))
                    <div class="alert alert-danger">{{$errors->first('email')}}</div>
                    @endif
                </div>
                <div class="form-group">
                    <strong>From Mail</strong>
                    <select class="form-control" name="from_email" required>
                        <?php $emailAddressArr = \App\EmailAddress::all(); ?>
                        @foreach ($emailAddressArr as $emailAddress)
                        <option value="{{ $emailAddress->from_address }}">{{ $emailAddress->from_name }} - {{ $emailAddress->from_address }} </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary">Send</button>
            </div>
          </form>
        </div>

    </div>
</div>

<div id="passwordSendEmailHistoryModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="padding: 0px;width: 90%;max-width: 90%;">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Password Email History</h4>
        </div>
        <div class="modal-body">
            <table class="table table-bordered table-hover" style="table-layout:fixed;">
            <thead>
                <th style="width:20%">Modal Type </th>
                <th style="width:20%">To Email</th>
                <th style="width:20%">From Email</th>
                <th style="width:20%">Subject</th>
                <th style="width:25%">Message</th>
                <th style="width:10%">Date</th>
            </thead>
            <tbody class="password-email-history">

            </tbody>
            </table>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>



@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prettify/r298/prettify.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/multiselect/2.2.9/js/multiselect.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">

        $(document).ready( function () {
            $('#passwords-table').DataTable();
        });

         $('.checkbox_ch').change(function(){
             var values = $('input[name="userIds[]"]:checked').map(function(){return $(this).val();}).get();
             $('#userIds').val(values);
         });

        jQuery(document).ready(function($) {
            $('#keepRenderingSort').multiselect({
                keepRenderingSort: true
            });
        });

        $(".send_password_email").on("click", function(){
            var email = $(this).data('email');
            $("#email").val(email);
        });

        $(document).on("click", ".show_password_history", function() {
            var email = $(this).data('email');
            $.ajax({
                method: "GET",
                url: "{{ route('password.email.history') }}" ,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "email": email,
                },
                dataType: 'html'
                })
            .done(function(result) {
                $('#passwordSendEmailHistoryModal').modal('show');
                $('.password-email-history').html(result);
            });
        });
    </script>
@endsection