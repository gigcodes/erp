@extends('layouts.app')

@section('content')
<h2 class="text-center">Groups</h2>
<div class="container">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groups as $group)
            <tr>
                <td>{{$group['id']}}</td>
                <td>{{$group['name']}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection