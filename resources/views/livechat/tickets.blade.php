@extends('layouts.app')

@section('content')

@php($users = $query = \App\User::get())


    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">{{ (isset($title)) ? ucfirst($title) : "tickets"}} (<span id="list_count">{{ $data->total() }}</span>)  </h2>
            <div class="pull-left">
            <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <select class="form-control" name="users_id" id="users_id">
                                    <option value="">Select Users</option>
                                    @foreach($users as $key => $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" name="ticket_id" id="ticket">
                                    <option value="">Select Ticket</option>
                                    @foreach($data as $key => $ticket)
                                    <option value="{{ $ticket->ticket_id }}">{{ $ticket->ticket_id }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <select class="form-control" name="status_id" id="status_id">
                                    <option value="">Select Status</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class='input-group date' id='filter_date'>
                                    <input type='text' class="form-control" id="date" name="date" value="" />

                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            </div>
                            <div class="clearfix">&nbsp;&nbsp;</div>
                            <div class="row">
                            <div class="col-md-3">
                                <input name="term" type="text" class="form-control"
                                       value="{{ isset($term) ? $term : '' }}"
                                       placeholder="Name of User" id="term">
                            </div>
                            <div class="col-md-2">
                               <button type="button" class="btn btn-image" onclick="submitSearch()"><img src="{{ asset('images/filter.png')}}"/></button>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-image" id="resetFilter" onclick="resetSearch()"><img src="{{ asset('images/resend2.png')}}"/></button>    
                            </div>
                        </div>
                    </div>
           
            </div>
            <div class="pull-right">
            </div>
        </div>
    </div>

    

    <div class="table-responsive mt-3">
      <table class="table table-bordered" id="list-table">
        <thead>
          <tr>
            <th>Sr. No.</th>
            <th>Ticket</th>
            <th>Name</th>
            <th>Email</th>
            <th>Subject</th>
            <th>Message</th>
            <th>Assigned To</th>
            <th>Status</th>
            <th>Action</th>
            
           
          </tr>
        </thead>

        <tbody>
        @include('livechat.partials.ticket-list')

        </tbody>
      </table>
    </div>
    {!! $data->render() !!}

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  
 <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  
<script type="text/javascript">
   // $('.select-multiple').select2({width: '100%'});
   $('#filter_date').datetimepicker({
        format: 'YYYY-MM-DD'
    });

    function submitSearch(){
        src = "{{url('livechat/tickets')}}";
        term = $('#term').val();
        ticket_id = $('#ticket').val();
        status_id = $('#status_id').val();
        date = $('#date').val();
        $.ajax({
            url: src,
            dataType: "json",
            data: {
                term : term,
                ticket_id : ticket_id,
                status_id : status_id,
                date : date

            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $("#list-table tbody").empty().html(data.tbody);
            $("#list_count").text(data.count);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }

        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });
        
    }

    function resetSearch(){
      src = "{{url('livechat/tickets')}}";
        blank = '';
        $.ajax({
            url: src,
            dataType: "json",
            data: {
               
               blank : blank, 

            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $('#term').val('')
            $('#ticket').val('')
            $("#list-table tbody").empty().html(data.tbody);
            $("#list_count").text(data.count);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }

        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });
    }
</script>

@endsection

