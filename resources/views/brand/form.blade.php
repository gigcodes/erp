@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ $modify ? 'Edit Brand' : 'Create Brand' }}</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('brand.index') }}"> Back</a>
            </div>
        </div>
    </div>


    <form action="{{ $modify ? route('brand.update',$id) : route('brand.store')  }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($modify)
            @method('PUT')
        @endif
        <div class="row">

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Name</strong>
                    <input type="text" class="form-control" name="name" placeholder="name" value="{{old('name') ? old('name') : $name}}"/>
                    @if ($errors->has('name'))
                        <div class="alert alert-danger">{{$errors->first('name')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Euro To Inr</strong>
                    <input type="text" class="form-control" name="euro_to_inr" placeholder="euro_to_inr" value="{{old('euro_to_inr') ? old('euro_to_inr') : $euro_to_inr}}"/>
                    @if ($errors->has('euro_to_inr'))
                        <div class="alert alert-danger">{{$errors->first('euro_to_inr')}}</div>
                    @endif
                </div>
            </div>


            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Deduction %</strong>
                    <input type="number" class="form-control" name="deduction_percentage" placeholder="deduction_percentage" value="{{old('deduction_percentage') ? old('deduction_percentage') : $deduction_percentage}}"/>
                    @if ($errors->has('deduction_percentage'))
                        <div class="alert alert-danger">{{$errors->first('deduction_percentage')}}</div>
                    @endif
                </div>
            </div>


            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Magento Id</strong>
                    <input type="text" class="form-control" name="magento_id" placeholder="Magento ID" value="{{old('magento_id') ? old('magento_id') : $magento_id}}"/>
                    @if ($errors->has('magento_id'))
                        <div class="alert alert-danger">{{$errors->first('magento_id')}}</div>
                    @endif
                </div>
            </div>



            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>

        </div>
    </form>


@endsection