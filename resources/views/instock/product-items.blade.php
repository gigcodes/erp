{!! $products->appends(Request::except('page'))->links() !!}
<div class="row col-md-12">
  @foreach ($products as $product)
  <div class="col-md-3 col-xs-6 text-left">
      <a href="{{ route('products.show', $product->id) }}">
        <img src="{{ $product->getMedia(config('constants.media_tags'))->first()
            ? $product->getMedia(config('constants.media_tags'))->first()->getUrl()
            : ''
          }}" class="img-responsive grid-image" alt="" />
      </a>  
      <p>Brand : {{ isset($product->brands) ? $product->brands->name : "" }}</p>
      <p class="transist_status_{{$product->id}}">Transist Status : {{ $product->purchase_status }}</p>
      <p class="location_{{$product->id}}">Location : {{ ($product->location) ? $product->location : "" }}</p>
      <p>Sku : {{ $product->sku }}</p>
      <p>Id : {{ $product->id }}</p>
      <p>Size : <span class="text-editable" data-field-name="size" data-product-id="{{ $product->id }}">{{ ($product->size) ? $product->size : "N/A"}}</span></p>
      <p>Price : <span class="text-editable" data-field-name="price_inr_special" data-product-id="{{ $product->id }}">{{ ($product->price_inr_special > 0) ? $product->price_inr_special : "N/A" }}</span></p>
      <p>Status : <?php echo Form::select("stock_status",[null => "- Select --"] + \App\Product::STOCK_STATUS,$product->stock_status,["class" => "form-control update-product-stock-status","data-product-id" => $product->id]); ?></p>

      <button type="button" data-product-id="{{ $product->id }}" class="btn btn-image crt-instruction" title="Create Dispatch / Location Change"><img src="/images/support.png"></button>
      <button type="button" data-product-id="{{ $product->id }}" class="btn btn-image crt-instruction-history" title="Product Location History"><img src="/images/remark.png"></button>
      <button type="button" data-product-id="{{ $product->id }}" class="btn btn-image crt-product-dispatch" title="Create Dispatch"><img src="/images/resend.png"></button>
      <?php 
        $getMedia = $product->getMedia(config('constants.media_tags'));
        $image = [];
        foreach ($getMedia as $value) {
            $image[] = $value->id;
        }
      ?>
      <button type="button" data-media-ids="{{ implode(',', $image) }}" class="btn btn-image crt-attach-images" title="Attach Images to Message"><img src="/images/attach.png"></button>

      <input type="checkbox" class="select-product-edit" name="product_id" data-id="{{ $product->id }}">

      @if ($type == 'private_viewing')
      <a href="#" class="btn btn-secondary select-product" data-id="{{ $product->id }}" data-attached="0">Select</a>
      @endif

      {{--

        {!! Form::open(['method' => 'POST','route' => ['products.archive', $product->id],'style'=>'display:inline']) !!}
        <button type="submit" class="btn btn-image"><img src="/images/archive.png" /></button>
        {!! Form::close() !!}
      
      --}}
      @if(auth()->user()->isAdmin())
      {!! Form::open(['method' => 'DELETE','route' => ['products.destroy', $product->id],'style'=>'display:inline']) !!}
      <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
      {!! Form::close() !!}
      @endif
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
<?php
  request()->request->add(['instock' => 'yes']);
?>
{!! $products->appends(Request::except('page'))->links() !!}
