@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Edit Selection</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('productselection.index') }}"> Back</a>
            </div>
        </div>
    </div>

    @if (  $productselection->isApproved == -1 )
        <div class="alert alert-danger alert-block mt-2">
            <button type="button" class="close" data-d ismiss="alert">×</button>
            <p><strong>Product has been rejected</strong></p>
            <p><strong>Reason : </strong> {{ $productselection->rejected_note }}</p>
        </div>
    @endif

    <form action="{{ route('productselection.update',$productselection->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Sku:</strong>
                    <input type="text" name="sku" value="{{ old('sku') ? old('sku') : $productselection->sku }}" class="form-control" placeholder="Sku">
                    @if ($errors->has('sku'))
                        <div class="alert alert-danger">{{$errors->first('sku')}}</div>
                    @endif
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Size:</strong>
                    <input type="text" class="form-control" name="size" placeholder="Size" value="{{old('size') ? old('size') : $productselection->size }}"/>
                    @if ($errors->has('size'))
                        <div class="alert alert-danger">{{$errors->first('size')}}</div>
                    @endif
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Price (in Euro):</strong>
                    <input type="number" class="form-control" name="price" placeholder="Price" value="{{old('price') ? old('price') : $productselection->price }}"/>
                    @if ($errors->has('price'))
                        <div class="alert alert-danger">{{$errors->first('price')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong> Supplier Link :</strong>
                    <input type="text" class="form-control" name="supplier_link" placeholder="Supplier Link" value="{{ old('supplier_link') ? old('supplier_link') : $productselection->supplier_link }}"/>
                    @if ($errors->has('supplier_link'))
                        <div class="alert alert-danger">{{$errors->first('supplier_link')}}</div>
                    @endif
                </div>
            </div>

      {{--      <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong> Description Link :</strong>
                    <input type="text" class="form-control" name="description_link" placeholder="Description Link" value="{{ old('description_link') ? old('description_link') : $productselection->description_link }}"/>
                    @if ($errors->has('description_link'))
                        <div class="alert alert-danger">{{$errors->first('description_link')}}</div>
                    @endif
                </div>
            </div>--}}


            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="old-image" style="
                     @if ($errors->has('image'))
                        display: none;
                     @endif
                ">
                    <p>
                     <?php $image = $productselection->getMedia(config('constants.media_tags'))->first() ?>
                    <img src="{{ $image ? $image->getUrl() : '' }}"
                         class="img-responsive" style="max-width: 200px;"  alt="">
                    <input type="text" hidden name="oldImage" value="0">
                    </p>
                    <button class="btn btn-danger removeOldImage" data-id="" media-id="{{ $image->id }}">Remove</button>
                </div>
                <div class="form-group new-image" style="
                @if ( !$errors->has('image'))
                display: none;
                @endif
                ">
                    <strong>Upload Image:</strong>
                    <input  type="file" enctype="multipart/form-data" class="form-control" name="image" />
                    @if ($errors->has('image'))
                        <div class="alert alert-danger">{{$errors->first('image')}}</div>
                    @endif
                </div>
            </div>


            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <input type="text" hidden name="stage" value="1">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
@endsection