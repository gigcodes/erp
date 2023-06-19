<!-- Modal -->
@php
 $liveLaravelLogsSummary = (new \App\Http\Controllers\LaravelLogController)->liveLogsSummary();
@endphp
<div id="live-laravel-logs-summary-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Live Laravel Logs</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th width="20%">Filename</th>
                            <th width="10%">Channel</th>
                            <th width="40%">Log</th>
                        </tr>
                    </thead>
                    <tbody class="show-search-password-list">
                        @if (isset($liveLaravelLogsSummary['logs']))
                        @foreach ($liveLaravelLogsSummary['logs'] as $log)
                            @php
                                $str = $log;
                                $temp1 = explode(".",$str);
                                $temp2 = explode(" ",$temp1[0]);
                                $type = $temp2[2];

                                $file_name = explode('===',$log);
                                $log = str_replace("===".$file_name[1],"",$log);
                            @endphp
                        
                            <tr>
                                <td>{{ $file_name[1] }}</td>
                                <td>{{ $type }}</td>
                                <td class="expand-row table-hover-cell">
                                    <span class="td-mini-container">
                                    {{ strlen( $log ) > 110 ? substr( $log , 0, 110).'...' :  $log }}
                                    </span>
                                    <span class="td-full-container hidden">
                                    {{ $log }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table> 
           </div>
        </div>
    </div>
</div>
