@extends('layouts.app')

@section('link-css')
<style type="text/css">
  .form-group {
    padding: 10px;
  }
</style>
@endsection

@section('content')

@if(Session::has('message'))
<div class="alert alert-success alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>{{ Session::get('message') }}</strong>
</div>
@endif

@if(!empty($auth))
<div class="text-center">
  <p>You are not authorized on hubstaff</p>
  <a class="btn btn-primary" href="{{ $auth }}">Authorize</a>
</div>
@endif

<h2 class="text-center">Projects List from Hubstaff </h2>

<div class="container">
  @if(!empty($projects))
  <div class="row">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Project ID</th>
          <th>Name</th>
          <th>Description</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      @foreach($projects as $project)
      <tbody>
        <tr>
          <td>{{ $project->hubstaff_project_id }}</td>
          <td>{{ ucwords($project->hubstaff_project_name) }}</td>
          <td> {{ $project->hubstaff_project_description }} </td>
          <td>
            @if($project->hubstaff_project_status == "active")
            <span class="badge badge-success">Active</span>
            @else
            <span class="badge badge-danger">In active</span>
            @endif
          </td>
          <td>
            <a href="projects/{{$project->id}}">Edit</a>
          </td>
        </tr>
      </tbody>
      @endforeach
    </table>
    <br>
    <hr>
  </div>
  @else
  <div style="text-align: center;color: red;font-size: 14px;">
  </div>
  @endif
</div>
@endsection