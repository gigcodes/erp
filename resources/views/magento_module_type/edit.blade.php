@extends('layouts.app')

@section('content')
    <div class="row mt-5">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Add {{ $title }}</h2>
            </div>
            <div class="pull-right mr-5">
                <a class="btn btn-primary" href="{{ route('magento_module_types.index') }}"> Back</a>

            </div>
        </div>
    </div>
    @if ($message = Session::get('success'))
        <div class="row ml-5 mr-5">
            <div class="col-lg-12">
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            </div>
        </div>
    @endif

    {!! Form::model($magento_module_type, ['method' => 'PATCH', 'route' => ['magento_module_types.update', $magento_module_type->id], 'class' => 'form mb-15', 'enctype' => 'multipart/form-data']) !!}

    {!! Form::hidden('id', $magento_module_type->id) !!}

    @include('magento_module_type.form')

    {!! Form::close() !!}
@endsection
