@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Jobs / Queues</h2>
        </div>
    </div>

    <div class="col-md-12">
        <div class="panel panel-default">

            <div class="panel-body p-0">
                <form action="{{ route('jobs.list') }}" method="GET">
                    <div class="row p-3">
                        <div class="col-md-3">
                            <label for="start_date">Queue Type</label>
                            <input type="text" class="form-control" id="queue" name="queue" value="{{ old('queue') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="start_date">Reserved Date</label>
                            <input type="date" class="form-control" id="reserved_date" name="reserved_date" value="{{ old('reserved_date') ? date('m/d/Y',strtotime(old('reserved_date'))) : null }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date">Available Date</label>
                            <input type="date" class="form-control" id="available_date" name="available_date" value="{{ old('available_date') ? date('m/d/Y',strtotime(old('available_date'))) : null }}">
                        </div>
                        <button class="btn btn-light" id="submit">
                            <span class="fa fa-filter"></span> Filter Results
                        </button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <th>Queue</th>
                            <th>Payload</th>
                            <th>Attempts</th>
                            <th>Reserved Date</th>
                            <th>Available Date</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach($jobs as $item)
                                <tr>
                                    <td> {{ $item->queue }} </td>
                                    <td>
                                        @if(strlen($item->payload)>60)
                                            {{substr($item->payload,0,60)}} ....<a href="#" data-toggle="modal" data-target="#detail_modal" data-id="{{ $item}}" class="job-details">View Detail</a>
                                        @else
                                            {{$item->payload}}
                                        @endif
                                    </td>
                                    <td> {{ $item->attempts }} </td>
                                    <td>
                                        @if($item->attempts!=0)
                                            {{ date('M d, Y',$item->reserved_at)}}
                                        @endif
                                    </td>
                                    <td> 
                                        @if($item->attempts!=0)
                                            {{ date('M d, Y',$item->reserved_at)}}
                                        @endif
                                    </td>
                                    <td>
                                        <button data-toggle="modal" data-target="#detail_modal" class="btn btn-primary job-details" data-id="{{ $item}}"><i class="fa fa-eye"></i></button>
                                    </td>
                                </tr>
                            @endforeach()
                        </tbody>
                    </table>

                    <div class="text-center">
                        {!! $jobs->appends($filters)->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="detail_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Job Detail</h4>
                </div>
                <div class="modal-body">
                    <div class="col-md-12" id="payload-detail">
                    
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection()
@section('scripts')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
        
        $(document).on("click", ".job-details", function () {
            var detail = $(this).data('id');

            var payload=detail['payload'];
            var type=typeof payload;
            try {
                json=JSON.parse(payload);
                type= typeof json;
                if (type == "object")
                {
                    var html="<b> Display Name </b>"+json['displayName']+"<br><b>Job</b> "+json['job']+"<br><b>maxTries</b> "+json['maxTries']+"<br><b>timeout</b> "+json['timeout']+"<br><b>timeoutAt</b> "+json['timeoutAt']+"<br><b>Data</b> "+JSON.stringify(json['data']);
                    $("#payload-detail").html( html);
                }
            }catch(err) {
                $("#payload-detail").html(payload);
            }
        });

    </script>
    @if (Session::has('errors'))
        <script>
            toastr["error"]("{{ $errors->first() }}", "Message")
        </script>
    @endif
@endsection