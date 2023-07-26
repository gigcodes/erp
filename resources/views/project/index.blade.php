@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Projects ({{ $projects->total() }})</h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-6">
                    <form action="{{ route('project.index') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-4 pd-sm">
                                <input type="text" name="keyword" placeholder="keyword" class="form-control h-100" value="{{ request()->get('keyword') }}">
                            </div>
                            <div class="col-md-4">
                                <?php 
									if(request('store_websites_search')){   $store_websites_search = request('store_websites_search'); }
									else{ $store_websites_search = []; }
								?>
								<select name="store_websites_search[]" id="store_websites_search" class="form-control select2" multiple>
									<option value="" @if($store_websites_search=='') selected @endif>-- Select a Store website --</option>
									@forelse($store_websites as $swId => $swName)
									<option value="{{ $swId }}" @if(in_array($swId, $store_websites_search)) selected @endif>{!! $swName !!}</option>
									@empty
									@endforelse
								</select>
                            </div>
                            
                            <div class="col-md-4 pd-sm pl-0 mt-2">
                                 <button type="submit" class="btn btn-image search">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                                <a href="{{ route('project.index') }}" class="btn btn-image" id="">
                                    <img src="/images/resend2.png" style="cursor: nwse-resize;">
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-6">
                    <div class="pull-right">
                        <a href="{{ route('project.buildProcessErrorLogs') }}" class="btn btn-secondary"> Build Process Error Logs </a>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#serverenv-create"> Create Serverenv </button>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#projecttype-create"> Create Project Type </button>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#project-create"> Create Project </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

