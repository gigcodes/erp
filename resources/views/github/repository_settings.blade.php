@extends('layouts.app')

@section('content')
<h2 class="text-center">{{ $repository->name }} branches</h2>
<div class="container">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Behind By</th>
                    <th>Ahead By</th>
                </tr>
            </thead>
            <tbody>
                @foreach($branches as $branch)
                <tr>
                    <td>{{$branch->branch_name}}</td>
                    <td>{{$branch->ahead_by}}</td>
                    <td>{{$branch->behind_by}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection