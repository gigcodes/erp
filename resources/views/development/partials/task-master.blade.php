<style type="text/css">
    .green-notification {
        color: green;
    }

    .red-notification {
        color: grey;
    }
    .table-scrapper, .table-scrapper th, .table-scrapper td{font-size: 14px}
    .add-scrapper-remarks{float: left;    padding: 10px 2px;}
    .add-scrapper-textarea{float: left; display: inline-block;   width: 90%;}
</style>
<div style="overflow-x:auto;">
<table class="table table-bordered table-striped" id="task_Tables">
    <tr>
        @if (Auth::user()->isAdmin())
            @if(!empty($dynamicColumnsToShowDl))
                @if (!in_array('ID', $dynamicColumnsToShowDl))
                    <th style="width:8%;">ID</th>
                @endif

                @if (!in_array('Module', $dynamicColumnsToShowDl))
                    <th style="width:5%;">Module</th>
                @endif

                @if (!in_array('Date', $dynamicColumnsToShowDl))
                    <th style="width:5%;">Date</th>
                @endif

                @if (!in_array('Subject', $dynamicColumnsToShowDl))
                    <th style="width:5%;">Subject</th>
                @endif

                @if (!in_array('Communication', $dynamicColumnsToShowDl))
                    <th style="width:15%;">Communication</th>
                @endif
                
                @if (!in_array('Est Completion Time', $dynamicColumnsToShowDl))
                    <th style="width:5%;">Est Completion Time</th>
                @endif

                @if (!in_array('Est Completion Date', $dynamicColumnsToShowDl))
                    <th style="width:5%;">Est Completion Date</th>
                @endif

                @if (!in_array('Tracked Time', $dynamicColumnsToShowDl))
                    <th style="width:9%;">Tracked Time</th>
                @endif

                @if (!in_array('Developers', $dynamicColumnsToShowDl))
                    <th style="width:8%;">Developers</th>
                @endif

                @if (!in_array('Status', $dynamicColumnsToShowDl))
                    <th style="width:8%;">Status</th>
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
                    <th style="width:10%">Estimated Datetime</th>
                @endif

                @if (!in_array('Shortcuts', $dynamicColumnsToShowDl))
                    <th style="width:20%">Shortcuts</th>
                @endif

                @if (!in_array('Actions', $dynamicColumnsToShowDl))
                    <th style="width:7%;">Actions</th>
                @endif
            @else
                <th style="width:8%;">ID</th>
                <th style="width:5%;">Module</th>
                <th style="width:5%;">Date</th>
                <th style="width:5%;">Subject</th>
                <th style="width:15%;">Communication</th>
                <th style="width:5%;">Est Completion Time</th>
                <th style="width:5%;">Est Completion Date</th>
                <th style="width:9%;">Tracked Time</th>
                <th style="width:8%;">Developers</th>
                <th style="width:8%;">Status</th>
                <th style="width:5%;">Cost</th>
                <th style="width:7%;">Milestone</th>
                <th style="width:10%">Estimated Time</th>
                <th style="width:10%">Estimated Datetime</th>
                <th style="width:20%">Shortcuts</th>
                <th style="width:7%;">Actions</th>
            @endif
        @else
            <th style="width:8%;">ID</th>
            <th style="width:5%;">Module</th>
            <th style="width:5%;">Date</th>
            <th style="width:5%;">Subject</th>
            <th style="width:15%;">Communication</th>
            <th style="width:5%;">Est Completion Time</th>
            <th style="width:5%;">Est Completion Date</th>
            <th style="width:9%;">Tracked Time</th>
            <th style="width:8%;">Developers</th>
            <th style="width:8%;">Status</th>
            <th style="width:5%;">Cost</th>
            <th style="width:7%;">Milestone</th>
            <th style="width:10%">Estimated Time</th>
            <th style="width:10%">Estimated Datetime</th>
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
@include("development.partials.add-scrapper")

