<script>
if(typeof currTaskInformationTaskId === 'undefined') {
    var currTaskInformationTaskId = 0;
}

if(typeof funGetTaskInformationModal === 'undefined') {
    function funGetTaskInformationModal() {
        return jQuery('#modalTaskInformationUpdates');
    }
}

if(typeof funTaskInformationModal === 'undefined') {
    function funTaskInformationModal(ele, taskId) {
        siteLoader(1);
        currTaskInformationTaskId = taskId;
        let mdl = funGetTaskInformationModal();
        jQuery.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: "{!! route('development.task.get') !!}",
            type: 'GET',
            data: {
                id: taskId,
            },
        }).done(function(res) {
            siteLoader(0);
            if (res.data) {
                mdl.find('input[name="start_date"]').val(res.data.start_date);
                mdl.find('input[name="estimate_date"]').val(res.data.estimate_date);
                mdl.find('input[name="cost"]').val(res.data.cost);
                mdl.find('input[name="estimate_minutes"]').val(res.data.estimate_minutes);
                mdl.find('input[name="lead_estimate_time"]').val(res.data.lead_estimate_time);
                mdl.find('input[name="remark"]').val('');
                mdl.find('input[name="lead_remark"]').val('');

                mdl.find('.cls-actual_start_date').html(res.data.actual_start_date ? res.data.actual_start_date : '-');
                mdl.find('.cls-actual_end_date').html(res.data.actual_end_date ? res.data.actual_end_date : '-');

                mdl.find('.show-time-history').attr('data-id', res.data.id);
                mdl.find('.show-time-history').attr('data-userid', res.data.user_id);

                if (mdl.find('.show-lead-time-history').length) {
                    mdl.find('.show-lead-time-history').attr('data-id', res.data.id);
                }
                mdl.modal("show");
            } else {
                siteErrorAlert(res);
            }
        }).fail(function(err) {
            siteLoader(0);
            siteErrorAlert(err);
        });
    }
}
if(typeof funTaskInformationUpdates === 'undefined') {
    function funTaskInformationUpdates(type) {
        if (type == 'start_date') {
            if (confirm('Are you sure, do you want to update?')) {
                siteLoader(1);
                let mdl = funGetTaskInformationModal();
                jQuery.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('development.update.start-date') }}",
                    type: 'POST',
                    data: {
                        id: currTaskInformationTaskId,
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
        } else if (type == 'estimate_date') {
            if (confirm('Are you sure, do you want to update?')) {
                siteLoader(1);
                let mdl = funGetTaskInformationModal();
                jQuery.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('development.update.estimate-date') }}",
                    type: 'POST',
                    data: {
                        id: currTaskInformationTaskId,
                        value: mdl.find('input[name="estimate_date"]').val(),
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
        } else if (type == 'cost') {
            if (confirm('Are you sure, do you want to update?')) {
                siteLoader(1);
                let mdl = funGetTaskInformationModal();
                jQuery.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('development.update.cost') }}",
                    type: 'POST',
                    data: {
                        id: currTaskInformationTaskId,
                        value: mdl.find('input[name="cost"]').val(),
                    }
                }).done(function(res) {
                    siteLoader(0);
                    siteSuccessAlert(res);
                }).fail(function(err) {
                    siteLoader(0);
                    siteErrorAlert(err);
                });
            }
        } else if (type == 'estimate_minutes') {
            if (confirm('Are you sure, do you want to update?')) {
                siteLoader(1);
                let mdl = funGetTaskInformationModal();
                jQuery.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('development.update.estimate-minutes') }}",
                    type: 'POST',
                    data: {
                        issue_id: currTaskInformationTaskId,
                        estimate_minutes: mdl.find('input[name="estimate_minutes"]').val(),
                        remark: mdl.find('textarea[name="remark"]').val(),
                    }
                }).done(function(res) {
                    siteLoader(0);
                    siteSuccessAlert(res);
                }).fail(function(err) {
                    siteLoader(0);
                    siteErrorAlert(err);
                });
            }
        } else if (type == 'lead_estimate_time') {
            if (confirm('Are you sure, do you want to update?')) {
                siteLoader(1);
                let mdl = funGetTaskInformationModal();
                jQuery.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('development.update.lead-estimate-minutes') }}",
                    type: 'POST',
                    data: {
                        issue_id: currTaskInformationTaskId,
                        lead_estimate_time: mdl.find('input[name="lead_estimate_time"]').val(),
                        remark: mdl.find('input[name="lead_remark"]').val(),
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
}

if(typeof funTaskHistories === 'undefined') {
    function funTaskHistories(type) {
        if (type == 'start_date' || type == 'estimate_date' || type == 'cost') {
            siteLoader(1);
            let mdl = jQuery('#modalTaskHistories');
            mdl.find('input[name="type"]').val(type);

            let url = '';

            if (type == 'start_date') {
                mdl.find('.modal-title').html('Estimated Start Datetime History');
                url = "{{ route('development.history.start-date.index') }}";
            } else if (type == 'estimate_date') {
                mdl.find('.modal-title').html('Estimated End Datetime History');
                url = "{{ route('development.history.estimate-date.index') }}";
            } else if (type == 'cost') {
                mdl.find('.modal-title').html('Cost History');
                url = "{{ route('development.history.cost.index') }}";
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
}

if(typeof funTaskApproveRecord === 'undefined') {
    function funTaskApproveRecord(btn) {
        let type = jQuery(btn).attr('data-recordtype');
        jQuery.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: "{!! route('development-task.history.approve') !!}",
            type: 'POST',
            data: jQuery(btn).closest('form').serialize(),
        }).done(function(res) {
            siteSuccessAlert(res);
            siteLoader(0);
        }).fail(function(err) {
            siteErrorAlert(err);
            siteLoader(0);
        });
    }
}

if(typeof funTaskApproveHistory === 'undefined') {
    function funTaskApproveHistory(ele) {
        let type = jQuery('#modalTaskHistories').find('input[name="type"]').val();

        let mdl = jQuery('#modalTaskApprovedHistories');
        mdl.find('.modal-title').html('Approved History');
        if (type == 'start_date') {} else if (type == 'estimate_date') {} else {
            return;
        }

        siteLoader(1);
        jQuery.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('development-task.history.approve-history') }}",
            type: 'GET',
            data: {
                type: type,
                id: currTaskInformationTaskId
            }
        }).done(function(response) {
            mdl.find('.modal-body').html(response.data);
            mdl.modal('show');
            siteLoader(0);
        }).fail(function(err) {
            siteErrorAlert(err);
            siteLoader(0);
        });

    }
}
</script>