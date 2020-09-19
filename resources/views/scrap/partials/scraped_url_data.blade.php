@foreach ($logs as $log)
    
                <tr @if($log->validated == 0) style="background:red !important;" @endif>
                    @if($log->product_id)
                    <td><a href="{{ route('products.show', $log->product_id) }}" target="_blank">{{ $log->product_id }}</a></td>
                    @else
                    <td></td>
                    @endif
                    <td>{{ $log->website }}</td>
                    <td class="expand-row table-hover-cell"><span class="td-mini-container">
                        <a href="{{ $log->url }}" target="_blank">{{ strlen( $log->url ) > 20 ? substr( $log->url , 0, 20).'...' :  $log->url }}</a>
                        </span>
                        <span class="td-full-container hidden">
                        <a href="{{ $log->url }}" target="_blank">{{ $log->url }}</a>
                        </span>
                    </td>
                   <td class="expand-row table-hover-cell">
                        <span class="td-mini-container">
                            {{ strlen( $log->sku ) > 20 ? substr( $log->sku , 0, 20).'...' :  $log->sku }}
                        </span>
                        <span class="td-full-container hidden">
                            {{ $log->sku }}
                        </span>
                       
                    </td>
                    <td>{{ $log->brand->name }}</td>
                    <td>{{ $log->title }}</td>
                    <td>{{ $log->currency }}</td>
                    <td>{{ $log->price }}</td>
                    <td>
                        @if($log->images)
                            <div class="green_img"></div>
                        @else
                            <div class="red_img"></div>
                        @endif
                    </td>
                    <td>{{ $log->created_at->format('d-m-y') }}</td>
                    <!-- <td>{{ $log->updated_at->format('d-m-y H:i:s') }}</td> -->
                    @if($response != null)
                    
                    @if(in_array('color',$response['columns']))
                    <th class="expand-row table-hover-cell">
                        @if(is_array($log->properties))
                            <span class="td-mini-container">
                                {{ isset($log->properties['color'])?strlen($log->properties['color']) > 5 ? substr( $log->properties['color'] , 0, 5).'...' :  $log->properties['color']:'' }}
                            </span>
                            <span class="td-full-container hidden">
                                {{ isset($log->properties['color'])?$log->properties['color']:'' }}
                            </span>                            
                        @else
                            {{ unserialize($log->properties)['color'] }}                         
                        @endif
                    </th>
                    @endif
                    
                    @if(in_array('category',$response['columns']))
                    <th class="expand-row table-hover-cell">@if(($log->category != null && $log->category != '') || isset($log->properties['category']))
                            @if(isset($log->properties['category']))
                                @if(is_array($log->properties['category']))
                                    <span class="td-mini-container">
                                            {{ count($log->properties['category']) > 2 ? substr( implode(' , ',$log->properties['category']) , 0, 10).'...' : implode(' , ',$log->properties['category']) }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ implode(' , ',$log->properties['category']) }}
                                    </span>  
                                @else
                                    {{ $log->properties['category'] }}
                                @endif
                            @elseif(is_array(unserialize($log->category)))
                                {{ implode(' ',unserialize($log->category) )}} 
                            @else
                                {{ unserialize($log->category) }} 
                            @endif
                        @endif
                    </th>
                    @endif

                    @if(in_array('description',$response['columns']))
                    <th>{{ $log->description }}</th>
                    @endif

                    @if(in_array('size_system',$response['columns']))
                    <th>{{ $log->size_system }}</th>
                    @endif

                    @if(in_array('is_sale',$response['columns']))
                    <th>{{ $log->is_sale }}</th>
                    @endif

                    @if(in_array('gender',$response['columns']))
                    <th>@if(isset(unserialize($log->properties)['gender'])) 
                        {{ unserialize($log->properties)['gender']  }} 
                        @endif
                    </th>
                    @endif
                    
                    @if(in_array('composition',$response['columns']))
                    <th>@if(isset(unserialize($log->properties)['composition'])) 
                        {{ unserialize($log->properties)['composition']  }} 
                        @endif
                    </th>
                    @endif

                    @if(in_array('size',$response['columns']))
                    <th class="expand-row table-hover-cell">
                        @if(is_array($log->properties))                            
                            @if(isset($log->properties['sizes']))
                                @if(is_array($log->properties['sizes']))
                                <span class="td-mini-container">
                                    {{ count($log->properties['sizes']) > 2 ? substr( implode(' , ',$log->properties['sizes']) , 0, 10).'...' : implode(' , ',$log->properties['sizes']) }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ implode(' , ',$log->properties['sizes']) }}
                                </span>                                        
                                @else
                                    {{ $log->properties['sizes'] }}
                                @endif
                            @elseif(isset($log->properties['size']))
                                @if(is_array($log->properties['size']))
                                    <span class="td-mini-container">
                                        {{ count($log->properties['size']) > 2 ? substr( implode(' , ',$log->properties['size']) , 0, 10).'...' : implode(' , ',$log->properties['size']) }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ implode(' , ',$log->properties['size']) }}
                                    </span>    
                                @else
                                    {{ $log->properties['size'] }}
                                @endif
                            @endif
                        @elseif(isset(unserialize($log->properties)['size']) )
                            @if(is_array(unserialize($log->properties)['size']))
                                {{ implode(' , ',unserialize($log->properties)['size'] )}} 
                            @else
                                {{ unserialize($log->properties)['size'] }}
                            @endif    
                        @elseif(isset(unserialize($log->properties)['sizes']))
                            @if(is_array(unserialize($log->properties)['sizes']))
                                {{ implode(' , ',unserialize($log->properties)['sizes'] )}} 
                            @else
                                {{ unserialize($log->properties)['sizes'] }}
                            @endif     
                        @endif
                    </th>
                    @endif
                    @if(in_array('dimension',$response['columns']))
                    <th class="expand-row table-hover-cell">
                        @if(is_array($log->properties))                            
                            @if(isset($log->properties['dimension']))
                                @if(is_array($log->properties['dimension']))
                                <span class="td-mini-container">
                                    {{ count($log->properties['dimension']) > 2 ? substr( implode(' , ',$log->properties['dimension']) , 0, 10).'...' : implode(' , ',$log->properties['dimension']) }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ implode(' , ',$log->properties['dimension']) }}
                                </span>                                        
                                @else
                                    {{ $log->properties['dimension'] }}
                                @endif
                            @endif
                        @endif
                    </th>
                    @endif
                    @if(in_array('lmeasurement',$response['columns']))
                    <th>@if(isset(unserialize($log->properties)['lmeasurement'])) {{ unserialize($log->properties)['lmeasurement']  }} @endif</th>
                    @endif

                    @if(in_array('hmeasurement',$response['columns']))
                    <th>@if(isset(unserialize($log->properties)['hmeasurement'])) {{ unserialize($log->properties)['hmeasurement']  }} @endif</th>
                    @endif

                    @if(in_array('dmeasurement',$response['columns']))
                    <th>@if(isset(unserialize($log->properties)['dmeasurement'])) {{ unserialize($log->properties)['dmeasurement']  }} @endif</th>
                    @endif

                    @if(in_array('measurement_size_type',$response['columns']))
                    <th>@if(isset(unserialize($log->properties)['measurement_size_type'])) {{ unserialize($log->properties)['measurement_size_type']  }} @endif</th>
                    @endif

                    @endif
                </tr>
                
@endforeach

                
            
