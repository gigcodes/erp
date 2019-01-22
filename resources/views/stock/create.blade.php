@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Create Stock</h2>

            <div class="pull-right">
                <a class="btn btn-secondary" href="{{ route('stock.index') }}">Back</a>
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

    <div class="row">
      <div class="col-xs-12 col-md-8 col-md-offset-2">
        {!! Form::open(array('route' => 'stock.store','method'=>'POST')) !!}
          <div class="form-group">
            <strong>Courier:</strong>
            {!! Form::text('courier', old('courier'), array('placeholder' => 'Courier','class' => 'form-control', 'required'  => true)) !!}
            @if ($errors->has('courier'))
              <div class="alert alert-danger">{{$errors->first('courier')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>AWB:</strong>
            <input type="text" name="awb" class="form-control" placeholder="00000000000" value="{{ old('awb') }}" required>
            @if ($errors->has('awb'))
              <div class="alert alert-danger">{{$errors->first('awb')}}</div>
            @endif
          </div>

          <strong>Size dimensions</strong>
          <div class="row">
            <div class="col-xs-4">
              <div class="form-group">
                <input type="text" name="l_dimension" class="form-control" placeholder="L" value="{{ old('l_dimension') }}">
                @if ($errors->has('l_dimension'))
                  <div class="alert alert-danger">{{$errors->first('l_dimension')}}</div>
                @endif
              </div>
            </div>

            <div class="col-xs-4">
              <div class="form-group">
                <input type="text" name="w_dimension" class="form-control" placeholder="W" value="{{ old('w_dimension') }}">
                @if ($errors->has('w_dimension'))
                  <div class="alert alert-danger">{{$errors->first('w_dimension')}}</div>
                @endif
              </div>
            </div>

            <div class="col-xs-4">
              <div class="form-group">
                <input type="text" name="h_dimension" class="form-control" placeholder="H" value="{{ old('h_dimension') }}">
                @if ($errors->has('h_dimension'))
                  <div class="alert alert-danger">{{$errors->first('h_dimension')}}</div>
                @endif
              </div>
            </div>
          </div>

          <div class="form-group">
            <strong>Weight:</strong>
            <input type="number" name="weight" class="form-control" placeholder="3.2" step="0.01" value="{{ old('weight') }}">
            @if ($errors->has('weight'))
              <div class="alert alert-danger">{{$errors->first('weight')}}</div>
            @endif
          </div>

          <div class="text-center">
            <button type="submit" class="btn btn-secondary">Create Stock</button>
          </div>
        {!! Form::close() !!}
      </div>
    </div>
@endsection
