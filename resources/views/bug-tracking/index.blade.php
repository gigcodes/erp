@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
	<style type="text/css">
		.preview-category input.form-control {
			width: auto;
		}
	</style>

	<div class="row" id="common-page-layout">
		<div class="col-lg-12 margin-tb">
			<h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
		</div>
		<br>
		<div class="col-lg-12 margin-tb">
			<div class="row">
				<div class="col col-md-6">
					<div class="row">

						<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-add-action"
								data-toggle="modal" data-target="#colorCreateModal">
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
	@include("bug-tracking.templates.create-bug-tracking-template")
	@include("bug-tracking.templates.bug-environment")
	@include("bug-tracking.templates.bug-severity")
	@include("bug-tracking.templates.bug-status")
	@include("bug-tracking.templates.bug-type")



	<script type="text/javascript" src="/js/jsrender.min.js"></script>
	<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
	<script src="/js/jquery-ui.js"></script>
	<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/bug-tracker.js') }}"></script>

	<script type="text/javascript">
		page.init({
			bodyView: $("#common-page-layout"),
			baseUrl: "<?php echo url("/"); ?>"
		});
	</script>
@endsection