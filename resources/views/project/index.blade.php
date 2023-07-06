@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Projects ({{ $projects->total() }})</h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-8">
                    {{-- <form action="{{ route('project.index') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-4 pd-sm">
                                <input type="text" name="keyword" placeholder="keyword" class="form-control h-100" value="{{ request()->get('keyword') }}">
                            </div>
                            <div class="col-lg-4">
                                <input class="form-control" type="date" name="event_start" value="{{ request()->get('event_start') }}">
                            </div>
                            <div class="col-md-4 pd-sm pl-0 mt-2">
                                 <button type="submit" class="btn btn-image search">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                                <a href="{{ route('project.index') }}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                            </div>
                        </div>
                    </form> --}}
                </div>
                <div class="col-4">
                    <div class="pull-right">
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
                                    {!! Form::open(['method' => 'DELETE','route' => ['project.destroy', $project->id],'style'=>'display:inline']) !!}
                                    <button type="submit" class="btn btn-xs">
                                        <i class="fa fa-trash" style="color: #808080;"></i>
                                    </button>
                                    {!! Form::close() !!}
                                    <button title="Build Process" data-id="{{ $project->id }}" type="button" class="btn open-build-process-template" style="padding:1px 0px;">
                                        <a href="javascript:void(0);" style="color:gray;"><i class="fa fa-simplybuilt"></i></a>
                                    </button>

                                    <button title="Build Process History" data-id="{{ $project->id }}" type="button" class="btn open-build-process-history" style="padding:1px 0px;">
                                        <a href="javascript:void(0);" style="color:gray;"><i class="fa fa-info-circle"></i></a>
                                    </button>
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
@include('project.partials.build-process-modal')

<script type="text/javascript">
    $('.select2').select2();
    $(document).ready(function(){
        $(document).on("click",".open-build-process-template",function(e) {
            e.preventDefault();
            var id=$(this).attr("data-id");
            $(".build_process_project_id").val(id);
            $('#build-process-modal').modal('show'); 
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
                }
            }).done(function(response) {
                jQuery('#build_branch_name').html(response.data);
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
    })
</script>
@endsection