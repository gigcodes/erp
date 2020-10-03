
<div class="row">
    <div class="col text-center">
        <button type="button" class="btn btn-image my-3" id="sendImageMessage"><img src="/images/filled-sent.png" /></button>
    </div>
    <div class="text-right">
        @php
        $customer_id = (!empty($_GET["customer_id"])) ?  $_GET["customer_id"] : $_GET["get_customer_id"];
        $customer = \App\Customer::whereId($customer_id)->first();
        @endphp
        @if($customer_id)
        <p>Customer Name: {{$customer->name}}</p>
        <p>Customer Number: {{$customer->phone}}</p>
        <p>Customer Id: {{$customer->id}}</p>
        @endif
    </div>
</div>

<div class="infinite-scroll">
    <div class="container">
        @php
        $count = 0;
        @endphp
        @foreach ($products as $kr => $product)
        @if ($product->hasMedia(config('constants.attach_image_tag')))
        @php
        $imageDetails = $product->getMedia(config('constants.attach_image_tag'))->first();
        $image_key = $imageDetails->getKey();
        $selected_all = true;
        $im = [
        "abs" => $imageDetails->getAbsolutePath(),
        "url" => $imageDetails->getUrl(),
        "id"  => $imageDetails->getKey()
        ];
        if (!in_array($imageDetails->getKey(), $selected_products)) {
        $selected_all = false;
        }
        $image_keys = json_encode($image_key);
        if($count == 6){
            $count = 0;
        }
        if($count == 0){
                echo '<div class="row parent-row">';
        }
        @endphp
        <div class="col-md-2 col-xs-4 text-center product-list-card mb-4">
            <div data-interval="false" id="carousel_{{ $product->id }}" class="carousel slide" data-ride="carousel">
                <a href="{{ route('products.show', $product->id) }}" data-toggle="tooltip" data-html="true" data-placement="top" title="<strong>Supplier: </strong>{{ $product->supplier }} <strong>Status: </strong>{{ $product->purchase_status }}">
                    <div class="carousel-inner maincarousel">
                        <div class="item" style="display: block;"> <img src="{{ urldecode($im['url'])}}" style="height: 150px; width: 150px;"> </div>
                    </div>
                </a>
            </div>
            <div class="row pl-4 pr-4" style="padding: 0px; margin-bottom: 8px;">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input select-pr-list-chk" id="defaultUnchecked_{{ $product->id.$kr}}" checked="" >
                    <label class="custom-control-label" for="defaultUnchecked_{{ $product->id.$kr}}"></label>
                </div>

                <a href="javascript:;" class="btn btn-sm btn-image {{ in_array($imageDetails->getKey(), $selected_products) ? 'btn-success' : '' }} attach-photo" data-image="{{ ($model_type == 'purchase-replace' || $model_type == 'broadcast-images' || $model_type == 'landing-page') ? $product->id : $imageDetails->getKey() }}" data-attached="{{ in_array($imageDetails->getKey(), $selected_products) ? 1 : 0 }}"><img src="{{asset('images/attach.png')}}"></a>
                @if($model_type != 'landing-page')
                <a href="javascript:;" class="btn btn-sm btn-image {{ $selected_all ? 'btn-success' : '' }} attach-photo-all" data-image="{{ ($model_type == 'purchase-replace' || $model_type == 'broadcast-images' || $model_type == 'landing-page') ? $product->id : $imageDetails->getKey() }}" data-attached="{{ $selected_all ? 1 : 0 }}"><img src="{{asset('images/double-attach.png')}}"></a>
                    @endif
                    <a href="javascript:;" class="btn btn-sm select_row" title="Select Row"><i class="fa fa-arrows-h" aria-hidden="true"></i></a>
                    <a href="javascript:;" class="btn btn-sm create-product-lead-dimension" data-id="{{$product->id}}" data-customer-id="{{$customer->id}}" title="Dimensions"><i class="fa fa-delicious" aria-hidden="true"></i></a>
                    <a href="javascript:;" class="btn btn-sm create-product-lead" data-id="{{$product->id}}" data-customer-id="{{$customer->id}}" title="Lead"><i class="fa fa-archive" aria-hidden="true"></i></a>
                    <a href="javascript:;" class="btn btn-sm create-detail_image" data-id="{{$product->id}}" data-customer-id="{{$customer->id}}" title="Detailed Images"><i class="fa fa-file-image-o" aria-hidden="true"></i></a>
                    <a href="javascript:;" class="btn btn-sm create-product-order" data-id="{{$product->id}}" data-customer-id="{{$customer->id}}" title="Order"><i class="fa fa-cart-arrow-down" aria-hidden="true"></i></a>
                    <a href="javascript:;" class="btn btn-sm create-kyc-customer" data-media-key="{{$image_key}}" data-customer-id="cc" title="KYC"><i class="fa fa-id-badge" aria-hidden="true"></i></a>
                    <a href="javascript:;" title="Forward" class="btn btn-sm forward-btn" data-toggle="modal" data-target="#forwardModal" data-id="{{$image_key}}" title="Forward"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
                    <a href="javascript:;" title="Resend" data-id="{{$image_key}}" class="btn btn-sm resend-message" title="Resend"><i class="fa fa-repeat" aria-hidden="true"></i></a>
                    <a href="javascript:;" title="Remove"  class="btn btn-sm delete-message" data-id="{{$image_key}}" title="Remove"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    <a href="javascript:;" title="Mark as reviewed" class="btn btn-sm review-btn" data-id="{{$image_key}}" title="Mark as reviewd"><i class="fa fa-check" aria-hidden="true"></i></a>
                    <!--<a title="Dialog" href="javascript:;" class="btn btn-sm create-dialog"><i class="fa fa-plus" aria-hidden="true"></i></a>-->
            </div>
        </div>
        @php
          $count++;
         if($count == 6){
           echo '</div>';
         }
        @endphp

        @else
        <div class="col-md-3 col-xs-6 text-center mb-5">
            <a href="{{ route('products.show', $product->id) }}" data-toggle="tooltip" data-html="true" title="{{ 'Nothing to show' }}">
                <img src="" class="img-responsive grid-image" alt="" />
                <p>Sku : {{ strlen($product->sku) > 18 ? substr($product->sku, 0, 15) . '...' : $product->sku }}</p>
                <p>Id : {{ $product->id }}</p>
                <p>Title : {{ $product->name }} </p>
            </a>
            <p>Category : 
                <select class="form-control select-multiple-cat-list update-product" data-id={{ $product->id }}>
                    @foreach($categoryArray as $category)
                    <option value="{{ $category['id'] }}" @if($category['id'] == $product->category) selected @endif >{{ $category['value'] }}</option>
                    @endforeach
                </select>
            </p>
            <a href="{{ route('products.show', $product->id) }}" data-toggle="tooltip" data-html="true" data-placement="top" title="<strong>Supplier: </strong>{{ $product->supplier }} <strong>Status: </strong>{{ $product->purchase_status }}">

                <p>Size : {{ strlen($product->size) > 17 ? substr($product->size, 0, 14) . '...' : $product->size }}</p>
                <p>Price EUR special: {{ $product->price_eur_special }}</p>
                <p>Price INR special: {{ $product->price_inr_special }}</p>
            </a>
            <a href="#" class="btn btn-secondary attach-photo" data-image="" data-attached="0">Attach</a>
        </div>
        @endif
        @endforeach
    <div class="row">
        <div class="col text-center">
            <button type="button" class="btn btn-image my-3" id="sendImageMessage"><img src="/images/filled-sent.png" /></button>
        </div>
    </div>
    {{ request()->request->add(["from"=>"attach-image"]) }}
    {!! $products->appends(Request::except('page'))->links() !!}
</div>

