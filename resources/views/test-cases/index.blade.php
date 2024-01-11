@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
    <style type="text/css">
        .preview-category input.form-control {
            width: auto;
        }
        .break {
            word-break: break-all !important;
        }
    </style>

    <div class="row" id="common-page-layout">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
        </div>
        <br>
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class=" col-md-12">
                    <div class="h" style="margin-bottom:10px;">
                        <div class="row">
                            <form class="form-inline message-search-handler" method="get">
                                <div class="col">

                                    <div class="form-group col-md-1 cls_filter_inputbox mr-2 mt-2">
                                        <?php
                                        $test_case_status = request('test_case_status');
                                        ?>
                                        <select class="form-control" name="test_case_status" id="test_case_status">
                                            <option value="">Select Test Case Status</option>
                                            <?php
                                            foreach ($testCaseStatuses as $testCaseStatus) { ?>
                                            <option value="<?php echo $testCaseStatus->id; ?>" <?php if ($test_case_status == $testCaseStatus->id) echo "selected"; ?>><?php echo $testCaseStatus->name; ?></option>
                                            <?php }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-1 cls_filter_inputbox p-2 mr-2">
                                        <?php
                                        $module_id = request('module_id');
                                        ?>
                                        <select class="form-control" name="module_id" id="module_id">
                                            <option value="">Select Module</option>
                                            @foreach($filterCategories as  $filterCategory)
                                                <option value="{{$filterCategory}}">{{$filterCategory}} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input name="step_to_reproduce" type="text" class="form-control"
                                               placeholder="Search Reproduce" id="bug-search" data-allow-clear="true"/>
                                    </div>
                                    <div class="form-group cls_filter_inputbox">
                                        <input name="name" type="text" class="form-control" placeholder="Search Name"
                                               id="name" data-allow-clear="true"/>
                                    </div>
                                    <div class="form-group cls_filter_inputbox">
                                        <input name="expected_result" type="text" class="form-control"
                                               placeholder="Search Expected Result" id="expected_result"
                                               data-allow-clear="true"/>
                                    </div>
                                    <div class="form-group m-1">
                                        <input name="suite" type="text" class="form-control" placeholder="Search suite"
                                               id="suite" data-allow-clear="true"/>
                                    </div>
                                    <div class="form-group col-md-1 cls_filter_inputbox p-2 mr-2">
                                        <?php
                                        $website = request('website');
                                        ?>
                                        <select class="form-control" name="website" id="website">
                                            <option value="">Select Website</option>
                                            @foreach($filterWebsites as  $filterWebsite)

                                                <option value="{{$filterWebsite->id}}">{{$filterWebsite->title}} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-1 cls_filter_inputbox p-2 mr-2">
                                        <?php
                                        $assign_to_user = request('assign_to_user');
                                        ?>
                                        <select class="form-control selectpicker" name="assign_to_user[]" multiple id="assign_to_user">
                                            <option value="">Select Assign to</option>
                                            @foreach($users as  $user)

                                                <option value="{{$user->id}}">{{$user->name}} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-1 cls_filter_inputbox p-2 mr-2">
                                        <?php
                                        $created_by = request('created_by');
                                        ?>
                                        <select class="form-control" name="created_by" id="created_by">
                                            <option value="">Select Created By</option>
                                            @foreach($users as  $user)
                                                <option value="{{$user->id}}">{{$user->name}} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input name="date" type="date" class="form-control" placeholder="Search Date"
                                               id="bug-date" data-allow-clear="true"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="button">&nbsp;</label>
                                        <button type="submit" style="display: inline-block;width: 10%"
                                                class="btn btn-sm btn-image btn-search-action">
                                            <img src="/images/search.png" style="cursor: default;">
                                        </button>
                                        <a href="/test-cases" class="btn btn-image" id=""><img src="/images/resend2.png"
                                                                                               style="cursor: nwse-resize;"></a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col col-md-6">
                    <div class="row">

                        <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-add-action"
                                data-toggle="modal" data-target="#testCaseCreateModal">
                            <img src="/images/add.png" style="cursor: default;">
                        </button>
                        <div class="pull-left">

                            <button class="btn btn-secondary btn-xs btn-add-status" style="color:white;"
                                    data-toggle="modal" data-target="#newStatus"> Status
                            </button>&nbsp;&nbsp;
							@if(Auth::user()->hasRole('Admin'))
                            <button class="btn btn-secondary btn-xs btn-add-test-case-modal" style="color:white;"
                                    data-toggle="modal" data-target="#newTestCaseModal"> Add Test Cases
                            </button>&nbsp;&nbsp;
							
							<button class="btn btn-secondary btn-xs delete-tast-cases" style="color:white;"> Delete
                            </button>&nbsp;&nbsp;
                            @endif
                        </div>&nbsp;&nbsp;
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-success" id="alert-msg" style="display: none;">
                        <p></p>
                    </div>
                </div>
            </div>
            <div class="col-md-12 margin-tb" id="page-view-result">

            </div>
        </div>
    </div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
          50% 50% no-repeat;display:none;">
    </div>

    <div class="common-modal modal" role="dialog">
        <div class="modal-dialog" role="document">

        </div>
    </div>


    @include("test-cases.template.list-template")
    @include("test-cases.template.test-status")	@include("test-cases.template.test-cases")									  
    @include("test-cases.create")
    @include("test-cases.edit")

