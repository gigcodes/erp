{!! $products->appends(Request::except('page'))->links() !!}

<?php
  $query = http_build_query( Request::except( 'page' ) );
  $query = url()->current() . ( ( $query == '' ) ? $query . '?page=' : '?' . $query . '&page=' );
?>

<div class="row page-goto">
    <div class="col-2">
        <div class="form-group">
            Goto :
            <select onchange="location.href = this.value;" class="form-control page-goto">
                @for($i = 1 ; $i <= $products->lastPage() ; $i++ )
                    <option value="{{ $query.$i }}" {{ ($i == $products->currentPage() ? 'selected' : '') }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
    </div>
</div>

<div class="row">
  @foreach ($products as $kr => $product)
    @if ($product->hasMedia(config('constants.media_tags')))
      @php
        $image = $product->getMedia(config('constants.media_tags'))->first();
        $image_keys = [];
        $selected_all = true;

        foreach ($product->getMedia(config('constants.media_tags')) as $img) {
          $image_keys[] = $img->getKey();

          if (!in_array($img->getKey(), $selected_products)) {
            $selected_all = false;
          }
        }

        $image_keys = json_encode($image_keys);
      @endphp
      {{-- @foreach ($images as $image) --}}
        <div class="col-md-3 col-xs-6 text-center mb-5 product-list-card">
          <a href="{{ route('products.show', $product->id) }}" data-toggle="tooltip" data-html="true" data-placement="top" title="<strong>Supplier: </strong>{{ $product->supplier }} <strong>Status: </strong>{{ $product->purchase_status }}">
            
            <img data-src="{{ $image->getUrl() }}" class="lazy img-responsive grid-image" alt="" />
            <div style="text-align: left;">
            <p>Sku : {{ strlen($product->sku) > 18 ? substr($product->sku, 0, 15) . '...' : $product->sku }}</p>
            <p>Id : {{ $product->id }}</p>
            <p>Title : {{ $product->name }} </p>
            <p>Size : {{ strlen($product->size) > 17 ? substr($product->size, 0, 14) . '...' : $product->size }}</p>
            <p>Price : {{ $product->price_special }}</p>
            <p>Color : {{ $product->color }} </p>
          </div>
          </a>
          <div style="text-align: left;">
            <p onclick="myFunction({{ $product->id }})" id="description{{ $product->id }}">Description : {{ strlen($product->short_description) > 40 ? substr($product->short_description, 0, 40).'...' : $product->short_description }}</p>
            <p style="display: none;" id="description_full{{ $product->id }}">Description :{{ $product->short_description }}</p>
          </div>
          
          <div class="custom-control custom-checkbox mb-4">
              <input type="checkbox" class="custom-control-input select-pr-list-chk" id="defaultUnchecked_{{ $kr}}">
              <label class="custom-control-label" for="defaultUnchecked_{{ $kr}}"></label>
          </div>
          <a href="#" class="btn btn-xs {{ in_array($image->getKey(), $selected_products) ? 'btn-success' : 'btn-secondary' }} attach-photo" data-image="{{ ($model_type == 'purchase-replace' || $model_type == 'broadcast-images') ? $product->id : $image->getKey() }}" data-attached="{{ in_array($image->getKey(), $selected_products) ? 1 : 0 }}">Attach</a>
          <a href="#" class="btn btn-xs {{ $selected_all ? 'btn-success' : 'btn-secondary' }} attach-photo-all" data-image="{{ ($model_type == 'purchase-replace' || $model_type == 'broadcast-images') ? $product->id : $image_keys }}" data-attached="{{ $selected_all ? 1 : 0 }}">Attach All</a>
        </div>
      {{-- @endforeach --}}
    @else
      <div class="col-md-3 col-xs-6 text-center mb-5">
        <a href="{{ route('products.show', $product->id) }}" data-toggle="tooltip" data-html="true" title="{{ 'Nothing to show' }}">
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

<div class="row page-goto">
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
