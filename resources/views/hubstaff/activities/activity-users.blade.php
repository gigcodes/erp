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
            <div class="col-md-12 margin-tb">
                <div class="row">
                    <form class="form-check-inline" action="{{route('hubstaff-acitivties.activities')}}" method="get">
                        <div class="form-group col-md-2">
                            <?php echo Form::select("user_id",["" => "-- Select User --"]+$users,$user_id,["class" => "form-control select2"]); ?>
                        </div>
                        <div class="form-group col-md-2">
                            <?php echo Form::text("developer_task_id",request('developer_task_id'),["class" => "form-control","placeholder" => "Enter Developer Task ID"]); ?>
                        </div>
                        <div class="form-group col-md-2">
                            
                            <?php echo Form::text("task_id",request('task_id'),["class" => "form-control","placeholder" => "Enter Task ID"]); ?>
                        </div>
                        <div class="form-group col-md-3">
                            <input type="text" value="{{$start_date}}" name="start_date" hidden/>
                            <input type="text" value="{{$end_date}}" name="end_date" hidden/>
                            <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span></span> <i class="fa fa-caret-down"></i>
                            </div>
                        </div>
                       
                        <div class="form-group col-md-2">
                            <select name="status" id="" class="form-control">
                            <option value="">Select</option>
                            <option value="new" {{$status == 'new' ? 'selected' : ''}}>New</option>
                            <option value="forwarded_to_admin" {{$status == 'forwarded_to_admin' ? 'selected' : ''}}>Forwarded to admin</option>
                            <option value="forwarded_to_lead" {{$status == 'forwarded_to_lead' ? 'selected' : ''}}>Forwarded to team lead</option>
                            <option value="approved" {{$status == 'approved' ? 'selected' : ''}}>Approved by admin</option>
                            <option value="pending" {{$status == 'pending' ? 'selected' : ''}}>Pending by admin</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="button">&nbsp;</label>
                            <button type="submit" style="display: inline-block;width: 10%" class="btn btn-sm btn-image">
                                <img src="/images/search.png" style="cursor: default;">
                            </button>
                        </div>  
                    </form> 
                </div>    
                
            </div>
        
            <div class="col-md-12 margin-tb">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                        <th width="4%">Date</th>
                        <th>User</th>
                        <th>Time tracked (Minutes)</th>
                        <th>Tasks</th>
                        <th>Time approved</th>
                        <th>Time Pending</th>
                        <th>User Requested</th>
                        <th>Pending payment time</th>
                        <th>Status</th>
                        <th width="30%">Note</th>
                        <th width="10%" colspan="2" class="text-center">Action</th>
                        </tr>
                        @php
                            $totalTracked = 0;
                            $totalApproved = 0;
                            $totalPending = 0;
                            $totalUserRequested = 0;
                            $totalPaymentPending = 0;
                        @endphp  
                        @foreach ($activityUsers as $user)
                            <tr>
                            <td>{{ \Carbon\Carbon::parse($user['date'])->format('d-m') }} </td>
                            <td>{{ $user['userName'] }}</td>
                            @php
                                $totalTracked +=  $user['total_tracked'];
                                $totalApproved +=  $user['totalApproved'];
                                $totalPending +=  $user['totalPending'];
                                $totalUserRequested +=  $user['totalUserRequest'];
                                $totalPaymentPending +=  $user['totalNotPaid'];
                            @endphp
                            <td>{{number_format($user['total_tracked'] / 60,2,".",",")}}</td>
                            <td>
                                <?php if(!empty($user['tasks'])) { ?>
                                        <?php foreach($user['tasks'] as $ut) { ?>
                                            <?php 
                                                @list($taskid,$devtask) = explode("||",$ut);
                                                $trackedTime = \App\Hubstaff\HubstaffActivity::where('task_id', $taskid)->first()->tracked;
                                            ?>
                                            <?php if(Auth::user()->isAdmin()) { ?> 
                                                <a class="show-task-histories " data-user-id="{{$user['user_id']}}" data-task-id="{{$taskid}}" href="javascript:;">{{$devtask}} {{ (isset($trackedTime) && $devtask ) ? '-'.$trackedTime.' Min' : '' }}</a><br>
                                            <?php }else{ ?>
                                                <a class="" data-user-id="{{$user['user_id']}}" data-task-id="{{$taskid}}" href="javascript:;">{{$devtask}} {{ (isset($trackedTime) && $devtask ) ? '-'.$trackedTime.' Min' : '' }} </a><br>
                                            <?php } ?>    
                                        <?php } ?>
                                <?php } ?>
                            </td>
                            <td><span class="replaceme">{{number_format($user['totalApproved'] / 60,2,".",",")}}</span> </td>
                            <td>{{ number_format($user['totalPending'] / 60,2,".",",") }}</td>
                            <td><span>{{number_format($user['totalUserRequest'] / 60,2,".",",")}}</span> </td>
                            <td><span>{{number_format($user['totalNotPaid'] / 60,2,".",",")}}</td>
                            <td>{{$user['status']}}</td>
                            <td>{{$user['note']}}</td>
                            
                            <td>
                                @if($user['forworded_to'] == Auth::user()->id && !$user['final_approval'])
                                <form action="">
                                    <input type="hidden" class="user_id" name="user_id" value="{{$user['user_id']}}">
                                    <input type="hidden" class="date" name="date" value="{{$user['date']}}">
                                    <a class="btn btn-secondary show-activities">+</a>
                                </form>
                                @endif
                                @if(Auth::user()->isAdmin() && $user['final_approval'])
                                <form action="">
                                    <input type="hidden" class="user_id" name="user_id" value="{{$user['user_id']}}">
                                    <input type="hidden" class="date" name="date" value="{{$user['date']}}">
                                    <a class="btn btn-secondary show-activities"><i class="fa fa-check" aria-hidden="true"></i></a>
                                </form>
                                @endif
                            </td>
                        @endforeach
                        <tr>
                        <th>Total</th>
                        <th></th>
                        <th>{{number_format($totalTracked / 60,2,".","")}}</th>
                        <th></th>
                        <th>{{number_format($totalApproved / 60,2,".","")}}</th>
                        <th>{{number_format($totalPending / 60,2,".","")}}</th>
                        <th>{{number_format($totalUserRequested / 60,2,".","")}}</th>
                        <th>{{number_format($totalPaymentPending / 60,2,".","")}}</th>
                        <th></th>
                        <th></th>
                        <th width="10%" colspan="2" class="text-center"></th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div id="records-modal" class="modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 1200px !important; width: 100% !important;">
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
                <input type="text" name="starts_at" value="" class="form-control" id="custom_hour" required placeholder="Enter Date">
            </div>
            <div class="form-group">
                <label for="">Total time (In minutes)</label>
                <input type="number" name="total_time" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="">Task Id</label>
                <input type="number" name="task_id" class="form-control" placeholder="Enter task id, eg. 2997">
            </div>
            <div class="form-group">
                <label for=""> Notes </label>
                <textarea name="user_notes" class="form-control" style="resize:none" required placeholder="Add Your Comments Here"></textarea>
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

