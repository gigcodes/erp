<div id="modalTaskInformationUpdates" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Task's Information Update</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <?php
                $cls_1 = 'col-md-8';
                $cls_2 = 'col-md-4';
                ?>
                <div class="row">
                    <div class="col-md-4">
                        <label>Estimated Time: [In Minutes]</label>
                        <div class="form-group">
                            <input type="number" class="form-control" name="approximate" value="" min="1" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Remark:</label>
                        <div class="form-group">
                            <textarea class="form-control" name="remark" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="{{$cls_2}}">
                        <label>Actions</label>
                        <div class="form-group">
                            <button type="button" class="btn btn-secondary" onclick="funTaskInformationUpdates('approximate')">Update</button>
                            <button type="button" class="btn btn-default show-time-history" >History</button>
                        </div>
                    </div>
                </div>

                <hr />

                <div class="row">
                    <div class="{{$cls_1}}">
                        <label>Estimated Start Datetime:</label>
                        <div class="form-group">
                            <div class='input-group date cls-start-due-date'>
                                <input type="text" class="form-control" name="start_date" value="" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="{{$cls_2}}">
                        <label>Actions</label>
                        <div class="form-group">
                            <button type="button" class="btn btn-secondary" onclick="funTaskInformationUpdates('start_date')">Update</button>
                            <button type="button" class="btn btn-default" onclick="funTaskHistories('start_date')">History</button>
                        </div>
                    </div>
                </div>

                <hr />

                <div class="row">
                    <div class="{{$cls_1}}">
                        <label>Estimated End Datetime: [Due Date]</label>
                        <div class="form-group">
                            <div class='input-group date cls-start-due-date'>
                                <input type="text" class="form-control" name="due_date" value="" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="{{$cls_2}}">
                        <label>Actions</label>
                        <div class="form-group">
                            <button type="button" class="btn btn-secondary" onclick="funTaskInformationUpdates('due_date')">Update</button>
                            <button type="button" class="btn btn-default" onclick="funTaskHistories('due_date')">History</button>
                        </div>
                    </div>
                </div>

                <hr />

                <div class="row">
                    <div class="{{$cls_1}}">
                        <label>Cost:</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="cost" value="" />
                        </div>
                    </div>
                    <div class="{{$cls_2}}">
                        <label>Actions</label>
                        <div class="form-group">
                            <button type="button" class="btn btn-secondary" onclick="funTaskInformationUpdates('cost')">Update</button>
                            <button type="button" class="btn btn-default" onclick="funTaskHistories('cost')">History</button>
                        </div>
                    </div>
                </div>

                <hr />

                <div class="row">
                    <div class="col-md-6">
                        <label>Actual Start Time:</label>
                        <div class="form-group cls-actual_start_date"></div>
                    </div>
                    <div class="col-md-6">
                        <label>Actual End Time:</label>
                        <div class="form-group cls-actual_end_date"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="modalTaskHistories" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    var currTaskInformationTaskId = 0;

    function funGetTaskInformationModal() {
        return jQuery('#modalTaskInformationUpdates');
    }

    function funTaskInformationModal(ele, taskId) {
        siteLoader(1);
        currTaskInformationTaskId = taskId;
        let mdl = funGetTaskInformationModal();
        jQuery.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: "{!! route('task.information.get') !!}",
            type: 'GET',
            data: {
                id: taskId,
            },
        }).done(function(res) {
            siteLoader(0);
            if (res.data) {
                mdl.find('input[name="start_date"]').val(res.data.start_date);
                mdl.find('input[name="due_date"]').val(res.data.due_date);
                mdl.find('input[name="cost"]').val(res.data.cost);
                mdl.find('input[name="approximate"]').val(res.data.approximate);
                mdl.find('input[name="remark"]').val('');

                mdl.find('.cls-actual_start_date').html(res.data.actual_start_date ? res.data.actual_start_date : '-');
                mdl.find('.cls-actual_end_date').html(res.data.actual_end_date ? res.data.actual_end_date : '-');

                mdl.find('.show-time-history').attr('data-id', res.data.id);
                mdl.find('.show-time-history').attr('data-user_id', res.data.assign_to);
                mdl.modal("show");
            } else {
                siteErrorAlert(res);
            }
        }).fail(function(err) {
            siteLoader(0);
            siteErrorAlert(err);
        });
    }

    function funTaskInformationUpdates(type) {
        if (type == 'start_date') {
            if (confirm('Are you sure, do you want to update?')) {
                siteLoader(1);
                let mdl = funGetTaskInformationModal();
                jQuery.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('task.update.start-date') }}",
                    type: 'POST',
                    data: {
                        task_id: currTaskInformationTaskId,
                        value: mdl.find('input[name="start_date"]').val(),
                    }
                }).done(function(res) {
                    siteLoader(0);
                    siteSuccessAlert(res);
                }).fail(function(err) {
                    siteLoader(0);
                    siteErrorAlert(err);
                });
            }
        } else if (type == 'due_date') {
            if (confirm('Are you sure, do you want to update?')) {
                siteLoader(1);
                let mdl = funGetTaskInformationModal();
                jQuery.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('task.update.due-date') }}",
                    type: 'POST',
                    data: {
                        task_id: currTaskInformationTaskId,
                        value: mdl.find('input[name="due_date"]').val(),
                    }
                }).done(function(res) {
                    siteLoader(0);
                    siteSuccessAlert(res);
                }).fail(function(err) {
                    siteLoader(0);
                    siteErrorAlert(err);
                });
            }
        } else if (type == 'cost') {
            if (confirm('Are you sure, do you want to update?')) {
                siteLoader(1);
                let mdl = funGetTaskInformationModal();
                jQuery.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('task.update.cost') }}",
                    type: 'POST',
                    data: {
                        task_id: currTaskInformationTaskId,
                        cost: mdl.find('input[name="cost"]').val(),
                    }
                }).done(function(res) {
                    siteLoader(0);
                    siteSuccessAlert(res);
                }).fail(function(err) {
                    siteLoader(0);
                    siteErrorAlert(err);
                });
            }
        } else if (type == 'approximate') {
            if (confirm('Are you sure, do you want to update?')) {
                siteLoader(1);
                let mdl = funGetTaskInformationModal();
                jQuery.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('task.update.approximate') }}",
                    type: 'POST',
                    data: {
                        task_id: currTaskInformationTaskId,
                        approximate: mdl.find('input[name="approximate"]').val(),
                        remark: mdl.find('input[name="remark"]').val(),
                    }
                }).done(function(res) {
                    siteLoader(0);
                    siteSuccessAlert(res);
                }).fail(function(err) {
                    siteLoader(0);
                    siteErrorAlert(err);
                });
            }
        }
    }

    function funTaskHistories(type) {
        if (type == 'start_date' || type == 'due_date' || type == 'cost') {
            siteLoader(1);
            let mdl = jQuery('#modalTaskHistories');
            let url = '';

            if (type == 'start_date') {
                mdl.find('.modal-title').html('Estimated Start Datetime History');
                url = "{{ route('task.history.start-date.index') }}";
            } else if (type == 'due_date') {
                mdl.find('.modal-title').html('Estimated End Datetime History [Due Datetime]');
                url = "{{ route('task.history.due-date.index') }}";
            } else if (type == 'cost') {
                mdl.find('.modal-title').html('Cost History');
                url = "{{ route('task.history.cost.index') }}";
            }

            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'GET',
                data: {
                    id: currTaskInformationTaskId
                }
            }).done(function(response) {
                mdl.find('.modal-body').html(response.data);
                mdl.modal('show');
                siteLoader(0);
            }).fail(function(err) {
                siteLoader(0);
                siteErrorAlert(err);
            });
        }
    }

    jQuery(document).ready(function() {
        applyDateTimePicker(jQuery('.cls-start-due-date'));
    });
</script>
@endpush