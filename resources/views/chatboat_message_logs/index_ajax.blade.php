
              @foreach($logListMagentos as $item)
          <tr>
                  <td>
                    <a class="show-product-information" data-id="{{ $item->id }}" href="/products/{{ $item->id }}" target="__blank">{{ $item->id }}</a>
                  </td>
                  <td> {{$item->model}} </td>
                  <td> {{$item->model_id}} </td>
                  <td> {{$item->cname}} </td>
                  
                  <td> {{$item->chat_message_id}} </td>
                  <td class="expand-row-msg" data-name="message" data-id="{{$item->id}}">
                    <span class="show-short-message-{{$item->id}}">{{ str_limit($item->message, 6, '...')}}</span>
                    <span style="word-break:break-all;" class="show-full-message-{{$item->id}} hidden">{{$item->message}}</span>
                  </td>
                  <td> {{$item->status}} </td>
                  <td>
                    @if(isset($item->updated_at))
                      {{ date('M d, Y',strtotime($item->updated_at))}}
                    @endif
                  </td>
                  
                  <td style="padding: 1px 7px">
                    <button class="btn btn-xs btn-none-border chatbot-log-list" data-id="{{$item->id}}"><i class="fa fa-eye"></i></button>
                  </td>
                </tr>
              @endforeach()
           