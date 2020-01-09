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
                <td>{{$repository['id']}}</th>
                <th>{{$repository['name']}}</th>
                <th>{{$repository['updated_at']}}</th>
                <th>
                    <a href="#">Settings</a>
                    <a href="{{ url('/github/repos/'.$repository['name'].'/users') }}">Users</a>
                </th>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection