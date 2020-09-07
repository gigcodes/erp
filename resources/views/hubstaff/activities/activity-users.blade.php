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
        <a class="btn btn-secondary" data-toggle="modal" data-target="#fetch-activity-modal" style="color:white;">Fetch Activity</a>
        <a class="btn btn-secondary" data-toggle="modal" data-target="#open-timing-modal" style="color:white;">Add manual timings</a>
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
						    <label for="keyword">Status:</label>
                            <select name="status" id="" class="form-control">
                            <option value="">Select</option>
                            <option value="new" {{$status == 'new' ? 'selected' : ''}}>New</option>
                            <option value="forwarded_to_admin" {{$status == 'forwarded_to_admin' ? 'selected' : ''}}>Forwarded to admin</option>
                            <option value="forwarded_to_lead" {{$status == 'forwarded_to_lead' ? 'selected' : ''}}>Forwarded to team lead</option>
                            <option value="approved" {{$status == 'approved' ? 'selected' : ''}}>Approved by admin</option>
                            </select>
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
          <th>Task Efficiency</th>
          <th>Note</th>
          <th width="10%" colspan="2" class="text-center">Action</th>
        </tr>
          @foreach ($activityUsers as $user)
            <tr>
            <td>{{ \Carbon\Carbon::parse($user['date'])->format('d-m') }} </td>
              <td>{{ $user['userName'] }}</td>
              <td>{{number_format($user['total_tracked'] / 60,2,".",",")}}</td>
              <td><span class="replaceme">{{number_format($user['totalApproved'] / 60,2,".",",")}}</span> </td>
              <td><span>{{number_format($user['totalNotPaid'] / 60,2,".",",")}}</td>
              <td>{{$user['status']}}</td>
              <td>
              <div class="form-group">
                   <p> <strong>Admin :</strong> {{ (isset($user['admin_efficiency'])) ? $user['admin_efficiency'] : '-'}}</p>
                   <p> <strong>Users :</strong> {{ (isset($user['user_efficiency'])) ? $user['user_efficiency'] : '-'}}</p>

                   @if(isset($users)) 
                   
                    <select name="efficiency" class="task_efficiency form-control"  data-user_id="{{ $user['user_id']}}">
                        <option value="">Select One</option>
                        <option value="Excellent" {{ (isset($user['efficiency']) && $user['efficiency'] =='Excellent') ? 'selected' : '' }} >Excellent</option>
                        <option value="Good" {{ (isset($user['efficiency']) && $user['efficiency'] =='Good') ? 'selected' : '' }}>Good</option>
                        <option value="Average" {{ (isset($user['efficiency']) && $user['efficiency'] =='Average') ? 'selected' : '' }}>Average </option>
                        <option value="Poor" {{ (isset($user['efficiency']) && $user['efficiency'] =='Poor') ? 'selected' : '' }}>Poor</option>
                    </select>
                    
                    
                    

                    @endif


                </div>


              </td>
              <td>{{$user['note']}}</td>
              
              <td>
                @if($user['forworded_to'] == Auth::user()->id && !$user['final_approval'])
                <form action="">
                    <input type="hidden" class="user_id" name="user_id" value="{{$user['user_id']}}">
                    <input type="hidden" class="date" name="date" value="{{$user['date']}}">
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


<div id="open-timing-modal" class="modal" role="dialog">
  	<div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form>
            @csrf
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
            <div class="form-group">
                <label for="">Date</label>
                <input type="text" name="starts_at" value="" class="form-control" id="starts_at" required placeholder="Enter Date">
            </div>
            <div class="form-group">
                <label for="">Total time (In minutes)</label>
                <input type="number" name="total_time" class="form-control" required>
            </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-danger submit-manual-record">Submit</button> 
            </div>
        </form>
      </div>
  	</div>	
</div>
<div id="fetch-activity-modal" class="modal" role="dialog">
  	<div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form>
            @csrf
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
            <div class="form-group">
                <label for="">Activity available up to</label>
                <input id="activity-available" type="text"  value="" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label for="">Date from</label>
                <input type="text" name="starts_at" value="" class="form-control" id="time_from" required placeholder="Enter Date">
            </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-secondary submit-fetch-activity">Submit</button> 
            </div>
        </form>
      </div>
  	</div>	
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>

<script type="text/javascript">

$(document).on('change', '.task_efficiency', function(e) 
{
    $user_id = $(this).data('user_id');
    $efficiency = $(this).val();

    var $action_url = '{{ route("efficiency.save") }}';						
		jQuery.ajax({
				
			type: "POST",
			url: $action_url,
			data: { user_id: $user_id,efficiency:$efficiency },
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
			//cache: false,
			//dataType: 'json',
            success: function(data)
            {
                toastr['success'](data.message);
				
			},
            error: function(error)
            {
                toastr['error'](data.message);
            },
            	
		});
		return false;

});

$("#activity-available").val(new Date().toUTCString());
$(".select2").select2({tags:true});

$('#starts_at').datetimepicker({
    format: 'YYYY-MM-DD'
});
$('#time_from').datetimepicker({
    format: 'YYYY-MM-DD HH:mm:ss'
});
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
            thisRaw.closest("tr").find('.show-activities').css("display", "none");
            }).fail(function(errObj) {
                toastr['error'](errObj.responseJSON.message, 'error');
            $("#loading-image").hide();
            });
        });



        $(document).on('click', '.submit-manual-record', function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        var thiss = $(this);
        var type = 'POST';
            $.ajax({
            url: '/hubstaff-activities/activities/manual-record',
            type: type,
            dataType: 'json',
            data: form.serialize(),
            beforeSend: function() {
                $("#loading-image").show();
            }
            }).done( function(response) {
            $("#loading-image").hide();
            $('#open-timing-modal').modal('hide');
            toastr['success']('Successful');
            }).fail(function(errObj) {
                toastr['error'](errObj.responseJSON.message, 'error');
            $("#loading-image").hide();
            });
        });

        $(document).on('click', '.selectall', function(e) {
            var cls = '.'+$(this).data("id");
            if ($(this).is(':checked')) {
                $(cls).attr('checked', true);
            } else {
                $(cls).attr('checked', false);
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
            // $(".show-activities").css("display", "none");
            thisRaw.closest("tr").find('.show-activities').css("display", "none");
            }).fail(function(errObj) {
                toastr['error'](errObj.responseJSON.message, 'error');
            $("#loading-image").hide();
            });
        });


        $(document).on('click', '.submit-fetch-activity', function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        var thiss = $(this);
        var type = 'POST';
            $.ajax({
            url: '/hubstaff-activities/activities/fetch',
            type: type,
            dataType: 'json',
            data: form.serialize(),
            beforeSend: function() {
                $("#loading-image").show();
            }
            }).done( function(response) {
            $("#loading-image").hide();
            window.location.reload();
            }).fail(function(errObj) {
                toastr['error'](errObj.responseJSON.message, 'error');
            $("#loading-image").hide();
            });
        });


        $(document).on('click', '.expand-row-btn', function () {
            $(this).closest("tr").find(".expand-col").toggleClass('dis-none');
        });

</script>
@endsection