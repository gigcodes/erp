@extends('layouts.app')


@section('title', $title)

@section('content')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>

<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
        <div class="pull-right">
        <a class="btn btn-secondary" href="{{ route('hubstaff-acitivties.pending-payments') }}">Approved timings</a>
    </div>
    </div>
   
    <br>
    <div class="col-lg-12 margin-tb">
    	<div class="row">
		    <div class="col">
		    	<div class="h" style="margin-bottom:10px;">
		    		<form class="form-inline" action="{{route('hubstaff-acitivties.activities')}}" method="get">
					  <div class="row">
			  			<div class="form-group">
						    <label for="keyword">User:</label>
                            <?php echo Form::select("user_id",["" => "-- Select User --"]+$users,$user_id,["class" => "form-control select2"]); ?>
					  	</div>
					  	<div class="form-group">
		                    <strong>Date Range</strong>
		                    <input type="text" value="{{$start_date}}" name="start_date" hidden/>
		                    <input type="text" value="{{$end_date}}" name="end_date" hidden/>
		                    <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
		                        <i class="fa fa-calendar"></i>&nbsp;
		                        <span></span> <i class="fa fa-caret-down"></i>
		                    </div>
		                </div>
		               	<div class="form-group">
					  		<label for="button">&nbsp;</label>
					  		<button type="submit" style="display: inline-block;width: 10%" class="btn btn-sm btn-image">
					  			<img src="/images/search.png" style="cursor: default;">
					  		</button>
					  	</div>	
					  </div>	
					</form>	
		    	</div>
		    </div>
	    </div>	
		<div class="col-md-12 margin-tb">
        <div class="table-responsive">
        <table class="table table-bordered">
        <tr>
          <th>Date</th>
          <th>User</th>
          <th>Time tracked (Minutes)</th>
          <th>Time approved</th>
          <th>Pending payment time</th>
          <th>Status</th>
          <th width="10%" colspan="2" class="text-center">Action</th>
        </tr>
          @foreach ($activityUsers as $user)
            <tr>
            <td>{{ \Carbon\Carbon::parse($user->date)->format('d-m') }} </td>
              <td>{{ $user->userName }}</td>
              <td>{{number_format($user->total_tracked / 60,2,".",",")}}</td>
              <td><span class="replaceme">{{$user->totalApproved}}</span> </td>
              <td><span>{{$user->totalNotPaid}}</td>
              <td>{{$user->status}}</td>
              <td>
                @if($user->forworded_to == Auth::user()->id && !$user->final_approval)
                <form action="">
                    <input type="hidden" class="user_id" name="user_id" value="{{$user->user_id}}">
                    <input type="hidden" class="date" name="date" value="{{$user->date}}">
                    <a class="btn btn-secondary show-activities">+</a>
                </form>
                @endif
              </td>
          @endforeach
      </table>
    </div>
		</div>
	</div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div id="records-modal" class="modal" role="dialog">
  	<div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" id="record-content">

      </div>
  	</div>	
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>

<script type="text/javascript">

$(".select2").select2({tags:true});

let r_s = jQuery('input[name="start_date"]').val();
        let r_e = jQuery('input[name="end_date"]').val()

        if(r_s == "0000-00-00 00:00:00") {
           r_s = undefined; 
        }

        if(r_e == "0000-00-00 00:00:00") {
           r_e = undefined; 
        }

        let start = r_s ? moment(r_s, 'YYYY-MM-DD') : moment().subtract(6, 'days');
        let end = r_e ? moment(r_e, 'YYYY-MM-DD') : moment();

        // jQuery('input[name="range_start"]').val(start.format('YYYY-MM-DD'));
        // jQuery('input[name="range_end"]').val(end.format('YYYY-MM-DD'));

        function cb(start, end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#reportrange').daterangepicker({
            startDate: start,
            maxYear: 1,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end);

        $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
            jQuery('input[name="start_date"]').val(picker.startDate.format('YYYY-MM-DD'));
            jQuery('input[name="end_date"]').val(picker.endDate.format('YYYY-MM-DD'));
        });
    // $(document).on('click', '.show-activitie1s', function(e) {
    //     e.preventDefault();
    //     var form = $(this).closest("form");
    //     console.log();
    //     var thiss = $(this);
    //     var type = 'GET';
    //         $.ajax({
    //         url: '/hubstaff-activities/activities/details',
    //         type: type,
    //         dataType: 'json',
    //         data: form.serialize(),
    //         beforeSend: function() {
    //             // $(thiss).text('Loading');
    //         }
    //         }).done( function(response) {
    //             console.log(response);
    //             $('#records-modal').modal('show');
    //             $('#record-content').html(response);
    //         // $(thiss).closest('tr').removeClass('row-highlight');
    //         // $(thiss).prev('span').text('Approved');
    //         // $(thiss).remove();
    //         }).fail(function(errObj) {
    //         alert("Could not change status");
    //         });
    //     });
    
        var thisRaw = null;
        $(document).on('click', '.show-activities', function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        var thiss = $(this);
        thisRaw = thiss;
        var type = 'GET';
            $.ajax({
            url: '/hubstaff-activities/activities/details?'+form.serialize(),
            type: type,
            beforeSend: function() {
                $("#loading-image").show();
            }
            }).done( function(response) {
            $("#loading-image").hide();
            $('#records-modal').modal('show');
            $('#record-content').html(response);
            }).fail(function(errObj) {
            $("#loading-image").hide();
            toastr['error'](errObj.responseJSON.message, 'error');
            });
        });


        $(document).on('click', '.submit-record', function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        var thiss = $(this);
        var type = 'POST';
            $.ajax({
            url: '/hubstaff-activities/activities/details',
            type: type,
            dataType: 'json',
            data: form.serialize(),
            beforeSend: function() {
                $("#loading-image").show();
            }
            }).done( function(response) {
            $("#loading-image").hide();
            thisRaw.closest("tr").find('.replaceme').html(response.totalApproved);
            $('#records-modal').modal('hide');
            $(".show-activities").css("display", "none");
            }).fail(function(errObj) {
                toastr['error'](errObj.responseJSON.message, 'error');
            $("#loading-image").hide();
            });
        });

        $(document).on('click', '.selectall', function(e) {
            if ($(this).is(':checked')) {
                $('td input').attr('checked', true);
            } else {
                $('td input').attr('checked', false);
            }
        });

        $(document).on('change', '.select-forword-to', function(e) {
           var person = $(this).data('person');
           $("#hidden-forword-to").val(person);
        });

        $(document).on('click', '.final-submit-record', function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        var thiss = $(this);
        var type = 'POST';
            $.ajax({
            url: '/hubstaff-activities/activities/final-submit',
            type: type,
            dataType: 'json',
            data: form.serialize(),
            beforeSend: function() {
                $("#loading-image").show();
            }
            }).done( function(response) {
            $("#loading-image").hide();
            thisRaw.closest("tr").find('.replaceme').html(response.totalApproved);
            $('#records-modal').modal('hide');
            $(".show-activities").css("display", "none");
            }).fail(function(errObj) {
                toastr['error'](errObj.responseJSON.message, 'error');
            $("#loading-image").hide();
            });
        });

</script>
@endsection

