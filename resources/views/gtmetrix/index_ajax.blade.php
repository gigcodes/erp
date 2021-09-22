
                        @foreach ($list as $key)
                        
                            <tr>
                            
                                <td> <input type="checkbox" name ="multi-run-test-type" class= "multi-run-test" value ="{{ $key->id }}"><a href="{{ $key->website_url }}" target="_blank" title="Goto website"> {{ !empty($key->website_url) ? $key->website_url : $key->store_view_id }} </a></td>
                                <td>{{ $key->test_id }}</td>
                                <td>{{ $key->status }}</td>
                                <td>{{ $key->error }}</td>
                                <td><a href="{{$key->report_url}}" target="_blank" title="Show report"> Report </a></td>
                                <td>{{ $key->html_load_time }}</td>
                                <td>{{ $key->html_bytes }}</td>
                                <td>{{ $key->page_load_time }}</td>
                                <td>{{ $key->page_bytes }}</td>
                                <td>{{ $key->page_elements }}</td>
                                <td>{{ $key->pagespeed_score }}</td>
                                <td>{{ $key->yslow_score }}</td>
                                <td>
                                    @if (!empty($key->resources) && is_array($key->resources))
                                        <ul style="display: inline-block;">
                                            @foreach ($key->resources as $item => $value)
                                                    <li> <a href="{{ $value }}" target="_blank" rel="noopener noreferrer"> {{ $item }} </a> </li>
                                            @endforeach
                                        </ul>
                                    @else
                                     --
                                    @endif
                                    
                                <td>{{ $key->created_at }}</td>
                                <td><a target="__blank" href="{{url('/')}}{{ $key->pdf_file }}"> {{ !empty($key->pdf_file) ? 'Open' : 'N/A' }} </a></td>
                                <td>  
                                    <button class="btn btn-secondary show-history btn-xs" title="Show old history" data-url="{{ route('gtmetrix.history',[ 'id'=>$key->store_view_id ])}}">
                                        <i class="fa fa-history"></i>
                                    </button>
                                    <button class="btn btn-secondary run-test btn-xs" title="Run Test" data-id="{{ $key->id }}">
                                        <i class="fa fa-play"></i>
                                    </button>
                                    @if ($key->status == "completed")
                                        <button class="btn btn-secondary show-pagespeed btn-xs" title="Show Pagespeed Stats" data-url="{{ route('gtmetrix.getPYstats',['type'=>'pagespeed','id'=>$key->test_id])}}" data-type="pagespeed">
                                            <i class="fa fa-tachometer"></i>
                                        </button>
                                        <button class="btn btn-secondary show-pagespeed btn-xs" title="Show Yslow Stats" data-url="{{ route('gtmetrix.getPYstats',['type'=>'yslow','id'=>$key->test_id])}}">
                                            <i class="fa fa-compass"></i>
                                        </button>
                                        <button class="btn btn-secondary show-comparison btn-xs" title="Show comparison" data-url="{{ route('gtmetrix.getstatsCmp',['id'=>$key->test_id])}}">
                                        <i class="fa fa-balance-scale"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                   