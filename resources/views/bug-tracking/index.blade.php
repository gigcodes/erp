@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
    <style type="text/css">
        .preview-category input.form-control {
            width: auto;
        }
        .break{
            word-break: break-all !important;
        }
    </style>


<style>
th {border: 1px solid black;}
table{border-collapse: collapse;}
.ui-icon, .ui-widget-content .ui-icon {background-image: none;}
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

									<div class="form-group col-md-1 cls_filter_inputbox p-2 mr-3" style="width: 200px;">
										<?php
										$bug_type = request('bugtype');
										?>
										<select class="form-control selectpicker" name="bug_type[]" multiple id="bug_type" title="Select BugType">
											<option value="">Select BugType</option>
											<?php
											foreach ($bugTypes as $bugtype) { ?>
											<option value="<?php echo $bugtype->id; ?>" <?php if ($bug_type == $bugtype->id) echo "selected"; ?>><?php echo $bugtype->name; ?></option>
											<?php }
											?>
										</select>
									</div>
									<div class="form-group col-md-1 cls_filter_inputbox p-2 mr-2" style="width: 200px;">
										<?php
										$bug_environment = request('bug_enviornment');
										?>
										<select class="form-control selectpicker" name="bug_enviornment[]" multiple id="bug_enviornment" title="Select BugEnvironment">
											<option value="">Select BugEnvironment</option>
											<?php
											foreach ($bugEnvironments as $bugenvironment) { ?>
											<option value="<?php echo $bugenvironment->id; ?>" <?php if ($bug_environment == $bugenvironment->id) echo "selected"; ?>><?php echo $bugenvironment->name; ?></option>
											<?php }
											?>
										</select>
									</div>
									<div class="form-group col-md-1 cls_filter_inputbox p-2 mr-2" style="width: 200px;">
										<?php
										$bug_severity = request('bug_severity');
										?>
										<select class="form-control selectpicker" name="bug_severity[]" multiple id="bug_severity" title="Select BugSeverity">
											<option value="">Select BugSeverity</option>
											<?php
											foreach ($bugSeveritys as $bugseverity) { ?>
											<option value="<?php echo $bugseverity->id; ?>" <?php if ($bug_severity == $bugseverity->id) echo "selected"; ?>><?php echo $bugseverity->name; ?></option>
											<?php }
											?>
										</select>
									</div>
									<div class="form-group col-md-1 cls_filter_inputbox p-2 mr-2" style="width: 200px;">
										<?php
										$bug_status = request('bugstatus');
										?>
										<select class="form-control selectpicker" name="bug_status[]" multiple id="bug_status" title="Select BugStatus">
											<option value="">Select BugStatus</option>
											<?php
											foreach ($bugStatuses as $bugstatus) { ?>
											<option value="<?php echo $bugstatus->id; ?>" <?php if ($bug_status == $bugstatus->id) echo "selected"; ?>><?php echo $bugstatus->name; ?></option>
											<?php }
											?>
										</select>
									</div>
									<div class="form-group  cls_filter_inputbox p-2 mr-3" style="width: 200px;">
										<?php
										$module_id = request('module_id');
										?>
										<select class="form-control selectpicker" name="module_id[]" multiple id="module_id" title="Select Module">
											<option value="">Select Module</option>
											@foreach($filterCategories as  $filterCategory)
												<option value="{{$filterCategory}}">{{$filterCategory}} </option>
											@endforeach
										</select>
									</div>
									<div class="form-group" style="width: 200px;">
										<input name="step_to_reproduce" type="text" class="form-control" placeholder="Search Reproduce" id="bug-search" data-allow-clear="true" />
									</div>
									<div class="form-group m-3" style="width: 200px;">
										<input name="summary" type="text" class="form-control" placeholder="Search Summary" id="bug-summary" data-allow-clear="true" />
									</div>
									<div class="form-group m-1" style="width: 200px;">
										<input name="url" type="text" class="form-control" placeholder="Search Url" id="bug-url" data-allow-clear="true" />
									</div>
									<div class="form-group cls_filter_inputbox p-2 mr-2" style="width: 200px;">
										<?php
										$website = request('website');
										?>
										<select class="form-control selectpicker" name="website[]" multiple id="website" title="Select Website">
											<option value="">Select Website</option>
											@foreach($filterWebsites as  $filterWebsite)

												<option value="{{$filterWebsite->id}}">{{$filterWebsite->title}} </option>
											@endforeach
										</select>
									</div>

									<div class="form-group col-md-1 cls_filter_inputbox p-2 mr-2" style="width: 200px;">
										<?php
										$assign_to_user = request('assign_to_user');
										?>
										<select class="form-control selectpicker" name="assign_to_user[]" multiple id="assign_to_user" title="Select Assign to">
											<option value="">Select Assign to</option>
											@foreach($users as  $user)

												<option value="{{$user->id}}">{{$user->name}} </option>
											@endforeach
										</select>
									</div>


									<div class="form-group col-md-1 cls_filter_inputbox p-2 mr-2" style="width: 200px;">
										<?php
										$created_by = request('created_by');
										?>
										<select class="form-control selectpicker" name="created_by[]" multiple id="created_by" title="Select Created by">
											<option value="">Select Created by</option>
											@foreach($users as  $user)

												<option value="{{$user->id}}">{{$user->name}} </option>
											@endforeach
										</select>
									</div>
									<div class="form-group" style="width: 200px;"	>
										<input name="date" type="date" class="form-control" placeholder="Search Date" id="bug-date" data-allow-clear="true" />
									</div>
									<div class="form-group" >
										<label for="button">&nbsp;</label>
										<button type="submit" style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-search-action">
											<img src="/images/search.png" style="cursor: default;">
										</button>
										<a href="/bug-tracking" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="col col-md-6">
					<div class="row">

						<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-add-action"
								data-toggle="modal" data-target="#bugtrackingCreateModal">
							<img src="/images/add.png" style="cursor: default;">
						</button>
						<div class="pull-left">
							<button class="btn btn-secondary btn-xs btn-add-environment" style="color:white;"
									data-toggle="modal" data-target="#newEnvironment"> Environment
							</button>&nbsp;&nbsp;
							<button class="btn btn-secondary btn-xs btn-add-type" style="color:white;"
									data-toggle="modal" data-target="#newType"> Type
							</button>&nbsp;&nbsp;
							<button class="btn btn-secondary btn-xs btn-add-status" style="color:white;"
									data-toggle="modal" data-target="#newStatus"> Status
							</button>&nbsp;&nbsp;
							<button class="btn btn-secondary btn-xs btn-add-severity" style="color:white;"
									data-toggle="modal" data-target="#newSeverity"> Severity
							</button>
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


	@include("bug-tracking.templates.list-template")
    @include("bug-tracking.create")
    @include("bug-tracking.edit")
	@include("bug-tracking.templates.create-bug-tracking-template")
	@include("bug-tracking.templates.bug-environment")
	@include("bug-tracking.templates.bug-severity")
	@include("bug-tracking.templates.bug-status")
	@include("bug-tracking.templates.bug-type")

	<div id="newHistoryModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h3>Bug Tracker History</h3>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<table class="table">
					<tr>

						<th>Date</th>
						<th>Type of Bug</th>
						<th>Summary</th>
						<th>Environment</th>
						<th>Assign to</th>
						<th>Status</th>
						<th>Severity</th>
						<th>Module/Feature</th>
						<th>Updated By </th>
					</tr>
					<tbody class="tbh">

					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div id="newuserHistoryModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h3>User History</h3>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<table class="table">
					<tr>

						<th>Date</th>
						<th>New User</th>
						<th>Old User</th>
						<th>Updated By </th>
					</tr>
					<tbody class="tbhuser">

					</tbody>
				</table>
			</div>
		</div>
	</div>
