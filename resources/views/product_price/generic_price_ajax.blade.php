@php $i=1; @endphp
@foreach ($product_list as $product) 
   <tr  data-id="{{$i}}" data-country_code="{{$product['country']['country_code']}}" class="tr_{{$i++}}">
       <td class="expand-row" style="word-break: break-all">
           <span class="td-mini-container">
              {{ strlen( $product['categoryName']) > 9 ? substr( $product['categoryName'], 0, 8).'...' :  $product['categoryName'] }}
           </span>

           <span class="td-full-container hidden">
               {{ $product['categoryName'] }}
           </span>
        </td>

        <td class="expand-row" style="word-break: break-all">
           <span class="td-mini-container">
              {{ strlen( $product['product_website']) > 9 ? substr( $product['product_website'], 0, 8).'...' :  $product['product_website'] }}
           </span>

           <span class="td-full-container hidden">
               {{ $product['product_website'] }}
           </span>
        </td>

       <td class="expand-row" style="word-break: break-all">
            <span class="td-mini-container">
                        {{ strlen( $product['brandSegment']) > 15 ? substr( $product['brandSegment'], 0, 15).'...' :  $product['brandSegment'] }}
             </span>

           <span class="td-full-container hidden">
                       {{ $product['brandSegment'] }}
                    </span>
       </td>
       <td>{{ $product['product'] }}</td>
     
       <td class="expand-row" style="word-break: break-all">

           <span class="td-mini-container">
                {{ strlen( $product['country']['dutySegment']) > 9 ? substr( $product['country']['dutySegment'], 0, 9).'...' :  $product['country']['dutySegment'] }}
           </span>

           <span class="td-full-container hidden">
               {{ $product['country']['dutySegment'] }}
           </span>
       </td>
    
       <td>{{ $product['product_price'] }}</td>

          @foreach($category_segments as $category_segment)
            <td>
                @php
                    $category_segment_discount = \DB::table('category_segment_discounts')->where('brand_id', $product['brandId'])->where('category_segment_id', $category_segment->id)->first();
                @endphp

                @if($category_segment_discount)
                    <input type="text" class="form-control seg_discount1" value="{{ $category_segment_discount->amount }}" = data-ref="{{ $category_segment->id }}" onKeyUp="updateSegmentPrice({{$category_segment->id}}, {{$product['brandId']}}, this)"></th>
                @else
                    <input type="text" class="form-control seg_discount1" value="" data-ref="{{ $category_segment->id }}" onKeyUp="updateSegmentPrice({{$category_segment->id}}, {{$product['brandId']}}, this)"></th>
                @endif
            </td>
          @endforeach 
      
       <td>
           <div class="form-group">
               <div class="input-group">
                   <input style="width: 75%;border-radius: 4px;" placeholder="add duty" data-ref="{{str_replace(' ', '_', $product['country']['country_name'])}}" value="{{ str_replace('%', '', $product['country']['default_duty']) }}" type="text" class="form-control add_duty {{str_replace(' ', '_', $product['country']['country_name'])}}" name="add_duty" onKeyUp="updateDutyPrice({{$product['country']['id']}}, this)">
                   <div class="ml-2" style="float: left;">%</div>
               </div>
           </div>
       </td>
       <td>{{ $product['less_IVA'] }}</td>
       <td>{{ $product['final_price'] }}</td>
       <td><button class="btn btn-secondary UpdateProduct" data-brandId ="{{$product['brandId']}}" data-countryId ="{{$product['country']['id']}}">Update</button></td>
   </tr> 
@endforeach
                   