@foreach ($logs as $log)

                <tr>
                     <td>{{ $filename }}</td>
                    <td class="expand-row table-hover-cell"><span class="td-mini-container">
                        {{ strlen( $log ) > 110 ? substr( $log , 0, 110).'...' :  $log }}
                        </span>
                        <span class="td-full-container hidden">
                        {{ $log }}
                        </span>
                    </td>
                </tr>
@endforeach