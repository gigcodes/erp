@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Build process logs</h2>
        {{-- <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-8">
                    <form action="{{ route('project.index') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-3 pd-sm">
                                <input type="text" name="keyword" placeholder="keyword" class="form-control h-100" value="{{ request()->get('keyword') }}">
                            </div>
                            <div class="col-md-3">
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
                <div class="col-4">
                    <div class="pull-right">
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#serverenv-create"> Create Serverenv </button>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#project-create"> Create Project </button>
                    </div>
                </div>
            </div>
        </div> --}}
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
                    <table class="table table-bordered" style="table-layout: fixed;" id="build-process-logs-list">
                        <tr>
                            <th width="2%">ID</th>
                            <th width="10%">Organization</th>
                            <th width="10%">Repo</th>
                            <th width="10%">Branch</th>
                            <th width="10%">Build By</th>
                            <th width="10%">Build Number</th>
                            <th width="10%">Build Name</th>
                            <th width="10%">Text</th>
                            <th width="5%">Status</th>
                            <th width="5%">Date</th>
                            <th width="5%">Job Status</th>
                        </tr>
                        @foreach ($responseLogs as $key => $responseLog)
                            <tr data-id="{{ $responseLog->id }}">
                                <td>{{ $responseLog->id }}</td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $responseLog->organization?->name }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $responseLog->repository?->name }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $responseLog->github_branch_state_name }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($responseLog->usersname) > 30 ? substr($responseLog->usersname, 0, 30).'...' :  $responseLog->usersname }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $responseLog->usersname }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $responseLog->build_number }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $responseLog->build_name }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($responseLog->text) > 30 ? substr($responseLog->text, 0, 30).'...' :  $responseLog->text }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $responseLog->text }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $responseLog->status }}
                                    <button type="button" class="btn btn-xs show-status-modal" title="Show Status History" data-id="{{$responseLog->id}}"><i class="fa fa-info-circle"></i></button>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $responseLog->created_at }}
                                </td>
                                <td>
                                    @if(count($responseLog->job_status) > 0)
                                        @foreach ($responseLog->job_status as $key=>$value )
                                            <strong>{{"Job: "}}</strong>{{ $key."(".$value.") "}}
                                        @endforeach
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                {!! $responseLogs->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>
<div id="status-history-listing" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Status History</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">

                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="30%">Old Status</th>
                                <th width="30%">New Status</th>
                                <th width="20%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="status-history-listing-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
$( document ).ready(function() {
    $(document).on('click', '.show-status-modal', function() {
            var id = $(this).attr('data-id');
            $("#loading-image-preview").show();
            $.ajax({
                method: "GET",
                url: "{{ route('project.buildProcessStatusLogs')}}",
                dataType: "json",
                data: {
                    id:id,
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${v.id} </td>
                                        <td> <div class="expand-row module-text" style="width: 100%;"><div class="flex  items-center justify-left td-mini-container" title="${v.old_status}">${setStringLength(v.old_status, 50)}</div><div class="flex items-center justify-left td-full-container hidden" title="${v.old_status}">${v.old_status}</div></div> </td>
                                        <td> <div class="expand-row module-text" style="width: 100%;"><div class="flex  items-center justify-left td-mini-container" title="${v.status}">${setStringLength(v.status, 50)}</div><div class="flex items-center justify-left td-full-container hidden" title="${v.status}">${v.status}</div></div> </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#status-history-listing").find(".status-history-listing-view").html(html);
                        $("#status-history-listing").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                    $("#loading-image-preview").hide();
                }
            });
        });
});
</script>
@endsection