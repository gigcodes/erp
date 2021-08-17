@foreach($logListMagentos as $item)
			    <tr>
                  <td>
                    <a class="show-product-information" data-id="{{ $item->product_id }}" href="/products/{{ $item->product_id }}" target="__blank">{{ $item->product_id }}</a>
                  </td>
                  <td class="expand-row-msg" data-name="sku" data-id="{{$item->id}}">
                    <span class="show-short-sku-{{$item->id}}">{{ str_limit($item->sku, 4 ,'...')}}</span>
                    <span style="word-break:break-all;" class="show-full-sku-{{$item->id}} hidden"><a href="{{ $item->website_url }}/default/catalogsearch/result/?q={{ $item->sku }}" target="__blank">{{$item->sku}}</a></span>
                  </td>
                  <td class="expand-row-msg" data-name="brand_name" data-id="{{$item->id}}">
                    <span class="show-short-brand_name-{{$item->id}}">{{ str_limit($item->brand_name, 10, '...')}}</span>
                    <span style="word-break:break-all;" class="show-full-brand_name-{{$item->id}} hidden">{{$item->brand_name}}</span>
                  </td>
                  <td class="expand-row-msg" data-name="category_title" data-id="{{$item->id}}">
                    <span class="show-short-category_title-{{$item->id}}">{{ str_limit($item->category_home, 10, '...')}}</span>
                    <span style="word-break:break-all;" class="show-full-category_title-{{$item->id}} hidden">{{$item->category_home}}</span>
                  </td>
                  <td> {{$item->price}} </td>
                  <td class="expand-row-msg" data-name="message" data-id="{{$item->id}}">
                    <span class="show-short-message-{{$item->id}}">{{ str_limit($item->message, 6, '...')}}</span>
                    <span style="word-break:break-all;" class="show-full-message-{{$item->id}} hidden">{{$item->message}}</span>
                  </td>
                  <td>
                    @if(isset($item->log_created_at))
                      {{ date('M d, Y',strtotime($item->log_created_at))}}
                    @endif
                  </td>
                  <td class="expand-row-msg" data-name="website_title" data-id="{{$item->id}}">
                    <span class="show-short-website_title-{{$item->id}}">{{ str_limit($item->website_title, 10, '...')}}</span>
                    <span style="word-break:break-all;" class="show-full-website_title-{{$item->id}} hidden">{{$item->website_title}}</span>
                  </td>
                  <td class="">
                    {{ (isset($item->stock) && $item->stock > 0) ? 'Available' : 'Out of Stock' }}
                  </td>
                  <td> {{(!empty($item->languages)) ? implode(", ",json_decode($item->languages)) : ''}} </td>
                  <td> {{$item->sync_status}} </td>

                    <td class="expand-row-msg" data-name="brand_name" data-id="{{$item->id}}">
                        <span class="show-short-brand_name-{{$item->id}}">{{ str_limit($item->job_start_time, 10, '...')}}</span>
                        <span style="word-break:break-all;" class="show-full-brand_name-{{$item->id}} hidden">{{$item->job_start_time}}</span>
                    </td>


{{--                    <td class="expand-row-msg" data-id="{{$item->id}}">--}}
{{--                        <span class="show-short-message-{{$item->job_start_time}}">{{ str_limit($item->job_start_time, 6, '...')}}</span>--}}
{{--                        <span style="word-break:break-all;" class="show-full-message-{{$item->id}} hidden">{{$item->job_start_time}}</span>--}}
{{--                    </td>--}}
                    <td>{{$item->job_end_time}} </td>
                  <td>{{$item->total_request_assigned}} </td>
                  <td>{{$item->total_success}} </td>
                  <td> {{$item->total_error}}</td>
                  <td>{{$item->log_user_name}}</td>
                  <td>{{Carbon\Carbon::parse($item->log_created_at)->format('H:i')}}</td>
                  <td>@if(!empty($item->size_chart_url)) <a href="{{$item->size_chart_url}}" target="__blank">Yes</a> @else No @endif</td>
                  <td>@if($item->queue) #{{$item->queue_id}}({{$item->queue}}) @else - @endif</td>
                  <td>{{$item->tried}}</td>
                  <td style="padding: 1px 7px">
                    <span style="display:flex;">
                      <button data-toggle="modal" data-target="#update_modal" class="btn btn-xs btn-none-border update_modal" data-id="{{ $item}}"><i class="fa fa-edit"></i></button>
                      <button class="btn btn-xs btn-none-border show_error_logs" data-id="{{ $item->log_list_magento_id}}" data-website="{{ $item->store_website_id}}"><i class="fa fa-eye"></i></button>
                      <button class="btn btn-xs btn-product-screenshot" data-id="{{ $item->log_list_magento_id}}" data-website="{{ $item->store_website_id}}"><i style="font-size:13px;" class="fa fa-image"></i></button>
                    </span>
                      <span style="display:flex;">
                        <a target="__blank" href="{{$item->website_url}}/admin/?sku={{$item->getWebsiteSku()}}"><button class="btn btn-xs"><i class="fa fa-globe"></i></button></a>
                      <input style="width:14px;height:15px;margin-left:7px;margin-top:5px;" type="checkbox" class="form-control selectProductCheckbox_class" value="{{ $item->sku }}{{ $item->color }}" websiteid="{{$item->store_website_id}}" name="selectProductCheckbox"/>
                      <i style="cursor: pointer;" class="ml-2 btn btn-xs fa fa-upload upload-single" data-id="{{ $item->product_id }}" title="push to magento" aria-hidden="true"></i>
                    </span>
                  </td>
                </tr>
              @endforeach()