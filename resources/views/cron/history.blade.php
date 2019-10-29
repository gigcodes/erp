@extends('layouts.app')

@section('styles')
    <style>
        .users {
            display: none;
        }

    </style>

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">

@endsection


@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Cron Job History </h2>
            <div class="pull-left">
                <form action="{{ route('cron.history.search') }}" method="POST" class="form-inline align-items-start">
                    
                    <div class="form-group ml-3">
                        <div class='input-group date' id='filter-date'>
                            <input type='text' class="form-control" name="date" value="{{ isset($date) ? $date : '' }}" placeholder="Date" />

                            <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                        </div>
                    </div>

                    <input type="hidden" name="signature" value="{{ $signature }}">
                    <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
                </form>
            </div>
            <div class="pull-right">
              <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#passwordCreateModal">+</a>
            </div>
        </div>
    </div>

    

    <div class="table-responsive mt-3">
      <table class="table table-bordered">
        <thead>
          <tr>
           <th>Signature</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Updated</th>
          </tr>
        </thead>

        <tbody>

        @if($reports->isEmpty())

            <tr>
                <td>
                    No Result Found
                </td>
            </tr>
        @else

          @foreach ($reports as $report)

            <tr>
            	<td>
            		{{ $report->signature }}
            	</td>
              <td>
                {{ $report->start_time }}
              </td>
              <td>{{ $report->end_time }}</td>
             
              <td>{{ $report->updated_at->format('Y-m-d H:i:s')  }}</td>
              
              
            </tr>


          @endforeach
          {!! $reports->appends(Request::except('page'))->links() !!}
          @endif
        </tbody>
      </table>
    </div>

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script>

        $(document).ready(function() {
            $(".select-multiple").multiselect();
            $(".select-multiple2").select2();
        });

        $('#filter-date').datetimepicker({
            format: 'YYYY-MM-DD'
        });


    function changePassword(password_id) {
        $("#passwordEditModal"+ password_id +"" ).modal('show');
    }
    $(".check").change(function() {
        if(this.checked) {
            $(".users").show();
        }else{
            $(".users").hide();
        }
    });

    function getData(password_id) {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: "{{ route('password.history') }}",
            data: {"_token": "{{ csrf_token() }}", "password_id": password_id},
            dataType: "json",
            success: function (message) {
               $c =  message.length;
                if($c == 0){
                   alert('No History Exist');
                } else{
                    var detials="";
                    $.each( message, function( key, value ) {
                        detials += "<tr><th>" + value.website + "</th><th>" + value.username +"</th><th>" + value.password_decrypt + "</th><th>" + value.registered_with +"</th><tr>";
                    });
                    console.log(detials);
                    $('#data').html(detials);
                    $("#getHistory").modal('show');
                }
            }, error: function () {

            }

        });
    }
</script>
@endsection
