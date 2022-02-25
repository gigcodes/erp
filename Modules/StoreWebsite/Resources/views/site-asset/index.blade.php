@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Site Asset')

@section('styles')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
<style type="text/css">
	.preview-category input.form-control {
		width: auto;
	}

	#loading-image {
		position: fixed;
		top: 50%;
		left: 50%;
		margin: -50px 0px 0px -50px;
	}

	.dis-none {
		display: none;
	}

	.pd-5 {
		padding: 3px;
	}

	.toggle.btn {
		min-height: 25px;
	}

	.toggle-group .btn {
		padding: 2px 12px;
	}

	.latest-remarks-list-view tr td {
		padding: 3px !important;
	}
	#latest-remarks-modal .modal-dialog {
		 max-width: 1100px;
		width:100%;
	}
	.btn-secondary{
		border: 1px solid #ddd;
		color: #757575;
		background-color: #fff !important;
	}
	.modal {
		overflow-y:auto;
	}
	body.overflow-hidden{
		overflow: hidden;
	}

	span.user_point_none button, span.admin_point_none button{
		pointer-events: none;
		cursor: not-allowed;
	}table tr:last-child td {
		 border-bottom: 1px solid #ddd !important;
	 }
	 select.globalSelect2 + span.select2 {
    width: calc(100% - 26px) !important;
}

</style>
@endsection

@section('large_content')

<div id="myDiv">
	<img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
</div>

<div class="row" id="common-page-layout" style="overflow: hidden">
	<div class="col-md-12 margin-tb infinite-scroll">
		<div class="row">
			<div class="table-responsive">
			<table class="table table-bordered" id="documents-table">
				<thead>
					<tr>
						<th width="10%">Website</th>
						@forelse($site_development_categories as $sdc)
							<th>{{ $sdc->title }}</th>
						@empty
						@endforelse
					</tr>
				</thead>
				<tbody class="infinite-scroll-pending-inner">
					@include("storewebsite::site-asset.partials.data")
				</tbody>
			</table>
			</div>
		</div>
	</div>
</div>

@endsection


@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
<script type="text/javascript">


</script>

@endsection