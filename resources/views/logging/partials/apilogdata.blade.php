@foreach ($logs as $log)

                <tr class="currentPage" data-page="{{$logs->currentPage()}}">

                    <td>{{$log->id}}</td>

                    <td>{{$log->ip}}</td>

                    @if($log->api_name)
                        @php 
                            $api_name = explode('@', $log->api_name);
                        @endphp
                        <td>{{ wordwrap($api_name[0], 30) }}</td>

                        <td>{{ wordwrap($api_name[1], 30) }}</td>
                    @else
                    <td></td>
                    <td></td>
                    @endif
                    <td>{{$log->method}}</td>

                    

                        
                     <td class="expand-row table-hover-cell">
                        <span class="td-mini-container">
                        {{ strlen( $log->url ) > 50 ? substr( $log->url , 0, 50).'...' :  $log->url }}
                        </span>
                        <span class="td-full-container hidden">
                        {{ $log->url }}
                        </span>
                    </td>
                     
                    <td class="expand-row table-hover-cell"><span class="td-mini-container">
                        {{ strlen( $log->request) > 60 ? substr( $log->request, 0, 60).'...' :  $log->request}}
                        </span>
                        <span class="td-full-container hidden">
                        {{ $log->request}}
                        </span>
                    </td>
                    <td>{{ $log->status_code }}</td>
                    <td>{{ $log->time_taken }}</td>
                    <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d-m-y H:i:s')  }}</td>
                    <td><button class="btn btn-warning showModalResponse" data-id="{{$log->id}}">View</button></td>
                </tr>
@endforeach