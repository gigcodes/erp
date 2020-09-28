
<div class="row">
    <div class="col text-center">
        <button type="button" class="btn btn-image my-3" id="sendImageMessage"><img src="/images/filled-sent.png" /></button>
    </div>
</div>
<div class="infinite-scroll">
    <div class="row">
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
        @endphp
        <div class="col-md-2 col-xs-4 text-center product-list-card mb-4">
            <div style="text-align: left;">
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

            <div data-interval="false" id="carousel_{{ $product->id }}" class="carousel slide" data-ride="carousel">
                <a href="{{ route('products.show', $product->id) }}" data-toggle="tooltip" data-html="true" data-placement="top" title="<strong>Supplier: </strong>{{ $product->supplier }} <strong>Status: </strong>{{ $product->purchase_status }}">
                    <div class="carousel-inner maincarousel">
                        <div class="item" style="display: block;"> <img src="{{ urldecode($im['url'])}}" style="height: 150px; width: 150px;"> </div>
                    </div>
                </a>
            </div>

            <div class="custom-control custom-checkbox" style="padding: 0px; margin-bottom: 10px;">
                <input type="checkbox" class="custom-control-input select-pr-list-chk" id="defaultUnchecked_{{ $product->id.$kr}}" checked="" >
                <label class="custom-control-label" for="defaultUnchecked_{{ $product->id.$kr}}"></label>
            </div>

            <div class="btn-group" role="group" aria-label="Basic example">
                <a href="#" class="btn btn-xs {{ in_array($imageDetails->getKey(), $selected_products) ? 'btn-success' : 'btn-secondary' }} attach-photo" data-image="{{ ($model_type == 'purchase-replace' || $model_type == 'broadcast-images' || $model_type == 'landing-page') ? $product->id : $imageDetails->getKey() }}" data-attached="{{ in_array($imageDetails->getKey(), $selected_products) ? 1 : 0 }}">Attach</a>
                @if($model_type != 'landing-page')
                <a href="#" class="btn btn-xs {{ $selected_all ? 'btn-success' : 'btn-secondary' }} attach-photo-all" data-image="{{ ($model_type == 'purchase-replace' || $model_type == 'broadcast-images' || $model_type == 'landing-page') ? $product->id : $imageDetails->getKey() }}" data-attached="{{ $selected_all ? 1 : 0 }}">Attach All</a>
                @endif
            </div>

            <div class="dropdown load-chat-images-dropdown-menu mt-3 mb-4">
                <button type="button" class="btn btn-primary dropdown-toggle load-chat-images-actions">
                    Actions
                </button>
                <div class="dropdown-menu">
                    <a href="#" class="dropdown-item create-product-lead-dimension" data-id="{{$product->id}}" data-customer-id="{{$customer->id}}">+ Dimensions</a>
                    <a href="#" class="dropdown-item create-product-lead" data-id="{{$product->id}}" data-customer-id="{{$customer->id}}">+ Lead</a>
                    <a href="#" class="dropdown-item create-detail_image" data-id="{{$product->id}}" data-customer-id="{{$customer->id}}"> - Detailed Images</a>
                    <a href="#" class="dropdown-item create-product-order" data-id="{{$product->id}}" data-customer-id="{{$customer->id}}">+ Order</a>
                    <a href="#" class="dropdown-item create-kyc-customer" data-media-key="{{$image_key}}" data-customer-id="cc">+ KYC</a>
                    <a href="#" title="Forward" class="dropdown-item forward-btn" data-toggle="modal" data-target="#forwardModal" data-id="{{$image_key}}">- Forward</a>
                    <a href="#" title="Resend" data-id="{{$image_key}}" class="dropdown-item resend-message">- Resend</a>
                    <a href="#" title="Remove"  class="dropdown-item delete-message" data-id="{{$image_key}}">- Remove</a>
                    <a href="#" title="Mark as reviewed" class="dropdown-item review-btn" data-id="{{$image_key}}">- Mark as reviewed</a>
                </div>
            </div> 
        </div>

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
    </div>
    <div class="row">
        <div class="col text-center">
            <button type="button" class="btn btn-image my-3" id="sendImageMessage"><img src="/images/filled-sent.png" /></button>
        </div>
    </div>
    {{ request()->request->add(["from"=>"attach-image"]) }}
    {!! $products->appends(Request::except('page'))->links() !!}
</div>

