{!! $products->appends(Request::except('page'))->links() !!}

<?php
  $query = http_build_query( Request::except( 'page' ) );
  $query = url()->current() . ( ( $query == '' ) ? $query . '?page=' : '?' . $query . '&page=' );
?>

<div class="row">
    <div class="col-2">
        <div class="form-group">
            Goto :
            <select onchange="location.href = this.value;">
                @for($i = 1 ; $i <= $products->lastPage() ; $i++ )
                    <option value="{{ $query.$i }}" {{ ($i == $products->currentPage() ? 'selected' : '') }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
    </div>
</div>

<div class="row">
  @foreach ($products as $product)
    @if ($images = $product->getMedia(config('constants.media_tags')))
      @foreach ($images as $image)
        <div class="col-md-3 col-xs-6 text-center mb-5">
          <a href="{{ route('products.show', $product->id) }}" data-toggle="tooltip" data-html="true" data-placement="top" title="<strong>Supplier: </strong>{{ $product->supplier }}">
            <img data-src="{{ $image->getUrl() }}" class="lazy img-responsive grid-image" alt="" />
            <p>Sku : {{ strlen($product->sku) > 18 ? substr($product->sku, 0, 15) . '...' : $product->sku }}</p>
            <p>Id : {{ $product->id }}</p>
            <p>Size : {{ strlen($product->size) > 17 ? substr($product->size, 0, 14) . '...' : $product->size }}</p>
            <p>Price : {{ $product->price_special }}</p>
          </a>
          <a href="#" class="btn {{ in_array($image->getKey(), $selected_products) ? 'btn-success' : 'btn-secondary' }} attach-photo" data-image="{{ $image->getKey()  }}" data-attached="{{ in_array($image->getKey(), $selected_products) ? 1 : 0 }}">Attach</a>
        </div>
      @endforeach
    @else
      <div class="col-md-3 col-xs-6 text-center mb-5">
        <a href="{{ route('products.show', $product->id) }}" data-toggle="tooltip" data-html="true" title="{{ $images_html ? $images_html : 'Nothing to show' }}">
          <img src="" class="img-responsive grid-image" alt="" />
          <p>Sku : {{ strlen($product->sku) > 18 ? substr($product->sku, 0, 15) . '...' : $product->sku }}</p>
          <p>Id : {{ $product->id }}</p>
          <p>Size : {{ strlen($product->size) > 17 ? substr($product->size, 0, 14) . '...' : $product->size }}</p>
          <p>Price : {{ $product->price_special }}</p>
        </a>
        <a href="#" class="btn btn-secondary attach-photo" data-image="" data-attached="0">Attach</a>
      </div>
    @endif
  @endforeach
</div>

<div class="row">
  <div class="col text-center">
    <button type="button" class="btn btn-image my-3" id="sendImageMessage"><img src="/images/filled-sent.png" /></button>
  </div>
</div>

{!! $products->appends(Request::except('page'))->links() !!}

<div class="row">
    <div class="col-2">
        <div class="form-group">
            Goto :
            <select onchange="location.href = this.value;">
                @for($i = 1 ; $i <= $products->lastPage() ; $i++ )
                    <option value="{{ $query.$i }}" {{ ($i == $products->currentPage() ? 'selected' : '') }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
    </div>
</div>