<div id="dev_scrapper_statistics" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="p-0 m-0">Scrapper Statistics <!-- <a href="javascript:void(0)" id="scrapper-history"><i class="fa fa-list" aria-hidden="true"></i></a> --></h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body" id="dev_scrapper_statistics_content">
            </div>
        </div>
    </div>
</div>

<div id="dev_scrapper_statistics_history" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="p-0 m-0">Scrapper Statistics History</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body" id="dev_scrapper_statistics_history_content">
            </div>
        </div>
    </div>
</div>
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

        $(".add-scrapper").click(function (e) { 
            e.preventDefault();
            let task_id = $(this).data("task_id")
            let task_type = $(this).data("task_type")
            $("#addScrapperModel").find('input[name=task_id]').val(task_id);
            $("#addScrapperModel").find('input[name=task_type]').val(task_type);
            $("#addScrapperModel").modal("show");
        });

        $(document).on("click", ".create-scrapper", function(e) {
            e.preventDefault();
            var form = $(this).closest("form");
            $.ajax({
                url: form.attr("action"),
                type: 'POST',
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: form.serialize(),
                beforeSend: function() {
                    $(this).text('Loading...');
                    $("#loading-image").show();
                },
                success: function(response) {
                    $("#loading-image").hide();
                    if (response.code == 200) {
                        form[0].reset();
                        toastr['success'](response.message);
                        $("#addScrapperModel").modal("hide");
                    } else {
                        toastr['error'](response.message);
                    }
                }
            }).fail(function(response) {
                $('#loading-image').hide();
                toastr['error'](response.responseJSON.message);
            });
        });

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        $(document).on("click", ".count-dev-scrapper", function() {

            var $this = $(this);
            var task_id = $(this).data("id");

            $('#scrapper-history').attr('data-id', task_id);

            $.ajax({
                type: 'get',
                url: '/development/countscrapper/' + task_id,
                dataType: "json",
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(data) {

                    $("#dev_scrapper_statistics").modal("show");
                    var table = `<div class="table-responsive">
                        <table class="table table-bordered table-striped table-scrapper" style="font-size:14px;">`;
                        table = table + '<tr>';
                        table = table + '<th width="10%">Column Name</th>';
                        table = table + '<th width="30%">Values</th>';
                        table = table + '<th width="15%">Status</th>';
                        table = table + '<th width="45%">Remarks</th>';
                        table = table + '</tr>';
                    if(data.values!=''){

                        $('#scrapper-history').attr('data-scrapperid', data.id);

                        $.each(data.values, function(key, value) {
                            table = table + '<tr>';
                            table = table + '<th>'+capitalizeFirstLetter(key.replace("_", " "));
                            table = table + '</th>';

                            if(key=='properties'){
                                if(data.values.properties!=''){
                                    table = table + '<td><table class="table table-bordered table-striped">';
                                    $.each(data.values.properties, function(key, value) {
                                        table = table + '<tr>';
                                            table = table + '<th>'+capitalizeFirstLetter(key.replace("_", " "));
;
                                            table = table + '</th>';
                                            table = table + '<td>'+value;
                                            table = table + '</td>';
                                        table = table + '</tr>';

                                    });
                                    table = table + '</table></td>';

                                    var approveValue = '';
                                    var unapproveValue = '';
                                    var StatusValue = ''
                                    for (var i = 0; i < data.ScrapperValuesHistory.length; i++) {

                                        if(data.ScrapperValuesHistory[i].column_name==key){

                                            StatusValue = data.ScrapperValuesHistory[i].status;

                                            if(StatusValue=='Approve'){
                                                approveValue = 'selected';
                                            }

                                            if(StatusValue=='Unapprove'){
                                                unapproveValue = 'selected';
                                            }

                                        }                            
                                    }

                                    var remarksValue = '';
                                    for (var i = 0; i < data.ScrapperValuesRemarksHistory.length; i++) {

                                        if(data.ScrapperValuesRemarksHistory[i].column_name==key){

                                            remarksValue = data.ScrapperValuesRemarksHistory[i].remarks;

                                        }                            
                                    }

                                    @if (Auth::user()->isAdmin())
                                        table = table + '<td>';
                                            table = table + '<select class="add-scrapper-status form-control" id="status_values_'+data.task_id+'_'+key+'" data-value="'+key+'" data-taskid="'+data.task_id+'">';
                                            table = table + '<option>Select Status</option>';
                                            table = table + '<option '+approveValue+' value="Approve">Approve</option>';
                                            table = table + '<option '+unapproveValue+' value="Unapprove">Unapprove</option>';
                                            table = table + '</select>';
                                        table = table + '</td>';

                                        table = table + '<td>';

                                        if(unapproveValue=='selected'){
                                            table = table + '<textarea rows="1" class="add-scrapper-textarea form-control" id="remarks_values_'+data.task_id+'_'+key+'">'+remarksValue+'</textarea>';

                                            table = table + '<button class="btn btn-sm btn-image add-scrapper-remarks"  title="Send approximate" data-taskid="'+data.task_id+'" data-value="'+key+'"><i class="fa fa-paper-plane" aria-hidden="true"></i></button></button>';
                                        }

                                        table = table + '</td>';
                                    @else   
                                        table = table + '<td>'+StatusValue+'</td>';

                                        table = table + '<td>';
                                        if(unapproveValue=='selected'){
                                            table = table +remarksValue;
                                        }
                                        table = table + '</td>';
                                    @endif
                                }
                            } else if(key=='images'){
                                if(data.values.images!=''){
                                    table = table + '<td><table class="table table-bordered table-striped">';
                                    table = table + '<tr><td>';
                                    $.each(data.values.images, function(key, value) {
                                        table = table + '<img src="'+value+'" width="50px" style="cursor: default;margin-right: 10px;">';
                                    });
                                    table = table + '</td></tr>';
                                    table = table + '</table></td>';

                                    var approveValue = '';
                                    var unapproveValue = '';
                                    var StatusValue = ''
                                    for (var i = 0; i < data.ScrapperValuesHistory.length; i++) {

                                        if(data.ScrapperValuesHistory[i].column_name==key){

                                            StatusValue = data.ScrapperValuesHistory[i].status;

                                            if(StatusValue=='Approve'){
                                                approveValue = 'selected';
                                            }

                                            if(StatusValue=='Unapprove'){
                                                unapproveValue = 'selected';
                                            }

                                        }                            
                                    }

                                    var remarksValue = '';
                                    for (var i = 0; i < data.ScrapperValuesRemarksHistory.length; i++) {

                                        if(data.ScrapperValuesRemarksHistory[i].column_name==key){

                                            remarksValue = data.ScrapperValuesRemarksHistory[i].remarks;

                                        }                            
                                    }

                                    @if (Auth::user()->isAdmin())

                                        table = table + '<td>';
                                            table = table + '<select class="add-scrapper-status form-control" id="status_values_'+data.task_id+'_'+key+'" data-value="'+key+'" data-taskid="'+data.task_id+'">';
                                            table = table + '<option>--Select Status--</option>';
                                            table = table + '<option '+approveValue+' value="Approve">Approve</option>';
                                            table = table + '<option '+unapproveValue+' value="Unapprove">Unapprove</option>';
                                            table = table + '</select>';
                                        table = table + '</td>';

                                        table = table + '<td>';
                                        if(unapproveValue=='selected'){
                                            table = table + '<textarea rows="1" class="add-scrapper-textarea form-control" id="remarks_values_'+data.task_id+'_'+key+'">'+remarksValue+'</textarea>';

                                            table = table + '<button class="btn btn-image add-scrapper-remarks"  title="Send approximate" data-taskid="'+data.task_id+'" data-value="'+key+'"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>';
                                        }

                                        table = table + '</td>';

                                    @else   
                                        table = table + '<td>'+StatusValue+'</td>';

                                        table = table + '<td>';
                                        if(unapproveValue=='selected'){
                                            table = table +remarksValue;
                                        }
                                        table = table + '</td>';
                                    @endif
                                }
                            } else {
                                table = table + '<td>'+value;
                                table = table + '</td>';

                                var approveValue = '';
                                var unapproveValue = '';
                                var StatusValue = ''
                                for (var i = 0; i < data.ScrapperValuesHistory.length; i++) {

                                    if(data.ScrapperValuesHistory[i].column_name==key){

                                        StatusValue = data.ScrapperValuesHistory[i].status;

                                        if(StatusValue=='Approve'){
                                            approveValue = 'selected';
                                        }

                                        if(StatusValue=='Unapprove'){
                                            unapproveValue = 'selected';
                                        }

                                    }                            
                                }

                                var remarksValue = '';
                                for (var i = 0; i < data.ScrapperValuesRemarksHistory.length; i++) {

                                    if(data.ScrapperValuesRemarksHistory[i].column_name==key){

                                        remarksValue = data.ScrapperValuesRemarksHistory[i].remarks;

                                    }                            
                                }

                                @if (Auth::user()->isAdmin())
                                    table = table + '<td>';
                                        table = table + '<select class="add-scrapper-status form-control" id="status_values_'+data.task_id+'_'+key+'" data-value="'+key+'" data-taskid="'+data.task_id+'">';
                                        table = table + '<option>--Select Status--</option>';
                                        table = table + '<option '+approveValue+' value="Approve">Approve</option>';
                                        table = table + '<option '+unapproveValue+' value="Unapprove">Unapprove</option>';
                                        table = table + '</select>';
                                    table = table + '</td>';

                                    table = table + '<td>';

                                    if(unapproveValue=='selected'){
                                        table = table + '<textarea rows="1" class="add-scrapper-textarea form-control" id="remarks_values_'+data.task_id+'_'+key+'">'+remarksValue+'</textarea> ';

                                        table = table + '<button class="btn btn-image add-scrapper-remarks"  title="Send approximate" data-taskid="'+data.task_id+'" data-value="'+key+'"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>';
                                    }

                                    table = table + '</td>';
                                @else   
                                    table = table + '<td>'+StatusValue+'</td>';

                                    table = table + '<td>';
                                    if(unapproveValue=='selected'){
                                        table = table +remarksValue;
                                    }
                                    table = table + '</td>';
                                @endif

                                
                            }
                            table = table + '</tr>';
                        });
                    }

                    table = table + '</table></div>';
                    $("#loading-image").hide();
                    $(".modal").css("overflow-x", "hidden");
                    $(".modal").css("overflow-y", "auto");
                    $("#dev_scrapper_statistics_content").html(table);
                },
                error: function(error) {
                    console.log(error);
                    $("#loading-image").hide();
                }
            });
        });

        $(document).on("change", ".add-scrapper-status", function(e) {

            let task_id = $(this).data("taskid");
            let column_name = $(this).data("value");
            var status = $(this).val();

            $.ajax({
                url: "{{route('development.updatescrapperdata')}}",
                type: 'POST',
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    'task_id' :task_id,
                    'column_name' :column_name,
                    'status' :status,
                },
                beforeSend: function() {
                    $(this).text('Loading...');
                    $("#loading-image").show();
                },
                success: function(response) {
                    $('.count-dev-scrapper_'+task_id).trigger('click');
                    $("#loading-image").hide();
                    if (response.code == 200) {
                        toastr['success'](response.message);
                    } else {
                        toastr['error'](response.message);
                    }

                    window.location.reload();
                }
            }).fail(function(response) {
                $('#loading-image').hide();
                toastr['error'](response.responseJSON.message);
            });
            
        });

        $(document).on("click", ".add-scrapper-remarks", function() {

            let task_id = $(this).data("taskid");
            let column_name = $(this).data("value");
            var remarks = $('#remarks_values_'+task_id+'_'+column_name).val();
            
            $.ajax({
                url: "{{route('development.updatescrapperremarksdata')}}",
                type: 'POST',
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    'task_id' :task_id,
                    'column_name' :column_name,
                    'remarks' :remarks,
                },
                beforeSend: function() {
                    $(this).text('Loading...');
                    $("#loading-image").show();
                },
                success: function(response) {
                    $("#loading-image").hide();
                    if (response.code == 200) {
                        toastr['success'](response.message);
                    } else {
                        toastr['error'](response.message);
                    }
                }
            }).fail(function(response) {
                $('#loading-image').hide();
                toastr['error'](response.responseJSON.message);
            });
        });
    });

    $(document).on("click", "#scrapper-history", function() {

        var $this = $(this);
        var task_id = $(this).data("id");
        var scrapperid_id = $(this).data("scrapperid");
            
        $.ajax({
            type: 'post',
            url: "{{route('development.historyscrapper')}}",
            dataType: "json",
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'task_id' :task_id,
                'id' :scrapperid_id
            },
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(data) {

                $("#dev_scrapper_statistics_history").modal("show");
                var table = `<div class="table-responsive infinite-scroll" style="overflow-y: auto">
                    <table class="table table-bordered table-striped" style="font-size:14px;">`;
                    table = table + '<tr>';
                    table = table + '<th width="10%">Title</th>';
                    table = table + '<th width="7%">Website</th>';
                    table = table + '<th width="7%">Sku</th>';
                    table = table + '<th width="5%">Url</th>';
                    table = table + '<th width="4%">Images</th>';
                    table = table + '<th width="5%">Description</th>';
                    table = table + '<th width="5%">Properties</th>';
                    table = table + '<th width="5%">Currency</th>';
                    table = table + '<th width="4%">Size System</th>';
                    table = table + '<th width="3%">Price</th>';
                    table = table + '<th width="5%">Discounted Price</th>';
                    table = table + '<th width="5%">Discounted Percentage</th>';
                    table = table + '<th width="3%">B2b Price</th>';
                    table = table + '<th width="3%">Brand</th>';
                    table = table + '<th width="3%">Is Sale</th>';
                    table = table + '<th width="7%">Date</th>';
                    table = table + '</tr>';
                if(data.values!=''){
                    $.each(data.values, function(key, value) {
                        table = table + '<tr>';
                        table = table + '<td>'+value.title+'</td>';
                        table = table + '<td>'+value.website+'</td>';
                        table = table + '<td>'+value.sku+'</td>';
                        table = table + '<td>'+value.url+'</td>';
                        table = table + '<td>'+value.title+'</td>';
                        table = table + '<td>'+value.description+'</td>';
                        table = table + '<td>'+value.title+'</td>';
                        table = table + '<td>'+value.currency+'</td>';
                        table = table + '<td>'+value.size_system+'</td>';
                        table = table + '<td>'+value.price+'</td>';
                        table = table + '<td>'+value.discounted_price+'</td>';
                        table = table + '<td>'+value.discounted_percentage+'</td>';
                        table = table + '<td>'+value.b2b_price+'</td>';
                        table = table + '<td>'+value.brand+'</td>';
                        table = table + '<td>'+value.is_sale+'</td>';
                        table = table + '</tr>';
                    });
                }

                table = table + '</table></div>';
                $("#loading-image").hide();
                $(".modal").css("overflow-x", "hidden");
                $(".modal").css("overflow-y", "auto");
                $("#dev_scrapper_statistics_history_content").html(table);
            },
            error: function(error) {
                console.log(error);
                $("#loading-image").hide();
            }
        });
    });
</script>