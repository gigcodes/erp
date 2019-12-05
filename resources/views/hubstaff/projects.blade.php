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

    <h2 class="text-center">Projects List from Hubstaff </h2>

    <div class="container">
        @if(!empty($projects) && !$projects['error'])
            <div class="row">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Project ID</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                @foreach($projects as $project)
                  <tbody>
                    <tr>
                      <td>{{ $project->id }}</td>
                      <td>{{ ucwords($project->name) }}</td>
                      <td>{{ $project->email }}</td>
                      <td>
                        @if($project->status == "active")
                          <span class="badge badge-success">Active</span>
                        @else
                          <span class="badge badge-danger">In active</span>
                        @endif
                      </td>
                      <td>
                          <a href="#">Edit</a>
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
                {{$projects['error_description']}}
            </div>
        @endif
    </div>
@endsection