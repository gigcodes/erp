@foreach ($products as $product)
  <div class="col-md-3 col-xs-6 text-center">
    <a href="` + product['link'] + `">
        <img src="{{ $product->getMedia(config('constants.media_tags'))->first()
                      ? $product->getMedia(config('constants.media_tags'))->first()->getUrl()
                      : ''
                   }}" class="img-responsive grid-image" alt="" />
        <p>Sku : {{ $product->sku }}</p>
        <p>Id : {{ $product->id }}</p>
        <p>Size : {{ $product->size}}</p>
        <p>Price : {{ $product->price_special }}</p>
    </a>
    <a href="#" class="btn btn-primary attach-photo" data-image="{{ $product->getMedia(config('constants.media_tags'))->first()
                  ? $product->getMedia(config('constants.media_tags'))->first()->getUrl()
                  : ''
               }}" data-attached="0">Attach to Message</a>
  </div>
@endforeach
