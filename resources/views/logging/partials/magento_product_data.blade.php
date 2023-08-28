@foreach($logListMagentos as $item)
@php
    $syncStatus = \App\Loggers\LogListMagentoSyncStatus::where('name', $item->sync_status)->first();
@endphp
			    <tr style="background-color: {{$syncStatus?->color}}!important;">
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
                  <td class="expand-row-msg" data-name="message" data-id="{{$item->id}}">
                    <span class="show-short-message-{{$item->id}}">{{ Str::limit($item->message, 6, '..')}}</span>
                    <span style="word-break:break-all;" class="show-full-message-{{$item->id}} hidden">{{$item->message}}</span>
                  </td>
                  <td class="expand-row-msg" data-name="log_created_at" data-id="{{$item->id}}">
                    <span class="show-short-log_created_at-{{$item->id}}">
                      @if(isset($item->log_created_at))
                        {{ Str::limit(date('M d, Y',strtotime($item->log_created_at)), 5, '..') }}
                      @endif
                    </span>
                    <span style="word-break:break-all;" class="show-full-log_created_at-{{$item->id}} hidden">
                      @if(isset($item->log_created_at))
                        {{ date('M d, Y',strtotime($item->log_created_at))}}
                      @endif
                  </span>
                  </td>
                  <td class="expand-row-msg" data-name="website_title" data-id="{{$item->id}}">
                    <span class="show-short-website_title-{{$item->id}}">{{ Str::limit($item->website_title, 6, '..')}}</span>
                    <span style="word-break:break-all;" class="show-full-website_title-{{$item->id}} hidden">{{$item->website_title}}</span>
                  </td>
                  <td class="">
                    {{ (isset($item->stock) && $item->stock > 0) ? 'Available' : 'No Stk' }}
                  </td>
                  <td class="expand-row-msg" data-name="languages" data-id="{{$item->id}}"> 
                    <i data-id="{{$item->product_id}}" class="fa fa-info get-translation-product"></i>
                    <span class="show-short-languages-{{$item->id}}">
                      @if(!empty($item->languages)) {{ Str::limit(implode(", ",json_decode($item->languages)), 2, '..')}} @endif
                    </span>
                    <span style="word-break:break-all;" class="show-full-languages-{{$item->id}} hidden">
                    @if(!empty($item->languages)) {{ implode(", ",json_decode($item->languages)) }} @endif
                  </span>
                  </td>
                  <td class="expand-row-msg" data-name="sync_status" data-id="{{$item->id}}">
                    <span class="show-short-sync_status-{{$item->id}}">{{ Str::limit($item->sync_status, 6, '..')}}</span>
                    <span style="word-break:break-all;" class="show-full-sync_status-{{$item->id}} hidden">{{$item->sync_status}}</span>
                  </td>

                  <td class="expand-row-msg" data-name="job_start_time" data-id="{{$item->id}}">
                        <span class="show-short-job_start_time-{{$item->id}}">{{ Str::limit($item->job_start_time, 8, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-job_start_time-{{$item->id}} hidden">{{$item->job_start_time}}</span>
                  </td>
                  <td class="expand-row-msg" data-name="job_end_time" data-id="{{$item->id}}">
                        <span class="show-short-job_end_time-{{$item->id}}">{{ Str::limit($item->job_end_time, 8, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-job_end_time-{{$item->id}} hidden">{{$item->job_end_time}}</span>
                  </td>
                  <td>{{$item->total_request_assigned}} </td>
                  <td>{{$item->total_success}} </td>
                  <td> {{$item->total_error}}</td>
                  <td class="expand-row-msg" data-name="log_user_name" data-id="{{$item->id}}">
                  <span class="show-short-log_user_name-{{$item->id}}">{{ Str::limit($item->log_user_name, 2, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-log_user_name-{{$item->id}} hidden">{{$item->log_user_name}}</span>
                  </td>
                  <td>{{Carbon\Carbon::parse($item->log_created_at)->format('H:i')}}</td>
                  <td>@if(!empty($item->size_chart_url)) <a class="text-dark" href="{{$item->size_chart_url}}" target="__blank">Yes</a> @else No @endif</td>
                  <td class="expand-row-msg" data-name="queue" data-id="{{$item->id}}">
                    <span class="show-short-queue-{{$item->id}}">@if($item->queue) {{ Str::limit('#'.$item->queue_id.'('.$item->queue.')', 6, '..')}} @else - @endif</span>
                    <span style="word-break:break-all;" class="show-full-queue-{{$item->id}} hidden">@if($item->queue) {{ '#'.$item->queue_id.'('.$item->queue.')'}} @else - @endif</span>
                  </td>
                  <td>{{$item->tried}}</td>
                  <td style="padding: 1px 7px">
                    <span style="display:flex;">
                        <button data-toggle="modal" data-target="#update_modal" class="btn btn-xs btn-none-border update_modal" data-id="{{ $item}}" title="Update Product"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-xs btn-none-border show_error_logs" data-id="{{ $item->log_list_magento_id}}" data-website="{{ $item->store_website_id}}" title="Magento product push error logs"><i class="fa fa-eye"></i></button>
                        <button class="btn btn-xs btn-none-border show_product_push_logs" data-id="{{ $item->log_list_magento_id}}" data-website="{{ $item->store_website_id}}" title="Magento product push logs"><i class="fa fa-eye"></i></button>
                        <button class="btn btn-xs btn-none-border push_journey" data-id="{{ $item->log_list_magento_id}}" data-website="{{ $item->store_website_id}}" title="Product push journey"><i class="fa fa-info-circle"></i></button>
                        <button class="btn btn-xs btn-none-border push_journey_horizontal" data-product_id="{{ $item->product_id }}" data-sku="{{$item->sku}}" data-id="{{ $item->log_list_magento_id}}" data-website="{{ $item->store_website_id}}" title="Product push journey horizontal"><i class="fa fa-info-circle"></i></button>
                        <button class="btn btn-xs btn-none-border show_prices" data-id="{{ $item->log_list_magento_id}}" data-website="{{ $item->store_website_id}}" data-product="{{ $item->product_id}}" title="Price details"><i class="fa fa-money"></i></button>
                        <button class="btn btn-xs btn-product-screenshot" data-id="{{ $item->log_list_magento_id}}" data-website="{{ $item->store_website_id}}" title="Live Screenshot"><i style="font-size:13px;" class="fa fa-image"></i></button>
                        <a target="__blank" href="{{$item->website_url}}/admin/?sku={{$item->getWebsiteSku()}}"><button class="btn btn-xs" title="Store website"><i class="fa fa-globe"></i></button></a>
                        <input style="width:14px;height:15px;margin-left:7px;margin-top:5px;" type="checkbox" class="form-control selectProductCheckbox_class" value="{{ $item->sku }}{{ $item->color }}" websiteid="{{$item->store_website_id}}" name="selectProductCheckbox"/>
                        <i style="cursor: pointer;" class="ml-2 btn btn-xs fa fa-upload upload-single" data-id="{{ $item->product_id }}" title="push to magento" aria-hidden="true"></i>
                    </span>
                  </td>
                </tr>
              @endforeach()