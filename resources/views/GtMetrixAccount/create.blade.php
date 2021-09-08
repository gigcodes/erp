@extends('layouts.app')


@section('content')
<div class="row m-4">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2><strong>Create New GT Metrix Account</strong></h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('GtMetrixAccount.index') }}"> Back</a>
        </div>
    </div>
</div>


@if (count($errors) > 0)
<div class="alert alert-danger">
    <strong>Whoops!</strong> There were some problems with your input.<br><br>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif



{!! Form::open(array('route' => 'account.store','method'=>'POST')) !!}
<div class="row m-4">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Email:</strong>
            {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Password:</strong>
            {!! Form::text('password', null, array('placeholder' => 'Password','class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Api Key:</strong>
            {!! Form::text('account_id', null, array('placeholder' => 'Api Key','class' => 'form-control')) !!}
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Status:</strong>
            {!! Form::select('status', ["active" => "Active" , "error" => "Error", "in-active" => "In-Active"],request('status'), array('class' => 'form-control')) !!}
        </div>
    </div>
    
    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
        <button type="submit" class="btn btn-success">+</button>
    </div>
    
</div>
{!! Form::close() !!}


@endsection