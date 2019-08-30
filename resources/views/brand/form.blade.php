@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ $modify ? 'Edit Brand' : 'Create Brand' }}</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-secondary" href="{{ route('brand.index') }}"> Back</a>
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

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Segment</strong>
                    <select name="brand_segment">
                        <option value=""></option>
                        <option value="A" {{$brand_segment == 'A' ? ' SELECTED' : ''}}>A</option>
                        <option value="B" {{$brand_segment == 'B' ? ' SELECTED' : ''}}>B</option>
                        <option value="C" {{$brand_segment == 'C' ? ' SELECTED' : ''}}>C</option>
                    </select>
                    @if ($errors->has('brand_segment'))
                        <div class="alert alert-danger">{{$errors->first('brand_segment')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Strip last # characters from SKU</strong>
                    <input type="text" class="form-control" name="sku_strip_last" placeholder="Strip last # characters from SKU" value="{{old('sku_strip_last') ? old('sku_strip_last') : $sku_strip_last}}"/>
                    @if ($errors->has('sku_strip_last'))
                        <div class="alert alert-danger">{{$errors->first('sku_strip_last')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Add to SKU for brand site</strong>
                    <input type="text" class="form-control" name="sku_add" placeholder="Add to SKU for brand site" value="{{old('sku_add') ? old('sku_add') : $sku_add}}"/>
                    @if ($errors->has('sku_add'))
                        <div class="alert alert-danger">{{$errors->first('sku_add')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-secondary">+</button>
            </div>

        </div>
    </form>


@endsection
