@extends('layouts.app')


@section('content')
<div class="row ">
    <div class="col-lg-12 margin-tb pl-5 pr-5">
        <div class="pull-left">
            <h2>Create New GT Metrix Account</h2>
        </div>
        <div class="pull-right mt-4 ">
            <a class="btn custom-button" href="{{ route('GtMetrixAccount.index') }}"> Back</a>
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
    <div class="col-xs-12 col-sm-12 col-md-3">
        <div class="form-group">
            {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3">
        <div class="form-group">
            {!! Form::text('password', null, array('placeholder' => 'Password','class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3">
        <div class="form-group">
            {!! Form::text('account_id', null, array('placeholder' => 'Api Key','class' => 'form-control')) !!}
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-2">
        <div class="form-group">
            {!! Form::select('status', ["active" => "Active" , "error" => "Error", "in-active" => "In-Active"],request('status'), array('class' => 'form-control')) !!}
        </div>
    </div>
    
    <div class="col-xs-12 col-sm-12 col-md-1 text-center">
        <button type="submit" class="btn custom-button">+</button>
    </div>
    
</div>
{!! Form::close() !!}


@endsection