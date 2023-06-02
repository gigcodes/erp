@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">jenkins Logs({{ $monitorJenkinsBuilds->total() }})</h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-12">
                    <form action="{{ route('monitor-jenkins-build.index') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-2 pd-sm">
                                <input type="text" name="keyword" placeholder="keyword" class="form-control h-100" value="{{ request()->get('keyword') }}">
                            </div>
                            <div class="col-md-2 pd-sm pl-0 mt-2">
                                 <button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                            </div>
                        </div>
                    </form>
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

<div class="tab-content ">
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout: fixed;" id="quick-reply-list">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="10%">Build Number</th>
                            <th width="10%">Project</th>
                            <th width="10%">Worker</th>
                            <th width="10%">StoreId</th>
                            <th width="10%">Clone Repository</th>
                            <th width="10%">LockBuild</th>
                            <th width="10%">Update Code</th>
                            <th width="10%">Composer Install</th>
                            <th width="10%">Make Config</th>
                            <th width="10%">Setup Upgrade</th>
                            <th width="10%">Compile Code</th>
                            <th width="10%">Static Content</th>
                            <th width="10%">Reindexes</th>
                            <th width="10%">Magento Cache Flush</th>
                            <th width="10%">Error</th>
                            <th width="10%">Build Status</th>
                            <th width="10%">Full Log</th>
                        </tr>
                        @foreach ($monitorJenkinsBuilds  as $key => $monitorJenkinsBuild)
                            <tr class="quick-website-task-{{ $monitorJenkinsBuild->id }}" data-id="{{ $monitorJenkinsBuild->id }}">
                                <td id="monitor_server_id">{{ $monitorJenkinsBuild->id }}</td>
                                <td>{{ $monitorJenkinsBuild->build_number }}</td>
                                <td>{{ $monitorJenkinsBuild->project }}</td>
                                <td>{{ $monitorJenkinsBuild->worker }}</td>
                                <td>{{ $monitorJenkinsBuild->store_id }}</td>
                                <td><span class="badge badge-pill badge-success">{{ $monitorJenkinsBuild->clone_repository === 0 ? 'Success' : 'Failure'}}</span></td>
                                <td><span class="badge badge-pill badge-success">{{ $monitorJenkinsBuild->lock_build === 0 ? 'Success' : 'Failure' }}</td>
                                <td><span class="badge badge-pill badge-success">{{ $monitorJenkinsBuild->update_code === 0 ? 'Success' : 'Failure' }}</span></td>
                                <td><span class="badge badge-pill badge-success">{{ $monitorJenkinsBuild->composer_install === 0 ? 'Success' : 'Failure'}}</span></td>
                                <td><span class="badge badge-pill badge-success">{{ $monitorJenkinsBuild->make_config === 0 ? 'Success' : 'Failure'}}</span></td>
                                <td><span class="badge badge-pill badge-success">{{ $monitorJenkinsBuild->setup_upgrade === 0 ? 'Success' : 'Failure'}}</span></td>
                                <td><span class="badge badge-pill badge-success">{{ $monitorJenkinsBuild->compile_code === 0 ? 'Success' : 'Failure'}}</span></td>
                                <td><span class="badge badge-pill badge-success">{{ $monitorJenkinsBuild->static_content === 0 ? 'Success' : 'Failure'}}</span></td>
                                <td><span class="badge badge-pill badge-success">{{ $monitorJenkinsBuild->reindexes === 0 ? 'Success' : 'Failure'}}</span></td>
                                <td><span class="badge badge-pill badge-success">{{ $monitorJenkinsBuild->magento_cache_flush === 0 ? 'Success' : 'Failure'}}</td>
                                <td>{{ $monitorJenkinsBuild->error }}</td>
                                <td><span class="badge badge-pill badge-success">{{ $monitorJenkinsBuild->build_status === 0 ? 'Success' : 'Failure' }}</span></td>
                                <td>{{ $monitorJenkinsBuild->full_log }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                {!! $monitorJenkinsBuilds->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection