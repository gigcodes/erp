@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2> {{ $data->sender }}</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-secondary" href="{{ url('/scrap/gmail') }}"> Back</a>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Tags:</strong>
                @php 
                $count = 0;
                @endphp
                 @foreach($data->tags as $tag)
                    <li>{{ $tag }}</li>
                    @php
                    $count++
                    @endphp
                @endforeach
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong><a href="{{ $data->page_url }}">Visit:</a></strong>
                
            </div>
        </div>
        
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Images:</strong>
                <div class="row">
                    @foreach($data->images as $image)
                    <div class="col-md-4">
                        <img src="{{ $image }}" alt="" class="img-responsive">
                    </div>
                    @endforeach
                </div>
                
            </div>
        </div>
        
    </div>
@endsection



