@extends('layouts.app')

@section('content')
<h2 class="text-center">Github Repositories</h2>
<div class="container">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Serial Number</th>
                <th>Name</th>
                <th>Last Update </th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($repositories as $repository)
            <tr>
                <td>{{$repository['id']}}</td>
                <td>{{$repository['name']}}</td>
                <td>{{$repository['updated_at']}}</td>
                <td>
                    <a href="#">Settings</a>
                    <a href="{{ url('/github/repos/'.$repository['name'].'/users') }}">Users</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection