@extends('layouts.app')

@section('link-css')
  <style type="text/css">
    .form-group{
      padding: 10px;
    }
  </style>
@endsection

@section('content')

@if(Session::has('message'))
  <div class="alert alert-success alert-block" >
    <button type="button" class="close" data-dismiss="alert">×</button> 
        <strong>{{ Session::get('message') }}</strong>
  </div>
@endif

    <h2 class="text-center">Tasks List from Hubstaff </h2>

    <div class="container">
        @if(!empty($tasks) && !$tasks['error'])
            <div class="row">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                  </tr>
                </thead>
                @foreach($members as $member)
                  <tbody>
                    <tr>
                      <td>{{ $member->id }}</td>
                      <td>{{ ucwords($member->name) }}</td>
                      <td>{{ $member->email }}</td>
                      <td>
                        @if($member->status == "active")
                          <span class="badge badge-success">Active</span>
                        @else
                          <span class="badge badge-danger">In active</span>
                        @endif
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
                {{$tasks['error_description']}}
            </div>
      @endif

    </div>
@endsection