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

@include('project.partials.project-create-modal')

<script type="text/javascript">
    $('.select2').select2();
    $(document).ready(function(){
        
    })
</script>
@endsection