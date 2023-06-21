@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Zabbix Webhook Datas ({{ $zabbixWebhookDatas->total() }})</h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-12">
                    <form action="{{ route('zabbix-webhook-data.index') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-2 pd-sm">
                                <input type="text" name="keyword" placeholder="keyword" class="form-control h-100" value="{{ request()->get('keyword') }}">
                            </div>
                            <div class="col-lg-2">
                                <input class="form-control" type="date" name="event_start" value="{{ request()->get('event_start') }}">
                            </div>
                            <div class="col-md-2 pd-sm pl-0 mt-2">
                                 <button type="submit" class="btn btn-image search">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                                <a href="{{ route('zabbix-webhook-data.index') }}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

<div class="tab-content">
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout: fixed;" id="zabbix-webhook-data-list">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="10%">Subject</th>
                            <th width="20%">Message</th>
                            <th width="10%">Event Start</th>
                            <th width="10%">Event Name</th>
                            <th width="10%">Host</th>
                            <th width="10%">Severity</th>
                            <th width="20%">Operational Data</th>
                            <th width="5%">Action</th>
                        </tr>
                        @foreach ($zabbixWebhookDatas as $key => $zabbixWebhookData)
                            <tr data-id="{{ $zabbixWebhookData->id }}">
                                <td>{{ $zabbixWebhookData->id }}</td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($zabbixWebhookData->subject) > 15 ? substr($zabbixWebhookData->subject, 0, 15).'...' :  $zabbixWebhookData->subject }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $zabbixWebhookData->subject }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($zabbixWebhookData->message) > 30 ? substr($zabbixWebhookData->message, 0, 30).'...' :  $zabbixWebhookData->message }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $zabbixWebhookData->message }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($zabbixWebhookData->event_start) > 15 ? substr($zabbixWebhookData->event_start, 0, 15).'...' :  $zabbixWebhookData->event_start }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $zabbixWebhookData->event_start }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($zabbixWebhookData->event_name) > 15 ? substr($zabbixWebhookData->event_name, 0, 15).'...' :  $zabbixWebhookData->event_name }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $zabbixWebhookData->event_name }}
                                    </span>
                                </td>
                                <td style="word-break: break-all">{{ $zabbixWebhookData->host }}</td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($zabbixWebhookData->severity) > 15 ? substr($zabbixWebhookData->severity, 0, 15).'...' :  $zabbixWebhookData->severity }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $zabbixWebhookData->severity }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($zabbixWebhookData->operational_data) > 30 ? substr($zabbixWebhookData->operational_data, 0, 30).'...' :  $zabbixWebhookData->operational_data }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $zabbixWebhookData->operational_data }}
                                    </span>
                                </td>
                                <td class="Website-task"title="">
                                    <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$zabbixWebhookData->id}}')"><i class="fa fa-arrow-down"></i></button>
                                </td>
                            </tr>
                            
                            <tr class="action-btn-tr-{{$zabbixWebhookData->id}} d-none">
                                <td>Action</td>
                                <td id="zabbix_webhook_data_action"  colspan="8" >
                                    <button type="button" class="btn btn-xs show-server-uptimes" title="Action colums" data-id="{{$zabbixWebhookData->id}}" data-type="developer">
                                        <i class="fa fa-info-circle" style="color: #808080;"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                {!! $zabbixWebhookDatas->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        
    })

    function Showactionbtn(id) {
        $(".action-btn-tr-" + id).toggleClass('d-none')
    }

    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });
</script>
@endsection