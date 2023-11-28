@extends('layouts.app')
@section('title', 'User Management > User Scheduler')
@section('favicon', 'user-management.png')
@section('large_content')
@include('partials.flash_messages')
<style>
.div-slot {
	min-width: 75px;
	padding: 3px !important;
}
</style>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;background-color:rgba(255,255,255,0.6);"></div>
<div class="row">
    <div class="col-md-12 p-0">
        <h2 class="page-heading">{{ $title }} <span id="listUserScheduleCount" class="count-text"></span></h2>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card d-normal">
                <div class="card-header">
                    <h4>
                        <a class="collapsed card-link" data-toggle="collapse" href="#collapseSearch" aria-expanded="false">
                            <i class="fa fa-arrow-up"></i>
                            <i class="fa fa-arrow-down"></i>
                            Filter Records
                        </a>
                    </h4>
                </div>
                <div id="collapseSearch" class="collapse">
                    <div class="card-body">
                        <form id="frm-search-crud" class="" method="post">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="form-label">Users List</label>
                                                <select class="form-control select2" name="srchUser">
                                                    <option value=""></option>
                                                    {!! makeDropdown($listUsers) !!}
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="form-label">From Date</label>
                                                <div class="input-group date d-datetime">
                                                    <input type="text" class="form-control input-sm" name="srchDateFrom" value="{{ request('srchDateFrom', date('Y-m-d')) }}" />
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="form-label">To Date</label>
                                                <div class="input-group date d-datetime">
                                                    <input type="text" class="form-control input-sm" name="srchDateTo" value="{{ request('srchDateTo', date('Y-m-d', strtotime('+3 days'))) }}" />
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="form-label">Status</label>
                                                {{ Form::select("task_status[]", $statusList, request('task_status'), ["class" => "form-control select2", "multiple" => true]) }}
                                                <?php //echo Form::select("task_status[]",$statusList,request()->get('task_status', []),["class" => "form-control multiselect","multiple" => true]); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="form-label">Task Type</label>
                                                <select class="form-control select2" name="task_type_filter">
                                                    <option value=""></option>
                                                    <option value="2">Planned</option>
                                                    <option value="1">Actual</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary" onclick="siteDatatableSearch('#listUserSchedule')">Search</button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="siteDatatableClearSearch('#listUserSchedule', '#frm-search-crud')">Clear</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <table id="listUserSchedule" class="table table-bordered" style="width:100%">
                <thead>
					<tr>
                        <th data-data="name" data-name="name" width="10%" data-sortable="false"></th>
                        <th data-data="date" data-name="date" width="6%" data-sortable="false"></th>
                        <th width="81%" colspan="9" style="text-align: center;">Hourly Slots [T-? = Task] [DT-? = Dev Task]</th>
                    </tr>
                    <tr>
                        <th data-data="name" data-name="name" width="10%" data-sortable="false">User Name</th>
                        <th data-data="date" data-name="date" width="6%" data-sortable="false">Date</th>
						<?php for($i=0;$i<9;$i++) { ?>
                            <th width="9%" data-data="slots<?php echo $i; ?>" data-name="slots<?php echo $i; ?>" width="5%"   data-sortable="false" style="text-align: center;">Slots <?php echo $i; ?></th>
						<?php } ?>
						
                    </tr>
                </thead>
                <tbody>
				
				</tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="getEstimateTime" role="dialog" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Estimate Time</h3>
                <button class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <p class="estimateTimeText"></p>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{env('APP_URL')}}/js/jsrender.min.js"></script>
<script type="text/javascript" src="{{env('APP_URL')}}/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="{{env('APP_URL')}}/js/jquery-ui.js"></script>
<script type="text/javascript" src="{{env('APP_URL')}}/js/common-helper.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"> </script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"> </script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pnp-sp-taxonomy/1.3.11/sp-taxonomy.es5.umd.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.6/jquery.tagsinput.min.js" integrity="sha512-wTIaZJCW/mkalkyQnuSiBodnM5SRT8tXJ3LkIUA/3vBJ01vWe5Ene7Fynicupjt4xqxZKXA97VgNBHvIf5WTvg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endsection

