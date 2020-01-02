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

<h2 class="text-center">Tasks List from Hubstaff </h2>

<div class="container">
  @if(!empty($tasks))
  <div class="row">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Task Id</th>
          <th>Summary</th>
          <th>Status</th>
        </tr>
      </thead>
      @foreach($task as $tasks)
      <tbody>
        <tr>
          <td>{{ $task->id }}</td>
          <td>{{ ucwords($task->summary) }}</td>
          <td>{{ $task->status }}</td>
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