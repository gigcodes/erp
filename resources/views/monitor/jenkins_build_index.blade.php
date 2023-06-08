@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Jenkins Logs({{ $monitorJenkinsBuilds->total() }})</h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-12">
                    <form action="{{ route('monitor-jenkins-build.index') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-2 pd-sm">
                                <input type="text" name="keyword" placeholder="keyword" class="form-control h-100" value="{{ request()->get('keyword') }}">
                            </div>
                            <div class="col-md-2 pd-sm">
                                <label> Search Projects </label>
                                {{ Form::select("project_id[]", \app\Models\MonitorJenkinsBuild::pluck('project','id')->toArray(),request('project_id'),["class" => "form-control globalSelect2", "multiple", "data-placeholder" => "Select Website",]) }}
                            </div>
                            <div class="col-md-2 pd-sm">
                                <label>Search worker </label>
                                {{ Form::select("worker_id[]", \app\Models\MonitorJenkinsBuild::pluck('worker','id')->toArray(),request('worker_id'),["class" => "form-control globalSelect2", "multiple", "data-placeholder" => "Select Website"]) }}
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
                            <th width="20%">Project</th>
                            <th width="20%">Worker</th>
                            <th width="10%">StoreId</th>
                            <th width="15%">Clone Repository</th>
                            <th width="15%">LockBuild</th>
                            <th width="15%">Update Code</th>
                            <th width="15%">Composer Install</th>
                            <th width="15%">Make Config</th>
                            <th width="15%">Setup Upgrade</th>
                            <th width="15%">Compile Code</th>
                            <th width="15%">Static Content</th>
                            <th width="15%">Reindexes</th>
                            <th width="15%">Magento Cache Flush</th>
                            <th width="45%">Error</th>
                            <th width="15%">Build Status</th>
                            <th width="25%">Full Log</th>
                            <th width="15%">Meta Update</th>
                        </tr>
                        @foreach ($monitorJenkinsBuilds  as $key => $monitorJenkinsBuild)
                            <tr class="quick-website-task-{{ $monitorJenkinsBuild->id }}" data-id="{{ $monitorJenkinsBuild->id }}">
                                <td id="monitor_server_id">{{ $monitorJenkinsBuild->id }}</td>
                                <td>{{ $monitorJenkinsBuild->build_number }}</td>
                                <td>{{ $monitorJenkinsBuild->project }}</td>
                                <td>{{ $monitorJenkinsBuild->worker }}</td>
                                <td>{{ $monitorJenkinsBuild->store_id }}</td>
                                <td><span class="badge badge-pill" style ="background-color: {{ $monitorJenkinsBuild->clone_repository === 0 ? 'green' : 'orange' }}">{{ $monitorJenkinsBuild->clone_repository === 0 ? 'Success' : 'Failure' }}</span></td>
                                <td><span class="badge badge-pill" style ="background-color: {{$monitorJenkinsBuild->lock_build === 0 ? 'green' : 'orange' }}">{{ $monitorJenkinsBuild->lock_build  === 0 ? 'Success' : 'Failure' }}</span></td>
                                <td><span class="badge badge-pill" style ="background-color: {{ $monitorJenkinsBuild->update_code === 0 ? 'green' : 'orange' }}">{{ $monitorJenkinsBuild->update_code === 0 ? 'Success' : 'Failure' }}</span></td>
                                <td><span class="badge badge-pill" style ="background-color: {{ $monitorJenkinsBuild->composer_install === 0 ? 'green' : 'orange'}}">{{ $monitorJenkinsBuild->composer_install === 0 ? 'Success' : 'Failure'}}</span></td>
                                <td><span class="badge badge-pill" style ="background-color: {{ $monitorJenkinsBuild->make_config === 0 ? 'green' : 'orange'}}">{{ $monitorJenkinsBuild->make_config === 0 ? 'Success' : 'Failure'}}</span></td>
                                <td><span class="badge badge-pill" style ="background-color: {{ $monitorJenkinsBuild->setup_upgrade === 0 ? 'green' : 'orange'}}">{{ $monitorJenkinsBuild->setup_upgrade === 0 ? 'Success' : 'Failure'}}</span></td>
                                <td><span class="badge badge-pill" style ="background-color: {{ $monitorJenkinsBuild->compile_code === 0 ? 'green' : 'orange'}}">{{ $monitorJenkinsBuild->compile_code === 0 ? 'Success' : 'Failure'}}</span></td>
                                <td><span class="badge badge-pill" style ="background-color: {{ $monitorJenkinsBuild->static_content === 0 ? 'green' : 'orange'}}">{{ $monitorJenkinsBuild->static_content === 0 ? 'Success' : 'Failure'}}</span></td>
                                <td><span class="badge badge-pill" style ="background-color: {{ $monitorJenkinsBuild->reindexes === 0 ? 'green' : 'orange'}}">{{ $monitorJenkinsBuild->reindexes === 0 ? 'Success' : 'Failure'}}</span></td>
                                <td><span class="badge badge-pill" style ="background-color:{{ $monitorJenkinsBuild->magento_cache_flush === 0 ? 'green' : 'orange'}}">{{ $monitorJenkinsBuild->magento_cache_flush === 0 ? 'Success' : 'Failure'}}</span></td>
                                <td>{{ $monitorJenkinsBuild->error }}</td>
                                <td><span class="badge badge-pill" style = "background-color:{{ $monitorJenkinsBuild->build_status === 0 ? 'green' : 'orange' }}">{{ $monitorJenkinsBuild->build_status === 0 ? 'Success' : 'Failure' }}</span></td>
                                <td>{{ $monitorJenkinsBuild->full_log }}</td>
                                <td><span class="badge badge-pill" style = "background-color:{{ $monitorJenkinsBuild->meta_update === 0 ? 'green' : 'orange' }}">{{ $monitorJenkinsBuild->meta_update === 0 ? 'Success' : 'Failure' }}</span></td>
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