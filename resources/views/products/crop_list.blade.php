@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">
                Cropped Images
            </h2>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12 text-center">
                    {!! $products->links() !!}
                </div>
            </div>
            <div class="row">
                @foreach($products as $product)
                    <div class="col-md-4 mt-2">
                        <div class="card">
                            <img class="card-img-top" src="{{ $product->imageurl }}" alt="Card image cap">
                            <div class="card-body">
                                <h5 class="card-title">{{ $product->title }}</h5>
                                <p class="card-text">
                                    {{ $product->sku }}<br>
                                    {{ $product->supplier }}
                                </p>
                                <a href="{{ action('ProductCropperController@showImageToBeVerified', $product->id) }}" class="btn btn-primary">Check Cropping</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    {!! $products->links() !!}
                </div>
            </div>
        </div>
    </div>
@endsection