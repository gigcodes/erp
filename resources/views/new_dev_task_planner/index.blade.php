@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">New Dev Task Planner</h2>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
            <th rowspan="2">Sr.No.</th>
            <th rowspan="2" class="text-center">Module</th>
            <th rowspan="2" class="text-left">Page</th>
            <th rowspan="2" class="text-center">Details</th>
            <th rowspan="2" class="text-center">Attachements</th>
            <th rowspan="2" class="text-center">Date Created</th>
            <th rowspan="2" class="text-center">User Assigned</th>
            <th rowspan="2" class="text-center">Date Assigned</th>
            <th rowspan="2" class="text-center">Status</th>
            <th rowspan="2" class="text-center">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dev_task as $key =>$value)
                <tr>
                    <td>{{$value['id']}} </td>
                    <td>{{$value['module']}}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{$value['created_at']}}</td>
                    <td>{{$value['assigned_to']}}</td>
                    <td>{{$value['start_time']}}</td>
                    <td>{{$value['status']}}</td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


@endsection 