<div class="tab-content">
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout: fixed;" id="project-list">
                        <tr>
                            <th width="2%">ID</th>
                            <th width="10%">Project Name</th>
                            <th width="10%">Project Type</th>
                            <th width="10%">Job Name</th>
                            <th width="10%">Serverenv</th>
                            <th width="10%">Store Website Names</th>
                            <th width="5%">Action</th>
                        </tr>
                        @foreach ($projects as $key => $project)
                            <tr data-id="{{ $project->id }}">
                                <td>{{ $project->id }}</td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($project->name) > 30 ? substr($project->name, 0, 30).'...' :  $project->name }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $project->name }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $project->project_type }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($project->job_name) > 30 ? substr($project->job_name, 0, 30).'...' :  $project->job_name }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $project->job_name }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $project->serverenv }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($project->store_website_names) > 30 ? substr($project->store_website_names, 0, 30).'...' :  $project->store_website_names }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $project->store_website_names }}
                                    </span>
                                </td>
                                <td>
                                    <button type="button" data-id="{{ $project->id }}" class="btn btn-xs btn-edit-project">
                                        <i class="fa fa-pencil"></i>
                                    </button>

                                    {!! Form::open(['method' => 'DELETE','route' => ['project.destroy', $project->id],'style'=>'display:inline']) !!}
                                    <button type="submit" class="btn btn-xs">
                                        <i class="fa fa-trash" style="color: #808080;"></i>
                                    </button>
                                    {!! Form::close() !!}
                                    <button title="Build Process" data-job="{{ $project->job_name }}" data-id="{{ $project->id }}" type="button" class="btn open-build-process-template" style="padding:1px 0px;">
                                        <a href="javascript:void(0);" style="color:gray;"><i class="fa fa-simplybuilt"></i></a>
                                    </button>

                                    {{-- <button title="Build Process History" data-id="{{ $project->id }}" type="button" class="btn open-build-process-history" style="padding:1px 0px;">
                                        <a href="javascript:void(0);" style="color:gray;"><i class="fa fa-info-circle"></i></a>
                                    </button> --}}
                                    <button title="Build Process History" data-id="{{ $project->id }}" type="button" class="btn" style="padding:1px 0px;">
                                        <a href="{{route("project.buildProcessLogs", $project->id)}}" style="color:gray;"><i class="fa fa-info-circle"></i></a>
                                    </button>

                                    {{-- Directly initiate the build for (org: LUDXB & Repository:brands-labels & Branch: stage ) --}}
                                    {!! Form::open(['method' => 'POST','route' => ['project.buildProcess'],'style'=>'display:inline', 'id' => "initiate-build-directly"]) !!}
                                    {!! Form::hidden('project_id', $project->id, ['class' => 'form-control']) !!}
                                    {!! Form::hidden('job_name', $project->job_name, ['class' => 'form-control']) !!}
                                    {!! Form::hidden('organization', 2, ['class' => 'form-control']) !!}
                                    {!! Form::hidden('repository', 353671452, ['class' => 'form-control']) !!}
                                    {!! Form::hidden('branch_name', 'stage', ['class' => 'form-control']) !!}
                                    {!! Form::hidden('initiate_from', 'Project page - stage branch build', ['class' => 'form-control']) !!}
                                    <button title="Please initiate stage branch build" type="submit" class="btn btn-xs">
                                        <i class="fa fa-gear" style="color: #808080;"></i>
                                    </button>
                                    {!! Form::close() !!}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                {!! $projects->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>
<div id="build-process-history" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg" style="max-width: 95%;width: 100%;">
		<!-- Modal content-->
		<div class="modal-content ">
			<div class="modal-header">
				<h4 class="modal-title">Build Process History</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="table-responsive mt-3">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>ID</th>
								<th>Build By</th>
								<th>Build Number</th>
								<th>Build Name</th>
								<th>Text</th>
								<th>Status</th>
								<th>Date</th>
							</tr>
						</thead>
						<tbody id="build-process-history_tbody">

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@include('project.partials.project-create-modal')
@include('project.partials.project-edit-modal')
@include('project.partials.serverenv-create-modal')
@include('project.partials.projecttype-create-modal')
@include('project.partials.build-process-modal')

<script type="text/javascript">
    $('.select2').select2();
    $(document).ready(function(){
        $(document).on("click",".open-build-process-template",function(e) {
            e.preventDefault();
            var id=$(this).attr("data-id");
            var job=$(this).attr("data-job");
            $(".build_process_project_id").val(id);
            $("#build-process #job_name").val(job);
            $('#build-process-modal').modal('show'); 
        });

        $("#build_organization").on('change', function(e) {
            var url = "{{ route('project.getGithubRepo') }}";
            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: "GET",
                url: url,
                data: {
                    build_organization: jQuery('#build_organization').val(),
                },
                beforeSend: function() {
                    $("#loading-image-preview").show();
                }
                
            }).done(function(response) {
                jQuery('#build_repository').html(response.data);
                jQuery('#build_branch_name').html("");
                $("#loading-image-preview").hide();
            }).fail(function(response) {});
        });

        $("#build_repository").on('change', function(e) {
            var url = "{{ route('project.getGithubBranches') }}";
            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: "GET",
                url: url,
                data: {
                    build_repository: jQuery('#build_repository').val(),
                },
                beforeSend: function() {
                    $("#loading-image-preview").show();
                }
            }).done(function(response) {
                jQuery('#build_branch_name').html(response.data);
                $("#loading-image-preview").hide();
            }).fail(function(response) {});
        
        });
        $(document).on("click", ".open-build-process-history", function(href) {
            $.ajax({
                type: 'POST',
                url: 'project/buildProcessLogs/'+ $(this).data('id'),
                beforeSend: function () {
                    $("#loading-image-preview").show();
                },
                data: {
                    _token: "{{ csrf_token() }}",
                    id: $(this).data('id'),
                },
                dataType: "json"
            }).done(function (response) {
                $("#loading-image-preview").hide();
                if (response.code == 200) {
                    
                    $('#build-process-history_tbody').html(response.data);
                    $('#build-process-history').modal('show');
                    toastr['success'](response.message, 'success');
                }
            }).fail(function (response) {
                $("#loading-image-preview").hide();
                console.log("Sorry, something went wrong");
            });
        });

        $(".btn-edit-project").on('click', function(e) {
            var url = "{{ route('project.edit', '') }}/" + $(this).data("id");
            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: "GET",
                url: url,
            }).done(function(response) {
                $("#project-edit-form #id").val(response.data.id);
                $("#project-edit-form #name").val(response.data.name);
                $("#project-edit-form #job_name").val(response.data.job_name);
                $("#project-edit-form #serverenv").val(response.data.serverenv).trigger('change');
                $("#project-edit-form #project_type").val(response.data.project_type).trigger('change');
                var selectedWebsites = [];
                $(response.data.store_websites).each(function(index, store_websites) {
                    selectedWebsites.push(store_websites.id);
                });
                $("#project-edit-form #assign-new-website").val(selectedWebsites).trigger('change');
                $("#project-edit").modal("show");
            }).fail(function(response) {});
        });

        $(document).on('submit', 'form#initiate-build-directly', function(e){
            e.preventDefault();
            var self = $(this);
            let formData = new FormData(document.getElementById("initiate-build-directly"));
            var button = $(this).find('[type="submit"]');
            $.ajax({
                url: '{{ route("project.buildProcess") }}',
                type: "POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                beforeSend: function() {
                    $("#loading-image-preview").show();
                },
                complete: function() {
                    $("#loading-image-preview").hide();
                },
                success: function(response) {
                    if(response.code=='200'){
                        toastr["success"](response.message);
                        // $('#build-process-modal').modal('hide');
                    }else{
                        toastr["error"](response.message);
                    }
                    $("#loading-image-preview").hide();
                },
                error: function(xhr, status, error) { // if error occured
                    $("#loading-image-preview").hide();
                },
            });
        });
    })

    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });
   
</script>
@endsection