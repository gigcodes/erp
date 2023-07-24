@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection


@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">deployement Versions ({{$deploymentVersions->total()}})</h2>
		</div>
	</div>
	<div class="mt-3 col-md-12">
		<form action="{{route('deployement-version.index')}}" method="get" class="search">
			<div class="col-lg-2">
				<br>
				<input class="form-control" type="text" id="search_versionNumber" placeholder="Search Version number" name="search_versionNumber" value="{{ $search_versionNumber ?? '' }}">
			</div>
			<div class="col-lg-2">
				<br>
				<input class="form-control" type="text" id="search_jobName" placeholder="Search Job Name" name="search_jobName" value="{{ $search_jobName ?? '' }}">
			</div>
			<div class="col-lg-2">
				<br>
				<input class="form-control" type="text" id="search_branchName" placeholder="Search Branch Name" name="search_branchName" value="{{ $search_branchName ?? '' }}">
			</div>
			<div class="col-lg-2">
				<b>Deployment Date:</b> 
				<input class="form-control" type="date" name="deployment_date" value="{{$deployment_date ?? ''}}">
			</div>
			<div class="col-lg-2">
				<b>PR Date:</b> 
				<input class="form-control" type="date" name="pr_date" value="{{$pr_date ?? ''}}">
			</div>

			<div class="col-lg-2">
				<br>
				<button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
				   <img src="{{ asset('images/search.png') }}" alt="Search">
			   </button>
			   <a href="{{route('deployement-version.index')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
			</div>
		</form>
	</div>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
			    <tr>
			    	<th width="3%">ID</th>
			    	<th width="10%">Version Number</th>
			        <th width="10%">Build Number</th>
			        <th width="10%">Job Name</th>
			        <th width="10%">Revision</th>
			        <th width="10%">Branch Name</th>
                    <th width="10%">Pull No</th>
                    <th width="10%">Deployment Date</th>
                    <th width="10%">PR Date</th>
					<th width="10%">Deploy</th>

                </tr>
		    	<tbody>
                    @foreach ($deploymentVersions as $deploymentVersion)
                        <tr>
                            <td>{{$deploymentVersion->id}}</td>				
                            <td class="expand-row" style="word-break: break-all">
                                <span class="td-mini-container">
                                   {{ strlen($deploymentVersion->version_number) > 30 ? substr($deploymentVersion->version_number, 0, 30).'...' :  $deploymentVersion->version_number }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ $deploymentVersion->version_number }}
                                </span>
                            </td>
							<td class="expand-row" style="word-break: break-all">
                                <span class="td-mini-container">
                                   {{ strlen($deploymentVersion->build_number) > 30 ? substr($deploymentVersion->build_number, 0, 30).'...' :  $deploymentVersion->build_number }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ $deploymentVersion->build_number }}
                                </span>
                            </td>
							<td class="expand-row" style="word-break: break-all">
                                <span class="td-mini-container">
                                   {{ strlen($deploymentVersion->job_name) > 30 ? substr($deploymentVersion->job_name, 0, 30).'...' :  $deploymentVersion->job_name }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ $deploymentVersion->job_name }}
                                </span>
                            </td>
							<td class="expand-row" style="word-break: break-all">
                                <span class="td-mini-container">
                                   {{ strlen($deploymentVersion->revision) > 30 ? substr($deploymentVersion->revision, 0, 30).'...' :  $deploymentVersion->revision }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ $deploymentVersion->revision }}
                                </span>
							</td>
							<td class="expand-row" style="word-break: break-all">
                                <span class="td-mini-container">
                                   {{ strlen($deploymentVersion->branch_name) > 30 ? substr($deploymentVersion->branch_name, 0, 30).'...' :  $deploymentVersion->branch_name }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ $deploymentVersion->branch_name }}
                                </span>
							</td>
							<td class="expand-row" style="word-break: break-all">
                                <span class="td-mini-container">
                                   {{ strlen($deploymentVersion->pull_no) > 30 ? substr($deploymentVersion->pull_no, 0, 30).'...' :  $deploymentVersion->pull_no }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ $deploymentVersion->pull_no }}
                                </span>
							</td>
                            <td>{{$deploymentVersion->deployment_date}}</td>
                            <td>{{$deploymentVersion->pr_date}}</td>
							<td><button type="button" title="Deploy" data-id="{{$deploymentVersion->id}}" class="btn btn-xs btn-deploy-verison" data-toggle="modal" data-target="#create-server-modal" style="padding: 0px 5px !important";>
								<i class="fa fa-upload" aria-hidden="true"></i>
							</button></td>
						</tr>                        
                    @endforeach
		    	</tbody>
		    </thead>-
		</table>
		{!! $deploymentVersions->appends(Request::except('page'))->links() !!}
	</div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
    50% 50% no-repeat;display:none;">
</div>

<div id="create-server-modal" class="modal fade in" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <form action="">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Servers:</label><br>
                        <input type="radio" name="options" value="qa">Qa<br>
                        <input type="radio" name="options" value="production"> Production<br>
                        <input type="radio" name="options" value="live"> Live<br>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script type="text/javascript">

    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });

	$(document).ready(function () {
		$(document).on('click', '.btn-deploy-verison', function () {
			var deployVerId = $(this).data('id');
			$('input[type="radio"][name="options"]').on('click', function() {
				var selectedValue = $(this).val();
				$.ajax({
					url: "{{ route('deployement-version-jenkis') }}",
					type: 'GET',
					headers: {
						'X-CSRF-TOKEN': "{{ csrf_token() }}"
					},
					data: {
						deployVerId: deployVerId,
						selectedValue : selectedValue,
					},
					dataType: "json",
					beforeSend: function () {
						$("#loading-image").show();
					}
					}).done(function (response) {
						toastr['success'](response.message, 'success');
					$("#loading-image").hide();
					}).fail(function (response, ajaxOptions, thrownError) {
					toastr["error"](response.message);
					$("#loading-image").hide();
				});
			});
		});
});


</script>