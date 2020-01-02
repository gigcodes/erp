@extends('layouts.app')

@section('link-css')
<style type="text/css">
  .form-group {
    padding: 10px;
  }
</style>
@endsection

@section('content')

@if(!empty($auth) && $auth['should_show_login'] == true)
<div class="text-center">
  <p>You are not authorized on hubstaff</p>
  <a class="btn btn-primary" href="{{ $auth['link'] }}">Authorize</a>
</div>
@endif

@if(Session::has('message'))
<div class="alert alert-success alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>{{ Session::get('message') }}</strong>
</div>
@endif

<h2 class="text-center">Users List from Hubstaff </h2>

@if(empty($auth))
<div class="text-right">
  <button class="btn btn-primary">Refresh hubstaff users</button>
</div>
@endif

<div class="container">
  @if(!empty($members))
  <div class="row">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>HubStaff Id</th>
          <th>User Id</th>
          <th>Action</th>
        </tr>
      </thead>
      @foreach($members as $member)
      <tbody>
        <tr>
          <td>{{ $member->hubstaff_user_id }}</td>
          <td>{{ $member->user_id }}</td>
          <td></td>
        </tr>
      </tbody>
      @endforeach
    </table>
    <br>
    <hr>
  </div>
  @else
  <div style="text-align: center;color: red;font-size: 14px;">
    {{$members['error_description']}}
  </div>
  @endif
</div>
@endsection