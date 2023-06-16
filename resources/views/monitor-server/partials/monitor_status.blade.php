<!-- Modal -->
@php
 $status = \App\Models\MonitorServer::where('status', '=','Off')->get();
@endphp
<div id="create-status-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Website Off Status</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th width="5%">s.no</th>
                            <th width="20%">Website</th>
                        </tr>
                    </thead>
                    <tbody class="show-search-password-list">
                        @if (count($status) > 0)
                        @foreach($status as $key =>$stat)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$stat->ip }}</td>
                        </tr>
                        @endforeach
                        @else
                        <tr><td colspan="2">No records found</td></tr>
                        @endif
                    </tbody>
                </table> 
           </div>
        </div>
    </div>
</div>
