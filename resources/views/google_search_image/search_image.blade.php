@extends('layouts.app')

@section("styles")
@endsection
<style type="text/css">
  .dis-none {
    display: none;
  }
</style>
@section('content')
  @include('partials.flash_messages')

  <div class="productGrid" id="productGrid">
     <form  method="POST" action="{{route('google.search.image')}}">
      {{ csrf_field() }}
        <div class="row">
          @foreach ($productImage as $productImagesArr)
            @foreach ($productImagesArr as $product)
            <div class="col-md-3 col-xs-6 text-center">
              <a href="{{ route('products.show', $product['id']) }}">
                <img src="{{ $product['media_url'] }}" class="img-responsive grid-image" alt="" />
                <p>Sku : {{$product['sku']}}</p>
                <p>Id : {{$product['id']}}</p>
                <p>Category : {{$product['category']}}</p>
                <p>Color : {{$product['color']}}</p>
                <p>Composite : {{$product['composite']}}</p>
                <p>Gender : {{$product['gender']}}</p>
              </a>
            </div>
            @endforeach
          @endforeach
        </div>
      </form>
  </div>

@endsection

@section('scripts')
  
@endsection