$(document).on('click', '.expand-row', function () {
    var selection = window.getSelection();
    if (selection.toString().length === 0) {
        $(this).find('.td-mini-container').toggleClass('hidden');
        $(this).find('.td-full-container').toggleClass('hidden');
    }
});

$(document).on('change', '.task_efficiency', function(e) 
{
    var user_id = $(this).data('user_id');
    var efficiency = $(this).val();
    var type = $(this).data('type');
    var date = $(this).data('date');
    var hour = $(this).data('hour');

    var $action_url = '{{ route("hubstaff-acitivties.efficiency.save") }}';                 
        jQuery.ajax({
                
            type: "POST",
            url: $action_url,
            data: { user_id: user_id,efficiency: efficiency,type: type, date: date, hour: hour  },
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
$('#custom_hour').datetimepicker({
    format: 'YYYY-MM-DD HH:mm:ss'
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
        var status = $(this).data('status');
        // return false;
        var form = $(this).closest("form");
        var thiss = $(this);
        var type = 'POST';
        var data = form.serializeArray();
        data.push({name: 'status', value: status});

            $.ajax({
            url: '/hubstaff-activities/activities/final-submit',
            type: type,
            dataType: 'json',
            // data: form.serialize(),
            data: data,
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
                $("#loading-image").hide();
                if(errObj.responseJSON) {
                    toastr['error'](errObj.responseJSON.message, 'error');
                }
                window.location.reload();
            });
        });


        $(document).on('click', '.expand-row-btn', function () {
            $(this).closest("tr").find(".expand-col").toggleClass('dis-none');
        });

        $(document).on("click",".show-task-histories",function(e) {
            e.preventDefault();
            var $this = $(this);
            thisRaw = $this;
            $.ajax({
                url: '/hubstaff-activities/activities/task-activity',
                type: 'GET',
                data: {
                    "task_id":$this.data("task-id"),
                    "user_id":$this.data("user-id")
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done( function(response) {
                $("#loading-image").hide();
                $("#loading-image").hide();
                $('#records-modal').modal('show');
                $('#record-content').html(response);
            }).fail(function(errObj) {
                $("#loading-image").hide();
                if(errObj.responseJSON) {
                    toastr['error'](errObj.responseJSON.message, 'error');
                }
            });
        });


</script>
@endsection