<div id="newstatusHistoryModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h3>Status History</h3>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<table class="table">
					<tr>

						<th>Date</th>
						<th>New Status</th>
						<th>Old Status</th>
						<th>Updated By </th>
					</tr>
					<tbody class="tbhstatus">

					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div id="newCommunictionModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h3>Communication</h3>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
				<table class="table">
					<thead>
					<th></th>
					<th></th>
					</thead>
					<tbody class="tbhc">

					</tbody>
				</table>
				</div>
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
	<script type="text/javascript" src="{{ asset('/js/bug-tracker.js') }}"></script>

	<script type="text/javascript">
		page.init({
			bodyView: $("#common-page-layout"),
			baseUrl: "<?php echo url("/"); ?>"
		});
        $(document).ready(function () {
            {{--$(".btn-edit-template").click(function (event) {--}}
            {{--    var id = $(this).data('id');--}}
            {{--    $.ajax({--}}
            {{--        url: "/bug-tracking/edit/"+id,--}}
            {{--        type: "GET",--}}
            {{--        data: {--}}
            {{--            id: id,--}}
            {{--            _token: '{{ csrf_token() }}'--}}
            {{--        },--}}
            {{--        cache: false,--}}
            {{--        dataType: 'json',--}}
            {{--        success: function (data) {--}}
            {{--            --}}

            {{--        }--}}
            {{--    });--}}
            {{--});--}}
      })
	</script>
	<script type="text/javascript">
		$(document).on('click', '.expand-row-msg', function() {
			$('#bugtrackingShowFullTextModel').modal('toggle');
			$(".bugtrackingmanShowFullTextBody").html("");
			var id = $(this).data('id');
			var name = $(this).data('name');
			var full = '.expand-row-msg .show-full-' + name + '-' + id;
			var fullText = $(full).html();
			console.log(id,name,fullText,full)
			$(".bugtrackingmanShowFullTextBody").html(fullText.replaceAll("\n", "<br>"));
		});
		$(document).on("click",".btn-copy-url",function() {
			var url = $(this).data('id');
			var $temp = $("<input>");
			$("body").append($temp);
			$temp.val(url).select();
			document.execCommand("copy");
			$temp.remove();
			alert("Copied!");
		});


		$(window).on('load', function() {
			$( "th" ).resizable();
		});
	</script>
@endsection