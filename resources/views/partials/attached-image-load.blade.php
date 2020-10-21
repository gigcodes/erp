@foreach ($suggestedProducts as $sp => $suggested)
    <tr>
    <td>{{ \Carbon\Carbon::parse($suggested->last_attached)->format('d-m-y') }} </td>
    <td>{{$suggested->customer->id}}</td>
    <td>{{$suggested->customer->name}}</td>
    <td>{{$suggested->customer->phone}}</td>
    <td class="expand-row-msg" data-name="brand" data-id="{{$suggested->id}}">
    @php 
     $brandList = '';
     foreach($suggested->brdNames as $br) {
        $brandList = $brandList. ' '. $br->name.',';
     }
     @endphp

        <span class="show-short-brand-{{$suggested->id}}">{{ str_limit($brandList, 30, '...')}}</span>
            <span style="word-break:break-all;" class="show-full-brand-{{$suggested->id}} hidden">{{$brandList}},</span>
    </td>

    <td class="expand-row-msg" data-name="category" data-id="{{$suggested->id}}">
    @php 
     $catList = '';
     foreach($suggested->catNames as $cat) {
        $catList = $catList. ' '. $cat->title.',';
     }
     @endphp

        <span class="show-short-category-{{$suggested->id}}">{{ str_limit($catList, 30, '...')}}</span>
            <span style="word-break:break-all;" class="show-full-category-{{$suggested->id}} hidden">{{$catList}},</span>
    </td>
    <td>

    <button title="Open Images" type="button" class="btn preview-attached-img-btn btn-image no-pd" data-id="{{$suggested->customer_id}}">
	{{count($suggested->products)}}<img src="/images/forward.png" style="cursor: default;">
	</button>
    <button title="Select all products" type="button" class="btn btn-xs btn-secondary select-customer-all-products btn-image no-pd" data-id="{{$suggested->customer_id}}">
    <img src="/images/completed.png" style="cursor: default;"></button>
                    <button title="Remove Multiple products" type="button" class="btn btn-xs btn-secondary remove-products" data-id="{{$suggested->customer_id}}"><i class="fa fa-trash" aria-hidden="true"></i></button>
                    <button type="button" class="btn btn-xs btn-secondary forward-products" title="Attach images to new Customer" data-id="{{$suggested->customer_id}}"><i class="fa fa-paperclip" aria-hidden="true"></i></button>
                    <button title="Add more products" type="button" class="btn btn-xs btn-secondary add-more-products" data-id="{{$suggested->customer_id}}"><i class="fa fa-plus" aria-hidden="true"></i></button>
                    <button title="Send Images" type="button" class="btn btn-image sendImageMessage no-pd" data-id="{{$suggested->customer_id}}"><img src="/images/filled-sent.png" /></button>
    </td>
    </tr>
    <tr class="expand-{{$suggested->customer->id}} hidden">
    <td colspan="7">
    <div class="customer-count customer-list-{{$sp}} customer-{{$suggested->customer_id}}" style="padding: 0px 10px;">       
        @php
        $count = 0;
        @endphp
        @php
        $left = count($suggested->products);
        @endphp
        @foreach ($suggested->products as $kr => $pr)
            @php
                $left--;
                $product = \App\Product::find($pr->id);
                $customer = \App\Customer::find($suggested->customer_id);
            @endphp
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
        @endphp
        @if($count == 0)
        <div class="row parent-row">
        @endif
        <div class="col-md-2 col-xs-4 text-center product-list-card mb-4 single-image-{{$suggested->customer->id}}-{{$product->id}}" style="padding:0px 5px;margin-bottom:2px !important;">
            <div style="border: 1px solid #bfc0bf;padding:0px 5px;">
                <div data-interval="false" id="carousel_{{ $product->id }}" class="carousel slide" data-ride="carousel">
                    <a href="{{ route('products.show', $product->id) }}" data-toggle="tooltip" data-html="true" data-placement="top" title="<strong>Supplier: </strong>{{ $product->supplier }} <strong>Status: </strong>{{ $product->purchase_status }}">
                        <div class="carousel-inner maincarousel">
                            <div class="item" style="display: block;"> <img src="{{ urldecode($im['url'])}}" style="height: 150px; width: 150px;display: block;margin-left: auto;margin-right: auto;"> </div>
                        </div>
                    </a>
                </div>
                <div class="row pl-4 pr-4" style="padding: 0px; margin-bottom: 8px;">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input select-pr-list-chk" id="defaultUnchecked_{{ $product->id.$kr.$sp}}" >
                        <label class="custom-control-label" for="defaultUnchecked_{{ $product->id.$kr.$sp}}"></label>
                    </div>

                    <a href="javascript:;" class="btn btn-sm btn-image {{ in_array($imageDetails->getKey(), $selected_products) ? 'btn-success' : '' }} attach-photo new-{{$suggested->customer_id}}" data-image="{{ ($model_type == 'purchase-replace' || $model_type == 'broadcast-images' || $model_type == 'landing-page') ? $product->id : $imageDetails->getKey() }}" data-product={{$product->id}} data-attached="{{ in_array($imageDetails->getKey(), $selected_products) ? 1 : 0 }}"><img src="{{asset('images/attach.png')}}"></a>
                        <a href="javascript:;" class="btn btn-sm select_row" title="Select Single Row"><i class="fa fa-arrows-h" aria-hidden="true"></i></a>
                        <a href="javascript:;" class="btn btn-sm select_multiple_row" title="Select Multiple Row"><i class="fa fa-check" aria-hidden="true"></i></a>
                        <a href="javascript:;" title="Remove"  class="btn btn-sm delete-message" data-id="{{$product->id}}" data-customer="{{$suggested->customer_id}}" title="Remove"><i class="fa fa-trash" aria-hidden="true"></i></a>

                        <a href="javascript:;" class="btn btn-sm create-product-lead-dimension" data-id="{{$product->id}}" data-customer-id="{{$customer->id}}" title="Dimensions"><i class="fa fa-delicious" aria-hidden="true"></i></a>
                        <a href="javascript:;" class="btn btn-sm create-product-lead" data-id="{{$product->id}}" data-customer-id="{{$customer->id}}" title="Lead"><i class="fa fa-archive" aria-hidden="true"></i></a>
                        <a href="javascript:;" class="btn btn-sm create-product-order" data-id="{{$product->id}}" data-customer-id="{{$customer->id}}" title="Order"><i class="fa fa-cart-arrow-down" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
        @php
          $count++;
          if($left == 0) {
            $count = 0;
          }
          $total = count($suggested->products);
         if($count == 6 || $left == 0){
           echo '</div>';
         }
        @endphp
        @if($count == 6)
        <div class="row toggle-div-{{$suggested->customer->id}}">
        <div class="col-md-12"> 
        <button type="button" class="btn btn-image sendImageMessage pull-right" data-id="{{$suggested->customer_id}}" style="padding:0px;margin:0px;"><img src="/images/filled-sent.png" /></button>
        </div>
        </div>
         @endif
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
        <br>
        </div>
    </td>
    </tr>
@endforeach

{{$suggestedProducts->appends(request()->except("page"))->links()}}