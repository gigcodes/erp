@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Task Category</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-secondary" href="{{ route('task_category.create') }}">+</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <table class="table table-bordered">
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th width="280px">Action</th>
                </tr>
				<?php $i = 0; ?>
                @foreach ($data as $key => $task_category)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $task_category->name }}</td>
                        <td>
                            <a class="btn btn-image" href="{{ route('task_category.edit',$task_category->id) }}"><img src="/images/edit.png" /></a>
                            {!! Form::open(['method' => 'DELETE','route' => ['task_category.destroy', $task_category->id],'style'=>'display:inline']) !!}
                            <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
