@foreach($logListMagentos as $item)
<tr>
        <td>
          <a class="show-product-information text-dark" data-id="{{ $item->product_id }}" href="/products/{{ $item->product_id }}" target="__blank">{{ $item->product_id }}</a>
        </td>
        <td class="expand-row-msg" data-name="sku" data-id="{{$item->id}}">
          <span class="show-short-sku-{{$item->id}}">{{ Str::limit($item->sku, 3 ,'..')}}</span>
          <span style="word-break:break-all;" class="show-full-sku-{{$item->id}} hidden"><a class="text-dark" href="{{ $item->website_url }}/default/catalogsearch/result/?q={{ $item->sku }}" target="__blank">{{$item->sku}}</a></span>
        </td>
        <td class="expand-row-msg" data-name="brand_name" data-id="{{$item->id}}">
          <span class="show-short-brand_name-{{$item->id}}">{{ Str::limit($item->brand_name, 3, '..')}}</span>
          <span style="word-break:break-all;" class="show-full-brand_name-{{$item->id}} hidden">{{$item->brand_name}}</span>
        </td>
        <td class="expand-row-msg" data-name="category_title" data-id="{{$item->id}}">
          <span class="show-short-category_title-{{$item->id}}">{{ Str::limit($item->category_home, 6, '..')}}</span>
          <span style="word-break:break-all;" class="show-full-category_title-{{$item->id}} hidden">{{$item->category_home}}</span>
        </td>
        <td> {{$item->price}} </td>
        <?php $pushJourney = \App\ProductPushJourney::where('log_list_magento_id', $item->log_list_magento_id)->pluck( 'condition')->toArray(); 
              $category = \App\Category::find($item->category);
              if($category->parent_id !=0) {
                  $useStatus = 'status';
              } else {
                  $useStatus = "upteam_status";
              }
        ?>
        <td> @if(in_array('entered_in_product_push', $pushJourney)) <i class="fa fa-check-circle-o text-success fa-lg" aria-hidden="true"></i> @else <i class="fa fa-times-circle text-danger fa-lg" aria-hidden="true"></i> @endif</td>
        @foreach($conditions as $condition)
            <td>
                @if($condition->$useStatus == '1')
                  <i class="fa fa-check-circle-o text-success fa-lg" aria-hidden="true"></i>
                @else
                  <i class="fa fa-times-circle text-danger fa-lg" aria-hidden="true"></i>
                @endif
            </td>
        @endforeach()
      </tr>
    @endforeach()