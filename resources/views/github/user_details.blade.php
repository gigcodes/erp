@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Repositories for user: {{$userDetails['user']['username']}}</h2>
    </div>
</div>
<div class="container">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Rights</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($userDetails['repositories'] as $repository)
            <tr>
                <td>{{$repository['name']}}</td>
                <td>{{$repository['rights']}}</td>
                <td>
                    <a href="/github/repos/{{$repository['id']}}/users/{{$userDetails['user']['username']}}/remove" class="btn btn-sm btn-primary">Revoke</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection