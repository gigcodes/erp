<script>
    var configs = {
        routes : {
            'editName' : "{{ route('editName') }}",
            'updateName' : "{{ route('updateName') }}",
            'sop_store' : "{{ route('sop.store') }}",
            'user_search_global' : "{{ route('user-search-global') }}",
            'menu_sop_search' : "{{ route('menu.sop.search') }}",
            'menu_email_search' : "{{ route('menu.email.search') }}",
            'appointment_request_declien_remarks':"{{route('appointment-request.declien.remarks')}}",
            'task_module_search':"{{route('task.module.search')}}",
            'vendors_flowcharts_search':"{{route('vendors.flowcharts.search')}}",
            'vendors_qa_search':"{{route('vendors.qa.search')}}",
            'vendors_rqa_search':"{{route('vendors.rqa.search')}}",
            'devtask_module_search':"{{route('devtask.module.search')}}",

            'task_AssignTaskToUser':"{{route('task.AssignTaskToUser')}}",
            'list_all_participants':"{{route('list.all.participants')}}",
            'task_estimate_list':"{{route('task.estimate.list')}}",
            'task_estimate_list':"{{route('task.estimate.list')}}",
            'github_pr_request':"{{route('github.pr.request')}}",
            'logging_live_logs_summary':"{{route('logging.live.logs-summary')}}",
            'get_backup_monitor_lists':"{{route('get.backup.monitor.lists')}}",
            'db_update_isResolved':"{{route('db.update.isResolved')}}",
            'website_email_update':"{{route('website.email.update')}}",
            'magento_cron_error_list':"{{route('magento-cron-error-list')}}",
            'code_get_Shortcut_notes':"{{route('code.get.Shortcut.notes')}}",
            'documentShorcut_list':"{{route('documentShorcut.list')}}",
            'event_getEventAlerts':"{{route('event.getEventAlerts')}}",
            'script_documents_errorlogs':"{{route('script-documents.errorlogs')}}",
            'magento_getMagentoCommand':"{{route('magento.getMagentoCommand')}}",
            'get_timer_alerts':"{{route('get.timer.alerts')}}",
            'task_estimate_alert':"{{route('task.estimate.alert')}}",
            'event_getEventAlerts':"{{route('event.getEventAlerts')}}",
            'event_saveAlertLog':"{{route('event.saveAlertLog')}}",
            'script_documents_getScriptDocumentErrorLogsList':"{{route('script-documents.getScriptDocumentErrorLogsList')}}",
            'assetsManager_loadTable':"{{route('assetsManager.loadTable')}}",
            'google_drive_screencast_getGooglesScreencast':"{{route('google-drive-screencast.getGooglesScreencast')}}",
            'vendors_flowchart_saveremarks':"{{route('vendors.flowchart.saveremarks')}}",
            'vendors_flowchart_getremarks':"{{route('vendors.flowchart.getremarks')}}",
            'vendors_flowchartstatus_histories':"{{route('vendors.flowchartstatus.histories')}}",
            'vendors_question_saveanswer':"{{route('vendors.question.saveanswer')}}",
            'vendors_question_getgetanswer':"{{route('vendors.question.getgetanswer')}}",
            'vendors_qastatus_histories':"{{route('vendors.qastatus.histories')}}",
            'vendors_question_saveranswer':"{{route('vendors.question.saveranswer')}}",
            'vendors_rquestion_getgetanswer':"{{route('vendors.rquestion.getgetanswer')}}",
            'vendors_rqastatus_histories':"{{route('vendors.rqastatus.histories')}}",
            'todolist_module_search':"{{route('todolist.module.search')}}",
            'magento_getMagentoCommand':"{{route('magento.getMagentoCommand')}}",
            'event_updateAppointmentRequest':"{{route('event.updateAppointmentRequest')}}",
            'event_updateuserAppointmentRequest':"{{route('event.updateuserAppointmentRequest')}}",
            'useronlinestatus_status_update':"{{route('useronlinestatus.status.update')}}",
            'event_sendAppointmentRequest':"{{route('event.sendAppointmentRequest')}}",

            'task_user_history':"{{ route('task/user/history') }}",
            'todolist_ajax_store':"{{ route('todolist.ajax_store') }}",
            'whatsapp_send':"{{ route('whatsapp.send','task')}}",
            'user_management_get_database':"{{ route("user-management.get-database", ":id") }}",
            'shortcut_code_create':"{{ route('shortcut.code.create') }}",
            'shortcut_sop_create':"{{ route('shortcut.sop.create') }}",
            'user_management_create_database':"{{ route("user-management.create-database", ":id") }}",
            'user_management_assign_database_table':"{{ route("user-management.assign-database-table", ":id") }}",
            'user_management_delete_database_access':"{{ route("user-management.delete-database-access", ":id") }}",
            'get_user_list':"{{ route("get-user-list") }}",
            'stickyNotesCreate':"{{ route('stickyNotesCreate') }}",
            'notesCreate':"{{ route('notesCreate') }}",
            'encryption_forget_key':"{{ route('encryption.forget.key') }}",
            'save_store_wise_reply':"{{ route('save-store-wise-reply') }}",
            'script_documents+comment':"{{ route('script-documents.comment', ['']) }}",
            'getDropdownDatas':"{{ route('getDropdownDatas') }}",
            'todolist_status_update':"{{ route('todolist.status.update') }}",
            'code_get_Shortcut_data':"{{ route('code.get.Shortcut.data', ['']) }}",
            'event_getAppointmentRequest':"{{ route('event.getAppointmentRequest')}}",
            'get_store_wise_replies':"{{ url('get-store-wise-replies') }}",
            'request_path' : "{{ Request::path() }}",
            'wa_sendMessage':"{{action([\App\Http\Controllers\WhatsAppController::class, 'sendMessage'], 'SOP-Data')}}",
            'de_assignUser':"{{ action([\App\Http\Controllers\DevelopmentController::class, 'assignUser']) }}",
            'wa_sendMessage_issue':"{{ action([\App\Http\Controllers\WhatsAppController::class, 'sendMessage'], 'issue') }}",
            'de_uploadDocument':"{{ action([\App\Http\Controllers\DevelopmentController::class, 'uploadDocument']) }}",
            'de_getDocument':"{{ action([\App\Http\Controllers\DevelopmentController::class, 'getDocument']) }}",
            're_full_url' : "{{ request()->fullUrl() }}",
            're_url' : "{{ request()->url() }}",
        },
        'auth' : {
            'id' : {{ Auth::id() }},
            'user_name' : "{{ Auth::user()->name }}",
            'has_admin' : "{{ Auth::user()->hasRole('Admin') }}",
        },
        

    };
</script>
<script src="{{asset('js/custom_app.js')}}"></script>

@if ( !empty($_SERVER['HTTP_HOST']) && !empty($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] != "127.0.0.1" &&
!stristr($_SERVER['HTTP_HOST'], '.mac') )
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{$account_id}}"></script>
<script>
window.dataLayer = window.dataLayer || [];

function gtag() {
    dataLayer.push(arguments);
}

gtag('js', new Date());
//gtag('config', 'UA-171553493-1');
</script>
@endif


    @stack('scripts')

    <script>

        @if(session()->has('encrpyt'))
    
        var inactivityTime = function() {
            var time;
            window.onload = resetTimer;
            // DOM Events
            document.onmousemove = resetTimer;
            document.onkeypress = resetTimer;
    
            function remove_key() {
                $.ajax({
                        url: configs.routes.encryption_forget_key,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            private: '1',
                            "_token": $('meta[name="csrf-token"]').attr('content'),
                        },
                    })
                    .done(function() {
                        alert('Please Insert Private Key');
                        location.reload();
                        console.log("success");
                    })
                    .fail(function() {
                        console.log("error");
                    })
            }
    
            function resetTimer() {
                clearTimeout(time);
                time = setTimeout(remove_key, 1200000);
                // 1000 milliseconds = 1 second
            }
        };
    
        window.onload = function() {
            inactivityTime();
        }
    
        @endif
    </script>

    
    <script>
    @php
    if (!\Auth::guest()) {
    $path = Request::path();
    $hasPage = \App\AutoRefreshPage::where("page", $path)->where("user_id", \Auth()->user()->id)->first();
    if ($hasPage) {
    @endphp

    var idleTime = 0;

    function reloadPageFun() {
        idleTime = idleTime + 1000;
        var autoRefresh = $.cookie('auto_refresh');
        if (idleTime > {{$hasPage->time * 1000}} && (typeof autoRefresh == "undefined" || autoRefresh ==
                1)) {
            window.location.reload();
        }
    }

    $(document).ready(function() {
        //Increment the idle time counter every minute.
        setInterval(function() {
            reloadPageFun()
        }, 3000);
        //Zero the idle timer on mouse movement.
        $(this).mousemove(function(e) {
            idleTime = 0;
        });
        $(this).keypress(function(e) {
            idleTime = 0;
        });
    });

    @php }} @endphp
    

    @php
        $route = request()->route()->getName();
    @endphp
    @if (in_array($route, ["development.issue.index", "task.index", "development.summarylist", "chatbot.messages.list"]))
        $(".show-estimate-time").click(function (e) {
            e.preventDefault();
            var tasktype = $(this).data('task');
            $.ajax({
                type: "GET",
                url: configs.routes.task_estimate_list,
                
                success: function (response) {
                    $("#showLatestEstimateTime").modal('show');
                    $("#showLatestEstimateTime .modal-table").html(response);
                },
                error: function (error) {

                }

            });
        });
        $("#shortcut-estimate-search").select2();

        $("#shortcut-estimate-search").change(function (e) {
            e.preventDefault();
            let task_id = $(this).val();
            @if ($route == "development.issue.index")
                var  tasktype = "DEVTASK";
            @else
                var tasktype = "TASK";
            @endif
            $.ajax({
                type: "GET",
                url: configs.routes.task_estimate_list,
                data: {
                    task: tasktype,
                    task_id
                },
                success: function (response) {
                    $("#showLatestEstimateTime").modal('show');
                    $("#showLatestEstimateTime .modal-table").html(response);
                },
                error: function (error) {
                    toastr["error"]("Error while fetching data.");
                }

            });
        });
    @endif

</script>