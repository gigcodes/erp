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
                    <td>{{ $log->brand }}</td>
                    <td>{{ $log->title }}</td>
                    <td>{{ $log->currency }}</td>
                    <td>{{ $log->price }}</td>
                    <td>{{ $log->created_at->format('d-m-y H:i:s') }}</td>
                    <td>{{ $log->updated_at->format('d-m-y H:i:s') }}</td>
                </tr>
@endforeach

