@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2>{{ $title }}</h2>
        </div>
        <div class="col-md-12">
            <div class="text-center">
                <div class="text-center">
                    {!! $products->links() !!}
                </div>
            </div>
            <div class="row">
                @if($products->count())
                    @foreach($products as $product)
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-image">
                                    <img style="width: 100%;" src="{!! $product->images[0] ? asset('uploads/social-media/'.$product->images[0]) : 'http://lorempixel.com/555/300/black' !!}">
                                </div><!-- card image -->

                                <div class="card-content">
                                    <span class="card-title">
                                        <div style="font-size: 18px;">
                                            {{ $product->title }}
                                        </div>
                                        <div style="font-size: 14px;">
                                            <strong>{{ $product->brand ? $product->brand->name : 'N/A' }}</strong>
                                        </div>
                                        <div style="font-size: 14px;">
                                            <strong>{!! $product->price ?? 'N/A' !!}</strong>
                                        </div>
                                    </span>
                                </div>
                                <div class="card-action">
                                    <p>
                                        {!! $product->description !!}
                                    </p>
                                    @if ($product->properties)
                                        <strong>Properties</strong>
                                        <ul style="list-style: none;padding-left: 10px">
                                            @foreach($product->properties as $key=>$property)
                                                <li><strong>{{ ucfirst($key) }}</strong>: <strong class="text-info">{{ ucfirst($property) }}</strong></li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <h3 class="text-center m-5">
                        There are no scraped images for this website at the moment.
                    </h3>
                @endif
            </div>
            <div class="text-center">
                {!! $products->links() !!}
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/media-card.css') }}">
@endsection