<style>
#newHistoryModal table th{
	border:1px solid #ddd;
}
</style>
    <div id="newHistoryModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xl">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Test Case History</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
				<div class="modal-body">
                <table class="table">
                    <tr style="background-color:#3333;">
                        <th>Name</th>
                        <th>Status</th>
                        <th>Suite</th>
                        <th>Expected Result</th>
                        <th>Assign to</th>
                        <th>Module/Feature</th>
                        <th>Updated By</th>
						<th></th>
                    </tr>
                    <tbody class="tbh">

                    </tbody>
                </table>
				</div>
            </div>
        </div>
    </div>
    <div id="newtestHistoryModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h3>User Test History</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <table class="table">
                    <tr>

                        <th>Date</th>
                        <th>New User</th>
                        <th>Old User</th>
                        <th>Updated By </th>
                    </tr>
                    <tbody class="tbhusertest">

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade newstatusHistory" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Test Status History</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <table class="table">
                    <tr>

                        <th>Date</th>
                        <th>New Status</th>
                        <th>Old Status</th>
                        <th>Updated By </th>
                    </tr>
                    <tbody class="tbhuserteststatus">

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="bugtrackingShowFullTextModel" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content ">
                <div id="add-mail-content">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">Full text view</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body bugtrackingmanShowFullTextBody">

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <script type="text/javascript" src="{{ asset('/js/jsrender.min.js')}}"></script>
        <script type="text/javascript" src="{{ asset('/js/jquery.validate.min.js')}}"></script>
        <script src="{{ asset('/js/jquery-ui.js')}}"></script>
        <script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
        <script type="text/javascript" src="{{ asset('/js/test-case.js') }}"></script>

        <script type="text/javascript">
            page.init({
                bodyView: $("#common-page-layout"),
                baseUrl: "<?php echo url("/"); ?>"
            });
        </script>
        <script type="text/javascript">
            $(document).on('click', '.expand-row-msg', function () {
                $('#bugtrackingShowFullTextModel').modal('toggle');
                $(".bugtrackingmanShowFullTextBody").html("");
                var id = $(this).data('id');
                var name = $(this).data('name');
                var full = '.expand-row-msg .show-full-' + name + '-' + id;
                var fullText = $(full).html();
                console.log(id, name, fullText, full)
                $(".bugtrackingmanShowFullTextBody").html(fullText);
            });
            $(document).on("click", ".btn-copy-url", function () {
                var url = $(this).data('id');
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val(url).select();
                document.execCommand("copy");
                $temp.remove();
                alert("Copied!");
            });
			
			$(document).on('click', '.chkTaskDeleteCommon', function() {
				if ($(this).is(':checked')) {
					$("input[name='chkTaskNameChange[]']").attr('checked', true);
				} else {
					$("input[name='chkTaskNameChange[]']").attr('checked', false);
				}
			});
			
			$(document).ready(function() {
				  $(".delete-tast-cases").click(function(){
					  var arr = [];
					  $.each($("input[name='chkTaskNameChange[]']:checked"), function(){
						  arr.push($(this).val());
					  });
					  
					  
					  $.ajax({
						url: '/test-cases/delete-multiple-test-cases',
						type: 'delete',
						headers: {
							'X-CSRF-TOKEN': "{{ csrf_token() }}"
						},
						data: {
							data: arr
						},
						beforeSend: function() {
							$("#loading-image").show();
						}
					}).done(function(response) {
						location.reload();
					}).fail(function(jqXHR, ajaxOptions, thrownError) {
						toastr["error"]("Oops,something went wrong");
						$("#loading-image").hide();
					});
			
					  
				  });
			  });
        </script>
@endsection