@push('modals')
<div id="modalSlotAssign" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Task: Add To Slots</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <label>Tasks & Developer Tasks:</label>
                        <div class="form-group">
                            <select id="slotTaskId" name="slotTaskId" class="form-control select2">
                                <option value="">- Select -</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label>Remark</label>
                        <div class="form-group">
                            <textarea id="slotTaskRemarks" name="slotTaskRemarks" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="funSlotAssignSubmit('{!! route('task.slot.assign') !!}')">Save</button>
            </div>
        </div>
    </div>
</div>

<div id="modalSlotMoveAssign" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Task: Move To Slots</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <label>Slots:</label>
                        <div class="form-group">
                            <select id="slotMoveTaskId" name="slotMoveTaskId" class="form-control select2">
                                <option value="">- Select -</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="funSlotMoveSubmit('{!! route('task.slot.move') !!}')">Save</button>
            </div>
        </div>
    </div>
</div>

<div id="modalPastSlotAssign" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Note</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>You cannot edit the records which are more than one day in the past - pls. Contact admin</p>
            </div>
            <div class="modal-footer">
                
            </div>
        </div>
    </div>
</div>

<div id="estimateTimeModel" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
    <!-- Modal content-->
        <div class="modal-content ">
            <div id="add-mail-content">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Estimate Time</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body estimateTimeModelBody">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="remarksNoteModel" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
    <!-- Modal content-->
        <div class="modal-content ">
            <div id="add-mail-content">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Remarks</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body remarksNoteModelBody">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endpush

@push('styles')
<style>
   /* .div-slot {
        display: inline-block;
        padding: 4px;
        border: 1px solid #ddd;
        border-radius: 10px;
        margin: 2px;
    }*/
</style>
@endpush

@push("link-css")
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
@endpush

@push("jquery")
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
@endpush

