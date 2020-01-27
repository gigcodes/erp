@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Groups ({{sizeof($groups)}})</h2>
    </div>
</div>
<div class="container">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Members</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groups as $group)
            <tr>
                <td>{{$group['id']}}</td>
                <td>{{$group['name']}}</td>
                <td>
                    @foreach($group->users as $user)
                    <span class="badge badge-pill badge-light">{{$user->username}}</span>
                    @endforeach
                </td>
                <td>
                    <a href="/github/groups/{{ $group['id'] }}">Details</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection