@extends('layouts.app')

@section('content')
<h2 class="text-center">Groups Details</h2>

<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#repositories">Repositories</a></li>
    <li><a data-toggle="tab" href="#members">Members</a></li>
</ul>
<div class="tab-content">
    <div id="repositories" class="tab-pane fade in active">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Permission</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($repositories as $repository)
                <tr>
                    <td>{{$repository->id}}</td>
                    <td>{{$repository->name}}</td>
                    <td>{{$repository->pivot->rights}}</td>
                    <td>
                        <a class="btn btn-secondary btn-sm" href="/github/groups/{{$group->id}}/repos/{{$repository->id}}/remove">Remove</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div id="members" class="tab-pane fade">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{$user->id}}</td>
                    <td>{{$user->username}}</td>
                    <td>
                        <a class="btn btn-secondary btn-sm" href="/github/groups/{{$group->id}}/users/{{$user->id}}/remove">Remove</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection