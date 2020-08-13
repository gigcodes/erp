@extends('layouts.app')

@section('favicon' , 'development-issue.png')

@if($title == "devtask")
    @section('title', 'Development Issue')
@else
    @section('title', 'Development Task')
@endif

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <style type="text/css">

    </style>
@endsection

<style> 
    .status-selection .btn-group {
        padding: 0;
        width: 100%;
    }
    .status-selection .multiselect {
        width : 100%;
    }
    .pd-sm {
        padding: 0px 8px !important;
    }
    tr {
        background-color: #f9f9f9;
    }
</style>


@section('large_content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">{{ ucfirst($title) }}</h2>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            {{ $message }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $priorities = [
          '1' => 'Critical',
          '2' => 'Urgent',
          '3' => 'Normal'
        ];
    @endphp
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row mb-4">
        <div class="col-md-12">
            @include("development.partials.task-issue-search")
            <div class="pull-right mt-4">
            <a class="btn btn-secondary" 
                        data-toggle="collapse" href="#plannedFilterCount" role="button" aria-expanded="false" aria-controls="plannedFilterCount">
                           Show Planned count
            </a>
            <a class="btn btn-secondary" 
                        data-toggle="collapse" href="#inProgressFilterCount" role="button" aria-expanded="false" aria-controls="inProgressFilterCount">
                           Show In Progress count
            </a>
            <a style="color:white;" class="btn btn-secondary  priority_model_btn">Priority</a>
            @if(auth()->user()->isReviwerLikeAdmin())
                    <a href="javascript:" class="btn btn-secondary" id="newTaskModalBtn" data-toggle="modal" data-target="#newTaskModal">Add New Dev Task </a>
                @endif
        </div>


         
        </div>
    </div>
    @include("development.partials.task-issue-counter")

    <?php
        $query = http_build_query(Request::except('page'));
        $query = url()->current() . (($query == '') ? $query . '?page=' : '?' . $query . '&page=');
    ?>

    <div class="form-group position-fixed" style="top: 50px; left: 20px;">
        Goto :
        <select onchange="location.href = this.value;" class="form-control" id="page-goto">
            @for($i = 1 ; $i <= $issues->lastPage() ; $i++ )
                <option data-value="{{$i}}" value="{{ $query.$i }}" {{ ($i == $issues->currentPage() ? 'selected' : '') }}>{{ $i }}</option>
            @endfor
        </select>
    </div>
    <div class="infinite-scroll">
        <div >
        @if($title == 'issue' && auth()->user()->isReviwerLikeAdmin())
        <table class="table table-bordered table-striped">
            <tr class="add-new-issue">
                @include("development.partials.add-new-issue")
            </tr>
        </table>
        @endif
        <div class="infinite-scroll-products-inner">
            @include("development.partials.task-master")
        </div>
            <?php echo $issues->appends(request()->except("page"))->links(); ?>

            <img class="infinite-scroll-products-loader center-block" src="/images/loading.gif" alt="Loading..." style="display: none" />
        </div>
    </div>
    @include("development.partials.create-new-module")
    @include("development.partials.assign-issue-modal")
    @include("development.partials.assign-priority-modal")
    @include("development.partials.chat-list-history-modal")
    @include("development.partials.upload-document-modal")
    @include("partials.plain-modal")
    @include("development.partials.time-history-modal")
    <!-- @include("development.partials.time-priority-modal") -->
@endsection

@section('scripts')
    <script src="/js/bootstrap-datetimepicker.min.js"></script>
    <script src="/js/jquery-ui.js"></script>
    <script src="/js/jquery.jscroll.min.js"></script>
    <script src="/js/bootstrap-multiselect.min.js"></script>
    <script src="/js/bootstrap-filestyle.min.js"></script>
    <script>
        $(document).ready(function () {
            var isLoadingProducts = false;
            $(document).on('click', '.assign-issue-button', function () {
                var issue_id = $(this).data('id');
                var url = "{{ url('development') }}/" + issue_id + "/assignIssue";

                $('#assignIssueForm').attr('action', url);
            });

            $(".multiselect").multiselect({
                nonSelectedText:'Please Select'
            });

            $(window).scroll(function() {
                if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
                    loadMoreProducts();
                }
            });

            function loadMoreProducts() {
                if (isLoadingProducts)
                    return;
                isLoadingProducts = true;
                if(!$('.pagination li.active + li a').attr('href'))
                return;

                var $loader = $('.infinite-scroll-products-loader');
                $.ajax({
                    url: $('.pagination li.active + li a').attr('href'),
                    type: 'GET',
                    beforeSend: function() {
                        $loader.show();
                        $('ul.pagination').remove();
                    }
                })
                .done(function(data) {
                    // console.log(data);
                    if('' === data.trim())
                        return;

                    $loader.hide();

                    $('.infinite-scroll-products-inner').append(data);

                    isLoadingProducts = false;
                })
                .fail(function(jqXHR, ajaxOptions, thrownError) {
                    console.error('something went wrong');

                    isLoadingProducts = false;
                });
            }

            // $('.infinite-scroll').jscroll({
            //     debug: false,
            //     autoTrigger: true,
            //     loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
            //     padding: 20,
            //     nextSelector: '.pagination li.active + li a',
            //     contentSelector: '.infinite-scroll',
            //     callback: function () {
            //         $('ul.pagination:visible:first').remove();
            //         var next_page = $('.pagination li.active');
            //         if (next_page.length > 0) {
            //             var current_page = next_page.find("span").html();
            //             $('#page-goto option[data-value="' + current_page + '"]').attr('selected', 'selected');
            //         }
            //         $.each($("select.resolve-issue"),function(k,v){
            //             if (!$(v).hasClass("select2-hidden-accessible")) {
            //                 $(v).select2({width:"100%", tags:true});
            //             }
            //         });
            //         $('select.select2').select2({
            //             tags: true,
            //             width: "100%"
            //         });
            //     }
            // });
            
            $('select.select2').select2({
                tags: true,
                width: "100%"
            });


            $('.assign-master-user.select2').select2({
                width: "100%"
            });

            $('.assign-user.select2').select2({
                width: "100%"
            });

            $.each($(".resolve-issue"),function(k,v){
                if (!$(v).hasClass("select2-hidden-accessible")) {
                    $(v).select2({width:"100%"});
                }
            });

            $('select#priority_user_id').select2({
                tags: true,
                width: '100%'
            });

            $('.estimate-time').datetimepicker({
                format: 'HH:mm'
            });
        });

        function getPriorityTaskList(id) {
            var selected_issue = [0];

            $('input[name ="selected_issue[]"]').each(function () {
                if ($(this).prop("checked") == true) {
                    selected_issue.push($(this).val());
                }
            });

            $.ajax({
                url: "{{route('development.issue.list.by.user.id')}}",
                type: 'POST',
                data: {
                    user_id: id,
                    _token: "{{csrf_token()}}",
                    selected_issue: selected_issue,
                },
                success: function (response) {
                    var html = '';
                    response.forEach(function (issue) {
                        html += '<tr>';
                        html += '<td><input type="hidden" name="priority[]" value="' + issue.id + '">' + issue.id + '</td>';
                        html += '<td>' + issue.module + '</td>';
                        html += '<td>' + issue.subject + '</td>';
                        html += '<td>' + issue.task + '</td>';
                        html += '<td>' + issue.submitted_by + '</td>';
                        html += '<td><a href="javascript:;" class="delete_priority" data-id="' + issue.id + '">Remove<a></td>';
                        html += '</tr>';
                    });
                    $(".show_issue_priority").html(html);
                    <?php if (auth()->user()->isAdmin()) { ?>
                    $(".show_issue_priority").sortable();
                    <?php } ?>
                },
                error: function () {
                    alert('There was error loading priority task list data');
                }
            });
        }

        $(document).on('click', '.delete_priority', function (e) {
            var id = $(this).data('id');
            $('input[value ="' + id + '"]').prop('checked', false);
            $(this).closest('tr').remove();
        });
        $('.priority_model_btn').click(function () {
            $("#priority_user_id").val('');
            $(".show_task_priority").html('');
            <?php if (auth()->user()->isAdmin()) { ?>
                $("#priority_user_id").show();
                getPriorityTaskList($('#priority_user_id').val());
            <?php } else { ?>
                $("#priority_user_id").hide();
                getPriorityTaskList('{{auth()->user()->id}}');
            <?php } ?>
            $('#priority_model').modal('show');
        });

        $('#priority_user_id').change(function () {
            getPriorityTaskList($(this).val())
        });

        $(document).on('submit', '#priorityForm', function (e) {
            e.preventDefault();
            <?php if (auth()->user()->isAdmin()) { ?>
            $.ajax({
                url: "{{route('development.issue.set.priority')}}",
                type: 'POST',
                data: $(this).serialize(),
                success: function (response) {
                    toastr['success']('Priority successfully update!!', 'success');
                    $('#priority_model').modal('hide');
                },
                error: function () {
                    alert('There was error loading priority task list data');
                }
            });
            <?php } ?>
        });
        
        $(document).on('click', '.send-message', function (event) {

            var textBox = $(this).closest(".panel-footer").find(".send-message-textbox");
            var sendToStr  = $(this).closest(".panel-footer").find(".send-message-number").val();


            let issueId = textBox.attr('data-id');
            let message = textBox.val();
            if (message == '') {
                return;
            }

            let self = textBox;

            $.ajax({
                url: "{{action('WhatsAppController@sendMessage', 'issue')}}",
                type: 'POST',
                data: {
                    "issue_id": issueId,
                    "message": message,
                    "sendTo" : sendToStr,
                    "_token": "{{csrf_token()}}",
                   "status": 2
                },
                dataType: "json",
                success: function (response) {
                    toastr["success"]("Message sent successfully!", "Message");
                    $('#message_list_' + issueId).append('<li>' + response.message.created_at + " : " + response.message.message + '</li>');
                    $(self).removeAttr('disabled');
                    $(self).val('');
                },
                beforeSend: function () {
                    $(self).attr('disabled', true);
                },
                error: function () {
                    alert('There was an error sending the message...');
                    $(self).removeAttr('disabled', true);
                }
            });
        });



        $(document).on('click', '.send-message-open', function (event) {
            var textBox = $(this).closest(".expand-row").find(".send-message-textbox");
            var sendToStr  = $(this).closest(".expand-row").find(".send-message-number").val();

            let issueId = textBox.attr('data-id');
            let message = textBox.val();
            if (message == '') {
                return;
            }

            let self = textBox;

            $.ajax({
                url: "{{action('WhatsAppController@sendMessage', 'issue')}}",
                type: 'POST',
                data: {
                    "issue_id": issueId,
                    "message": message,
                    "sendTo" : sendToStr,
                    "_token": "{{csrf_token()}}",
                   "status": 2
                },
                dataType: "json",
                success: function (response) {
                    toastr["success"]("Message sent successfully!", "Message");
                    $('#message_list_' + issueId).append('<li>' + response.message.created_at + " : " + response.message.message + '</li>');
                    $(self).removeAttr('disabled');
                    $(self).val('');
                },
                beforeSend: function () {
                    $(self).attr('disabled', true);
                },
                error: function () {
                    alert('There was an error sending the message...');
                    $(self).removeAttr('disabled', true);
                }
            });
        });

        $(document).on('change', '.set-responsible-user', function () {
            let id = $(this).attr('data-id');
            let userId = $(this).val();

            if (userId == '') {
                return;
            }

            $.ajax({
                url: "{{action('DevelopmentController@assignResponsibleUser')}}",
                data: {
                    responsible_user_id: userId,
                    issue_id: id
                },
                success: function () {
                    toastr["success"]("User assigned successfully!", "Message")
                }
            });
        });
        $(document).on('change', '.assign-user', function () {
            let id = $(this).attr('data-id');
            let userId = $(this).val();

            if (userId == '') {
                return;
            }

            $.ajax({
                url: "{{action('DevelopmentController@assignUser')}}",
                data: {
                    assigned_to: userId,
                    issue_id: id
                },
                success: function () {
                    toastr["success"]("User assigned successfully!", "Message")
                },   
                error: function (error) {
                    toastr["error"](error.responseJSON.message, "Message")
                    
                }
            });

        });

        $(document).on('change', '.task-module', function () {
            let id = $(this).attr('data-id');
            let moduleID = $(this).val();

            if (moduleID == '') {
                return;
            }

            $.ajax({
                url: "{{action('DevelopmentController@changeModule')}}",
                data: {
                    module_id: moduleID,
                    issue_id: id
                },
                success: function () {
                    toastr["success"]("Module assigned successfully!", "Message")
                }
            });

        });

        $(document).on('change', '.assign-master-user', function () {
            let id = $(this).attr('data-id');
            let userId = $(this).val();

            if (userId == '') {
                return;
            }

            $.ajax({
                url: "{{action('DevelopmentController@assignMasterUser')}}",
                data: {
                    master_user_id: userId,
                    issue_id: id
                },
                success: function () {
                    toastr["success"]("Master User assigned successfully!", "Message")
                },
                error: function (error) {
                    toastr["error"](error.responseJSON.message, "Message")
                    
                }
            });

        });

        

        $(document).on('keyup', '.save-cost', function (event) {
            if (event.keyCode != 13) {
                return;
            }
            let id = $(this).attr('data-id');
            let amount = $(this).val();

            $.ajax({
                url: "{{action('DevelopmentController@saveAmount')}}",
                data: {
                    cost: amount,
                    issue_id: id
                },
                success: function () {
                    toastr["success"]("Price updated successfully!", "Message")
                }
            });
        });



        $(document).on('keyup', '.save-milestone', function (event) {
            if (event.keyCode != 13) {
                return;
            }
            let id = $(this).attr('data-id');
            let total = $(this).val();

            $.ajax({
                url: "{{action('DevelopmentController@saveMilestone')}}",
                data: {
                    total: total,
                    issue_id: id
                },
                success: function () {
                    toastr["success"]("Milestone updated successfully!", "Message")
                },   
                error: function (error) {
                    toastr["error"](error.responseJSON.message, "Message")
                    console.log(error.responseJSON.message);
                    
                }
            });
        });

        $(document).on('change', '.save-language', function (event) {
            
            let id = $(this).attr('data-id');
            let language = $(this).val();

            $.ajax({
                url: "{{action('DevelopmentController@saveLanguage')}}",
                data: {
                    language: language,
                    issue_id: id
                },
                success: function () {
                    toastr["success"]("Language updated successfully!", "Message")
                }
            });
        });

        $(document).on('click', '.expand-row', function () {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });

        $(document).on('keyup', '.estimate-time-change', function () {
            if (event.keyCode != 13) {
                return;
            }
            let issueId = $(this).data('id');
            let estimate_minutes = $("#estimate_minutes_" + issueId).val();
            $.ajax({
                url: "{{action('DevelopmentController@saveEstimateMinutes')}}",
                data: {
                    estimate_minutes: estimate_minutes,
                    issue_id: issueId
                },
                success: function () {
                    toastr["success"]("Estimate Minutes updated successfully!", "Message")
                }
            });

        });

        $(document).on('click', '.show-time-history', function() {
            var data = $(this).data('history');
            var issueId = $(this).data('id');
            $('#time_history_div table tbody').html('');
            $.ajax({
                url: "{{ route('development/time/history') }}",
                data: {id: issueId},
                success: function (data) {
                    if(data != 'error') {
                        $.each(data, function(i, item) {
                            $('#time_history_div table tbody').append(
                                '<tr>\
                                    <td>'+ moment(item['created_at']).format('DD/MM/YYYY') +'</td>\
                                    <td>'+ ((item['old_value'] != null) ? item['old_value'] : '-') +'</td>\
                                    <td>'+item['new_value']+'</td>\
                                </tr>'
                            );
                        });
                    }
                }
            });
            $('#time_history_modal').modal('show');
        });


        $(document).on('click', '.show-tracked-history', function() {
            var issueId = $(this).data('id');
            $('#time_history_div table tbody').html('');
            $.ajax({
                url: "{{ route('development/time/history') }}",
                data: {id: issueId},
                success: function (data) {
                    if(data != 'error') {
                        $.each(data, function(i, item) {
                            $('#time_history_div table tbody').append(
                                '<tr>\
                                    <td>'+ moment(item['created_at']).format('DD/MM/YYYY') +'</td>\
                                    <td>'+ ((item['old_value'] != null) ? item['old_value'] : '-') +'</td>\
                                    <td>'+item['new_value']+'</td>\
                                </tr>'
                            );
                        });
                    }
                }
            });
            $('#time_history_modal').modal('show');
        });

        $(document).on('change', '.change-task-status', function () {
            var taskId = $(this).data("id");
            var status = $(this).val();
            $.ajax({
                url: "{{ action('DevelopmentController@changeTaskStatus') }}",
                type: 'POST',
                data: {
                    task_id: taskId,
                    _token: "{{csrf_token()}}",
                    status: status
                },
                success: function () {
                    toastr['success']('Status Changed successfully!')
                }
            });
        });

        function sendImage(id) {

            $.ajax({
                url: "{{action('WhatsAppController@sendMessage', 'issue')}}",
                type: 'POST',
                data: {
                    issue_id: id,
                    type: 1,
                    message: '',
                    _token: "{{csrf_token()}}",
                    status: 2
                },
                success: function () {
                    toastr["success"]("Message sent successfully!", "Message");

                },
                beforeSend: function () {
                    $(self).attr('disabled', true);
                },
                error: function () {
                    alert('There was an error sending the message...');
                    $(self).removeAttr('disabled', true);
                }
            });

        }

        function sendUploadImage(id) {

            $('#file-input' + id).trigger('click');

            $('#file-input' + id).change(function () {
                event.preventDefault();
                let image_upload = new FormData();
                let TotalImages = $(this)[0].files.length;  //Total Images
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
                        url: "{{action('WhatsAppController@sendMessage', 'issue')}}",
                        data: image_upload,
                        async: true,
                        contentType: false,
                        processData: false,
                        beforeSend: function () {
                            $("#loading-image").show();
                        },
                        success: function (images) {
                            $("#loading-image").hide();
                            alert('Images send successfully');
                        },
                        error: function () {
                            console.log(`Failed`)
                        }
                    })
                }
            })
        }

        //Popup for add new task
        $(document).on('click', '#newTaskModalBtn', function () {
            if ($("#newTaskModal").length > 0) {
                $("#newTaskModal").remove();
            }

            $.ajax({
                url: "{{ action('DevelopmentController@openNewTaskPopup') }}",
                type: 'GET',
                dataType: "JSON",
                success: function (resp) {
                    console.log(resp);
                    if (resp.status == 'ok') {
                        $("body").append(resp.html);
                        $('#newTaskModal').modal('show');
                        $('select.select2').select2({tags: true});
                    }
                }
            });
        });

        function resolveIssue(obj, task_id) {

            let id = task_id;
            let status = $(obj).val();
            let self = this;

            $.ajax({
                url: "{{action('DevelopmentController@resolveIssue')}}",
                data: {
                    issue_id: id,
                    is_resolved: status
                },
                success: function () {
                    toastr["success"]("Status updated!", "Message")
                },
                error: function (error) {
                    toastr["error"](error.responseJSON.message);
                }
            });
        }

        console.log($('#filecount'));

        $('#filecount').filestyle({htmlIcon: '<span class="oi oi-random"></span>',badge: true, badgeName: "badge-danger"});

        $(document).on("click",".upload-document-btn",function() {
            var id = $(this).data("id");
            $("#upload-document-modal").find("#hidden-identifier").val(id);    
            $("#upload-document-modal").modal("show");
        });

        $(document).on("submit","#upload-task-documents",function(e) {
            e.preventDefault();
            var form = $(this);
            var postData = new FormData(form[0]);
            $.ajax({
                method : "post",
                url: "{{action('DevelopmentController@uploadDocument')}}",
                data: postData,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function (response) {
                    if(response.code == 200) {
                        toastr["success"]("Status updated!", "Message")
                        $("#upload-document-modal").modal("hide");
                    }else{
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        $(document).on("click",".list-document-btn",function() {
            var id = $(this).data("id");
            $.ajax({
                method : "GET",
                url: "{{action('DevelopmentController@getDocument')}}",
                data: {id : id},
                dataType: "json",
                success: function (response) {
                    if(response.code == 200) {
                        $("#blank-modal").find(".modal-title").html("Document List");
                        $("#blank-modal").find(".modal-body").html(response.data);
                        $("#blank-modal").modal("show");
                    }else{
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });


        $(document).on('change', '#is_milestone', function () {

            var is_milestone = $('#is_milestone').val();
            if(is_milestone == '1') {
                $('#no_of_milestone').attr('required', 'required');
            }
            else {
                $('#no_of_milestone').removeAttr('required');
            }
        });
    </script>
@endsection
