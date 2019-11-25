@extends('layouts.app')

@section("styles")
    <link rel="stylesheet" type="text/css" href="{{ asset('css/rcrop.min.css') }}">
@endsection
@section('content')
    
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Google Search Image Crop</h2>
        </div>
    </div>
    @include('partials.flash_messages')
    <form  method="POST" action="{{route('google.search.crop.post')}}">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <img id="image_crop" src="{{ $image }}" width="100%">
            </div>
        </div>
        <div class="row">
            <div class="col text-center">
                <input type="hidden" name="product_id" value="{{$product_id}}">
                <input type="hidden" name="media_id" value="{{$media_id}}">
                <button type="submit" class="btn btn-image my-3" id="sendImageMessage"><img src="/images/filled-sent.png" /></button>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
  <script src="{{ asset('js/rcrop.min.js') }}"></script>
  <script>
    $(document).ready(function() {
        $('#image_crop').rcrop({full : true});
    });
  </script>
@endsection
