@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Supplier Status Management</h2>
            <div class="pull-left">
            </div>
            <div class="pull-right">               
                    <a class="btn btn-secondary" href="{{ route('supplier-status.create') }}">+</a>
            </div>
        </div>
    </div>


    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Name</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($supplierstatus as $key => $data)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $data->name }}</td>
                <td><a class="btn btn-image" href="{{ route('supplier-status.edit',$data->id) }}"><img src="/images/edit.png" /></a>
                 
                {!! Form::open(['method' => 'DELETE','route' => ['supplier-status.destroy', $data->id],'style'=>'display:inline']) !!}
                <button type="submit" class="btn btn-image d-inline"><img src="/images/delete.png" /></button>
                {!! Form::close() !!}
              
                </td>
            </tr>
        @endforeach
    </table>
    </div>


    {!! $supplierstatus->render() !!}


@endsection
