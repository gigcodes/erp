@extends('layouts.app')

@section('title', 'Larave Log List')

@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
     <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
    </style>
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Scrap Api Logs</h2>

        </div>
    </div>

    <div class="mt-3 col-md-12">
        <table class="table table-bordered table-striped" id="log-table" style="width: 100%">
            <thead>
            <tr>
                <th width="5%">#</th>
                <th width="10%">Scraper</th>
                <th width="5%">Server id</th>
                <th width="80%">Logs</th>
            </tr>
            @php
                $i = 1;
            @endphp
            
            @foreach ($api_logs as $log)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $log->scraper_name }}</td>
                    <td>{{ $log->server_id }}</td>
                    @if (strlen($log->log_messages) > 250)
                        <td style="word-break: break-word;" data-log_message="{{ $log->log_messages }}" class="log-message-popup">{{ substr($log->log_messages,0,250) }}...</td>    
                    @else
                        <td style="word-break: break-word;">{{ $log->log_messages }}</td>
                    @endif
                </tr>
            @endforeach
                <tr>{{ $api_logs->links() }}</tr>
            </thead>

        </table>
    </div>

    <!--Log Messages Modal -->
    <div id="logMessageModel" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Scrap Api Log</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p style="word-break: break-word;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    
        </div>
    </div>
 

@endsection
@section("scripts")
    <script>
        $(document).on('click','.log-message-popup',function(){
            $('#logMessageModel').modal('show');
            $('#logMessageModel p').text($(this).data('log_message'));
        })
    </script>
@endsection