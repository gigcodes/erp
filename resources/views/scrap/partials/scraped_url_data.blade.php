@foreach ($logs as $log)
    
                <tr @if($log->validated == 0) style="background:red !important;" @endif>
                     <td>{{ $log->website }}</td>
                    <td class="expand-row table-hover-cell"><span class="td-mini-container">
                        <a href="{{ $log->url }}" target="_blank">{{ strlen( $log->url ) > 30 ? substr( $log->url , 0, 30).'...' :  $log->url }}</a>
                        </span>
                        <span class="td-full-container hidden">
                        <a href="{{ $log->url }}" target="_blank">{{ $log->url }}</a>
                        </span>
                    </td>
                   <td>{{ $log->sku }}</td>
                    <td>{{ $log->brand->name }}</td>
                    <td>{{ $log->title }}</td>
                    <td>{{ $log->currency }}</td>
                    <td>{{ $log->price }}</td>
                    <td>{{ $log->created_at->format('d-m-y H:i:s') }}</td>
                    <td>{{ $log->updated_at->format('d-m-y H:i:s') }}</td>
                    @if($response != null)
                    
                    @if(in_array('color',$response['columns']))
                    <th>@if(unserialize($log->properties)) {{ unserialize($log->properties)['color'] }} @endif</th>
                     @endif
                    
                    @if(in_array('category',$response['columns']))
                    <th>@if($log->category != null && $log->category != '')
                            @if(is_array(unserialize($log->category)))
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
                    <th>@if(isset(unserialize($log->properties)['size']) )
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

                
            
