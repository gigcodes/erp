@foreach($data as $key=>$datum)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td><a href="/scrap/gmails/{{ $datum->sender }}" target="_blank">Visit</a></td>
                        <td>{{ $datum->sender }}</td>
                        <td>{{ $datum->received_at }}</td>
                        <td>
                            @if(isset($datum->images[0]))
                            <a href="{{ $datum->images[0] }}">
                                    <img src="{{ $datum->images[0] }}" alt="" style="width: 150px;height: 150px;">
                                </a>
                            @endif
                            
                        </td>
                        <td>
                            @php 
                                $count = 0;
                                @endphp
                                 @foreach($datum->tags as $tag)
                                    @if($count == 6)
                                        @break
                                    @endif
                                    <li>{{ $tag }}</li>
                                    @php
                                    $count++
                                    @endphp
                                @endforeach
                            
                        </td>
                    </tr>
                @endforeach