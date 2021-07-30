
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
                               <td>
                                   <div class="d-flex" style="align-items: center">
                                       <span style="min-width:26px;">{{ $key['seg_discount'] }}</span>
                                       <input style="padding: 6px" placeholder="segment discount" data-ref="{{$key['segment']}}" value="{{ $key['segment_discount_per'] }}%" type="text" class="form-control seg_discount {{$key['segment']}}" name="seg_discount">
                                   </div>
                               </td>
                               <td>{{ $key['iva'] }}</td>
                               <td>{{ $key['net_price'] }}</td>
                               <td>
                                   <div class="form-group">
                                       <div class="input-group">
                                           <input style="min-width: 30px;" placeholder="add duty" data-ref="{{str_replace(' ', '_', $key['country_name'])}}" value="{{ $key['add_duty'] }}" type="text" class="form-control add_duty {{str_replace(' ', '_', $key['country_name'])}}" name="add_duty">
                                       </div>
                                   </div>
                               </td>
                               <td>
                                  <div class="d-flex" style="align-items: center">
                                      <span style="min-width:50px;">{{ $key['add_profit'] }}</span>
                                      <input style="padding: 6px" placeholder="add profit" data-ref="web_{{ $key['storeWebsitesID']}}" value="{{ $key['add_profit_per'] }}" type="text" class="form-control add_profit web_{{ $key['storeWebsitesID']}}" name="add_profit">
                                  </div>
                               </td>
                               <td>{{ $key['final_price'] }}</td>
                           </tr> 
                       @endforeach