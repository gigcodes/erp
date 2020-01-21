@extends('layouts.app')

@section('content')

<script>
    @if(Session::has('message'))
    var type = "{{ Session::get('alert-type', 'info') }}";
    switch (type) {
        case 'info':
            toastr.info("{{ Session::get('message') }}");
            break;

        case 'warning':
            toastr.warning("{{ Session::get('message') }}");
            break;

        case 'success':
            toastr.success("{{ Session::get('message') }}");
            break;

        case 'error':
            toastr.error("{{ Session::get('message') }}");
            break;
    }
    @endif
</script>

<h2 class="text-center">{{ $repository->name }} branches</h2>
<div class="container">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Behind By</th>
                <th>Ahead By</th>
                <th>Last Commit by</th>
                <th>Last Updated</th>
                <th>Deployment</th>
                <th>Merge</th>
            </tr>
        </thead>
        <tbody>
            @foreach($branches as $branch)
            <tr>
                <td>{{$branch->branch_name}}</td>
                <td>{{$branch->ahead_by}}</td>
                <td>{{$branch->behind_by}}</td>
                <td>{{$branch->last_commit_author_username}}</td>
                <td>{{$branch->last_commit_time}}</td>
                <td>
                    @if($branch->branch_name == $current_branch)
                    <span class="badge badge-pill badge-light">Deployed</span>
                    @else
                    <a class="btn btn-sm btn-primary" href="#">Deploy</a>
                    @endif
                </td>
                <td>
                    <div>
                        <a class="btn btn-sm btn-warning" href="{{url('/github/repos/'.$repository->id.'/branch/merge?source=master&destination='.urlencode($branch->branch_name))}}">
                            Merge from master
                        </a>
                    </div>
                    <div style="margin-top: 5px;">
                        <a class="btn btn-sm btn-info" href="{{url('/github/repos/'.$repository->id.'/branch/merge?destination=master&source='.urlencode($branch->branch_name))}}">
                            Merge into master
                        </a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection