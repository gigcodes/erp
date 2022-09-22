@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css">
@endsection

@section('favicon', 'vendor.png')

@section('title', 'Flag Dev Task')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
<style type="text/css">
    .numberSend {
        width: 160px;
        background-color: transparent;
        color: transparent;
        text-align: center;
        border-radius: 6px;
        position: absolute;
        z-index: 1;
        left: 19%;
        margin-left: -80px;
        display: none;
    }

    .input-sm {
        width: 60px;
    }

    #loading-image {
        position: fixed;
        top: 50%;
        left: 50%;
        margin: -50px 0px 0px -50px;
        z-index: 60;
    }

    .cls_filter_inputbox {
        width: 14%;
        text-align: center;
    }

    .message-chat-txt {
        color: #333 !important;
    }

    .cls_remove_leftpadding {
        padding-left: 0px !important;
    }

    .cls_remove_rightpadding {
        padding-right: 0px !important;
    }

    .cls_action_btn .btn {
        padding: 6px 12px;
    }

    .cls_remove_allpadding {
        padding-left: 0px !important;
        padding-right: 0px !important;
    }

    .cls_quick_message {
        width: 100% !important;
        height: 35px !important;
    }

    .cls_filter_box {
        width: 100%;
    }

    .select2-selection.select2-selection--single {
        height: 35px;
    }

    .cls_action_btn .btn-image img {
        width: 13px !important;
    }

    .cls_action_btn .btn {
        padding: 6px 2px;
    }

    .cls_textarea_subbox {
        width: 100%;
    }

    .btn.btn-image.delete_quick_comment {
        padding: 4px;
    }

    .vendor-update-status-icon {
        padding: 0px;
    }

    .cls_commu_his {
        width: 100% !important;
    }

    .vendor-update-status-icon {
        margin-top: -7px;
    }

    .clsphonebox .btn.btn-image {
        padding: 5px;
    }

    .clsphonebox {
        margin-top: -8px;
    }

    .send-message1 {
        padding: 0px 10px;
    }

    .load-communication-modal {
        margin-top: -6px;
        margin-left: 4px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 35px;
    }

    .select2-selection__arrow {
        display: none;
    }

    .cls_mesg_box {
        margin-top: -7px;
        font-size: 12px;
    }

    .pd-rt {
        padding-right: 0px !important;
    }

    .table-bordered {
        border: 1px solid #ddd !important;
    }

    .status-selection .btn-group {
        padding: 0;
        width: 100%;
    }

    .status-selection .multiselect {
        width: 100%;
    }

    .data-tb-custom td:nth-child(3) {
        display: flex;
    }
</style>
@endsection

@section('large_content')
<div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
</div>
<div class="row">
    <div class="col-md-12 p-0">
        <h2 class="page-heading">Quick Dev Task</h2>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <?php $base_url = URL::to('/'); ?>
        <div class="cls_filter_box">
            <form class="form-search-data" action="{{ route('development.flagtask') }}" method="GET">
                <div class="row mb-5">
                    <div class="col-md-2">
                        <div class="form-group">
                            <input type="text" name="subject" id="subject_query" placeholder="Issue Id / Subject" class="form-control mr-2" value="{{ !empty(app('request')->input('subject')) ? app('request')->input('subject') : '' }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select class="form-control select2" name="module_id[]" id="module_id" multiple  aria-placeholder="Select Assigned To">
                                <option value=""  @if(request('assigned_to') == '') selected @endif>- Select a Module -</option>
                                @foreach ($modules as $module)
                                <option {{ in_array($module->id, request('module_id', [])) ? 'selected' : '' }} value="{{ $module->id }}">{{ $module->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if (auth()->user()->isReviwerLikeAdmin())
                    <div class="col-md-2">
                        <div class="form-group">
                            <select class="form-control select2" name="assigned_to[]" id="assigned_to" multiple aria-placeholder="Select Assigned To">
                                <option value="" @if(request('assigned_to') == '') selected @endif>- Select Assigned To -</option>
                                {!! makeDropdown($users, request('assigned_to', [])) !!}
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-2">
                        <div class="form-group">
                            <select name="order" id="order_query" class="form-control">
                                <option {{$request->get('order')== "" ? 'selected' : ''}} value="">Latest Communication</option>
                                <option {{$request->get('order')== "latest_task_first" ? 'selected' : ''}} value="latest_task_first">Latest Task First</option>
                                <option {{$request->get('order')== "priority" ? 'selected' : ''}} value="priority">Sort by priority</option>
                                <option {{$request->get('order')== "oldest_first" ? 'selected' : ''}} value="oldest_first">Olderst First</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <?php
                            if (request('task_status')) {
                                echo Form::select('task_status[]', $statusList, request()->get('task_status', array_values($statusList)), ['class' => 'form-control select2', 'multiple' => true]);
                            } else {
                                echo Form::select('task_status[]', $statusList, 0, ['class' => 'form-control select2', 'multiple' => true]);
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label><input type="checkbox" name="empty_estimated_time" value="1" {{ $request->get('empty_estimated_time') == 1 ? 'checked' : '' }} style="height:auto;" /> Empty Estimated Time & Date</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label><input type="checkbox" name="time_is_overdue" value="1" {{ $request->get('time_is_overdue') == 1 ? 'checked' : '' }} style="height:auto;" /> Time is overdue</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="input-group date" id='datetimepicker'>
                                <input name="delivery_date" type='text' class="form-control" placeholder="Search Delivery Date" id="delivery_date">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <button type="submit" style="padding: 5px;margin-top:-1px;margin-left: 10px;margin-top: -13px;" class="btn btn-image" id="show"><img src="<?php echo $base_url; ?>/images/filter.png" /></button>
                            <a data-toggle="modal" data-target="#reminderMessageModal" class="btn pd-5 task-set-reminder" style="margin-top: -13px;">
                                <i class="fa fa-bell  red-notification " aria-hidden="true"></i>
                            </a>
                            <a data-toggle="modal" data-target="#reminderTimeMessageModal" class="btn pd-5 task-set-reminder" style="margin-top: -13px;">
                                <i class="fa fa-plus red-notification " aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

@include('partials.flash_messages')
<div class="infinite-scroll">
    <div class=" mt-3 data-tb-custom" style="">
        <table class="table table-bordered table-striped" id="reply_history_div" style="table-layout:fixed;margin-bottom:0px;">
            <thead>
                <tr>
                    <th max-width="4%">ID</th>
                    <th width="10%">Subject</th>
                    <th width="11%">Assigned To</th>
                    <th width="10%">Tracked Time</th>
                    <th width="10%">Estimated Time</th>
                    <th width="13%">Delivery Date</th>
                    <th width="22%">Communication</th>
                    <th width="13%">Status</th>
                    <th width="5%">Action</th>
                </tr>
            </thead>

            <tbody id="vendor-body">
                <?php
                $isReviwerLikeAdmin = auth()
                    ->user()
                    ->isReviwerLikeAdmin();
                $userID = Auth::user()->id;
                ?>
                @foreach ($issues as $key => $issue)
                @if ($isReviwerLikeAdmin)
                @include("development.partials.flagsummarydata")
                @elseif($issue->created_by == $userID || $issue->master_user_id == $userID ||
                $issue->assigned_to == $userID)
                @include("development.partials.flagdeveloper-row-view")
                @endif
                @endforeach
                @foreach ($tasks as $key => $issue)
                @if ($isReviwerLikeAdmin)
                @include("task-module.partials.flagsummarydata")
                @elseif($issue->created_by == $userID || $issue->master_user_id == $userID ||
                $issue->assigned_to == $userID)
                @include("task-module.partials.flagdeveloper-row-view")
                @endif
                @endforeach

            </tbody>
        </table>
    </div>

</div>
@include("development.partials.upload-document-modal")
@include("development.partials.time-tracked-modal")
@include("partials.plain-modal")


<div id="python-action-history" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Action History</h4>

            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="python-action-history_div">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Old Status</th>
                                    <th>New Status</th>
                                    <th>Updated by</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="status_quick_history_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Status History</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="row">
                <div class="col-md-12" id="status_history_div">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Old Status</th>
                                <th>New Status</th>
                                <th>Updated by</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>
<div id="tasktime-history-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sl no</th>
                                <th>Message</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody class="tasktime-history-list-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div id="logtasktime-history-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sl no</th>
                                <th>Log case id</th>
                                <th>Message</th>
                                <th>Log Message</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody class="logtasktime-history-list-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@include("development.partials.time-history-modal")
@include("task-module.partials.tracked-time-history")
@include("development.partials.user_history_modal")
<img class="infinite-scroll-products-loader center-block" src="{{asset('/images/loading.gif')}}" alt="Loading..." style="display: none" />

<div id="reminderMessageModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Set / Edit Reminder</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                {{ Form::model($taskMessage, array('url'=>route('development.taskmessage'), 'id'=>'task_message_form'))}}
                <div class="form-group">
                    <label for="frequency">Frequency</label>
                    <?php echo Form::select("frequency", drop_down_frequency(), null, ["class" => "form-control", "id" => "frequency", 'required']); ?>
                </div>
                <div class="form-group">
                    <label for="reminder_message">Reminder Message</label>
                    {{Form::textarea('message', null, array('class'=>'form-control', 'required'))}}
                </div>

                <div class="form-group">
                    {{Form::hidden('message_type', 'date_time_reminder_message')}}
                    {{Form::hidden('id', null)}}
                    <button type="button" class="btn btn-secondary task-submit-reminder">Save</button>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div id="reminderTimeMessageModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Set / Edit Time Message</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                {{ Form::model($taskMessage, array('url'=>route('development.tasktimemessage'), 'id'=>'task_time_message_form'))}}
                <div class="form-group">
                    <label for="frequency">Frequency</label>
                    <?php echo Form::select("frequency", drop_down_frequency(), null, ["class" => "form-control", "id" => "frequency", 'required']); ?>
                </div>
                <div class="form-group">
                    <label for="reminder_message">Estimated Time Message</label>
                    <textarea name="est_time_message" rows="3" required class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label for="reminder_message">Estimated Date Message</label>
                    <textarea name="est_date_message" rows="3" required class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label for="reminder_message">Overdue Time / Date Message</label>
                    <textarea name="overdue_time_date_message" rows="3" required class="form-control"></textarea>
                </div>

                <div class="form-group">
                    {{Form::hidden('message_type', 'task_date_time_reminder_message')}}
                    <button type="button" class="btn btn-secondary task-time-submit-reminder">Save</button>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
</script>
<script src="{{ asset('js/zoom-meetings.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="/js/bootstrap-multiselect.min.js"></script>

<script>
    $(document).on('click', '.task-submit-reminder', function() {
        var task_message_form = $("#task_message_form").serialize();
        $.ajax({
            url: "{{route('development.taskmessage')}}",
            type: 'POST',
            data: task_message_form,
            success: function() {
                toastr['success']('message updated successfully!');
            },
            error: function() {
                toastr['error']('Something went wrong, Please try again!');
            }
        });
    });
    $(".select2").select2();
    $(document).on('click', '.task-time-submit-reminder', function() {
        var task_time_message_form = $("#task_time_message_form").serialize();
        $.ajax({
            url: "{{route('development.tasktimemessage')}}",
            type: 'POST',
            data: task_time_message_form,
            success: function() {
                toastr['success']('message updated successfully!');
            },
            error: function() {
                toastr['error']('Something went wrong, Please try again!');
            }
        });
    });


    var isLoading = false;
    var page = 1;
    $(document).ready(function() {

        $(window).scroll(function() {
            if (($(window).scrollTop() + $(window).outerHeight()) >= ($(document).height() - 2500)) {
                loadMore();
            }
        });

        function loadMore() {
            if (isLoading)
                return;
            isLoading = true;
            var $loader = $('.infinite-scroll-products-loader');
            page = page + 1;
            $.ajax({
                url: "{{url('development/flagtask')}}?ajax=1&page=" + page,
                type: 'GET',
                data: $('.form-search-data').serialize(),
                beforeSend: function() {
                    $loader.show();
                },
                success: function(data) {
                    console.log(data);
                    $loader.hide();
                    $('#vendor-body').append(data);
                    isLoading = false;
                    if (data == "") {
                        isLoading = true;
                    }

                },
                error: function() {
                    $loader.hide();
                    isLoading = false;
                }
            });
        }
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        // $(".multiselect").multiselect({
        //     nonSelectedText: 'Please Select'
        // });

        // applySelect2(jQuery('.select2'));
    });


    $(document).on('click', '.expand-row-msg', function() {
        var id = $(this).data('id');
        console.log(id);
        var full = '.expand-row-msg .td-full-container-' + id;
        var mini = '.expand-row-msg .td-mini-container-' + id;
        $(full).toggleClass('hidden');
        $(mini).toggleClass('hidden');
    });
    $(document).on('change', '.assign-master-user', function() {
        let id = $(this).attr('data-id');
        let userId = $(this).val();

        if (userId == '') {
            return;
        }

        $.ajax({
            url: "{{ action('DevelopmentController@assignMasterUser') }}",
            data: {
                master_user_id: userId,
                issue_id: id
            },
            success: function() {
                toastr["success"]("Master User assigned successfully!", "Message")
            },
            error: function(error) {
                toastr["error"](error.responseJSON.message, "Message")

            }
        });

    });

    $(document).on('change', '.set-responsible-user', function() {
        let id = $(this).attr('data-id');
        let userId = $(this).val();

        if (userId == '') {
            return;
        }

        $.ajax({
            url: "{{ action('DevelopmentController@assignResponsibleUser') }}",
            data: {
                responsible_user_id: userId,
                issue_id: id
            },
            success: function() {
                toastr["success"]("User assigned successfully!", "Message")
            }
        });
    });

    $(document).on('change', '.assign-user', function() {
        let id = $(this).attr('data-id');
        let userId = $(this).val();

        if (userId == '') {
            return;
        }

        $.ajax({
            url: "{{ action('DevelopmentController@assignUser') }}",
            data: {
                assigned_to: userId,
                issue_id: id
            },
            success: function() {
                toastr["success"]("User assigned successfully!", "Message")
            },
            error: function(error) {
                toastr["error"](error.responseJSON.message, "Message")

            }
        });

    });

    $(document).on('change', '.task-module', function() {
        let id = $(this).attr('data-id');
        let moduleID = $(this).val();

        if (moduleID == '') {
            return;
        }

        $.ajax({
            url: "{{ action('DevelopmentController@changeModule') }}",
            data: {
                module_id: moduleID,
                issue_id: id
            },
            success: function() {
                toastr["success"]("Module assigned successfully!", "Message")
            }
        });

    });
</script>

<script type="text/javascript">
    function resolveIssue(obj, task_id) {
        let id = task_id;
        let status = $(obj).val();
        let self = this;

        $.ajax({
            url: "{{ action('DevelopmentController@resolveIssue') }}",
            data: {
                issue_id: id,
                is_resolved: status
            },
            success: function() {
                toastr["success"]("Status updatedd!", "Message")
            },
            error: function(error) {
                toastr["error"](error.responseJSON.message);
            }
        });
    }
</script>


<script type="text/javascript">
    var vendorToRemind = null;
    $('#vendor-search').select2({
        tags: true,
        width: '100%',
        ajax: {
            url: BASE_URL + '/vendor-search',
            dataType: 'json',
            delay: 750,
            data: function(params) {
                return {
                    q: params.term, // search term
                };
            },
            processResults: function(data, params) {
                for (var i in data) {
                    data[i].id = data[i].name ? data[i].name : data[i].text;
                }
                params.page = params.page || 1;

                return {
                    results: data,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
        },
        placeholder: 'Search by name',
        escapeMarkup: function(markup) {
            return markup;
        },
        minimumInputLength: 1,
        templateResult: function(customer) {

            if (customer.name) {
                //return "<p> <b>Id:</b> " + customer.id + (customer.name ? " <b>Name:</b> " + customer.name : "") + (customer.phone ? " <b>Phone:</b> " + customer.phone : "") + "</p>";
                return "<p>" + customer.name + "</p>";
            }
        },
        templateSelection: (customer) => customer.text || customer.name,

    });

    var vendorToRemind = null;
    $('#vendor-phone-number').select2({
        tags: true,
        width: '100%',
        ajax: {
            url: BASE_URL + '/vendor-search-phone',
            dataType: 'json',
            delay: 750,
            data: function(params) {
                return {
                    q: params.term, // search term
                };
            },
            processResults: function(data, params) {
                for (var i in data) {
                    data[i].id = data[i].phone ? data[i].phone : data[i].text;
                }
                params.page = params.page || 1;

                return {
                    results: data,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
        },
        placeholder: 'Search by phone number',
        escapeMarkup: function(markup) {
            return markup;
        },
        minimumInputLength: 1,
        templateResult: function(customer) {

            if (customer.name) {
                return "<p style='color:#BABABA;'>" + customer.phone + "</p>";
            }
        },
        templateSelection: (customer) => customer.text || customer.phone,

    });

    $(document).on('click', '.emailToAllModal', function() {
        var select_vendor = [];
        $('.select_vendor').each(function() {
            if ($(this).prop("checked")) {
                select_vendor.push($(this).val());
            }
        });

        if (select_vendor.length === 0) {
            alert('Please Select vendors!!');
            return false;
        }

        $('#emailToAllModal').find('form').find('input[name="vendor_ids"]').val(select_vendor.join());

        $('#emailToAllModal').modal("show");

    });

    $(document).on('click', '.send-email-to-vender', function() {
        $('#emailToAllModal').find('form').find('input[name="vendor_ids"]').val($(this).data('id'));
        $('#emailToAllModal').modal("show");
    });

    $(document).on('click', '.set-reminder', function() {
        let vendorId = $(this).data('id');
        let frequency = $(this).data('frequency');
        let message = $(this).data('reminder_message');
        let reminder_from = $(this).data('reminder_from');
        let reminder_last_reply = $(this).data('reminder_last_reply');

        $('#frequency').val(frequency);
        $('#reminder_message').val(message);
        $("#reminderModal").find("#reminder_from").val(reminder_from);
        if (reminder_last_reply == 1) {
            $("#reminderModal").find("#reminder_last_reply").prop("checked", true);
        } else {
            $("#reminderModal").find("#reminder_last_reply_no").prop("checked", true);
        }
        vendorToRemind = vendorId;
    });

    $(document).on('click', '.save-reminder', function() {
        var reminderModal = $("#reminderModal");
        let frequency = $('#frequency').val();
        let message = $('#reminder_message').val();
        let reminder_from = reminderModal.find("#reminder_from").val();
        let reminder_last_reply = (reminderModal.find('#reminder_last_reply').is(":checked")) ? 1 : 0;

        $.ajax({
            url: "{{ action('VendorController@updateReminder') }}",
            type: 'POST',
            success: function() {
                toastr['success']('Reminder updated successfully!');
            },
            data: {
                vendor_id: vendorToRemind,
                frequency: frequency,
                message: message,
                reminder_from: reminder_from,
                reminder_last_reply: reminder_last_reply,
                _token: "{{ csrf_token() }}"
            }
        });
    });

    $(document).on('click', '.edit-vendor', function() {
        var vendor = $(this).data('vendor');
        var url = "{{ url('vendors') }}/" + vendor.id;

        $('#vendorEditModal form').attr('action', url);
        $('#vendor_category option[value="' + vendor.category_id + '"]').attr('selected', true);
        $('#vendor_name').val(vendor.name);
        $('#vendor_address').val(vendor.address);
        $('#vendor_phone').val(vendor.phone);
        $('#vendor_email').val(vendor.email);
        $('#vendor_social_handle').val(vendor.social_handle);
        $('#vendor_website').val(vendor.website);
        $('#vendor_login').val(vendor.login);
        $('#vendor_password').val(vendor.password);
        $('#vendor_gst').val(vendor.gst);
        $('#vendor_account_name').val(vendor.account_name);
        $('#vendor_account_iban').val(vendor.account_iban);
        $('#vendor_account_swift').val(vendor.account_swift);
    });

    $(document).on('click', '.create-agent', function() {
        var id = $(this).data('id');

        $('#agent_vendor_id').val(id);
    });

    $(document).on('click', '.edit-agent-button', function() {
        var agent = $(this).data('agent');
        var url = "{{ url('agent') }}/" + agent.id;

        $('#editAgentModal form').attr('action', url);
        $('#agent_name').val(agent.name);
        $('#agent_address').val(agent.address);
        $('#agent_phone').val(agent.phone);
        $('#agent_email').val(agent.email);
    });

    $(document).on('click', '.make-remark', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        $('#add-remark input[name="id"]').val(id);

        $.ajax({
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('task.gettaskremark') }}",
            data: {
                id: id,
                module_type: "vendor"
            },
        }).done(response => {
            var html = '';

            $.each(response, function(index, value) {
                html += ' <p> ' + value.remark + ' <br> <small>By ' + value.user_name +
                    ' updated on ' + moment(value.created_at).format('DD-M H:mm') +
                    ' </small></p>';
                html + "<hr>";
            });
            $("#makeRemarkModal").find('#remark-list').html(html);
        });
    });

    $('#addRemarkButton').on('click', function() {
        var id = $('#add-remark input[name="id"]').val();
        var remark = $('#add-remark').find('textarea[name="remark"]').val();

        $.ajax({
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('task.addRemark') }}",
            data: {
                id: id,
                remark: remark,
                module_type: 'vendor'
            },
        }).done(response => {
            $('#add-remark').find('textarea[name="remark"]').val('');

            var html = ' <p> ' + remark + ' <br> <small>By You updated on ' + moment().format(
                'DD-M H:mm') + ' </small></p>';

            $("#makeRemarkModal").find('#remark-list').append(html);
        }).fail(function(response) {
            console.log(response);

            alert('Could not fetch remarks');
        });
    });

    $(document).on('click', '.expand-row', function() {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });

    $(document).on('click', '.load-email-modal', function() {
        var id = $(this).data('id');
        $.ajax({
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('vendors.email') }}",
            data: {
                id: id
            },
        }).done(function(response) {
            var html = '<div class="speech-wrapper">';
            response.forEach(function(message) {
                var content = '';
                content += 'To : ' + message.to + '<br>';
                content += 'From : ' + message.from + '<br>';
                if (message.cc) {
                    content += 'CC : ' + message.cc + '<br>';
                }
                if (message.bcc) {
                    content += 'BCC : ' + message.bcc + '<br>';
                }
                content += 'Subject : ' + message.subject + '<br>';
                content += 'Message : ' + message.message + '<br>';
                if (message.attachment.length) {
                    content += 'Attachment : ';
                }
                for (var i = 0; i < message.attachment.length; i++) {
                    var imageUrl = message.attachment[i];
                    imageUrl = imageUrl.trim();

                    // Set empty imgSrc
                    var imgSrc = '';

                    // Set image type
                    var imageType = imageUrl.substr(imageUrl.length - 4).toLowerCase();

                    // Set correct icon/image
                    if (imageType == '.jpg' || imageType == 'jpeg') {
                        imgSrc = imageUrl;
                    } else if (imageType == '.png') {
                        imgSrc = imageUrl;
                    } else if (imageType == '.gif') {
                        imgSrc = imageUrl;
                    } else if (imageType == 'docx' || imageType == '.doc') {
                        imgSrc = '/images/icon-word.svg';
                    } else if (imageType == '.xlsx' || imageType == '.xls' || imageType ==
                        '.csv') {
                        imgSrc = '/images/icon-excel.svg';
                    } else if (imageType == '.pdf') {
                        imgSrc = '/images/icon-pdf.svg';
                    } else if (imageType == '.zip' || imageType == '.tgz' || imageType ==
                        'r.gz') {
                        imgSrc = '/images/icon-zip.svg';
                    } else {
                        imgSrc = '/images/icon-file-unknown.svg';
                    }

                    // Set media
                    if (imgSrc != '') {
                        content += '<div class="col-4"><a href="' + message.attachment[i] +
                            '" target="_blank"><label class="label-attached-img" for="cb1_' +
                            i + '"><img src="' + imgSrc +
                            '" style="max-width: 100%;"></label></a></div>';
                    }
                }
                if (message.inout == 'in') {
                    html +=
                        '<div class="bubble"><div class="txt"><p class="name"></p><p class="message">' +
                        content + '</p><br/><span class="timestamp">' + message.created_at.date
                        .substr(0, 19) + '</span></div><div class="bubble-arrow"></div></div>';
                } else if (message.inout == 'out') {
                    html +=
                        '<div class="bubble alt"><div class="txt"><p class="name alt"></p><p class="message">' +
                        content + '</p><br/><span class="timestamp">' + message.created_at.date
                        .substr(0, 19) +
                        '</span></div> <div class="bubble-arrow alt"></div></div>';
                }
            });

            html += '</div>';

            $("#email-list-history").find(".modal-body").html(html);
            $("#email-list-history").modal("show");
        }).fail(function(response) {
            console.log(response);

            alert('Could not load email');
        });
    });
    $(document).on("keyup", '.search_email_pop', function() {
        var value = $(this).val().toLowerCase();
        $(".speech-wrapper .bubble").filter(function() {
            $(this).toggle($(this).find('.message').text().toLowerCase().indexOf(value) > -1)
        });
    });
    $(document).on('click', '.send-message', function() {
        var thiss = $(this);
        var data = new FormData();
        var vendor_id = $(this).data('vendorid');
        var message = $(this).siblings('input').val();

        data.append("vendor_id", vendor_id);
        data.append("message", message);
        data.append("status", 1);

        if (message.length > 0) {
            if (!$(this).is(':disabled')) {
                $.ajax({
                    url: BASE_URL + '/whatsapp/sendMessage/vendor',
                    type: 'POST',
                    "dataType": 'json', // what to expect back from the PHP script, if anything
                    "cache": false,
                    "contentType": false,
                    "processData": false,
                    "data": data,
                    beforeSend: function() {
                        $(this).attr('disabled', true);
                    }
                }).done(function(response) {
                    this.closest('tr').find('.chat_messages').html(this.siblings('input').val());
                    $(this).siblings('input').val('');

                    $(this).attr('disabled', false);
                }).fail(function(errObj) {
                    $(this).attr('disabled', false);

                    alert("Could not send message");
                    console.log(errObj);
                });
            }
        } else {
            alert('Please enter a message first');
        }
    });
    $(document).on('click', '.send-message1', function() {
        var thiss = $(this);
        var data = new FormData();
        var vendor_id = $(this).data('vendorid');
        var message = $("#messageid_" + vendor_id).val();
        data.append("vendor_id", vendor_id);
        data.append("message", message);
        data.append("status", 1);

        if (message.length > 0) {
            if (!$(this).is(':disabled')) {
                $.ajax({
                    url: BASE_URL + '/whatsapp/sendMessage/vendor',
                    type: 'POST',
                    "dataType": 'json', // what to expect back from the PHP script, if anything
                    "cache": false,
                    "contentType": false,
                    "processData": false,
                    "data": data,
                    beforeSend: function() {
                        $(thiss).attr('disabled', true);
                    }
                }).done(function(response) {
                    //thiss.closest('tr').find('.message-chat-txt').html(thiss.siblings('textarea').val());
                    $("#message-chat-txt-" + vendor_id).html(message);
                    $("#messageid_" + vendor_id).val('');

                    $(this).attr('disabled', false);
                }).fail(function(errObj) {
                    $(this).attr('disabled', false);

                    alert("Could not send message");
                    console.log(errObj);
                });
            }
        } else {
            alert('Please enter a message first');
        }
    });
    $(document).on('change', '.update-category-user', function() {
        let catId = $(this).attr('data-categoryId');
        let userId = $(this).val();

        $.ajax({
            url: "{{ action('VendorController@assignUserToCategory') }}",
            data: {
                user_id: userId,
                category_id: catId
            },
            success: function(response) {
                toastr['success']('User assigned to category completely!')
            }
        });

    });

    $(document).on('click', '.add-cc', function(e) {
        e.preventDefault();

        if ($('#cc-label').is(':hidden')) {
            $('#cc-label').fadeIn();
        }

        var el = `<div class="row cc-input">
            <div class="col-md-10">
                <input type="text" name="cc[]" class="form-control mb-3">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-image cc-delete-button"><img src="/images/delete.png"></button>
            </div>
        </div>`;

        $('#cc-list').append(el);
    });

    $(document).on('click', '.cc-delete-button', function(e) {
        e.preventDefault();
        var parent = $(this).parent().parent();

        parent.hide(300, function() {
            parent.remove();
            var n = 0;

            $('.cc-input').each(function() {
                n++;
            });

            if (n == 0) {
                $('#cc-label').fadeOut();
            }
        });
    });

    // bcc

    $(document).on('click', '.add-bcc', function(e) {
        e.preventDefault();

        if ($('#bcc-label').is(':hidden')) {
            $('#bcc-label').fadeIn();
        }

        var el = `<div class="row bcc-input">
            <div class="col-md-10">
                <input type="text" name="bcc[]" class="form-control mb-3">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-image bcc-delete-button"><img src="/images/delete.png"></button>
            </div>
        </div>`;

        $('#bcc-list').append(el);
    });

    $(document).on('click', '.bcc-delete-button', function(e) {
        e.preventDefault();
        var parent = $(this).parent().parent();

        parent.hide(300, function() {
            parent.remove();
            var n = 0;

            $('.bcc-input').each(function() {
                n++;
            });

            if (n == 0) {
                $('#bcc-label').fadeOut();
            }
        });
    });

    $(document).on('click', '.block-twilio', function() {
        var vendor_id = $(this).data('id');
        var thiss = $(this);

        $.ajax({
            type: "POST",
            url: "{{ route('vendors.block') }}",
            data: {
                _token: "{{ csrf_token() }}",
                vendor_id: vendor_id
            },
            beforeSend: function() {
                $(thiss).text('Blocking...');
            }
        }).done(function(response) {
            if (response.is_blocked == 1) {
                $(thiss).html('<img src="/images/blocked-twilio.png" />');
            } else {
                $(thiss).html('<img src="/images/unblocked-twilio.png" />');
            }
        }).fail(function(response) {
            $(thiss).html('<img src="/images/unblocked-twilio.png" />');

            alert('Could not block customer!');

            console.log(response);
        });
    });

    $(document).on('click', '.call-select', function() {
        var id = $(this).data('id');
        $('#show' + id).toggle();
        console.log('#show' + id);
    });

    $(document).ready(function() {
        src = "{{ route('vendors.index') }}";
        $(".search").autocomplete({
            source: function(request, response) {
                id = $('#id').val();
                name = $('#name').val();
                email = $('#email').val();
                phone = $('#phone').val();
                address = $('#address').val();
                category = $('#category').val();

                $.ajax({
                    url: src,
                    dataType: "json",
                    data: {
                        id: id,
                        name: name,
                        phone: phone,
                        email: email,
                        address: address,
                        category: category,
                    },
                    beforeSend: function() {
                        $("#loading-image").show();
                    },

                }).done(function(data) {
                    $("#loading-image").hide();
                    console.log(data);
                    $("#vendor-table tbody").empty().html(data.tbody);
                    if (data.links.length > 10) {
                        $('ul.pagination').replaceWith(data.links);
                    } else {
                        $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                    }
                    $(".select2-quick-reply").select2({});

                }).fail(function(jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });
            },
            minLength: 1,

        });


        $(document).ready(function() {
            src = "{{ route('vendors.index') }}";
            $("#search_id").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: src,
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        beforeSend: function() {
                            $("#loading-image").show();
                        },

                    }).done(function(data) {
                        $("#loading-image").hide();
                        $("#vendor-table tbody").empty().html(data.tbody);
                        if (data.links.length > 10) {
                            $('ul.pagination').replaceWith(data.links);
                        } else {
                            $('ul.pagination').replaceWith(
                                '<ul class="pagination"></ul>');
                        }
                        $(".select2-quick-reply").select2({});

                    }).fail(function(jqXHR, ajaxOptions, thrownError) {
                        alert('No response from server');
                    });
                },
                minLength: 1,

            });
        });

        $(document).on("change", ".quickComment", function(e) {

            var message = $(this).val();

            if ($.isNumeric(message) == false) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: BASE_URL + "/vendors/reply/add",
                    dataType: "json",
                    method: "POST",
                    data: {
                        reply: message
                    }
                }).done(function(data) {

                }).fail(function(jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });
            }
            $(this).closest("td").find(".quick-message-field").val($(this).find("option:selected")
                .text());

        });

        $(".select2-quick-reply").select2({
            tags: true
        });

        $(document).on("click", ".delete_quick_comment", function(e) {
            var deleteAuto = $(this).closest(".d-flex").find(".quickComment").find("option:selected")
                .val();
            if (typeof deleteAuto != "undefined") {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: BASE_URL + "/vendors/reply/delete",
                    dataType: "json",
                    method: "GET",
                    data: {
                        id: deleteAuto
                    }
                }).done(function(data) {
                    if (data.code == 200) {
                        $(".quickComment").empty();
                        $.each(data.data, function(k, v) {
                            $(".quickComment").append("<option value='" + k + "'>" + v +
                                "</option>");
                        });
                        $(".quickComment").select2({
                            tags: true
                        });
                    }

                }).fail(function(jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });
            }
        });
    });

    function createUserFromVendor(id, email) {
        $('#vendor_id').attr('data-id', id);
        if (email) {
            $('#createUser').attr('data-email', email);
        }
        $('#createUser').modal('show');
    }

    $('#createUser').on('hidden.bs.modal', function() {
        $('#createUser').removeAttr('data-email');
    })

    $(document).on("click", "#vendor_id", function() {
        $('#createUser').modal('hide');
        id = $(this).attr('data-id');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: BASE_URL + "/vendors/create-user",
            dataType: "json",
            method: "POST",
            data: {
                id: id
            },
            beforeSend: function() {
                $("#loading-image").show();
            },
        }).done(function(data) {
            $("#loading-image").hide();
            if (data.code == 200) {
                alert(data.data);
            }

        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            $("#loading-image").hide();
            alert('No response from server');
        });
    });

    function inviteGithub() {
        $('#createUser').modal('hide');
        const email = $('#createUser').attr('data-email');

        $.ajax({
                type: "POST",
                url: "/vendors/inviteGithub",
                data: {
                    _token: "{{ csrf_token() }}",
                    email
                }
            })
            .done(function(data) {
                alert(data.message);
            })
            .fail(function(error) {
                alert(error.responseJSON.message);
            });

        console.log(email);
    }

    function inviteHubstaff() {
        $('#createUser').modal('hide');
        const email = $('#createUser').attr('data-email');
        console.log(email);

        $.ajax({
                type: "POST",
                url: BASE_URL + "/vendors/inviteHubstaff",
                data: {
                    _token: "{{ csrf_token() }}",
                    email
                }
            })
            .done(function(data) {
                alert(data.message);
            })
            .fail(function(error) {
                alert(error.responseJSON.message);
            })
    }

    $('#reminder_from').datetimepicker({
        format: 'YYYY-MM-DD HH:mm'
    });

    $(document).on("change", ".vendor-update-status", function() {
        var $this = $(this);
        $.ajax({
            type: "POST",
            url: BASE_URL + "vendors/change-status",
            data: {
                _token: "{{ csrf_token() }}",
                vendor_id: $this.data("id"),
                status: $this.prop('checked')
            }
        }).done(function(data) {
            if (data.code == 200) {
                toastr["success"](data.message);
            }
        }).fail(function(error) {

        })
    });
    $(document).on("click", ".vendor-update-status-icon", function() {
        var $this = $(this);
        var vendor_id = $(this).attr("data-id");
        var hdn_vendorstatus = $("#hdn_vendorstatus_" + vendor_id).val();
        $.ajax({
            type: "POST",
            url: BASE_URL + "vendors/change-status",
            data: {
                _token: "{{ csrf_token() }}",
                vendor_id: $this.data("id"),
                status: hdn_vendorstatus
            }
        }).done(function(data) {
            if (data.code == 200) {
                //toastr["success"](data.message);
                if (hdn_vendorstatus == "true") {
                    var img_url = BASE_URL + 'images/do-disturb.png';
                    $("#btn_vendorstatus_" + vendor_id).html('<img src="' + img_url + '" />');
                    $("#btn_vendorstatus_" + vendor_id).attr("title", "On");
                    $("#hdn_vendorstatus_" + vendor_id).val('false');
                } else {
                    var img_url = BASE_URL + 'images/do-not-disturb.png';
                    $("#btn_vendorstatus_" + vendor_id).html('<img src="' + img_url + '" />');
                    $("#btn_vendorstatus_" + vendor_id).attr("title", "Off");
                    $("#hdn_vendorstatus_" + vendor_id).val('true');
                }

            }
        }).fail(function(error) {

        })
    });

    $('ul.pagination').show();
    $('.infinite-scroll').jscroll({
        autoTrigger: true,
        // debug: true,
        loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
        padding: 20,
        float: 'right',
        nextSelector: '.pagination li.active + li a',
        contentSelector: 'div.infinite-scroll',
        callback: function() {
            $('ul.pagination').first().remove();
            $('ul.pagination').show();
        }
    });

    $(document).on('click', '.create_broadcast', function() {
        var vendors = [];
        $(".select_vendor").each(function() {
            if ($(this).prop("checked") == true) {
                vendors.push($(this).val());
            }
        });
        if (vendors.length == 0) {
            alert('Please select vendor');
            return false;
        }
        $("#create_broadcast").modal("show");
    });

    $("#send_message").submit(function(e) {
        e.preventDefault();
        var vendors = [];
        $(".select_vendor").each(function() {
            if ($(this).prop("checked") == true) {
                vendors.push($(this).val());
            }
        });
        if (vendors.length == 0) {
            alert('Please select vendor');
            return false;
        }

        if ($("#send_message").find("#message_to_all_field").val() == "") {
            alert('Please type message ');
            return false;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('vendors/send/message') }}",
            data: {
                _token: "{{ csrf_token() }}",
                message: $("#send_message").find("#message_to_all_field").val(),
                vendors: vendors
            }
        }).done(function() {
            window.location.reload();
        }).fail(function(response) {
            $(thiss).text('No');

            alert('Could not say No!');
            console.log(response);
        });
    });

    function sendImage(id) {
        $.ajax({
            url: "{{ action('WhatsAppController@sendMessage', 'issue') }}",
            type: 'POST',
            data: {
                issue_id: id,
                type: 1,
                message: '',
                _token: "{{ csrf_token() }}",
                status: 2
            },
            success: function() {
                toastr["success"]("Message sent successfully!", "Message");

            },
            beforeSend: function() {
                $(self).attr('disabled', true);
            },
            error: function() {
                alert('There was an error sending the message...');
                $(self).removeAttr('disabled', true);
            }
        });

    }

    function sendUploadImage(id) {

        $('#file-input' + id).trigger('click');

        $('#file-input' + id).change(function() {
            event.preventDefault();
            let image_upload = new FormData();
            let TotalImages = $(this)[0].files.length; //Total Images
            let images = $(this)[0];

            for (let i = 0; i < TotalImages; i++) {
                image_upload.append('images[]', images.files[i]);
            }
            image_upload.append('TotalImages', TotalImages);
            image_upload.append('status', 2);
            image_upload.append('type', 2);
            image_upload.append('issue_id', id);
            if (TotalImages != 0) {

                $.ajax({
                    method: 'POST',
                    url: "{{ action('WhatsAppController@sendMessage', 'issue') }}",
                    data: image_upload,
                    async: true,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $("#loading-image").show();
                    },
                    success: function(images) {
                        $("#loading-image").hide();
                        alert('Images send successfully');
                    },
                    error: function() {
                        console.log(`Failed`)
                    }
                })
            }
        })
    }

    // $('#filecount').filestyle({htmlIcon: '<span class="oi oi-random"></span>',badge: true, badgeName: "badge-danger"});

    $(document).on("click", ".upload-document-btn", function() {
        var id = $(this).data("id");
        $("#upload-document-modal").find("#hidden-identifier").val(id);
        $("#upload-document-modal").modal("show");
    });

    $(document).on("submit", "#upload-task-documents", function(e) {
        e.preventDefault();
        var form = $(this);
        var postData = new FormData(form[0]);
        $.ajax({
            method: "post",
            url: "{{ action('DevelopmentController@uploadDocument') }}",
            data: postData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response) {
                if (response.code == 200) {
                    toastr["success"]("Status updated!", "Message")
                    $("#upload-document-modal").modal("hide");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    $(document).on("click", ".list-document-btn", function() {
        var id = $(this).data("id");
        $.ajax({
            method: "GET",
            url: "{{ action('DevelopmentController@getDocument') }}",
            data: {
                id: id
            },
            dataType: "json",
            success: function(response) {
                if (response.code == 200) {
                    $("#blank-modal").find(".modal-title").html("Document List");
                    $("#blank-modal").find(".modal-body").html(response.data);
                    $("#blank-modal").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });



    $(document).on('click', '.send-message-open', function(event) {
        var textBox = $(this).closest(".communication-td").find(".send-message-textbox");
        var sendToStr = $(this).closest(".communication-td").next().find(".send-message-number").val();
        let issueId = textBox.attr('data-id');
        let message = textBox.val();
        if (message == '') {
            return;
        }

        let self = textBox;

        $.ajax({
            url: "{{ action('WhatsAppController@sendMessage', 'issue') }}",
            type: 'POST',
            data: {
                "issue_id": issueId,
                "message": message,
                "sendTo": sendToStr,
                "_token": "{{ csrf_token() }}",
                "status": 2
            },
            dataType: "json",
            success: function(response) {
                toastr["success"]("Message sent successfully!", "Message");
                $('#message_list_' + issueId).append('<li>' + response.message.created_at + " : " +
                    response.message.message + '</li>');
                $(self).removeAttr('disabled');
                $(self).val('');
            },
            beforeSend: function() {
                $(self).attr('disabled', true);
            },
            error: function() {
                alert('There was an error sending the message...');
                $(self).removeAttr('disabled', true);
            }
        });
    });


    $(document).on('click', '.show-status-history', function() {
        var data = $(this).data('history');
        var issueId = $(this).data('id');
        var type = $(this).data('type');
        $('#status_quick_history_modal table tbody').html('');
        $.ajax({
            url: "{{ route('development/status/history') }}",
            data: {
                id: issueId,
                type: type
            },
            success: function(data) {
                if (data != 'error') {
                    $.each(data, function(i, item) {
                        if (item['is_approved'] == 1) {
                            var checked = 'checked';
                        } else {
                            var checked = '';
                        }
                        $('#status_quick_history_modal table tbody').append(
                            '<tr>\
                                            <td>' + moment(item['created_at']).format('DD/MM/YYYY') + '</td>\
                                            <td>' + ((item['old_value'] != null) ? item['old_value'] : '-') + '</td>\
                                            <td>' + item['new_value'] + '</td>\
                                            <td>' + item['name'] + '</td>\
                                        </tr>'
                        );
                    });
                }
            }
        });
        $('#status_quick_history_modal').modal('show');
    });

    //Popup for add new task
    $(document).on('click', '#newTaskModalBtn', function() {
        if ($("#newTaskModal").length > 0) {
            $("#newTaskModal").remove();
        }

        $.ajax({
            url: "{{ action('DevelopmentController@openNewTaskPopup') }}",
            type: 'GET',
            dataType: "JSON",
            success: function(resp) {
                console.log(resp);
                if (resp.status == 'ok') {
                    $("body").append(resp.html);
                    $('#newTaskModal').modal('show');
                    $('select.select2').select2({
                        tags: true
                    });
                }
            }
        });
    });
    $(document).on('click', '.show-user-history', function() {
        var issueId = $(this).data('id');
        var type = $(this).data('type');
        $('#user_history_div table tbody').html('');
        $.ajax({
            url: "{{ route('task/user/history') }}",
            data: {
                id: issueId,
                type: type
            },
            success: function(data) {

                $.each(data.users, function(i, item) {
                    $('#user_history_div table tbody').append(
                        '<tr>\
                                    <td>' + moment(item['created_at']).format('DD/MM/YYYY') + '</td>\
                                    <td>' + ((item['user_type'] != null) ? item['user_type'] : '-') + '</td>\
                                    <td>' + ((item['old_name'] != null) ? item['old_name'] : '-') + '</td>\
                                    <td>' + ((item['new_name'] != null) ? item['new_name'] : '-') + '</td>\
                                    <td>' + item['updated_by'] + '</td>\
                                </tr>'
                    );
                });
            }
        });
        $('#user_history_modal').modal('show');
    });
    $(document).on('click', '.show-date-history', function() {
        var data = $(this).data('history');
        var type = $(this).data('type');

        var issueId = $(this).data('id');
        $('#date_history_modal table tbody').html('');
        $.ajax({
            url: "{{ route('development/date/history') }}",
            data: {
                id: issueId,
                type: type
            },
            success: function(data) {
                console.log(data);
                if (data != 'error') {
                    $("#developer_task_id").val(issueId);
                    $.each(data, function(i, item) {
                        if (item['is_approved'] == 1) {
                            var checked = 'checked';
                        } else {
                            var checked = '';
                        }
                        $('#date_history_modal table tbody').append(
                            '<tr>\
                                    <td>' + moment(item['created_at']).format('DD/MM/YYYY') + '</td>\
                                    <td>' + ((item['old_value'] != null) ? item['old_value'] : '-') + '</td>\
                                    <td>' + item['new_value'] + '</td>\<td>' + item['name'] + '</td><td><input type="radio" name="approve_date" value="' + item['id'] + '" ' + checked + ' class="approve_date"/></td>\
                                </tr>'
                        );
                    });
                }
            }
        });
        $('#date_history_modal').modal('show');
    });
</script>

<script>
    $(document).on('change', '.assign-task-user', function() {
        let id = $(this).attr('data-id');
        let userId = $(this).val();
        if (userId == '') {
            return;
        }
        $.ajax({
            url: "{{route('task.AssignTaskToUser')}}",
            data: {
                user_id: userId,
                issue_id: id
            },
            success: function() {
                toastr["success"]("User assigned successfully!", "Message")
            },
            error: function(error) {
                toastr["error"](error.responseJSON.message, "Message")

            }
        });
    });


    $(document).on('click', '.flag-task', function() {
        var task_id = $(this).data('id');
        var task_type = $(this).data('type');
        var thiss = $(this);

        $.ajax({
            type: "POST",
            url: "{{ route('task.flag') }}",
            data: {
                _token: "{{ csrf_token() }}",
                task_id: task_id,
                task_type: task_type
            },
            beforeSend: function() {
                $(thiss).text('Flagging...');
            }
        }).done(function(response) {
            if (response.is_flagged == 1) {
                // var badge = $('<span class="badge badge-secondary">Flagged</span>');
                //
                // $(thiss).parent().append(badge);
                $(thiss).html('<img src="/images/flagged.png" />');
            } else {
                $(thiss).html('<img src="/images/unflagged.png" />');
                // $(thiss).parent().find('.badge').remove();
            }

            // $(thiss).remove();
        }).fail(function(response) {
            $(thiss).html('<img src="/images/unflagged.png" />');

            alert('Could not flag task!');

            console.log(response);
        });
    });
    $(document).on('click', '.show-tracked-history', function() {
        var issueId = $(this).data('id');
        var type = $(this).data('type');
        $('#time_tracked_div table tbody').html('');
        $.ajax({
            url: "{{ route('development/tracked/history') }}",
            data: {
                id: issueId,
                type: type
            },
            success: function(data) {
                if (data != 'error') {
                    $.each(data.histories, function(i, item) {
                        var sec = parseInt(item['total_tracked']);
                        $('#time_tracked_div table tbody').append(
                            '<tr>\
                                    <td>' + moment(item['created_at']).format('DD-MM-YYYY') + '</td>\
                                    <td>' + ((item['name'] != null) ? item['name'] : '') + '</td>\
                                    <td>' + humanizeDuration(sec, 's') + '</td>\
                                </tr>'
                        );
                    });
                }
            }
        });
        $('#time_tracked_modal').modal('show');
    });
    $(document).on('click', '.show-tracked-history_task', function() {
        var issueId = $(this).data('id');
        var type = $(this).data('type');
        $('#time_tracked_div table tbody').html('');
        $.ajax({
            url: "{{ route('task.time.tracked.history') }}",
            data: {
                id: issueId,
                type: type
            },
            success: function(data) {
                console.log(data);
                if (data != 'error') {
                    $.each(data.histories, function(i, item) {
                        var sec = parseInt(item['total_tracked']);
                        $('#time_tracked_div table tbody').append(
                            '<tr>\
                                    <td>' + moment(item['starts_at_date']).format('DD-MM-YYYY') + '</td>\
                                    <td>' + ((item['name'] != null) ? item['name'] : '') + '</td>\
                                    <td>' + humanizeDuration(sec, 's') + '</td>\
                                </tr>'
                        );
                    });
                }
            }
        });
        $('#time_tracked_modal').modal('show');
    });
    $(document).on('click', '.show-time-history-task', function() {
        var data = $(this).data('history');
        var userId = $(this).data('user_id');
        var issueId = $(this).data('id');
        $('#time_history_div table tbody').html('');

        //START - Purpose : Display Hide Remind, Revise Button - DEVTASK-4354
        const hasText = $(this).siblings('input').val();

        if (!hasText || hasText == 0) {
            $('#time_history_modal .revise_btn').prop('disabled', true);
            $('#time_history_modal .remind_btn').prop('disabled', false);
        } else {
            $('#time_history_modal .revise_btn').prop('disabled', false);
            $('#time_history_modal .remind_btn').prop('disabled', true);
        }
        //END - DEVTASK-4354

        $.ajax({
            url: "{{ route('task.time.history') }}",
            data: {
                id: issueId
            },
            success: function(data) {
                // if(data != 'error') {
                //     $.each(data, function(i, item) {
                //         $('#time_history_div table tbody').append(
                //             '<tr>\
                //                 <td>'+ moment(item['created_at']).format('DD/MM/YYYY') +'</td>\
                //                 <td>'+ ((item['old_value'] != null) ? item['old_value'] : '-') +'</td>\
                //                 <td>'+item['new_value']+'</td>\
                //             </tr>'
                //         );
                //     });
                // }

                if (data != 'error') {
                    $('input[name="developer_task_id"]').val(issueId);
                    $.each(data, function(i, item) {
                        if (item['is_approved'] == 1) {
                            var checked = 'checked';
                        } else {
                            var checked = '';
                        }
                        $('#time_history_div table tbody').append(
                            '<tr>\
                                    <td>' + moment(item['created_at']).format('DD/MM/YYYY') + '</td>\
                                    <td>' + ((item['old_value'] != null) ? item['old_value'] : '-') + '</td>\
                                    <td>' + item['new_value'] + '</td><td>' + item['name'] + '</td><td><input type="radio" name="approve_time" value="' + item['id'] + '" ' + checked + ' class="approve_time"/></td>\
                                </tr>'
                        );
                    });

                    $('#time_history_div table tbody').append(
                        '<input type="hidden" name="user_id" value="' + userId + '" class=" "/>'
                    );
                }
            }
        });
        $('#time_history_modal').modal('show');
    });
    //START - Purpose : Remind , Revise button Events - DEVTASK-4354
    $(document).on('click', '.remind_btn', function() {
        var issueId = $('#approve-time-btn input[name="developer_task_id"]').val();
        var userId = $('#approve-time-btn input[name="user_id"]').val();

        $('#time_history_div table tbody').html('');
        $.ajax({
            url: "{{ route('task.time.history.approve.sendRemindMessage') }}",
            type: 'POST',
            data: {
                id: issueId,
                user_id: userId,
                _token: '{{csrf_token()}}'
            },
            success: function(data) {
                toastr['success'](data.message, 'success');
            }
        });
        $('#time_history_modal').modal('hide');
    });

    $(document).on('click', '.revise_btn', function() {
        var issueId = $('#approve-time-btn input[name="developer_task_id"]').val();
        var userId = $('#approve-time-btn input[name="user_id"]').val();

        $('#time_history_div table tbody').html('');
        $.ajax({
            url: "{{ route('task.time.history.approve.sendMessage') }}",
            type: 'POST',
            data: {
                id: issueId,
                user_id: userId,
                _token: '{{csrf_token()}}'
            },
            success: function(data) {
                toastr['success'](data.message, 'success');
            }
        });
        $('#time_history_modal').modal('hide');
    });
    //END - DEVTASK-4354

    $(document).on('change', '.change-task-status', function() {

        let id = $(this).attr('data-id');
        let status = $(this).val();

        $.ajax({
            url: "{{route('task.change.status')}}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            dataType: "json",
            data: {
                'task_id': id,
                'status': status
            },
            success: function(response) {
                toastr["success"](response.message, "Message")
            },
            error: function(error) {
                toastr["error"](error.responseJSON.message, "Message")

            }
        });

    });
    $('.tasktime-history-btn').click(function() {
        var id = $(this).data('id');
        $.ajax({
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('development.gettasktimemessage') }}",
            data: {
                id: id,
            },
        }).done(response => {
            $('#tasktime-history-modal').find('.tasktime-history-list-view').html('');
            if (response.success == true) {
                $('#tasktime-history-modal').find('.tasktime-history-list-view').html(response.html);
                $('#tasktime-history-modal').modal('show');
            }

        }).fail(function(response) {

            alert('Could not fetch payments');
        });
    });
    $('.logtasktime-history-btn').click(function() {
        var id = $(this).data('id');
        $.ajax({
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('development.getlogtasktimemessage') }}",
            data: {
                id: id,
            },
        }).done(response => {
            $('#logtasktime-history-modal').find('.logtasktime-history-list-view').html('');
            if (response.success == true) {
                $('#logtasktime-history-modal').find('.logtasktime-history-list-view').html(response.html);
                $('#logtasktime-history-modal').modal('show');
            }

        }).fail(function(response) {

            alert('Could not fetch payments');
        });
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js"></script>


<script type="text/javascript">
    $(document).ready(function() {
        $('#datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss'
        });
        $('#datetimepicker2').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss'
        });

        applySelect2(jQuery('.select2'));
        $(".select2").select2();

    });
</script>
@endsection