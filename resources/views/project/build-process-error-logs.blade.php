@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Build process error logs</h2>
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
                    <table class="table table-bordered" style="table-layout: fixed;" id="build-process-error-logs-list">
                        <tr>
                            <th width="2%">ID</th>
                            <th width="10%">Project</th>
                            <th width="10%">Organization</th>
                            <th width="10%">Repo</th>
                            <th width="10%">Branch</th>
                            <th width="10%">Error Message</th>
                            <th width="10%">Error Code</th>
                        </tr>
                        @foreach ($buildProcessErrorLogs as $key => $buildProcessErrorLog)
                            <tr data-id="{{ $buildProcessErrorLog->id }}">
                                <td>{{ $buildProcessErrorLog->id }}</td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $buildProcessErrorLog->project->name ?? '' }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $buildProcessErrorLog->organization->name ?? '' }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $buildProcessErrorLog->repository->name ?? '' }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $buildProcessErrorLog->github_branch_state_name }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($buildProcessErrorLog->error_message) > 30 ? substr($buildProcessErrorLog->error_message, 0, 30).'...' :  $buildProcessErrorLog->error_message }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $buildProcessErrorLog->error_message }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $buildProcessErrorLog->error_code }}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                {!! $buildProcessErrorLogs->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
$(document).on('click', '.expand-row', function () {
    var selection = window.getSelection();
    if (selection.toString().length === 0) {
        $(this).find('.td-mini-container').toggleClass('hidden');
        $(this).find('.td-full-container').toggleClass('hidden');
    }
});

$( document ).ready(function() {

});
</script>
@endsection