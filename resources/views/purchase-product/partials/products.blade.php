<center><p>{{strtoupper($type)}}</p></center>
<button class="btn btn-secondary btn-xs pull-right btn-send" data-type="{{$type}}" data-id="{{$supplier_id}}">Send</button>
<div class="table-responsive mt-2">
      <table class="table table-bordered order-table" style="border: 1px solid #ddd !important; color:black;table-layout:fixed">
        <thead>
        <tr>
            <th width="2%"></th>
            <th width="8%">#</th>
            <th width="20%">View</th>
            <th width="20%">Name</th>
            @if($type == 'inquiry')<th width="20%">Is Owned?</th>@endif
            <th width="20%">SKU</th>
            <th width="10%">Price</th>
            <th width="10%">Discount</th>
            <th width="10%">Fixed Price</th>
            <th width="10%">Final Price</th>
            <th width="10%">Action</th>
         </tr>
        </thead>

        <tbody>
			@foreach ($products as $key => $product)
            <tr class="supplier-{{$supplier_id}}">
              <td><input type="checkbox" class="select-pr-list-chk" data-id="{{$product->id}}" data-order-id="{{ $product->order_product_id ?? 0}}"></td><!-- Purpose : Add Order id - DEVATSK-4236 -->
              <td>{{ ++$key }}</td>
              <td>
              {{-- START - Purpose : Replace $product to $product_data - DEVTASK-4048 --}}
              @php
                $product_data = \App\Product::find($product->id);
              @endphp

              {{-- Purpose : Add If Condition - DEVTASK-4048 --}}
              @if($product_data != null)
                @if ($product_data->hasMedia(config('constants.media_tags')))
                  <span class="td-mini-container">
                      <a data-fancybox="gallery" href="{{ $product_data->getMedia(config('constants.media_tags'))->first()->getUrl() }}">View</a>
                  </span>
                @endif
              @endif
              {{-- END - DEVTASK-4048 --}}
              </td>
              <td>{{ $product->name }}</td>
              @if($type == 'inquiry')<td>{{ $product->sup_id == $supplier_id ? 'Yes' : 'No'}}</td>@endif
              <td>{{$product->sku}}</td>
              <td>{{$product->product_price}}</td>
              <td>{{$product->discount}}</td>
              <td>{{$product->fixed_price}}</td>
              <td>
              @php 
            if($product->product_price)  {
              if($product->discount) {
                $discount = $product->product_price*($product->discount/100);
                $final_price = $product->product_price - $discount;
              }
              else {
                if($product->fixed_price) {
                  $final_price = $fixed_price;
                }
                else {
                  $final_price = $product->product_price;
                }
              } 
            }
            else {
              $final_price = 0;
            }
            @endphp
            {{number_format($final_price,2)}}
              
              
              </td>
              <td></td>
            </tr>
           @endforeach
        </tbody>
      </table>
	</div>