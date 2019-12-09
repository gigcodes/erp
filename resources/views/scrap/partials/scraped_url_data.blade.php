@foreach ($logs as $log)
                <tr>
                     <td>{{ $log->website }}</td>
                    <td class="expand-row table-hover-cell"><span class="td-mini-container">
                        {{ strlen( $log->url ) > 30 ? substr( $log->url , 0, 30).'...' :  $log->url }}
                        </span>
                        <span class="td-full-container hidden">
                        {{ $log->url }}
                        </span>
                    </td>
                    <td>{{ $log->sku }}</td>
                    <td>{{ $log->brand }}</td>
                    <td>{{ $log->title }}</td>
                    <td>{{ $log->currency }}</td>
                    <td>{{ $log->price }}</td>
                    <td>{{ $log->created_at->format('d-m-y') }}</td>
                    <td>{{ $log->updated_at->format('d-m-y') }}</td>
                </tr>
@endforeach

