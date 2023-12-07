<style type="text/css">
    .green-notification {
        color: green;
    }

    .red-notification {
        color: grey;
    }
</style>
<div style="overflow-x:auto;">
<table class="table table-bordered table-striped">
    <tr>
        @if(!empty($dynamicColumnsToShowDl))
            @if (!in_array('ID', $dynamicColumnsToShowDl))
                <th style="width:12%;">ID</th>
            @endif

            @if (!in_array('Module', $dynamicColumnsToShowDl))
                <th style="width:5%;">Module</th>
            @endif

            @if (!in_array('Date', $dynamicColumnsToShowDl))
                <th style="width:5%;">Date</th>
            @endif

            @if (!in_array('Subject', $dynamicColumnsToShowDl))
                <th style="width:8%;">Subject</th>
            @endif

            @if (!in_array('Communication', $dynamicColumnsToShowDl))
                <th style="width:20%;">Communication</th>
            @endif
            
            @if (!in_array('Est Completion Time', $dynamicColumnsToShowDl))
                <th style="width:10%;">Est Completion Time</th>
            @endif

            @if (!in_array('Est Completion Date', $dynamicColumnsToShowDl))
                <th style="width:10%;">Est Completion Date</th>
            @endif

            @if (!in_array('Tracked Time', $dynamicColumnsToShowDl))
                <th style="width:9%;">Tracked Time</th>
            @endif

            @if (!in_array('Developers', $dynamicColumnsToShowDl))
                <th style="width:13%;">Developers</th>
            @endif

            @if (!in_array('Status', $dynamicColumnsToShowDl))
                <th style="width:10%;">Status</th>
            @endif

            @if (!in_array('Cost', $dynamicColumnsToShowDl))
                <th style="width:5%;">Cost</th>
            @endif

            @if (!in_array('Milestone', $dynamicColumnsToShowDl))
                <th style="width:7%;">Milestone</th>
            @endif

            @if (!in_array('Estimated Time', $dynamicColumnsToShowDl))
                <th style="width:10%">Estimated Time</th>
            @endif

            @if (!in_array('Estimated Start Datetime', $dynamicColumnsToShowDl))
                <th style="width:10%">Estimated Start Datetime</th>
            @endif

            @if (!in_array('Estimated End Datetime', $dynamicColumnsToShowDl))
                <th style="width:10%">Estimated End Datetime</th>
            @endif

            @if (!in_array('Shortcuts', $dynamicColumnsToShowDl))
                <th width="5%">Shortcuts</th>
            @endif

            @if (!in_array('Actions', $dynamicColumnsToShowDl))
                <th style="width:7%;">Actions</th>
            @endif
        @else
            <th style="width:12%;">ID</th>
            <th style="width:5%;">Module</th>
            <th style="width:5%;">Date</th>
            <th style="width:8%;">Subject</th>
            <th style="width:20%;">Communication</th>
            <th style="width:10%;">Est Completion Time</th>
            <th style="width:10%;">Est Completion Date</th>
            <th style="width:9%;">Tracked Time</th>
            <th style="width:13%;">Developers</th>
            <th style="width:10%;">Status</th>
            <th style="width:5%;">Cost</th>
            <th style="width:7%;">Milestone</th>
            <th style="width:10%">Estimated Time</th>
            <th style="width:10%">Estimated Start Datetime</th>
            <th style="width:10%">Estimated End Datetime</th>
            <th width="5%">Shortcuts</th>
            <th style="width:7%;">Actions</th>
        @endif
    </tr>
    <?php
    $isReviwerLikeAdmin = auth()->user()->isReviwerLikeAdmin();
    $userID = Auth::user()->id;
    ?>
    <?php foreach ($issues as $key => $issue) { ?>
        <?php if ($isReviwerLikeAdmin) { ?>
            @include("development.partials.admin-row-view")
        <?php } elseif ($issue->created_by == $userID || $issue->master_user_id == $userID || $issue->assigned_to == $userID || $issue->team_lead_id == $userID || $issue->tester_id == $userID) { ?>
            @include("development.partials.developer-row-view")
        <?php } ?>
    <?php } ?>
</table>
<div id="taskGoogleDocModal" class="modal fade" role="dialog" style="display: none;">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Google Doc</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>

            <form action="{{route('google-docs.task')}}" method="POST">
                {{ csrf_field() }}
                <input type="hidden" id="task_id">
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Document type:</strong>

                        <select class="form-control" name="type" required id="doc-type">
                            <option value="spreadsheet">Spreadsheet</option>
                            <option value="doc">Doc</option>
                            <option value="ppt">Ppt</option>
                            <option value="xps">Xps</option>
                            <option value="txt">Txt</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <strong>Name:</strong>
                        <input type="text" name="doc_name" value="" class="form-control input-sm" placeholder="Document Name" required id="doc-name">
                    </div>

                    {{-- <div class="form-group">
                        <strong>Category:</strong>
                        <input type="text" name="doc_category" value="" class="form-control input-sm" placeholder="Document Category" required id="doc-category">
                    </div> --}}
                    {{-- <div class="form-group">
                        <strong>Category:</strong>
                        <select name="doc_category" class="form-control" id="doc-category" required>
                            <option>Select Category</option>
                            @if (isset($googleDocCategory) && count($googleDocCategory) > 0)
                                @foreach ($googleDocCategory as $key => $category)
                                    <option value="{{$key}}">{{$category}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div> --}}
                   
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-secondary" id="btnCreateTaskDocument">Create</button>
                </div>
            </form>
        </div>

    </div>
</div>
<div id="taskGoogleDocListModal" class="modal fade" role="dialog" style="display: none;">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Google Documents list</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered">
                    <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="5%">File Name</th>
                        <th width="5%">Created Date</th>
                        <th width="10%">URL</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
</div>
@include("development.partials.add-docs-permission")
@include("development.partials.column-visibility-list-modal")

<script>
    $(document).ready(function () {
        $(".add-document-permission").click(function (e) { 
            e.preventDefault();
            let user_id = $(this).data("assigned_to")
            let task_id = $(this).data("task_id")
            let task_type = $(this).data("task_type")
            $("#addGoogleDocPermission").find('input[name=user_id]').val(user_id);
            $("#addGoogleDocPermission").find('input[name=task_id]').val(task_id);
            $("#addGoogleDocPermission").find('input[name=task_type]').val(task_type);
            $.ajax({
                type: "GET",
                url: "{{route('google-docs.list')}}",
                data: "data",
                success: function (response) {
                    if(response.status == true) {
                        $("#assignDocumentList").html('').select2({
                            width: "100%", 
                            data: response.docs,
                            placeholder: "Select"
                        });
                        $("#addGoogleDocPermission").modal("show");
                        $("#assignDocumentList").val(null).trigger('change');
                    } else {
                        toastr['error']('Error while fetching the data.', 'Error');
                    }
                },
                error: function(error) {
                    toastr['error']('Error while fetching the data.', 'Error');
                }
            });
        });
    });
</script>