
                       @php $i=$count; @endphp
                       @foreach ($product_list as $key)
                           <tr data-storeWebsitesID="{{$key['storeWebsitesID']}}" data-id="{{$i}}" data-country_code="{{$key['country_code']}}" class="tr_{{$i++}}">

                               <td class="expand-row" style="word-break: break-all">


                                   <span class="td-mini-container">
                                                {{ strlen( $key['sku']) > 15 ? substr( $key['sku'], 0, 15).'...' :  $key['sku'] }}
                                            </span>

                                   <span class="td-full-container hidden">
                                                {{  $key['sku'] }}
                                            </span>



                               </td>
                               <td class="product_id">{{ $key['id'] }}</td>
                               <td>{{ $key['country_name'] }}</td>
                               <td>{{ $key['brand'] }}</td>
                               <td>{{ $key['segment'] }}</td>
                               <td class="expand-row" style="word-break: break-all">

                                   <span class="td-mini-container">
                                                {{ strlen( $key['website']) > 30 ? substr( $key['website'], 0, 30).'...' :  $key['website'] }}
                                            </span>

                                   <span class="td-full-container hidden">
                                                {{  $key['website'] }}
                                            </span>
                               </td>
                               <td>{{ $key['eur_price'] }}</td>
                               <!-- <td>
                                   <div style="align-items: center">
                                       <span style="min-width:26px;">{{ $key['seg_discount'] }}</span>
                                       <div class="ml-2" style="float: right;">%</div>
                                       <div style="float: right;width:50%;">
                                            <input style="padding: 6px" placeholder="segment discount" data-ref="{{$key['segment']}}" value="{{ $key['segment_discount_per'] }}" type="text" class="form-control seg_discount {{$key['segment']}}" name="seg_discount">
                                       </div>
                                   </div>
                               </td> -->

                               @foreach($category_segments as $category_segment)
                                    <td>
                                        @php
                                            $category_segment_discount = \DB::table('category_segment_discounts')->where('brand_id', $key['brand_id'])->where('category_segment_id', $category_segment->id)->first();
                                        @endphp

                                        @if($category_segment_discount)
                                            <input type="text" class="form-control seg_discount" value="{{ $category_segment_discount->amount }}" onchange="store_amount({{$key['brand_id'] }}, {{ $category_segment->id }})" data-ref="{{ $category_segment->id }}"></th>
                                        @else
                                            <input type="text" class="form-control seg_discount" value="" onchange="store_amount({{ $key['brand_id']}}, {{ $category_segment->id }})" data-ref="{{ $category_segment->id }}"></th>
                                        @endif
                                        {{-- <input type="text" class="form-control" value="{{ $brand->pivot->amount }}" onchange="store_amount({{ $key['brand_id'] }}, {{ $category_segment->id }})"> --}} {{-- Purpose : Comment code -  DEVTASK-4410 --}}
                                    </td>
                                @endforeach 

                               <td>{{ $key['iva'] }}</td>
                               <td>{{ $key['net_price'] }}</td>
                               <td>
                                   <div class="form-group">
                                       <div class="input-group">
                                           <input style="width: 75%;border-radius: 4px;" placeholder="add duty" data-ref="{{str_replace(' ', '_', $key['country_name'])}}" value="{{ str_replace('%', '', $key['add_duty']) }}" type="text" class="form-control add_duty {{str_replace(' ', '_', $key['country_name'])}}" name="add_duty">
                                           <div class="ml-2" style="float: left;">%</div>
                                       </div>
                                   </div>
                               </td>
                               <td>
                                  <div style="align-items: center">
                                      <span style="min-width:50px;">{{ $key['add_profit'] }}</span>
                                      <div class="ml-2" style="float: right;">%</div>
                                      <div style="float: right;width:50%;">
                                          <input style="padding: 6px" placeholder="add profit" data-ref="web_{{ $key['storeWebsitesID']}}" value="{{ $key['add_profit_per'] }}" type="text" class="form-control add_profit web_{{ $key['storeWebsitesID']}}" name="add_profit">
                                      </div>
                                  </div>
                               </td>
                               <td>{{ $key['final_price'] }}</td>
                           </tr> 
                       @endforeach