@push("scripts")
<script>
    var currSlotAssignee = null;
    var dtblListUserSchedule = null;
    var urlTaskDropdown = "{!! route('task.dropdown-user-wise') !!}";

    var urlTaskMoveDropdown = "{!! route('task.dropdown-slot-wise') !!}";

    function funSlotMoveModal(ele) {
        currSlotAssignee = ele;

        siteLoader(1);
        jQuery.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: urlTaskMoveDropdown,
            type: 'GET',
            data: {
                userId: jQuery(currSlotAssignee).attr('data-user_id'),
            }
        }).done(function(res) {
            jQuery('#slotMoveTaskId').html(res.list ? '<option value="">- Select -</option>' + res.list : '<option value="">No records found.</option>')
            jQuery('#modalSlotMoveAssign').modal('show');
            applySelect2(jQuery('#slotMoveTaskId'));
            siteLoader(0);
        }).fail(function(err) {
            siteErrorAlert(err);
            siteLoader(0);
        });
    }

    function funPastSlotAssignModal(ele) {
        currSlotAssignee = ele;
        jQuery('#modalPastSlotAssign').modal('show');
    }

    function funSlotAssignModal(ele) {
        currSlotAssignee = ele;

        siteLoader(1);
        jQuery.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: urlTaskDropdown,
            type: 'GET',
            data: {
                userId: jQuery(currSlotAssignee).attr('data-user_id'),
            }
        }).done(function(res) {
            jQuery('#slotTaskId').html(res.list ? '<option value="">- Select -</option>' + res.list : '<option value="">No records found.</option>')
            jQuery('#modalSlotAssign').modal('show');
            applySelect2(jQuery('#slotTaskId'));
            siteLoader(0);
        }).fail(function(err) {
            siteErrorAlert(err);
            siteLoader(0);
        });
    }

    function funSlotMoveSubmit(url) {
        if (jQuery('#slotMoveTaskId').val()) {
            siteLoader(1);
            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: {
                    taskTime: jQuery('#slotMoveTaskId').val(),
                    userId: jQuery(currSlotAssignee).attr('data-user_id'),
                    date: jQuery(currSlotAssignee).attr('data-date'),
                    slot: jQuery(currSlotAssignee).attr('data-slot'),
                    tasks: jQuery(currSlotAssignee).attr('data-tasks'),
                    dev_tasks: jQuery(currSlotAssignee).attr('data-dev_tasks'),
                }
            }).done(function(response) {
                jQuery('#modalSlotMoveAssign').modal('hide');
                siteLoader(0);
                dtblListUserSchedule.draw(false);
            }).fail(function(err) {
                siteErrorAlert(err);
                siteLoader(0);
            });
        } else {
            siteErrorAlert("Please select slot.");
        }
    }

    function funSlotAssignSubmit(url) {
        if (jQuery('#slotTaskId').val()) {
            siteLoader(1);
            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: {
                    taskId: jQuery('#slotTaskId').val(),
                    slotTaskRemarks: jQuery('#slotTaskRemarks').val(),
                    userId: jQuery(currSlotAssignee).attr('data-user_id'),
                    date: jQuery(currSlotAssignee).attr('data-date'),
                    slot: jQuery(currSlotAssignee).attr('data-slot'),
                }
            }).done(function(response) {
                jQuery('#modalSlotAssign').modal('hide');
                siteLoader(0);
                dtblListUserSchedule.draw(false);
            }).fail(function(err) {
                siteErrorAlert(err);
                siteLoader(0);
            });
        } else {
            siteErrorAlert("Please select task.");
        }
    }

    jQuery(document).ready(function() {

        applySelect2(jQuery('.select2'));
        applyDatePicker(jQuery('.d-datetime'));

        // Render datatable
        dtblListUserSchedule = jQuery('#listUserSchedule').DataTable({
            lengthChange: false,
            searching: false,
            autoWidth: false,
            processing: true,
            serverSide: true,
            paging: false,
            ajax: {
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "{{ $urlLoadData }}",
                data: function(d) {
                    //return siteDatatableMergeSearch(d, '#frm-search-crud');
                    return $("#frm-search-crud").serialize()
                },
                dataSrc: function(json) {
                    return json.data;
                },
                error: function(xhr, error, code) {
                    siteErrorAlert(xhr);
                    jQuery('#listUserSchedule_processing').hide();
                }
            },
            initComplete: function(settings, json) {
                jQuery('#listUserScheduleCount').html('(' + dtblListUserSchedule.data().count() + ')');
            },
            drawCallback: function(settings) {
                jQuery('#listUserScheduleCount').html('(' + dtblListUserSchedule.data().count() + ')');
            },
            order: [],
        });
    });

    $(document).on("click", ".getEstimateTimeClass", function(href) {
        var estimateTime = $(this).data("id");
        
        $('.estimateTimeText').text(estimateTime);
    });

    $(document).on('click', '.estimate_minutes_class', function() {
        $('#estimateTimeModel').modal('toggle');
        $(".estimateTimeModelBody").html("");
        var time = $(this).data('time')+' Minutes';
        $(".estimateTimeModelBody").html(time);
    });

    $(document).on('click', '.slotTaskRemarks_class', function() {
        $('#remarksNoteModel').modal('toggle');
        $(".remarksNoteModelBody").html("");
        var remarks = $(this).data('remarks');
        $(".remarksNoteModelBody").html(remarks);
    });

    $(document).on("click", "#send-request-date", function (e) {
        e.preventDefault();
        if (confirm("Are you sure you want to add task on this date?")) {
            alert('Yes');
        } else {
            alert('No');
        }
    });
</script>
@endpush