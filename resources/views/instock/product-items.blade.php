{!! $products->appends(Request::except('page'))->links() !!}

<?php
  $query = http_build_query( Request::except('page' ) );
  $query = url()->current() . ( ( $query == '' ) ? $query . '?page=' : '?' . $query . '&page=' );
?>

<div class="row">
  <div class="col-2">
    <div class="form-group">
      Goto :
      <select onchange="location.href = this.value;" class="form-control">
        @for($i = 1 ; $i <= $products->lastPage() ; $i++ )
          <option value="{{ $query.$i }}" {{ ($i == $products->currentPage() ? 'selected' : '') }}>{{ $i }}</option>
          @endfor
      </select>
    </div>
  </div>
</div>

<div class="row">
  @foreach ($products as $product)
  <div class="col-md-3 col-xs-6 text-center">
    <a href="{{ route('products.show', $product->id) }}">
      <img src="{{ $product->getMedia(config('constants.media_tags'))->first()
          ? $product->getMedia(config('constants.media_tags'))->first()->getUrl()
          : ''
        }}" class="img-responsive grid-image" alt="" />
      <p>Sku : {{ $product->sku }}</p>
      <p>Id : {{ $product->id }}</p>
      <p>Size : {{ $product->size}}</p>
      <p>Price : {{ $product->price_special }}</p>

      @if ($type == 'private_viewing')
      <a href="#" class="btn btn-secondary select-product" data-id="{{ $product->id }}" data-attached="0">Select</a>
      @endif

      {!! Form::open(['method' => 'POST','route' => ['products.archive', $product->id],'style'=>'display:inline']) !!}
      <button type="submit" class="btn btn-image"><img src="/images/archive.png" /></button>
      {!! Form::close() !!}

      @can('admin')
      {!! Form::open(['method' => 'DELETE','route' => ['products.destroy', $product->id],'style'=>'display:inline']) !!}
      <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
      {!! Form::close() !!}
      @endcan
    </a>
  </div>
  @endforeach
</div>

@if ($type == 'private_viewing')
  <div class="row">
    <div class="col text-center">
      <button type="button" class="btn btn-secondary my-3" id="privateViewingButton">Set Up for Private Viewing</button>
    </div>
  </div>
@endif

{!! $products->appends(Request::except('page'))->links() !!}

<div class="row">
  <div class="col-2">
    <div class="form-group">
      Goto :
      <select onchange="location.href = this.value;" class="form-control">
        @for($i = 1 ; $i <= $products->lastPage() ; $i++ )
          <option value="{{ $query.$i }}" {{ ($i == $products->currentPage() ? 'selected' : '') }}>{{ $i }}</option>
          @endfor
      </select>
    </div>
  </div>
</div>
