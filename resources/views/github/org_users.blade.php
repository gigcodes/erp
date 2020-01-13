@extends('layouts.app')

@section('content')
<h2 class="text-center">Users</h2>
<div class="container">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{$user['id']}}</td>
                <td>{{$user['username']}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection